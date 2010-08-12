/**
 * $Id: waypoint.c,v 1.3 2010-08-12 21:52:26 ylafon Exp $
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

/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns a boolean, 1 if waypoint was crossed, 0 otherwise
 */
int check_waypoint_crossed(double prev_latitude, double prev_longitude,
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
    // FIXME normalize
    icegate_long1 = wp->longitude1;
    icegate_long2 = wp->longitude2;
    // FIXME
    break;
  case WP_ICE_GATE_S:
    if ((prev_latitude < wp->latitude1)&&(current_latitude < wp->latitude1)) {
      return 0;
    }
    // FIXME normalize
    icegate_long1 = wp->longitude1;
    icegate_long2 = wp->longitude2;
    // FIMXE
    break;
  case WP_ICE_GATE_E:
  case WP_ICE_GATE_W:
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
