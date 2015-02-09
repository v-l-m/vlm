/**
 * $Id: winds.h,v 1.12 2010-12-09 13:32:15 ylafon Exp $
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

#ifndef _VLMC_WINDS_H_
#define _VLMC_WINDS_H_

#include <math.h>
#include <time.h>
#include <sys/time.h>

#include "defs.h"
#include "types.h"

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param boat, a <code>boat *</code>, a pointer to a <em>boat</em> structure
 * the time used is the boat last vacation time added with the vacation duration
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
void get_wind_info PARAM2(boat *, wind_info *);
void get_wind_info_context PARAM3(vlmc_context *, boat *, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong PARAM4(double, double, time_t, wind_info *);
wind_info *get_wind_info_latlong_context PARAM5(vlmc_context *,
						double, double, 
						time_t, wind_info *);

/**
 * get wind info based on the location and at the time the function is called
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong_now PARAM3(double, double, wind_info *);
wind_info *get_wind_info_latlong_now_context PARAM4(vlmc_context *,
						    double, double, 
						    wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong_TWSA PARAM4(double, double, time_t, 
					     wind_info *);
wind_info *get_wind_info_latlong_TWSA_context PARAM5(vlmc_context *,
						     double, double, time_t, 
						     wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * and switch to UV if rotations are happenning in two opposite directions
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong_selective_TWSA PARAM4(double, double, time_t, 
						       wind_info *);
wind_info *get_wind_info_latlong_selective_TWSA_context PARAM5(vlmc_context *,
							       double, double,
							       time_t, 
							       wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * It uses the bilinear interpolation in UV
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong_UV PARAM4(double, double, time_t, wind_info *);
wind_info *get_wind_info_latlong_UV_context PARAM5(vlmc_context *, double, 
						   double, time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV to compute the wind angle
 * and bilinear interpolation on wind speed as calculated in TWSA for speed 
 * @param latitude, a <code>double</code>, in <em>radians</em>
 * @param longitude, a <code>double</code>, in <em>radians</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>radians</em>
 */
wind_info *get_wind_info_latlong_hybrid PARAM4(double, double, 
					       time_t, wind_info *);
wind_info *get_wind_info_latlong_hybrid_context PARAM5(vlmc_context *, double, 
						       double, time_t,
						       wind_info *);


/* get the timestamp (in seconds , see time()) of the first grib entry
   technically, an observation, not a prevision */
time_t get_min_prevision_time();
time_t get_min_prevision_time_context PARAM1(vlmc_context *);

/* get the timestamp (in seconds , see time()) of the last prevision */
time_t get_max_prevision_time();
time_t get_max_prevision_time_context PARAM1(vlmc_context *);

/* get the number of stored gribs in the windtable structure */
int get_prevision_count();
int get_prevision_count_context PARAM1(vlmc_context *);

/**
 * get the timestamp (in seconds , see time()) of the n-th prevision
 * @return 0 if out of bounds, or no windtable struct is present
 */
time_t get_prevision_time_index PARAM1(int);
time_t get_prevision_time_index_context PARAM2(vlmc_context *, int);

#endif /* _VLMC_WINDS_H_ */
