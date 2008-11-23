/**
 * $Id: lines.c,v 1.21 2008/08/06 09:50:06 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *      See COPYING file for copying and redistribution conditions.
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; version 2 of the License.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 * Contact: <yves@raubacapeu.net>
 */

#include <stdio.h>
#include <math.h>
#include <assert.h>
#include <stdlib.h>
#include "defs.h"
#include "types.h"
#include "ortho.h"
#include "lines.h"

/**
 * The shore line is composed of an array of "coast zones" consisting of
 * an int, the number of segments to be checked, and the segments, with
 * longitude and latitude coordinates of the two points of each segment
 * in radians, this is a global variable 
 */
extern vlmc_context *global_vlmc_context;

/**
 * input: longitude/latitude of old position -> new position
 * longitude/latitude of wpa->wpb or coast segment
 * return a double is intersects, with inter_longitude/latitude filled
 * -1 otherwise */

double intersects(double latitude, double longitude,
		  double new_latitude, double new_longitude,
		  double seg_a_latitude, double seg_a_longitude,
		  double seg_b_latitude, double seg_b_longitude, 
		  double *inter_latitude, double *inter_longitude) {
  double x, y, x1, x2, t, t_seg,d;

#if 0
  /* normalization of longitude */
  longitude = fmod(longitude, TWO_PI);
  new_longitude = fmod(new_longitude, TWO_PI);
  seg_a_longitude = fmod(seg_a_longitude, TWO_PI);
  seg_b_longitude = fmod(seg_b_longitude, TWO_PI);
#endif /* 0 */

  /* then move to 0 -> TWO_PI interval */
  if (longitude <0) {
    longitude += TWO_PI;
  }
  if (new_longitude <0) {
    new_longitude += TWO_PI;
  }
  if (seg_a_longitude <0) {
    seg_a_longitude += TWO_PI;
  }
  if (seg_b_longitude <0) {
    seg_b_longitude += TWO_PI;
  }
  /* now check if the segments are crossing the '0' line */
  if (fabs(longitude - new_longitude) > PI) {
    if (longitude > new_longitude) {
      longitude -= TWO_PI;
    } else {
      new_longitude -= TWO_PI;
    }
  }
  if (fabs(seg_a_longitude - seg_b_longitude) > PI) {
    if (seg_a_longitude > seg_b_longitude) {
      seg_a_longitude -= TWO_PI;
    } else {
      seg_b_longitude -= TWO_PI;
    }
  }
  /* 
     and the last check, is one segment on the negative side, but 
     in the TWO-PI range, while the other one is across the 0 line in the 0
     range
  */
  if (fabs(longitude - seg_a_longitude) > PI) {
    if (longitude > seg_a_longitude) {
      longitude -= TWO_PI;
      new_longitude -= TWO_PI;
    } else {
      seg_a_longitude -= TWO_PI;
      seg_b_longitude -= TWO_PI;
    }
  }
  /* normalization done, perform regular checks */
#ifdef DEBUG
  printf("Checking intersection between %.10fx%.10f->%.10fx%.10f and %.10fx%.10f->%.10fx%.10f\n",
	 latitude, longitude, new_latitude, new_longitude,
	 seg_a_latitude, seg_a_longitude, seg_b_latitude,seg_b_longitude);
#endif /* DEBUG */

  x1 = (new_longitude - longitude);
  x2 = (seg_b_longitude - seg_a_longitude);
  
  d = ((seg_b_latitude - seg_a_latitude)*x1 - x2 * (new_latitude - latitude));
  
  if (d == 0.0) {
    return -1;
  }
  x = (longitude - seg_a_longitude);
  y = (latitude - seg_a_latitude);

  t = (x2*y - (seg_b_latitude - seg_a_latitude)*x) / d;
  /* out of the first segment... return ASAP */
  if (t < INTER_MIN_LIMIT || t > INTER_MAX_LIMIT) {
    return -1;
  }

  t_seg = (x1*y - (new_latitude - latitude)*x) / d;
  
#ifdef DEBUG
  printf("segment ratio: %.20f %.4f and %.4f\n", d, t_seg, t);
#endif /* DEBUG */
  if (t_seg>=INTER_MIN_LIMIT && t_seg <=INTER_MAX_LIMIT) {
    *inter_longitude = longitude + t*(new_longitude - longitude);
    *inter_latitude = latitude + t*(new_latitude - latitude);
#ifdef DEBUG
    printf(" -> YES\n");
#endif /* DEBUG */
    return t;
  }
  return -1;
}

