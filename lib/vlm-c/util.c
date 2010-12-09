/**
 * $Id: util.c,v 1.4 2010-12-09 13:54:27 ylafon Exp $
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

#include "defs.h"
#include "types.h"
#include "util.h"

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

char *get_vlm_build_information() {
  static char *vlm_build_info = VLM_BUILD_DATE;

  return vlm_build_info;
}

char *get_vlm_wind_interpolation_scheme() {
  static char *vlm_wind_interp_scheme = VLM_WIND_INTERPOLATION;
    
  return vlm_wind_interp_scheme;
}
