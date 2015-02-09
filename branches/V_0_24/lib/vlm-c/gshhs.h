/**
 * $Id: gshhs.h,v 1.6 2010-12-09 13:32:14 ylafon Exp $
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

#ifndef _VLMC_GSHHS_H_
#define _VLMC_GSHHS_H_

#include "defs.h"
#include "types.h"

void init_coastline();
/**
 * min latitude, min longitude, max latitude, max longitude 
 * all in radians, in min latitude > max latitude, it selects the whole
 * world
 */
void init_partial_coastline PARAM4(double, double, double, double);

/* free the allocated arrays of coast_seg, then set everything to 0 */
void free_gshhs();

/* getter for a square of 1/10 degree */
coast_zone *get_coastzone PARAM2(int, int);

coast_seg *get_coastseg PARAM2(coast_zone *, int);

#endif /* _VLMC_GSHHS_H_ */
