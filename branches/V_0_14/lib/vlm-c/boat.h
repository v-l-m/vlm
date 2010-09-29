/**
 * $Id: boat.h,v 1.3 2009-08-31 11:39:28 ylafon Exp $
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

#ifndef _BOAT_H_
#define _BOAT_H_

#include "defs.h"
#include "types.h"

boat *init_boat PARAM6(boat *, int, char *, double, double, double);
boat *set_wp PARAM4(boat *, double, double, double);
void associate_polar_boat PARAM2(boat *, char *);
void associate_polar_boat_context PARAM3(vlmc_context *, boat *, char *);

#endif /* _BOAT_H_ */
