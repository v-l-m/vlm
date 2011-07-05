/**
 * $Id: grib.h,v 1.12 2011-07-05 13:10:34 ylafon Exp $
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

#ifndef _VLMC_GRIB_H_
#define _VLMC_GRIB_H_

#include <math.h>

#include "defs.h"
#include "types.h"

/**
 * init global wind structures in the context based on a grib file
 * containing only UGRD/VGRD entries in matched pairs.
 * time offest is set to compile time default. (GRIB_TIME_OFFSET)
 * it corresponds by default to 35 to 40mn time delay, from VLM average.
 * unit is seconds.
 */
void init_grib();


#ifdef USE_GRIB_UPDATE_TIME
/**
 * set the grib update time without having to re-read the grib
 */
void set_grib_update_time PARAM1(time_t);
/**
 * get the current grib update time 
 */
time_t get_grib_update_time ();
#else
/**
 * set the grib offset (see above) without having to re-read the grib
 */
void set_grib_offset PARAM1(long);
/**
 * get the current grib offset (see above)
 */
long get_grib_offset ();
#endif /* USE_GRIB_UPDATE_TIME */

/**
 * init global wind structures in the context based on a grib file
 * containing only UGRD/VGRD entries in matched pairs.
 * time offest is given as a parameter (see above)
 * (ignored if USE_GRIB_UPDATE_TIME is defined)
 */
void init_grib_offset PARAM1(long);

/**
 * get rid of past gribs entry and free structure 
 */
void purge_gribs();

/**
 * merge gribs, and purge if parameter is true (non zero)
 */
void merge_gribs PARAM1(int);

/**
 * generate interim grib for a specific time (real time, further
 * corrected depending on offset
 * One usage is to generate a grib with a date of 'now' prior merging
 * new gribs, and purge the old ones
 */
winds *generate_interim_grib PARAM1(time_t);

/**
 * generate interim grib for a specific time (real time, further
 * corrected depending on offset
 * One usage is to generate a grib with a date of 'now' prior merging
 * new gribs, and purge the old ones
 * Interpolation takes place in the UV domain
 */
winds *generate_interim_grib_UV PARAM1(time_t);

/**
 * generate interim grib for a specific time (real time, further
 * corrected depending on offset
 * One usage is to generate a grib with a date of 'now' prior merging
 * new gribs, and purge the old ones
 * Interpolation takes place in the TWSA domain
 */
winds *generate_interim_grib_TWSA PARAM1(time_t);

/**
 * generate interim grib for a specific time (real time, further
 * corrected depending on offset
 * One usage is to generate a grib with a date of 'now' prior merging
 * new gribs, and purge the old ones
 * Interpolation is done using the Hybrid mode (TWSA for speed, UB for angle)
 */
winds *generate_interim_grib_hybrid PARAM1(time_t);

/**
 * generate a snapshot of current time (offset corrected), then merge with
 * a new grib collection file, and purge the stale entries
 */
void interpolate_and_merge_grib();

#endif /* _VLMC_GRIB_H_ */
