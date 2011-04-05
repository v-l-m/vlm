/**
 * $Id: polar.h,v 1.7 2011-04-05 22:29:13 ylafon Exp $
 *
 * (c) 2010 by Yves Lafon
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

#ifndef _VLMC_POLAR_H_
#define _VLMC_POLAR_H_

#include "defs.h"
#include "types.h"

void       init_polar ();
double     find_speed                    PARAM3(boat *, double, double);
double     find_speed_polar              PARAM3(double *, double, double);
boat_polar *get_polar_by_name            PARAM1(char *);
boat_polar *get_polar_by_name_context    PARAM2(vlmc_context *, char *);

int        get_nb_polars_context         PARAM1(vlmc_context *);
char       *get_polar_name_index_context PARAM2(vlmc_context *, int);

#endif /* _VLMC_POLAR_H_ */
