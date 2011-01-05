/**
 * $Id: vmg.h,v 1.15 2010-12-09 13:32:15 ylafon Exp $
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

#ifndef _VLMC_VMG_H_
#define _VLMC_VMG_H_

#include "defs.h"
#include "types.h"

double get_heading_bvmg              PARAM2(boat *, int);
double get_heading_bvmg_context      PARAM3(vlmc_context *, boat *, int);

void   set_heading_bvmg              PARAM2(boat *, int);
void   set_heading_vbvmg             PARAM2(boat *, int);

double get_best_angle_close_hauled   PARAM3(boat *, double, int);
double get_best_angle_broad_reach    PARAM3(boat *, double, int);

double get_heading_vbvmg             PARAM2(boat *, int);
double get_heading_vbvmg_context     PARAM3(vlmc_context *, boat *, int);
double get_wind_angle_vbvmg          PARAM2(boat *, int);
double get_wind_angle_vbvmg_context  PARAM3(vlmc_context *, boat *, int);

double get_heading_bvmg              PARAM2(boat *, int);
double get_heading_bvmg_context      PARAM3(vlmc_context *, boat *, int);
double get_wind_angle_bvmg           PARAM2(boat *, int);
double get_wind_angle_bvmg_context   PARAM3(vlmc_context *, boat *, int);

void   do_vbvmg                      PARAM10(boat *, int, double *, double *, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *);

void   do_vbvmg_context              PARAM11(vlmc_context *, boat *, int, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *, 
					     double *, double *);

void   do_bvmg                       PARAM4(boat *, int, double *, double *);
void   do_bvmg_context               PARAM5(vlmc_context *, boat *, int, 
					    double *, double *);
#endif /* _VLMC_VMG_H_ */
