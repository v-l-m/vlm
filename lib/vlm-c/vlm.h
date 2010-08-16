/**
 * $Id: vlm.h,v 1.24 2010-08-16 13:17:51 ylafon Exp $
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
wind_info *VLM_get_wind_info_latlong_deg      PARAM4(double, double, 
						     time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_deg_context PARAM5(vlmc_context *, 
							double, double, 
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
wind_info *VLM_get_wind_info_latlong_deg_UV   PARAM4(double, double, 
						     time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_deg_UV_context PARAM5(vlmc_context *,
							   double, double, 
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
wind_info *VLM_get_wind_info_latlong_deg_TWSA PARAM4(double, double, 
						     time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_deg_TWSA_context PARAM5(vlmc_context *,
							     double, double, 
							     time_t, 
							     wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * In selective mode
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_deg_selective_TWSA PARAM4(double, double, 
							       time_t, 
							       wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * In selective mode
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>degrees</em>
 * @param longitude, a <code>double</code>, in <em>degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_deg_selective_TWSA_context PARAM5(
							       vlmc_context *,
							       double, double, 
							       time_t, 
							       wind_info *);

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
wind_info *VLM_get_wind_info_latlong_millideg      PARAM4(double, double, 
							  time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the default interpolation defined at compile time, between
 * interpolation in UV or True Wind Speed/Angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_context PARAM5(vlmc_context *,
							     double, double, 
							     time_t,
							     wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_UV   PARAM4(double, double, 
							  time_t, wind_info *);
/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in UV
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_UV_context PARAM5(vlmc_context *,
								double, double, 
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
wind_info *VLM_get_wind_info_latlong_millideg_TWSA PARAM4(double, double, 
							  time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_TWSA_context PARAM5(
							       vlmc_context *,
							       double, double, 
							   time_t, wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * In selective mode
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_selective_TWSA PARAM4(double,
								    double, 
								    time_t, 
								   wind_info *);

/**
 * get wind info based on the location and time
 * It uses the bilinear interpolation in True Wind Speed / Angle
 * In selective mode
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param vac_time, a <code>time_t</code>, in seconds since epoch
 * @param wind, a <code>wind_info *</code>, a pointer to a wind_info structure
 * that will be filled with speed in <em>kts</em>, and angle in <em>degrees</em>
 */
wind_info *VLM_get_wind_info_latlong_millideg_selective_TWSA_context PARAM5(
							        vlmc_context *,
								double, double, 
								time_t, 
								wind_info *);

/**
 * Compute the orthodromic distance between two points, A & B
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @return a double, the distance, a <code>double</code> in nautic miles.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_ortho_distance PARAM4(double, double, double, double);

/**
 * Compute the orthodromic heading between two points, A & B
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @return a double, the heading, a <code>double</code> in degrees 
 *         from 0 to 360.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_ortho_heading PARAM4(double, double, double, double);

/**
 * Get loxodromic distance
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_long, a <code>double</code>, in <em>milli-degrees</em>
 * @return heading, a <code>double</code>, the resulting
 *                 distance in <em>nm</em>
 */
double VLM_loxo_distance PARAM4(double, double, double, double);

/**
 * Get loxodromic heading
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_long, a <code>double</code>, in <em>milli-degrees</em>
 * @return heading, a <code>double</code>, the resulting
 *                 heading in <em>degrees</em>
 */
double VLM_loxo_heading PARAM4(double, double, double, double);

/**
 * Compute the orthodromic distance between a point and a line defined
 * by two points, A & B
 * This is done in cartesian coordinates to find the intersection point
 * which is a _bad_ approximation for long distances. Then ortho is used
 * to get the real distance.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param xing_latitude, a pointer to a <code>double</code>, 
 *        in <em>milli-degrees</em>
 * @param xing_longitude, a pointer to a <code>double</code>, 
 *        in <em>milli-degrees</em>
 * @param ratio, a pointer to a <code>double</code>, value between 0 and 1
 *        ratio = position of the point from a to b.
 * @return a double, the distance, a <code>double</code> in nautic miles.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_distance_to_line_ratio_xing PARAM9( double, double,
					       double, double,
					       double, double,
					       double *, double *,
					       double *);


/**
 * Compute the orthodromic distance between a point and a line defined
 * by two points, A & B
 * This is done in cartesian coordinates to find the intersection point
 * which is a _bad_ approximation for long distances. Then ortho is used
 * to get the real distance.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @return a double, the distance, a <code>double</code> in nautic miles.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_distance_to_line PARAM6( double, double,
				    double, double,
				    double, double);


/**
 * Compute the coordinate of a point computed form an origin, using a 
 * loxodromic course, with a specified heading and distance.
 * it is clipped at +/-80 degrees.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param distance, a <code>double</code>, in <em>milli-degrees</em>
 * @param heading, a <code>double</code>, in <em>degrees</em>
 * @param target_lat, a pointer to a <code>double</code>, the resulting
 *                    latitude in <em>milli-degrees</em>
 * @param target_long, a pointer to a <code>double</code>, the resulting
 *                    longitude in <em>milli-degrees</em>
 */
void VLM_get_loxo_coord_from_dist_angle PARAM6(double, double, double, double,
					       double *, double *);

/**
 * Compute the loxodromic distance and heading from one point to another.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_long, a <code>double</code>, in <em>milli-degrees</em>
 * @param distance, a pointer to a <code>double</code>, the resulting
 *                  distance in <em>nautic miles</em>
 * @param heading, a pointer to a <code>double</code>, the resulting
 *                 heading in <em>degrees</em>
 */
void VLM_loxo_distance_angle PARAM6(double, double, double, double,
				    double *, double *);

