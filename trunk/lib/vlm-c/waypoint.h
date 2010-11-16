/**
 * $Id: waypoint.h,v 1.7 2010-11-16 07:08:00 ylafon Exp $
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

void init_waypoint          PARAM9(waypoint *, int, int,
				   double, double,
				   double, double,
				   double, double);

int  check_waypoint_crossed PARAM8(double, double, time_t,
				   double, double, time_t,
				   waypoint *, time_t *);
int  check_waypoint         PARAM8(double, double,  
				   double, double,  
				   waypoint *, double *,
				   double *, double *);

#endif /* _VLMC_WAYPOINT_H_ */
