/**
 * $Id: vlm.c,v 1.7 2008-11-23 16:14:06 ylafon Exp $
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
#include "loxo.h"
#include "vmg.h"
#include "ortho.h"
#include "winds.h"

/**
 * using VLM's PIM definition, set the heading function of the boat
 * accordingly 
 * @param aboat, a pointer to a boat struct
 * @param vlm_mode, an int, the numeric value of the PIM
 */
void set_vlm_pilot_mode(boat *aboat, int vlm_mode) {

   switch (vlm_mode) {
   case 1:
     /* needs heading filled in aboat->wp_heading */
     aboat->set_heading_func=&set_heading_constant;
     break;
   case 2:
     /* needs heading filled in aboat->wp_heading */
     aboat->set_heading_func=&set_heading_wind_angle;
     break;
   case 3:
     /* needs WP filled in aboat->wp_lat/long */
     aboat->set_heading_func=&set_heading_ortho;
     break;
   case 4:
     /* needs WP filled in aboat->wp_lat/long */
     aboat->set_heading_func=&set_heading_bvmg;
     break;
   default:
     /* add a warning? fail? exit? */
     ;
   }
}

/**
 * This function uses the default interpolation function as defined in the
 * compilation options
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
 wind_info *VLM_get_wind_info_latlong_deg(double latitude, double longitude,
					 time_t vac_time, wind_info *wind) {
  get_wind_info_latlong(degToRad(latitude), degToRad(longitude),
			vac_time, wind);
  /* VLM needs wind in the opposite direction */
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the U/V/time trilinear interpolation
 * @param latitude, a double, in degree.
 * @param longitude, a double, in degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_UV(double latitude, double longitude, 
					    time_t vac_time, wind_info *wind) {
  get_wind_info_latlong_UV(degToRad(latitude), degToRad(longitude),
			   vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * @param latitude, a double, in degree.
 * @param longitude, a double, in degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_TWSA(double latitude, double longitude,
					      time_t vac_time,
					      wind_info *wind) {
  get_wind_info_latlong_TWSA(degToRad(latitude), degToRad(longitude),
			     vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the default interpolation function as defined in the
 * compilation options
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg(double latitude, double longitude,
					      time_t vac_time, 
					      wind_info *wind) {
  get_wind_info_latlong(degToRad(latitude/1000.0), degToRad(longitude/1000.0),
			vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the U/V/time trilinear interpolation
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_UV(double latitude, 
						 double longitude, 
						 time_t vac_time,
						 wind_info *wind) {
  get_wind_info_latlong_UV(degToRad(latitude/1000.0), 
			   degToRad(longitude/1000.0),
			   vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_TWSA(double latitude,
						   double longitude,
						   time_t vac_time,
						   wind_info *wind) {
  get_wind_info_latlong_TWSA(degToRad(latitude/1000.0), 
			     degToRad(longitude/1000.0),
			     vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * Compute the orthodromic distance between two points, A & B
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @return a double, the distance, a <code>double</code> in nautic miles.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_ortho_distance(double latitude_a, double longitude_a, 
			  double latitude_b, double longitude_b) {
  /* sanity check */
  latitude_a  = latitude_a / 1000.0;
  longitude_a = fmod((longitude_a / 1000.0), 360.0);
  latitude_b  = latitude_b / 1000.0;
  longitude_b = fmod((longitude_b / 1000.0), 360.0);

  /* if something goes wrong, return -1 */
  if (latitude_a < -90.0 || latitude_a > 90.0 ||
      latitude_b < -90.0 || latitude_b > 90.0) {
    return -1.0;
  }

  return ortho_distance(degToRad(latitude_a), degToRad(longitude_a),
			degToRad(latitude_b), degToRad(longitude_b));
}

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
double VLM_distance_to_line(double latitude, double longitude, 
			    double latitude_a, double longitude_a, 
			    double latitude_b, double longitude_b) {
  /* sanity check */
  latitude    = latitude / 1000.0;
  longitude   = fmod((longitude / 1000.0), 360.0);
  latitude_a  = latitude_a / 1000.0;
  longitude_a = fmod((longitude_a / 1000.0), 360.0);
  latitude_b  = latitude_b / 1000.0;
  longitude_b = fmod((longitude_b / 1000.0), 360.0);

  /* if something goes wrong, return -1 */
  if (latitude   < -90.0 || latitude   > 90.0 ||
      latitude_a < -90.0 || latitude_a > 90.0 ||
      latitude_b < -90.0 || latitude_b > 90.0) {
    return -1.0;
  }

  return distance_to_line(degToRad(latitude)  , degToRad(longitude),
			  degToRad(latitude_a), degToRad(longitude_a),
			  degToRad(latitude_b), degToRad(longitude_b));
}
