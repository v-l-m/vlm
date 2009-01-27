/**
 * $Id: util.c,v 1.1 2008/04/26 09:09:18 ylafon Exp $
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

#include "defs.h"
#include "types.h"

/**
 * tell if we are in VLM compatibility mode
 * returns a boolean, true if in vlm mode
 * false otherwise
 */
int in_vlm_compat_mode() {
#ifdef VLM_COMPAT
  return 1;
#else
  return 0;
#endif /* VLM_COMPAT */
}

