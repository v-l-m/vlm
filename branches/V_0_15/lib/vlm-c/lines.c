/**
 * $Id: lines.c,v 1.33 2010-12-09 13:54:26 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *
 *      See COPYING file for copying and redistribution conditions.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * input: longitude/latitude of old position -> new position
 * longitude/latitude of wpa->wpb or coast segment
 * return a double is intersects, with inter_longitude/latitude filled
 * -1 otherwise */

double __intersects_no_norm PARAM10(double, double, double, double,
				    double, double, double, double,
				    double *, double *);

#ifdef SAVE_MEMORY
#  include "dist_gshhs.h"
double intersects_trans PARAM10(double, double, double, double,
			    int, int, int, int,
			    double *, double *);
#  define intersects_c(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb) \
  intersects_trans(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb);
#  ifdef PARANOID_COAST_CHECK
double paranoid_intersects_trans PARAM10(double, double, double, double,
					 int, int, int, int,
					 double *, double *);
#    define paranoid_intersects_c(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb)	\
  paranoid_intersects_trans(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb);
#  endif /* PARANOID_COAST_CHECK */
#else
#  define intersects_c(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb) \
  intersects(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb);
#  ifdef PARANOID_COAST_CHECK
#    define paranoid_intersects_c(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb)	\
  paranoid_intersects(aa,ab,ac,ad,ba,bb,bc,bd,ra,rb);
#  endif /* PARANOID_COAST_CHECK */
#endif /* SAVE_MEMORY */

#ifdef SAVE_MEMORY
double intersects_trans(double latitude, double longitude,
			double new_latitude, double new_longitude,
			int seg_a_latitude, int seg_a_longitude,
			int seg_b_latitude, int seg_b_longitude, 
			double *inter_latitude, double *inter_longitude) {
  double rad_seg_a_latitude, rad_seg_a_longitude;
  double rad_seg_b_latitude, rad_seg_b_longitude;

  rad_seg_a_latitude  = degToRad((double)seg_a_latitude * GSHHS_SCL);
  rad_seg_a_longitude = degToRad((double)seg_a_longitude * GSHHS_SCL);
  rad_seg_b_latitude  = degToRad((double)seg_b_latitude * GSHHS_SCL);
  rad_seg_b_longitude = degToRad((double)seg_b_longitude * GSHHS_SCL);
  return intersects(latitude, longitude, new_latitude, new_longitude,
		    rad_seg_a_latitude, rad_seg_a_longitude,
		    rad_seg_b_latitude, rad_seg_b_longitude,
		    inter_latitude, inter_longitude);
}
#  ifdef PARANOID_COAST_CHECK
double paranoid_intersects_trans(double latitude, double longitude,
				 double new_latitude, double new_longitude,
				 int seg_a_latitude, int seg_a_longitude,
				 int seg_b_latitude, int seg_b_longitude, 
				 double *inter_latitude, 
				 double *inter_longitude) {
  double rad_seg_a_latitude, rad_seg_a_longitude;
  double rad_seg_b_latitude, rad_seg_b_longitude;
  
  rad_seg_a_latitude  = degToRad((double)seg_a_latitude * GSHHS_SCL);
  rad_seg_a_longitude = degToRad((double)seg_a_longitude * GSHHS_SCL);
  rad_seg_b_latitude  = degToRad((double)seg_b_latitude * GSHHS_SCL);
  rad_seg_b_longitude = degToRad((double)seg_b_longitude * GSHHS_SCL);
  return paranoid_intersects(latitude, longitude, new_latitude, new_longitude,
			     rad_seg_a_latitude, rad_seg_a_longitude,
			     rad_seg_b_latitude, rad_seg_b_longitude,
			     inter_latitude, inter_longitude);
}
#  endif /* PARANOID_COAST_CHECK */
#endif /* SAVE_MEMORY */
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

  return __intersects_no_norm(latitude, longitude, new_latitude, new_longitude,
			      seg_a_latitude, seg_a_longitude,
			      seg_b_latitude, seg_b_longitude, 
			      inter_latitude, inter_longitude);
}

/**
 * input: longitude/latitude of old position -> new position
 * longitude/latitude of wpa->wpb or coast segment
 * return a double is intersects, with inter_longitude/latitude filled
 * -1 otherwise */

