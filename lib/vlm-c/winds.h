/**
 * $Id: winds.h,v 1.8 2009-05-12 22:10:47 ylafon Exp $
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

#ifndef _WINDS_H_
#define _WINDS_H_

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

/* get the timestamp (in seconds , see time()) of the first grib entry
   technically, an observation, not a prevision */
time_t get_min_prevision_time();

/* get the timestamp (in seconds , see time()) of the last prevision */
time_t get_max_prevision_time();

/* get the number of stored gribs in the windtable structure */
int get_prevision_count();

/**
 * get the timestamp (in seconds , see time()) of the n-th prevision
 * @return 0 if out of bounds, or no windtable struct is present
 */
time_t get_prevision_time_index PARAM1(int);

#endif /* _WINDS_H_ */
