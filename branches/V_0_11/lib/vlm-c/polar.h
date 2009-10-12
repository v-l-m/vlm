/**
 * $Id: polar.h,v 1.4 2009-08-31 22:06:43 ylafon Exp $
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

#ifndef _POLAR_H_
#define _POLAR_H_

#include "defs.h"
#include "types.h"

void       init_polar ();
double     find_speed                    PARAM3(boat *, double, double);
boat_polar *get_polar_by_name            PARAM1(char *);
boat_polar *get_polar_by_name_context    PARAM2(vlmc_context *, char *);

int        get_nb_polars_context         PARAM1(vlmc_context *);
char       *get_polar_name_index_context PARAM2(vlmc_context *, int);

#endif /* _POLAR_H_ */
