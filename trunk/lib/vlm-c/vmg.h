/**
 * $Id: vmg.h,v 1.13 2010-11-16 07:08:00 ylafon Exp $
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

#ifndef _VLMC_VMG_H_
#define _VLMC_VMG_H_

#include "defs.h"
#include "types.h"

double get_heading_bvmg              PARAM2(boat *, int);
double get_heading_bvmg_context      PARAM3(vlmc_context *, boat *, int);
void   set_heading_bvmg              PARAM1(boat *);

double get_best_angle_close_hauled   PARAM3(boat *, double, int);
double get_best_angle_broad_reach    PARAM3(boat *, double, int);

double get_heading_vbvmg             PARAM2(boat *, int);
double get_heading_vbvmg_context     PARAM3(vlmc_context *, boat *, int);
double get_wind_angle_vbvmg          PARAM2(boat *, int);
double get_wind_angle_vbvmg_context  PARAM3(vlmc_context *, boat *, int);

double get_heading_bvmg             PARAM2(boat *, int);
double get_heading_bvmg_context     PARAM3(vlmc_context *, boat *, int);
double get_wind_angle_bvmg          PARAM2(boat *, int);
double get_wind_angle_bvmg_context  PARAM3(vlmc_context *, boat *, int);

void   do_vbvmg                      PARAM10(boat *, int, double *, double *, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *);

void   do_vbvmg_context              PARAM11(vlmc_context *, boat *, int, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *);

void   do_bvmg                      PARAM4(boat *, int, double *, double *);
void   do_bvmg_context              PARAM5(vlmc_context *, boat *, int, 
					   double *, double *);
#endif /* _VLMC_VMG_H_ */
