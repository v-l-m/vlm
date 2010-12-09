/**
 * $Id: waypoint.c,v 1.17 2010-12-09 13:54:27 ylafon Exp $
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

#include "defs.h"
#include "types.h"
#include "lines.h"
#include "loxo.h"
#include "waypoint.h"

#define __vlm_long_norm(a)			\
  a = fmod (a, TWO_PI);				\
  if (a < 0) {					\
    a += TWO_PI;				\
  }

#define __vlm_seg_long_norm(a, b)		\
  __vlm_long_norm(a);				\
  __vlm_long_norm(b);				\
  if (fabs(b - a) > PI) {			\
    if (b > a) {				\
      b -= TWO_PI;				\
    } else {					\
      a -= TWO_PI;				\
    }						\
  }						
  
#define __vlm_longitude_normalize(a, b, c, d)	\
  __vlm_seg_long_norm(a, b)			\
  __vlm_seg_long_norm(c, d)			\
  if (fabs(a - c) > PI) {			\
    if (a > c) {				\
      a -= TWO_PI;				\
      b -= TWO_PI;				\
    } else {					\
      c -= TWO_PI;				\
      d -= TWO_PI;				\
    }						\
  }

/**
 * We assume that all the longitude or latitudes are normalized
 * return the ratio between 0.0 and 1.0 between prev_x and x where
 * we cross a<->b
 */
double get_waypoint_xing_ratio(double prev_x, double x,
			       double a, double b) {
  if ((a - prev_x)*(a - x)<0) {
    /* we crossed at buoy 'a' */
    return (a - prev_x)/(x - prev_x);
  } 
  if ((b-prev_x)*(b-x)<0) {
    return (b - prev_x)/(x - prev_x);
  }
  return -1.0;
}

/**
 * Create a two buoys wp structure out of any buoy definition
 * leave_at is an angle in rad
 */
void init_waypoint(waypoint *wp, int wp_type, int id,
		   double lat1, double long1, 
		   double lat2, double long2, 
		   double leave_at, double fake_length) {
  double new_lat, new_long;
  double ratio;
  
  wp->type       = wp_type;
  wp->idwaypoint = id;
  wp->latitude1  = lat1;
  wp->longitude1 = long1;

  switch (wp_type & WP_GATE_BUOY_MASK) {
  case WP_ONE_BUOY:
    wp->angle = leave_at;
    leave_at += PI;
    get_loxo_coord_from_dist_angle(lat1, long1, fake_length, leave_at,
				   &new_lat, &new_long);
    if (fabs(new_lat) > degToRad(80.0)) {
      ratio = (degToRad(80.0)-fabs(lat1)) / (fabs(new_lat)-fabs(lat1));
      fake_length *= ratio;
       get_loxo_coord_from_dist_angle(lat1, long1, fake_length, leave_at,
				   &new_lat, &new_long);
    }
    if (new_long > PI) {
      new_long -= TWO_PI;
    } else if (new_long < -PI) {
      new_long += TWO_PI;
    }
    wp->latitude2  = new_lat;
    wp->longitude2 = new_long;
    break;
  case WP_TWO_BUOYS:
  default:
    wp->latitude2  = lat2;
    wp->longitude2 = long2;
  }
}

/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns a boolean, 1 if waypoint was crossed, 0 otherwise
 */
int check_waypoint_crossed(double prev_latitude, double prev_longitude,
			   time_t prev_time,
			   double current_latitude, double current_longitude, 
			   time_t current_time,
			   waypoint *wp, time_t *xing_time) {
  double isect_ratio, isect_lat, isect_long;
  if (check_waypoint(prev_latitude, prev_longitude, 
		     current_latitude, current_longitude,
		     wp, &isect_ratio, &isect_lat, &isect_long) == 1) {
    *xing_time = prev_time + (long) rint(isect_ratio * 
					 ((double) (current_time - prev_time)));
    return 1;
  }
  return 0;
}

/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns an int,  1 if waypoint was crossed, 
 *                  -1 if waypoint is incorrectly crossed 
 *                   0 if not crossed
 */