/**
 * Check if the waypoint is crossed.
 * We check if we are in the range where approximation is valid,
 * otherwise we compute a clipped part of the gate, then use the
 * approximation method of checking the gate crossing
 * order of parameters is:
 * start lat/long of boat
 * end lat/long of boat
 * start WP of Gate
 * end WP of Gate
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param wp0_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param wp0_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param wp1_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param wp1_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param ratio, a pointer to  a <code>double</code>, the ratio of 
 *        the intersection, 0 (boat start) < ratio < 1 (boat end)
 * @return 1 if crossing occured, 0 otherwise
 */
int VLM_check_cross_WP PARAM11(double, double, double, double,
			       double, double, double, double,
			       double *, double *, double *);

/**
 * Check if the coast is crossed.
 * order of parameters is:
 * start lat/long of boat
 * end lat/long of boat
 * result:
 * crossing lat/long
 * ratio from the start to end of boat
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param xing_lat, a pointer to a <code>double</code>, 
 *                  in <em>milli-degrees</em>
 * @param xing_long, a pointer to a <code>double</code>, 
 *                   in <em>milli-degrees</em> 
 * @param ratio, a pointer to  a <code>double</code>, the ratio of 
 *        the intersection, 0 (boat start) < ratio < 1 (boat end)
 * @return 1 if crossing occured, 0 otherwise
 */
int VLM_check_cross_coast PARAM7(double, double, double, double,
				 double *, double *,
				 double *);

/**
 * Get the best VMG heading
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param polar_name, a pointer to <code>char</code>, a <em>string</em>
 *                    the full name of the polar
 * @param heading, a pointer to a <em>double</em>, the resulting
 *                 heading in <em>degrees</em>
 * @param vmg, a pointer to a <em>double</em>, the resulting
 *                 vmg in <em>knots</em>
 */
void VLM_best_vmg PARAM7(double, double, double, double, char *,
			 double *, double *);

/**
 * Get the best VMG heading
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param polar_name, a pointer to <code>char</code>, a <em>string</em>
 *                    the full name of the polar
 * @param heading, a pointer to a <em>double</em>, the resulting
 *                 heading in <em>degrees</em>
 * @param vmg, a pointer to a <em>double</em>, the resulting
 *                 vmg in <em>knots</em>
 */
void VLM_best_vmg_context PARAM8(vlmc_context *, double, double, 
				 double, double, char *,
				 double *, double *);

/**
 * Get the VBVMG heading
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param polar_name, a pointer to <code>char</code>, a <em>string</em>
 *                    the full name of the polar
 * @param heading, a pointer to a <em>double</em>, the resulting
 *                 heading in <em>degrees</em>
 * @param vmg, a pointer to a <em>double</em>, the resulting
 *                 vmg in <em>knots</em>
 */
void VLM_vbvmg PARAM7(double, double, double, double, char *,
		      double *, double *);

/**
 * Get the VBVMG heading
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param polar_name, a pointer to <code>char</code>, a <em>string</em>
 *                    the full name of the polar
 * @param heading, a pointer to a <em>double</em>, the resulting
 *                 heading in <em>degrees</em>
 * @param vmg, a pointer to a <em>double</em>, the resulting
 *                 vmg in <em>knots</em>
 */
void VLM_vbvmg_context PARAM8(vlmc_context *, double, double, 
			      double, double, char *,
			      double *, double *);

/**
 * Get the speed boat based on wind speed and wind angle
 * @param polar_name, a <code>char *</code>, the polar name
 * @param wind_speed, a <code>double</code>, in <em>kts</em>
 * @param angle_diff, a <code>double</code>, in <em>degrees</em>
 * @return a double, the boat speed in <em>kts</em>
 */
double VLM_find_boat_speed PARAM3(char *,double, double);

/**
 * Get the speed boat based on wind speed and wind angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param polar_name, a <code>char *</code>, the polar name
 * @param wind_speed, a <code>double</code>, in <em>kts</em>
 * @param angle_diff, a <code>double</code>, in <em>degrees</em>
 * @return a double, the boat speed in <em>kts</em>
 */
double VLM_find_boat_speed_context PARAM4(vlmc_context *, char *,
					  double, double);

/**
 * Move the boat with a specific distance and heading
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param heading, a <em>double</em>, the heading in <em>degrees</em>
 * @param distance, a <em>double</em>, the distance in <em>nautic miles</em>
 * @param new_latitude, a pointer to a <code>double</code>, 
 *                      in <em>milli-degrees</em>
 * @param new_longitude, a pointer to a <code>double</code>,
 *                       in <em>milli-degrees</em>
 */
void VLM_raw_move_loxo PARAM6(double, double, double, double,
			      double *, double *);

/**
 * Create a two buoys wp structure out of any buoy definition
 * @param wp, a <code>pointer to a waypoint struct</code>
 * @param wp_type, an <code>int</code> representing the wp type (bitmask+value)
 * @param id, an <code>int</code>, the id of the waypoint.
 * @param lat1, a <code>double</code>, in <em>milli-degrees</em>
 * @param long1, a <code>double</code>, in <em>milli-degrees</em>
 * @param lat2, a <code>double</code>, in <em>milli-degrees</em>
 * @param long2, a <code>double</code>, in <em>milli-degrees</em>
 * @param leave_at, a <em>double</em>, leave the buoy at this angle
 *                                     in <em>degrees</em>
 * @param gate_length, a <em>double</em>, the gate length in the 
 *                     one buoy+leave_at case (in <em>nautic miles</em>)
 * NOTE the wp struc is filled with lat/long in <code>degrees</code>
 * and angle in <code>radians</code>
 */
void VLM_init_waypoint PARAM9(waypoint *, int, int,
			      double, double, double, double,
			      double, double);
#endif /* _VLM_H_ */