double __intersects_no_norm(double latitude, double longitude,
			    double new_latitude, double new_longitude,
			    double seg_a_latitude, double seg_a_longitude,
			    double seg_b_latitude, double seg_b_longitude, 
			    double *inter_latitude, double *inter_longitude) {
  double x, y, x1, x2, t, t_seg,d;
  
  /* normalization done, perform regular checks */
#ifdef DEBUG
  printf("Checking intersection between %.10fx%.10f->%.10fx%.10f and %.10fx%.10f->%.10fx%.10f\n",
	 radToDeg(latitude), radToDeg(longitude),
	 radToDeg(new_latitude), radToDeg(new_longitude),
	 radToDeg(seg_a_latitude), radToDeg(seg_a_longitude),
	 radToDeg(seg_b_latitude), radToDeg(seg_b_longitude));
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
    inter = paranoid_intersects_c(latitude, longitude,			\
				  new_latitude, new_longitude,		\
				  seg_array->latitude_a,		\
				  seg_array->longitude_a,		\
				  seg_array->latitude_b,		\
				  seg_array->longitude_b,		\
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
    inter = intersects_c(latitude, longitude,				\
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

double distance_to_line_ratio(double latitude, double longitude,
			      double latitude_a, double longitude_a,
			      double latitude_b, double longitude_b,
			      double *ab_ratio) {
  double x_latitude, x_longitude;
  return distance_to_line_ratio_xing(latitude, longitude,
				     latitude_a, longitude_a,
				     latitude_b, longitude_b,
				     &x_latitude, &x_longitude,
				     ab_ratio);
}

#ifdef OLD_C_COMPILER
# define __distance(a,b) (sqrt(a*a+b*b))
#else
# define __distance(a,b) (hypot(a,b))
#endif /* OLD_C_COMPILER */

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate. It is at best an approximation, as the intersection
 * algorithm is not valid for long distances
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment
 */
double distance_to_line_ratio_xing(double latitude, double longitude,
				   double latitude_a, double longitude_a,
				   double latitude_b, double longitude_b,
				   double *x_latitude, double *x_longitude,
				   double *ab_ratio) {
  double dist_a, dist_b, max_dist, ab_dist, t_dist;
  double ortho_a, ortho_b;
  double t_latitude;
  double longitude_x, latitude_x, intersect;
  double longitude_y, latitude_y;
  double xing_latitude, xing_longitude;

  ortho_a = ortho_distance(latitude, longitude, latitude_a, longitude_a);
  ortho_b = ortho_distance(latitude, longitude, latitude_b, longitude_b);

  t_latitude = latToY(latitude);
  latitude_a = latToY(latitude_a);
  latitude_b = latToY(latitude_b);
  
  /* some normalization */
  /* normalize the line */
#ifdef DEBUG
  printf("Longitude A: %.2f Longitude B: .%2f\n", radToDeg(longitude_a),
	 radToDeg(longitude_b));
#endif /* DEBUG */
  if (fabs(longitude_a - longitude_b) > PI) {
    if (longitude_a > longitude_b) {
      if (longitude_a > 0.0) {
	longitude_a -= TWO_PI;
      } else {
	longitude_b += TWO_PI;
      }
    } else {
      if (longitude_b > 0.0) {
	longitude_b -= TWO_PI;
      } else {
	longitude_a += TWO_PI;
      }
    }
  }
#ifdef DEBUG
  printf("AB NORM: Longitude A: %.2f Longitude B: %2f\n", 
	 radToDeg(longitude_a), radToDeg(longitude_b));
  printf("Point: Longitude: %.2f\n", radToDeg(longitude));
#endif /* DEBUG */
  /* then the point */
  if ((fabs(longitude-longitude_a)>PI) && (fabs(longitude-longitude_b)>PI)) {
    if (longitude < longitude_a) {
      longitude += TWO_PI;
    } else {
      longitude -= TWO_PI;
    }
  }
#ifdef DEBUG
  printf("Point NORM: Longitude: %.2f\n", radToDeg(longitude));
#endif /* DEBUG */  
  dist_a = __distance((t_latitude-latitude_a), (longitude-longitude_a));
  dist_b = __distance((t_latitude-latitude_b), (longitude-longitude_b));
  ab_dist = __distance((latitude_a-latitude_b),(longitude_a-longitude_b));
  
  max_dist = fmax(dist_a, dist_b);
  /* we construct a line form the point, orthogonal to the segment, long of
     at least max_dist */
  latitude_x = t_latitude + (longitude_a - longitude_b) * max_dist / ab_dist;
  longitude_x = longitude + (latitude_b - latitude_a) * max_dist / ab_dist;
  
  latitude_y = t_latitude + (longitude_b - longitude_a) * max_dist / ab_dist;
  longitude_y = longitude + (latitude_a - latitude_b) * max_dist / ab_dist;

#ifdef DEBUG
  printf("Intersect point: Latitude X: %.2f, Longitude X: %.2f\n",
	 radToDeg(yToLat(latitude_x)), radToDeg(longitude_x));
  printf("Intersect point: Latitude Y: %.2f, Longitude Y: %.2f\n",
	 radToDeg(yToLat(latitude_y)), radToDeg(longitude_y));

#endif /* DEBUG */  

  intersect = __intersects_no_norm(latitude_a, longitude_a, 
				   latitude_b, longitude_b,
				   latitude_y, longitude_y, 
				   latitude_x, longitude_x,
				   &xing_latitude, &xing_longitude);
  if (intersect>=INTER_MIN_LIMIT && intersect<=INTER_MAX_LIMIT) { 
    *x_latitude  = yToLat(xing_latitude);
    *x_longitude = xing_longitude; 
#ifdef DEBUG
  printf("Intersect point: Latitude X: %.2f\n", radToDeg(*x_latitude));
  printf("Intersect point: Longitude Y: %.2f\n", radToDeg(*x_longitude));
  printf("Orig point: Latitude X: %.2f\n", radToDeg(latitude));
  printf("Orig point: Longitude Y: %.2f\n", radToDeg(longitude));
#endif /* DEBUG */ 
    t_dist = ortho_distance(latitude, longitude, *x_latitude, *x_longitude);
#ifdef DEBUG
    printf("Min dist: %.3f, found dist: %.3f\n", max_dist, t_dist);
    printf("Ortho_a: %.3f, Ortho_b: %.3f\n", ortho_a, ortho_b);
#endif /* DEBUG */
    if (t_dist < ortho_a && t_dist < ortho_b) {
      *ab_ratio = intersect;
      return t_dist;
    }
  }
  if (ortho_a < ortho_b) {
    *x_latitude = yToLat(latitude_a);
    *x_longitude = longitude_a;
    *ab_ratio = -1.0;
    return ortho_a;
  }
  *x_latitude = yToLat(latitude_b);
  *x_longitude = longitude_b;
  *ab_ratio = -2.0;
  return ortho_b;
}

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate.
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment
 */
double distance_to_line_dichotomy(double latitude, double longitude,
				  double latitude_a, double longitude_a,
				  double latitude_b, double longitude_b) {
  
  double x_latitude, x_longitude;
  return distance_to_line_dichotomy_xing(latitude, longitude,
					 latitude_a, longitude_a,
					 latitude_b, longitude_b,
					 &x_latitude, &x_longitude);
}

/**
 * compute an approximative distance to a segment. Useful to estimate 
 * distance to a gate.
 * Parameters: lat/long of point, then lat and long of A & B defining the
 * segment, and pointers to lat/long of closest point
 */
double distance_to_line_dichotomy_xing(double latitude, double longitude,
				       double latitude_a, double longitude_a,
				       double latitude_b, double longitude_b,
				       double *x_latitude, double *x_longitude){
  double p1_latitude, p1_longitude, p2_latitude, p2_longitude;
  double ortho_p1, ortho_p2;
  double limit;

  limit = PI/(180*60*1852); // 1m precision

#ifdef DEBUG
  printf("Longitude A: %.2f Longitude B: .%2f\n", radToDeg(longitude_a),
	 radToDeg(longitude_b));
#endif /* DEBUG */
  if (fabs(longitude_a - longitude_b) > PI) {
    if (longitude_a > longitude_b) {
      if (longitude_a > 0.0) {
	longitude_a -= TWO_PI;
      } else {
	longitude_b += TWO_PI;
      }
    } else {
      if (longitude_b > 0.0) {
	longitude_b -= TWO_PI;
      } else {
	longitude_a += TWO_PI;
      }
    }
  }
#ifdef DEBUG
  printf("AB NORM: Longitude A: %.2f Longitude B: %2f\n", 
	 radToDeg(longitude_a), radToDeg(longitude_b));
  printf("Point: Longitude: %.2f\n", radToDeg(longitude));
#endif /* DEBUG */

  p1_latitude  = latitude_a;
  p1_longitude = longitude_a;
  p2_latitude  = latitude_b;
  p2_longitude = longitude_b;

  ortho_p1 = ortho_distance(latitude, longitude, p1_latitude, p1_longitude);
  ortho_p2 = ortho_distance(latitude, longitude, p2_latitude, p2_longitude);

  // ending test on distance between two points.
  while (__distance((p1_latitude-p2_latitude), (p1_longitude-p2_longitude)) > 
	 limit) {
    if (ortho_p1 < ortho_p2) {
      p2_longitude = (p1_longitude+p2_longitude)/2;
      p2_latitude = yToLat((latToY(p1_latitude)+latToY(p2_latitude))/2);
    } else {
      p1_longitude = (p1_longitude+p2_longitude)/2;
      p1_latitude = yToLat((latToY(p1_latitude)+latToY(p2_latitude))/2);
    }
    ortho_p1 = ortho_distance(latitude, longitude, p1_latitude, p1_longitude);
    ortho_p2 = ortho_distance(latitude, longitude, p2_latitude, p2_longitude);
  }
  
  if (ortho_p1 < ortho_p2) {
    *x_latitude  = p1_latitude;
    *x_longitude = p1_longitude;
    return ortho_p1;
  }
  *x_latitude  = p2_latitude;
  *x_longitude = p2_longitude;
  return ortho_p2;
}
