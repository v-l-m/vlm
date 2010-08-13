/**
 * $Id: waypoint.c,v 1.5 2010-08-13 15:59:53 ylafon Exp $
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

#include "defs.h"
#include "types.h"
#include "lines.h"
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
    return (b - prev_x)/x - prev_x;
  }
  return -1.0;
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
		     wp, &isect_ratio, &isect_lat, &isect_long)) {
    *xing_time = prev_time + (long) rint(isect_ratio * 
					 ((double) (current_time - prev_time)));
    return 1;
  }
  return 0;
}

/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns a boolean, 1 if waypoint was crossed, 0 otherwise
 */
int check_waypoint(double prev_latitude, double prev_longitude,
		   double current_latitude, double current_longitude, 
		   struct waypoint_str *wp, double *intersection,
		   double *isect_latitude, double *isect_longitude) {
  double intersect_ratio;
  double icegate_long1, icegate_long2;
  double vgate_lat, vgate_long;
  double vboat_lat, vboat_long;
  double zvect;

  int isect;

  if (wp == NULL) {
    return 0;
  }
  isect = 0;
  switch (wp->type & 0xFFF0) {
  case WP_DEFAULT:
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp->latitude1, wp->longitude1,
				 wp->latitude2, wp->longitude2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
    } else {
      return 0;
    }
    break;
    /** IMPORTANT: We assume that the ice gates are always
	horizontal or vertical **/
  case WP_ICE_GATE_N:
    /** IMPORTANT: We assume that the ice gates are always
	horizontal or vertical **/
    if ((prev_latitude > wp->latitude1)&&(current_latitude > wp->latitude1)) {
      return 0;
    }
    /* do we have an intersection ? (general case) */
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp->latitude1, wp->longitude1,
				 wp->latitude2, wp->longitude2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
    } else {
      /* first, normalize the longitudes */
      icegate_long1 = wp->longitude1;
      icegate_long2 = wp->longitude2;
      __vlm_longitude_normalize(prev_longitude, current_longitude,
				icegate_long1, icegate_long2);
      /*
       * check if new longitude is between the two buoys 
       * As the waypoint was not crossed before, prev_longitude must always
       * be outside as we don't have an intersection
       */
      if((icegate_long1-current_longitude)*(icegate_long2-current_longitude)<0){
	/* if no intersection, check if we ended up north of the gate */
	if (current_latitude > wp->latitude1) {
	  intersect_ratio = get_waypoint_xing_ratio(prev_longitude,
						    current_longitude,
						    icegate_long1,
						    icegate_long2);
	  if (intersect_ratio >= 0.0) {
	    *isect_latitude = prev_latitude + 
	      intersect_ratio * (current_latitude - prev_latitude);
	    *isect_longitude = prev_longitude + 
	      intersect_ratio * (current_longitude - prev_longitude);
	    *intersection = intersect_ratio;
	    isect = 1;
	  }
	}
      }
    }
    break;
  case WP_ICE_GATE_S:
    if ((prev_latitude < wp->latitude1)&&(current_latitude < wp->latitude1)) {
      return 0;
    }
    /* do we have an intersection ? (general case) */
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp->latitude1, wp->longitude1,
				 wp->latitude2, wp->longitude2,
				 isect_latitude, isect_longitude);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      isect = 1;
      *intersection = intersect_ratio;
    } else {
      /* first, normalize everything */
      icegate_long1 = wp->longitude1;
      icegate_long2 = wp->longitude2;
      __vlm_longitude_normalize(prev_longitude, current_longitude,
				icegate_long1, icegate_long2);

    /*
     * check if new longitude is between the two buoys 
     * As the waypoint was not crossed before, prev_longitude must always
     * be outside as we don't have an intersection
     */
      if((icegate_long1-current_longitude)*(icegate_long2-current_longitude)<0){
	/* if no intersection, check if we ended up north of the gate */
	if (current_latitude > wp->latitude1) {
	  intersect_ratio = get_waypoint_xing_ratio(prev_longitude,
						    current_longitude,
						    icegate_long1, 
						    icegate_long2);
	  if (intersect_ratio >= 0.0) {
	    *isect_latitude = prev_latitude + 
	      intersect_ratio * (current_latitude - prev_latitude);
	    *isect_longitude = prev_longitude + 
	      intersect_ratio * (current_longitude - prev_longitude);
	    *intersection = intersect_ratio;
	    isect = 1;
	  }
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
    // FIXME normalize longitude
    vboat_lat  = current_latitude  - prev_latitude;
    vboat_long = current_longitude - prev_longitude; 
    vgate_lat  = wp->latitude2  - wp->latitude1;
    vgate_long = wp->longitude2 - wp->longitude1;
    zvect = vboat_long*vgate_lat - vboat_lat*vgate_long;
    // result is positive if we crossed the gate clockwise
    if (zvect < 0) {
      isect = 0;
    }
    break;
  case WP_CROSS_ANTI_CLOCKWISE:
    // FIXME normalize longitude
    vboat_lat  = current_latitude  - prev_latitude;
    vboat_long = current_longitude - prev_longitude; 
    vgate_lat  = wp->latitude2  - wp->latitude1;
    vgate_long = wp->longitude2 - wp->longitude1;
    zvect = vboat_long*vgate_lat - vboat_lat*vgate_long;
    // result is positive if we crossed the gate clockwise
    if (zvect > 0) {
      isect = 0;
    }
    break;
  default:
    break;
  }
  // final result
  return isect;
}
