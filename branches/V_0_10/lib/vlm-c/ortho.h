/**
 * $Id: ortho.h,v 1.3 2008/06/06 16:37:25 ylafon Exp $
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

#ifndef _ORTHO_H_
#define _ORTHO_H_

#include "defs.h"
#include "types.h"

void set_heading_ortho PARAM1(boat *);
void set_heading_ortho_nowind PARAM1(boat *);
double ortho_distance PARAM4(double, double, double, double);
double ortho_initial_angle PARAM4(double, double, double, double);
void ortho_distance_initial_angle PARAM6(double, double, double, double,
					 double *, double *);

#endif /* _ORTHO_H_ */
