/**
 * $Id: boat.h,v 1.6 2010-12-09 10:45:29 ylafon Exp $
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

#ifndef _VLMC_BOAT_H_
#define _VLMC_BOAT_H_

#include "defs.h"
#include "types.h"

boat *init_boat PARAM6(boat *, int, char *, double, double, double);
boat *set_wp PARAM4(boat *, double, double, double);
void associate_polar_boat PARAM2(boat *, char *);
void associate_polar_boat_context PARAM3(vlmc_context *, boat *, char *);

#endif /* _VLMC_BOAT_H_ */
