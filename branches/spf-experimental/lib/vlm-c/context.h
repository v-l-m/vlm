/**
 * $Id: context.h,v 1.3 2008/07/08 14:12:01 ylafon Exp $
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

#ifndef _CONTEXT_H_
#define _CONTEXT_H_

#include "defs.h"
#include "types.h"

void init_context_default PARAM1(vlmc_context *);
void set_grib_filename PARAM2(vlmc_context *, char *);
void set_gshhs_filename PARAM2(vlmc_context *, char *);
void set_polar_definition_filename PARAM2(vlmc_context *, char *);
void init_context PARAM1(vlmc_context *);

/**
 * check if all the relevant structures (gribs and polar) are
 * filled
 * @return a boolean, true if everything is ready
 */
int is_init_done PARAM1(vlmc_context *);

#endif /* _CONTEXT_H_ */
