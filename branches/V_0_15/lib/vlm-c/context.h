/**
 * $Id: context.h,v 1.5 2010-12-09 10:45:51 ylafon Exp $
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

#ifndef _VLMC_CONTEXT_H_
#define _VLMC_CONTEXT_H_

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

#endif /* _VLMC_CONTEXT_H_ */