#ifdef PARANOID_COAST_CHECK
/**
 * input: longitude/latitude of old position -> new position
 * longitude/latitude of wpa->wpb or coast segment
 * return a double is intersects, with inter_longitude/latitude filled
 * -1 otherwise */
double paranoid_intersects(double latitude, double longitude,
			   double new_latitude, double new_longitude,
			   double seg_a_latitude, double seg_a_longitude,
			   double seg_b_latitude, double seg_b_longitude, 
			   double *inter_latitude, double *inter_longitude) {
  double x, y, x1, x2, t, t_seg,d;

  /* then move to 0 -> TWO_PI interval */
  if (longitude <0) {
    longitude += TWO_PI;
  }
  if (new_longitude <0) {
    new_longitude += TWO_PI;
  }
  if (seg_a_longitude <0) {
    seg_a_longitude += TWO_PI;
  }
  if (seg_b_longitude <0) {
    seg_b_longitude += TWO_PI;
  }
  /* now check if the segments are crossing the '0' line */
  if (fabs(longitude - new_longitude) > PI) {
    if (longitude > new_longitude) {
      longitude -= TWO_PI;
    } else {
      new_longitude -= TWO_PI;
    }
  }
  if (fabs(seg_a_longitude - seg_b_longitude) > PI) {
    if (seg_a_longitude > seg_b_longitude) {
      seg_a_longitude -= TWO_PI;
    } else {
      seg_b_longitude -= TWO_PI;
    }
  }
  /* 
     and the last check, is one segment on the negative side, but 
     in the TWO-PI range, while the other one is across the 0 line in the 0
     range
  */
  if (fabs(longitude - seg_a_longitude) > PI) {
    if (longitude > seg_a_longitude) {
      longitude -= TWO_PI;
      new_longitude -= TWO_PI;
    } else {
      seg_a_longitude -= TWO_PI;
      seg_b_longitude -= TWO_PI;
    }
  }
  /* normalization done, perform regular checks */

  x1 = (new_longitude - longitude);
  x2 = (seg_b_longitude - seg_a_longitude);
  
  d = ((seg_b_latitude - seg_a_latitude)*x1 - x2 * (new_latitude - latitude));
  
  if (d == 0.0) {
    return -1;
  }
  x = (longitude - seg_a_longitude);
  y = (latitude - seg_a_latitude);

  t = (x2*y - (seg_b_latitude - seg_a_latitude)*x) / d;
  /* out of the first segment... return ASAP */
  if (t < COAST_INTER_MIN_LIMIT || t > COAST_INTER_MAX_LIMIT) {
    return -1;
  }

  t_seg = (x1*y - (new_latitude - latitude)*x) / d;
  
  if (t_seg>=COAST_INTER_MIN_LIMIT && t_seg <=COAST_INTER_MAX_LIMIT) {
    *inter_longitude = longitude + t*(new_longitude - longitude);
    *inter_latitude = latitude + t*(new_latitude - latitude);
    return t;
  }
  return -1;
}
#endif /* PARANOID_COAST_CHECK */

/**
 * input: longitude/latitude of boat's old and new position
 * return a double is intersects, with inter_longitude/latitude filled
 * -1 otherwise 
 */
