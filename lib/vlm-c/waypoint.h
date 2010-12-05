/**
 * $Id: waypoint.h,v 1.8 2010-12-05 16:47:00 ylafon Exp $
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

#ifndef _VLMC_WAYPOINT_H_
#define _VLMC_WAYPOINT_H_

#include <time.h>

#include "defs.h"
#include "types.h"

/**
 * Create a two buoys wp structure out of any buoy definition
 * leave_at is an angle in rad
 */
void   init_waypoint          PARAM9(waypoint *, int, int,
				     double, double,
				     double, double,
				     double, double);
/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns a boolean, 1 if waypoint was crossed, 0 otherwise
 */
int    check_waypoint_crossed PARAM8(double, double, time_t,
				     double, double, time_t,
				     waypoint *, time_t *);
/**
 * check if a waypoint was in the way
 * populates the time when the wp was crossed
 * @returns an int,  1 if waypoint was crossed, 
 *                  -1 if waypoint is incorrectly crossed 
 *                   0 if not crossed
 */
int    check_waypoint         PARAM8(double, double,  
				     double, double,  
				     waypoint *, double *,
				     double *, double *);
/**
 * Find the "closest" point of a WP, it populates the real targeted WP
 * of the boat and return the distance to that point.
 */
double best_way_to_waypoint   PARAM2(boat *, waypoint *);


#endif /* _VLMC_WAYPOINT_H_ */
