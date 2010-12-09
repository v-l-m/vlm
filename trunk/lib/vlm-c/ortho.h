/**
 * $Id: ortho.h,v 1.5 2010-12-09 13:32:14 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
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

#ifndef _VLMC_ORTHO_H_
#define _VLMC_ORTHO_H_

#include "defs.h"
#include "types.h"

void set_heading_ortho PARAM1(boat *);
void set_heading_ortho_nowind PARAM1(boat *);
double ortho_distance PARAM4(double, double, double, double);
double ortho_initial_angle PARAM4(double, double, double, double);
void ortho_distance_initial_angle PARAM6(double, double, double, double,
					 double *, double *);

#endif /* _VLMC_ORTHO_H_ */