double check_coast(double latitude, double longitude,
		   double new_latitude, double new_longitude, 
		   double *inter_latitude, double *inter_longitude) {
  double min_val = 1000.0;
  double inter;
  int i_lat, i_long, i_new_lat, i_new_long, idx, x_nb, y_nb;
  int i, j, k, i_min, i_max, j_min, j_max, nb_segments;
  coast *wholecoast;
  coast_zone *c_zone;
  coast_seg *seg_array;
  double t_lat, t_long;
  double min_lat, min_long;

  wholecoast = global_vlmc_context->shoreline;
  x_nb = wholecoast->nb_grid_x;
  y_nb = wholecoast->nb_grid_y;
  /* FIXME, must do sanity check on boundaries accross 0 for line tests */

#ifdef PARANOID_COAST_CHECK  
#  define _check_intersection_with_array				\
  nb_segments = c_zone->nb_segments;					\
  seg_array = c_zone->seg_array;					\
  for (k=0; k<nb_segments; k++) {					\
    inter = paranoid_intersects(latitude, longitude,			\
				new_latitude, new_longitude,		\
				seg_array->latitude_a,			\
				seg_array->longitude_a,			\
				seg_array->latitude_b,			\
				seg_array->longitude_b,			\
				&t_lat, &t_long);			\
    seg_array++;							\
    if (inter>=COAST_INTER_MIN_LIMIT && inter<=COAST_INTER_MAX_LIMIT) {	\
      if (inter < min_val) {						\
	min_val = inter;						\
	min_long = t_long;						\
	min_lat = t_lat;						\
      }									\
    }									\
  }
#else
#  define _check_intersection_with_array				\
  nb_segments = c_zone->nb_segments;					\
  seg_array = c_zone->seg_array;					\
  for (k=0; k<nb_segments; k++) {					\
    inter = intersects(latitude, longitude,				\
		       new_latitude, new_longitude,			\
		       seg_array->latitude_a, seg_array->longitude_a,	\
		       seg_array->latitude_b, seg_array->longitude_b,	\
		       &t_lat, &t_long);				\
    seg_array++;							\
    if (inter>=INTER_MIN_LIMIT && inter<=INTER_MAX_LIMIT) {		\
      if (inter < min_val) {						\
	min_val = inter;						\
	min_long = t_long;						\
	min_lat = t_lat;						\
      }									\
    }									\
  }
#endif /* PARANOID_COAST_CHECK */

  /* to keep the compiler happy */
  t_lat=t_long=min_lat=min_long=0.0;

  i_lat      = floor(radToDeg(latitude)*10.0) + 900;
  i_long     = floor(radToDeg(longitude)*10.0);
  i_new_lat  = floor(radToDeg(new_latitude)*10.0) + 900;
  i_new_long = floor(radToDeg(new_longitude)*10.0);

  if (i_long < 0) {
    i_long += 3600;
  } else if (i_long > 3600) {
    i_long -= 3600;
  }
  if (i_new_long < 0) {
    i_new_long += 3600;
  } else if (i_new_long > 3600) {
    i_new_long -= 3600;
  }

  /* get the loop in the right order */
  if (i_long < i_new_long) {
    i_min = i_long;
    i_max = i_new_long;
  } else {
    i_min = i_new_long;
    i_max = i_long;
  }
  if (i_lat < i_new_lat) {
    j_min = i_lat;
    j_max = i_new_lat;
  } else {
    j_min = i_new_lat;
    j_max = i_lat;
  }

#if DEBUG
  printf("Checking segments: [%d][%d] -> %d\n", i_long, i_lat, 
	 global_vlmc_context->shoreline[i_long][i_lat].nb_segments); 
#endif /* DEBUG */

  if ((i_max - i_min) < 1800) {
    for (i=i_min; i<=i_max; i++) {
      for (j=j_min; j<=j_max; j++) {
	idx = i*y_nb+j;
	c_zone=&wholecoast->zone_array[idx];
	_check_intersection_with_array;
      }
    }
  } else {
    for (j=j_min; j<=j_max; j++) {
      for (i=i_max; i<3601; i++) {
	idx = i*y_nb+j;
	c_zone=&wholecoast->zone_array[idx];
	_check_intersection_with_array;
      }
      for (i=i_min; i>=0; i--) {
	idx = i*y_nb+j;
	c_zone=&wholecoast->zone_array[idx];
	_check_intersection_with_array;
      }
    }
  }
#ifdef PARANOID_COAST_CHECK
  if (min_val<=COAST_INTER_MAX_LIMIT) {
#else
  if (min_val<=INTER_MAX_LIMIT) {
#endif /* PARANOID_COAST_CHECK */
    *inter_latitude = min_lat;
    *inter_longitude = min_long;
    return min_val;
  }
  return -1;
}

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate. It is at best an approximation, as the intersection
 * algorithm is not valid for long distances
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment
 */
double distance_to_line(double latitude, double longitude, 
			double latitude_a, double longitude_a,
			double latitude_b, double longitude_b) {
  double ratio;
  return distance_to_line_ratio(latitude, longitude, 
				latitude_a, longitude_a,
				latitude_b, longitude_b, &ratio);
}

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate. It is at best an approximation, as the intersection
 * algorithm is not valid for long distances
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment
 */
double distance_to_line_ratio(double latitude, double longitude,
			      double latitude_a, double longitude_a,
			      double latitude_b, double longitude_b,
			      double *ab_ratio) {
  double ortho_a, ortho_b, min_dist, ab_dist, t_dist;
  double longitude_x, latitude_x, intersect;

  ortho_a = ortho_distance(latitude, longitude, latitude_a, longitude_a);
  ortho_b = ortho_distance(latitude, longitude, latitude_b, longitude_b);
  ab_dist = ortho_distance(latitude_a, longitude_a, latitude_b, longitude_b);
  
  min_dist = fmin(ortho_a, ortho_b);
  /* we construct a line form the point, orthogonal to the segment, long of
     at least min_dist */
  latitude_x = latitude + (longitude_a - longitude_b) * min_dist / ab_dist;
  longitude_x = longitude + (latitude_b - latitude_a) * min_dist / ab_dist;
  
  intersect = intersects(latitude_a, longitude_a, latitude_b, longitude_b,
			 latitude, longitude, latitude_x, longitude_x,
			 &latitude_x, &longitude_x);
  if (intersect>=INTER_MIN_LIMIT && intersect<=INTER_MAX_LIMIT) { 
    t_dist = ortho_distance(latitude, longitude, latitude_x, longitude_x);
#ifdef DEBUG
    printf("Min dist: %.3f, found dist: %.3f\n", min_dist, t_dist);
#endif /* DEBUG */
    *ab_ratio = intersect;
    return fmin(min_dist, t_dist);
  }

  /* same as above, but opposite way (it could be factored in one, but this 
     reduces the odds of crossing the 0 line */
  latitude_x = latitude + (longitude_b - longitude_a) * min_dist / ab_dist;
  longitude_x = longitude + (latitude_a - latitude_b) * min_dist / ab_dist;
  
  intersect = intersects(latitude_a, longitude_a, latitude_b, longitude_b,
			 latitude, longitude, latitude_x, longitude_x,
			 &latitude_x, &longitude_x);
  if (intersect>=INTER_MIN_LIMIT && intersect<=INTER_MAX_LIMIT) { 
    t_dist = ortho_distance(latitude, longitude, latitude_x, longitude_x);
#ifdef DEBUG
    printf("Min dist: %.3f, found dist: %.3f\n", min_dist, t_dist);
#endif /* DEBUG */
    *ab_ratio = intersect;
    return fmin(min_dist, t_dist);
  }
  return min_dist;
}