int check_waypoint(double prev_latitude, double prev_longitude,
		   double current_latitude, double current_longitude, 
		   struct waypoint_str *wp, double *intersection,
		   double *isect_latitude, double *isect_longitude) {
  double intersect_ratio;
  double wp_long1, wp_long2;
  double wp_lat1, wp_lat2;
  double vgate_lat, vgate_long;
  double vboat_lat, vboat_long;
  double zvect;

  int isect;

  if (wp == NULL) {
    return 0;
  }
  isect = 0;
  /* use the projection */
  wp_lat1          = latToY(wp->latitude1);
  wp_lat2          = latToY(wp->latitude2);
  prev_latitude    = latToY(prev_latitude);
  current_latitude = latToY(current_latitude);
  /* first, normalize the longitudes */
  wp_long1 = wp->longitude1;
  wp_long2 = wp->longitude2;
  __vlm_longitude_normalize(prev_longitude, current_longitude,
			    wp_long1, wp_long2);

  switch (wp->type & WP_GATE_KIND_MASK) {
  case WP_DEFAULT:
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp_lat1, wp_long1,
				 wp_lat2, wp_long2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
      *isect_latitude = yToLat(*isect_latitude);
    } else {
      return 0;
    }
    break;
    /** IMPORTANT: We assume that the ice gates are always
	horizontal or vertical **/
  case WP_ICE_GATE_N:
    /** IMPORTANT: We assume that the ice gates are always
	horizontal or vertical **/
    if ((prev_latitude > wp_lat1) && (current_latitude > wp_lat1)) {
      return 0;
    }
    /* do we have an intersection ? (general case) */
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp_lat1, wp_long1,
				 wp_lat2, wp_long2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
      *isect_latitude = yToLat(*isect_latitude);
    } else {
      /*
       * check if new longitude is between the two buoys 
       * As the waypoint was not crossed before, prev_longitude must always
       * be outside as we don't have an intersection
       */
      if((wp_long1-current_longitude)*(wp_long2-current_longitude)<0){
	/* if no intersection, check if we ended up north of the gate */
	if (current_latitude < wp_lat1) {
	  intersect_ratio = get_waypoint_xing_ratio(prev_longitude,
						    current_longitude,
						    wp_long1,
						    wp_long2);
	  if (intersect_ratio >= 0.0) {
	    *isect_latitude = prev_latitude + 
	      intersect_ratio * (current_latitude - prev_latitude);
	    /* unproject */
	    *isect_latitude = yToLat(*isect_latitude);
	    *isect_longitude = prev_longitude + 
	      intersect_ratio * (current_longitude - prev_longitude);
	    *intersection = intersect_ratio;
	  } else {
	    /* should not happen, but let's be safe here */
	    *isect_latitude = yToLat(current_latitude);
	    *isect_longitude = current_longitude;
	    *intersection = 1;
	  }
	  isect = 1;
	}
      }
    }
    break;
  case WP_ICE_GATE_S:
    if ((prev_latitude < wp_lat1) && (current_latitude < wp_lat1)) {
      return 0;
    }
    /* do we have an intersection ? (general case) */
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp_lat1, wp_long1,
				 wp_lat2, wp_long2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
      *isect_latitude = yToLat(*isect_latitude);
    } else {
    /*
     * check if new longitude is between the two buoys 
     * As the waypoint was not crossed before, prev_longitude must always
     * be outside as we don't have an intersection
     */
      if((wp_long1-current_longitude)*(wp_long2-current_longitude)<0){
	/* if no intersection, check if we ended up north of the gate */
	if (current_latitude > wp_lat1) {
	  intersect_ratio = get_waypoint_xing_ratio(prev_longitude,
						    current_longitude,
						    wp_long1, 
						    wp_long2);
	  if (intersect_ratio >= 0.0) {
	    *isect_latitude = prev_latitude + 
	      intersect_ratio * (current_latitude - prev_latitude);
	    /* unproject */
	    *isect_latitude = yToLat(*isect_latitude);
	    *isect_longitude = prev_longitude + 
	      intersect_ratio * (current_longitude - prev_longitude);
	    *intersection = intersect_ratio;
	  } else {
	    /* should not happen, but let's be safe here */
	    *isect_latitude = yToLat(current_latitude);
	    *isect_longitude = current_longitude;
	    *intersection = 1;
	  }
	  isect = 1;
	}
      }
    }
    break;
  case WP_ICE_GATE_E:
  case WP_ICE_GATE_W:
    /* is this really needed ? In most cases a one-buoy gate is enough
       and add the possibility of specifiyng the way to cross the gate */
    return 0;
    
  default:
    return 0;
  }

  // no intersection, bail out
  if (!isect) {
    return 0;
  }

  // check other constraints.
  switch(wp->type & (WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE)) {
  case WP_DEFAULT:
    break;
  case WP_CROSS_CLOCKWISE:
    vboat_lat  = current_latitude  - prev_latitude;
    vboat_long = current_longitude - prev_longitude; 
    vgate_lat  = wp_lat2  - wp_lat1;
    vgate_long = wp_long2 - wp_long1;
    zvect = vboat_long*vgate_lat - vboat_lat*vgate_long;
    // result is positive if we crossed the gate clockwise
    if (zvect < 0) {
      isect = -1;
    }
    break;
  case WP_CROSS_ANTI_CLOCKWISE:
    vboat_lat  = current_latitude  - prev_latitude;
    vboat_long = current_longitude - prev_longitude; 
    vgate_lat  = wp_lat2  - wp_lat1;
    vgate_long = wp_long2 - wp_long1;
    zvect = vboat_long*vgate_lat - vboat_lat*vgate_long;
    // result is positive if we crossed the gate clockwise
    if (zvect > 0) {
      isect = -1;
    }
    break;
  default:
    break;
  }
  // final result
  return isect;
}

/**
 * Find the "closest" point of a WP, it populates the real targeted WP
 * of the boat and return the distance to that point.
 */
double best_way_to_waypoint(boat *b, waypoint *wp) {
  double x_lat, x_long, dist, ratio;
  double latitude_a, longitude_a, latitude_b, longitude_b;
  double latitude, longitude;
  
  /* sanity check */
  latitude    = b->latitude;
  longitude   = fmod(b->longitude, TWO_PI);
  if (longitude < 0.0) {
    longitude += TWO_PI;
  }
  latitude_a  = wp->latitude1;
  longitude_a = fmod(wp->longitude1, TWO_PI);
  if (longitude_a < 0.0) {
    longitude_a += TWO_PI;
  }
  latitude_b  = wp->latitude2;
  longitude_b = fmod(wp->longitude2, TWO_PI);
  if (longitude_b < 0.0) {
    longitude_b += TWO_PI;
  }
  
  /* if something goes wrong, return -1 */
  if (latitude   < -PI || latitude   > PI ||
      latitude_a < -PI || latitude_a > PI ||
      latitude_b < -PI || latitude_b > PI) {
    return -1.0;
  }
  
  dist = distance_to_line_ratio_xing(latitude  , longitude,
				     latitude_a, longitude_a,
				     latitude_b, longitude_b,
				     &x_lat, &x_long, &ratio);
  if (x_long > PI) {
    x_long -= TWO_PI;
  } else if (x_long < -PI) {
    x_long += TWO_PI;
  }
  b->wp_latitude  = x_lat;
  b->wp_longitude = x_long;
  return dist;
}
