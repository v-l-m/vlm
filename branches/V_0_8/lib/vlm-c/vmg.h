/**
 * $Id: vmg.h,v 1.2 2008/05/24 14:21:21 ylafon Exp $
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

#ifndef _VMG_H_
#define _VMG_H_

#include "defs.h"
#include "types.h"

void set_heading_bvmg                PARAM1(boat *);
void set_heading_bvmg2               PARAM1(boat *);
void set_heading_bvmg2_coast         PARAM1(boat *);
void automatic_selection_heading     PARAM1(boat *);

#endif /* _VMG_H_ */