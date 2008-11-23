/**
 * $Id: waypoint.c,v 1.2 2008/05/25 10:21:23 ylafon Exp $
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
			   time_t prev_time,
			   double current_latitude, double current_longitude, 
			   time_t current_time,
			   waypoint *wp, time_t *crossing_time) {
  double intersect_ratio;
  double junk_lat, junk_long;

  if (wp == NULL) {
    return 0;
  }
  switch (wp->type) {
  default:
    intersect_ratio = intersects(prev_latitude, prev_longitude, 
				 current_latitude, current_longitude,
				 wp->latitude1, wp->longitude1,
				 wp->latitude2, wp->longitude2,
				 &junk_lat, &junk_long);
    if (intersect_ratio >= INTER_MIN_LIMIT) {
      /* got it! Compute the time */
      *crossing_time = prev_time + (long) rint(intersect_ratio * 
				        ((double) (current_time - prev_time)));
      return 1;
    }
  }
  return 0;
}
