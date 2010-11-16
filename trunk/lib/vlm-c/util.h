/**
 * $Id: util.h,v 1.4 2010-11-16 07:08:00 ylafon Exp $
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

#ifndef _VLMC_UTIL_H_
#define _VLMC_UTIL_H_

#include "defs.h"
#include "types.h"

int in_vlm_compat_mode();
char *get_vlm_build_information();
char *get_vlm_wind_interpolation_scheme();

#endif /* _VLMC_UTIL_H_ */
