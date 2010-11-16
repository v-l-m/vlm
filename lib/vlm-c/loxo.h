/**
 * $Id: loxo.h,v 1.9 2010-11-16 07:07:59 ylafon Exp $
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

#ifndef _VLMC_LOXO_H_
#define _VLMC_LOXO_H_

#include <math.h>

#include "defs.h"
#include "types.h"

void raw_move_loxo                  PARAM6(double, double, double, double,
					   double *, double *);
void move_boat_loxo                 PARAM1(boat *);
void estimate_boat_loxo             PARAM5(boat *, int, double, 
					   double *, double *);
int  estimate_boat_loxo_coast       PARAM5(boat *, int, double, 
					   double *, double *);
void set_heading_loxo               PARAM1(boat *);
void set_heading_direct             PARAM2(boat *, double);
void set_heading_constant           PARAM1(boat *);
void set_heading_wind_angle         PARAM1(boat *);
void get_loxo_coord_from_dist_angle PARAM6(double, double, double, double,
					   double *, double *);

void loxo_distance_angle            PARAM6(double, double, double, double,
					   double *, double *);

#endif /* _VLMC_LOXO_H_ */
