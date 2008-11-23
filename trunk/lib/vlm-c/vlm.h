/**
 * $Id: vlm.h,v 1.5 2008/08/08 08:00:32 ylafon Exp $
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

#ifndef _VLM_H_
#define _VLM_H_

#include <math.h>

#include "defs.h"
#include "types.h"

void set_vlm_pilot_mode PARAM2(boat *, int);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_deg      PARAM4(double, double, 
						 time_t, wind_info *);
/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_deg_UV   PARAM4(double, double, 
						 time_t, wind_info *);
/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_deg_TWSA PARAM4(double, double, 
						 time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_millideg      PARAM4(double, double, 
						      time_t, wind_info *);
/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_millideg_UV   PARAM4(double, double, 
						      time_t, wind_info *);
/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *get_wind_info_latlong_millideg_TWSA PARAM4(double, double, 
						      time_t, wind_info *);

#endif /* _VLM_H_ */
