/**
 * $Id: vlm.c,v 1.38 2010-08-17 22:30:28 ylafon Exp $
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
#include <stdio.h>
#include <string.h>

#include "defs.h"
#include "types.h"
#include "lines.h"
#include "loxo.h"
#include "ortho.h"
#include "polar.h"
#include "vlm.h"
#include "vmg.h"
#include "winds.h"
#include "waypoint.h"
#include "boat.h"

extern vlmc_context *global_vlmc_context;

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
  return VLM_get_wind_info_latlong_deg_context(global_vlmc_context,
					       latitude, longitude,
					       vac_time, wind);
}

/**
 * This function uses the default interpolation function as defined in the
 * compilation options
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_context(vlmc_context *context,
						 double latitude, 
						 double longitude,
						 time_t vac_time,
						 wind_info *wind) {
  get_wind_info_latlong_context(context, degToRad(latitude), 
				degToRad(longitude), vac_time, wind);
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
  return VLM_get_wind_info_latlong_deg_UV_context(global_vlmc_context,
						  latitude, longitude,
						  vac_time, wind);
}

/**
 * This function uses the U/V/time trilinear interpolation
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in degree.
 * @param longitude, a double, in degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_UV_context(vlmc_context *context,
						    double latitude, 
						    double longitude, 
						    time_t vac_time,
						    wind_info *wind) {
  get_wind_info_latlong_UV_context(context, degToRad(latitude), 
				   degToRad(longitude), vac_time, wind);
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
  return VLM_get_wind_info_latlong_deg_TWSA_context(global_vlmc_context,
						    latitude, longitude,
						    vac_time, wind);
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
wind_info *VLM_get_wind_info_latlong_deg_TWSA_context(vlmc_context *context,
						      double latitude,
						      double longitude,
						      time_t vac_time,
						      wind_info *wind) {
  get_wind_info_latlong_TWSA_context(context, degToRad(latitude), 
				     degToRad(longitude), vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * in selective mode
 * @param latitude, a double, in degree.
 * @param longitude, a double, in degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_selective_TWSA(double latitude,
							double longitude,
							time_t vac_time,
							wind_info *wind) {
  return VLM_get_wind_info_latlong_deg_selective_TWSA_context(
					    global_vlmc_context,
					    latitude, longitude,
					    vac_time, wind);
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * in selective mode
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in degree.
 * @param longitude, a double, in degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_deg_selective_TWSA_context(
							 vlmc_context *context,
							 double latitude,
							 double longitude,
							 time_t vac_time,
							 wind_info *wind) {
  get_wind_info_latlong_selective_TWSA_context(context, degToRad(latitude), 
					       degToRad(longitude), vac_time,
					       wind);
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
  return VLM_get_wind_info_latlong_millideg_context(global_vlmc_context,
						    latitude, longitude,
						    vac_time, wind);
}

/**
 * This function uses the default interpolation function as defined in the
 * compilation options
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_context(vlmc_context *context,
						      double latitude,
						      double longitude,
						      time_t vac_time, 
						      wind_info *wind) {
  get_wind_info_latlong_context(context, degToRad(latitude/1000.0), 
				degToRad(longitude/1000.0), vac_time, wind);
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
  return VLM_get_wind_info_latlong_millideg_UV_context(global_vlmc_context,
						       latitude, longitude,
						       vac_time, wind);
}

/**
 * This function uses the U/V/time trilinear interpolation
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_UV_context(vlmc_context *context,
							 double latitude, 
							 double longitude, 
							 time_t vac_time,
							 wind_info *wind) {
  get_wind_info_latlong_UV_context(context, degToRad(latitude/1000.0), 
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
  return VLM_get_wind_info_latlong_millideg_TWSA_context(global_vlmc_context,
							 latitude, longitude,
							 vac_time, wind);
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_TWSA_context(
							vlmc_context *context,
							double latitude,
							double longitude,
							time_t vac_time,
							wind_info *wind) {
  get_wind_info_latlong_TWSA_context(context, degToRad(latitude/1000.0), 
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
wind_info *VLM_get_wind_info_latlong_millideg_selective_TWSA(double latitude,
							     double longitude,
							     time_t vac_time,
							     wind_info *wind) {
  get_wind_info_latlong_selective_TWSA(degToRad(latitude/1000.0), 
				       degToRad(longitude/1000.0),
				       vac_time, wind);
  wind->angle = fmod((radToDeg(wind->angle)+180.0), 360.0);
  return wind;
}

/**
 * This function uses the True Wind Speed & Angle interpolation function
 * (polar/time tri-linear interpolation)
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param latitude, a double, in milli-degree.
 * @param longitude, a double, in milli-degree.
 * @param vac_time, a time_t, in seconds since 00:00:00 January 1, 1970
 * @param wind, a pointer to a wind_info structure
 * @return the pointer to the wind_info structure above
 * NOTE: the wind_info structure is filled with
 * * speed, a double, in kts
 * * angle, a double, in degrees between 0.0 and 359.9999..
 */
wind_info *VLM_get_wind_info_latlong_millideg_selective_TWSA_context(
							 vlmc_context *context,
							 double latitude,
							 double longitude,
							 time_t vac_time,
							 wind_info *wind) {
  get_wind_info_latlong_selective_TWSA_context(context, 
					       degToRad(latitude/1000.0), 
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
 * Compute the orthodromic heading between two points, A & B
 * @param latitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_a, a <code>double</code>, in <em>milli-degrees</em>
 * @param latitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude_b, a <code>double</code>, in <em>milli-degrees</em>
 * @return a double, the heading, a <code>double</code> in degrees 
 *         from 0 to 360.
 * If the parameters are incorrect, -1.0 is returned.
 */
double VLM_ortho_heading(double latitude_a, double longitude_a, 
			 double latitude_b, double longitude_b) {
  double heading;
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

  heading = ortho_initial_angle(degToRad(latitude_a), degToRad(longitude_a),
				degToRad(latitude_b), degToRad(longitude_b));
  return radToDeg(heading);
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
double VLM_distance_to_line_ratio_xing(double latitude, double longitude, 
				       double latitude_a, double longitude_a, 
				       double latitude_b, double longitude_b,
				       double *xing_lat, double *xing_long,
				       double *ratio) {
  double x_lat, x_long, dist;
  
  /* sanity check */
  latitude    = latitude / 1000.0;
  longitude   = fmod((longitude / 1000.0), 360.0);
  if (longitude < 0.0) {
    longitude += 360.0;
  }
  latitude_a  = latitude_a / 1000.0;
  longitude_a = fmod((longitude_a / 1000.0), 360.0);
  if (longitude_a < 0.0) {
    longitude_a += 360.0;
  }
  latitude_b  = latitude_b / 1000.0;
  longitude_b = fmod((longitude_b / 1000.0), 360.0);
  if (longitude_b < 0.0) {
    longitude_b += 360.0;
  }

  /* if something goes wrong, return -1 */
  if (latitude   < -90.0 || latitude   > 90.0 ||
      latitude_a < -90.0 || latitude_a > 90.0 ||
      latitude_b < -90.0 || latitude_b > 90.0) {
    return -1.0;
  }

  dist = distance_to_line_ratio_xing(degToRad(latitude)  ,degToRad(longitude),
				     degToRad(latitude_a),degToRad(longitude_a),
				     degToRad(latitude_b),degToRad(longitude_b),
				     &x_lat, &x_long, ratio);
  if (x_long > PI) {
    x_long -= TWO_PI;
  } else if (x_long < -PI) {
    x_long += TWO_PI;
  }
  *xing_lat  = 1000.0 * radToDeg(x_lat);
  *xing_long = 1000.0 * radToDeg(x_long);
  return dist;
}

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
void VLM_get_loxo_coord_from_dist_angle(double latitude, double longitude,
					double distance, double heading,
					double *target_lat, 
					double *target_long) {
  double new_lat, new_long, ratio;
  /* first, sanitize everything */
  latitude = degToRad(latitude/1000.0);
  longitude = fmod(degToRad(longitude/1000.0), TWO_PI);
  heading = degToRad(heading);
  
  get_loxo_coord_from_dist_angle(latitude, longitude, distance, heading,
				 &new_lat, &new_long);
  if (fabs(new_lat) > degToRad(80.0)) {
    ratio = (degToRad(80.0)-fabs(latitude)) / (fabs(new_lat)-fabs(latitude));
    distance *= ratio;
    get_loxo_coord_from_dist_angle(latitude, longitude, distance, heading,
				   &new_lat, &new_long);
  }
  if (new_long > PI) {
    new_long -= TWO_PI;
  } else if (new_long < -PI) {
    new_long += TWO_PI;
  }
  *target_lat = 1000.0 * radToDeg(new_lat);
  *target_long = 1000.0 * radToDeg(new_long);
}

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
void VLM_loxo_distance_angle(double latitude, double longitude, 
			     double target_lat, double target_long,
			     double *distance, double *heading) {
  
  double new_heading;
  /* first, sanitize everything */
  latitude    = degToRad(latitude/1000.0);
  longitude   = fmod(degToRad(longitude/1000.0), TWO_PI);
  target_lat  = degToRad(target_lat/1000.0);
  target_long = fmod(degToRad(target_long/1000.0), TWO_PI);
  
  loxo_distance_angle(latitude, longitude, target_lat, target_long,
		      distance, &new_heading);
  *heading = radToDeg(fmod(new_heading+TWO_PI, TWO_PI));
}

/**
 * Get loxodromic distance
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_long, a <code>double</code>, in <em>milli-degrees</em>
 * @return heading, a <code>double</code>, the resulting
 *                 distance in <em>nm</em>
 */
double VLM_loxo_distance(double latitude, double longitude, 
			 double target_lat, double target_long) {
  double distance, heading;
  VLM_loxo_distance_angle(latitude, longitude, target_lat, target_long,
			  &distance, &heading);
  return distance;
}

/**
 * Get loxodromic heading
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param target_long, a <code>double</code>, in <em>milli-degrees</em>
 * @return heading, a <code>double</code>, the resulting
 *                 heading in <em>degrees</em>
 */
double VLM_loxo_heading(double latitude, double longitude, 
			double target_lat, double target_long) {
  double distance, heading;
  VLM_loxo_distance_angle(latitude, longitude, target_lat, target_long,
			  &distance, &heading);
  return heading;
}


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
 * result:
 * crossing lat/long
 * ratio from the start to end of boat
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param wp0_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param wp0_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param wp1_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param wp1_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param xing_lat, a pointer to a <code>double</code>, 
 *                  in <em>milli-degrees</em>
 * @param xing_long, a pointer to a <code>double</code>, 
 *                   in <em>milli-degrees</em> 
 * @param ratio, a pointer to  a <code>double</code>, the ratio of 
 *        the intersection, 0 (boat start) < ratio < 1 (boat end)
 * @return 1 if crossing occured, 0 otherwise
 */
int VLM_check_cross_WP(double latitude, double longitude, 
		       double new_lat, double new_long,
		       double wp0_lat, double wp0_long,
		       double wp1_lat, double wp1_long,
		       double *xing_lat, double *xing_long,
		       double *ratio) {

  double c_ratio, r_lat, r_long;

  latitude  = latToY(degToRad(latitude/1000.0));
  longitude = fmod(degToRad(longitude/1000.0), TWO_PI);
  new_lat   = latToY(degToRad(new_lat/1000.0));
  new_long  = fmod(degToRad(new_long/1000.0), TWO_PI);

  wp0_lat  = latToY(degToRad(wp0_lat/1000.0));
  wp0_long = degToRad(wp0_long/1000.0);
  wp1_lat  = latToY(degToRad(wp1_lat/1000.0));
  wp1_long = degToRad(wp1_long/1000.0);

  c_ratio = intersects(latitude, longitude, new_lat, new_long,
		       wp0_lat, wp0_long, wp1_lat, wp1_long,
		       &r_lat, &r_long);
  if (c_ratio > -1.0) {
    *ratio     = c_ratio;
    *xing_lat  = 1000.0 * radToDeg(yToLat(r_lat));
    *xing_long = 1000.0 * radToDeg(r_long);
    return 1;
  }
  return 0;
}

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
int VLM_check_cross_coast(double latitude, double longitude, 
			  double new_lat, double new_long,
			  double *xing_lat, double *xing_long,
			  double *ratio) {

  double c_ratio, r_lat, r_long;
  
  if ((latitude > MAX_LAT_GSHHS*1000) || latitude < (-1000*MAX_LAT_GSHHS)) {
    *ratio = 0;
    *xing_lat = (latitude<0)?-MAX_LAT_GSHHS*1000:MAX_LAT_GSHHS*1000;
    *xing_long = longitude;
    return 1;
  }
      
  latitude  = degToRad(latitude/1000.0);
  longitude = fmod(degToRad(longitude/1000.0), TWO_PI);
  new_lat   = degToRad(new_lat/1000.0);
  new_long  = fmod(degToRad(new_long/1000.0), TWO_PI);

  c_ratio = check_coast(latitude, longitude, new_lat, new_long,
			&r_lat, &r_long);
  if (c_ratio > -1.0) {
    *ratio     = c_ratio;
    if (r_long > PI) {
      r_long -= TWO_PI;
    } else if (r_long < -PI) {
      r_long += TWO_PI;
    }
    *xing_lat  = 1000.0 * radToDeg(r_lat);
    *xing_long = 1000.0 * radToDeg(r_long);
    return 1;
  }
  return 0;
}

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
void VLM_best_vmg(double latitude, double longitude,
		  double target_lat, double target_long,
		  char *polar_name, double *heading, double *vmg) {
  VLM_best_vmg_context(global_vlmc_context, latitude, longitude,
		       target_lat, target_long, polar_name, heading, vmg);
}

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
void VLM_best_vmg_context(vlmc_context *context,
			  double latitude, double longitude,
			  double target_lat, double target_long,
			  char *polar_name, double *heading, double *vmg) {
  char *real_polar_name;
  boat_polar *polar;
  boat aboat;
  race arace;
  double t_heading;

  /* if no polar are defined, bail out */
  if (!polar_name) {
    return;
  }
  
  if (!strncmp(polar_name, "boat_", 5)) {
    real_polar_name = &polar_name[5];
  } else {
    real_polar_name = polar_name;
  }

  latitude    = degToRad(latitude/1000.0);
  longitude   = fmod(degToRad(longitude/1000.0), TWO_PI);
  target_lat  = degToRad(target_lat/1000.0);
  target_long = fmod(degToRad(target_long/1000.0), TWO_PI);
  
  /* we fake stuff to have the bvmg computed "now" */
  polar = get_polar_by_name_context(context, real_polar_name);

  arace.vac_duration = 0;
  arace.boattype     = polar;

  aboat.latitude     = latitude;
  aboat.longitude    = longitude;
  aboat.wp_latitude  = target_lat;
  aboat.wp_longitude = target_long;
  aboat.in_race      = &arace;
  aboat.polar        = polar;
  time(&(aboat.last_vac_time));

  t_heading = get_heading_bvmg_context(context, &aboat, 0);
  *heading = radToDeg(t_heading);
  *vmg = find_speed(&aboat, aboat.wind.speed, aboat.wind.angle - t_heading);
}

/**
 * Get the VB VMG heading
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
void VLM_vbvmg(double latitude, double longitude,
	       double target_lat, double target_long,
	       char *polar_name, double *heading, double *vmg) {
  VLM_vbvmg_context(global_vlmc_context, latitude, longitude,
		    target_lat, target_long, polar_name, heading, vmg);
}

/**
 * Get the VB VMG heading
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
void VLM_vbvmg_context(vlmc_context *context,
		       double latitude, double longitude,
		       double target_lat, double target_long,
		       char *polar_name, double *heading, double *vmg) {
  char *real_polar_name;
  boat_polar *polar;
  boat aboat;
  race arace;
  double t_heading;

  /* if no polar are defined, bail out */
  if (!polar_name) {
    return;
  }
  
  if (!strncmp(polar_name, "boat_", 5)) {
    real_polar_name = &polar_name[5];
  } else {
    real_polar_name = polar_name;
  }

  latitude    = degToRad(latitude/1000.0);
  longitude   = fmod(degToRad(longitude/1000.0), TWO_PI);
  target_lat  = degToRad(target_lat/1000.0);
  target_long = fmod(degToRad(target_long/1000.0), TWO_PI);
  
  /* we fake stuff to have the bvmg computed "now" */
  polar = get_polar_by_name_context(context, real_polar_name);

  arace.vac_duration = 0;
  arace.boattype     = polar;

  aboat.latitude     = latitude;
  aboat.longitude    = longitude;
  aboat.wp_latitude  = target_lat;
  aboat.wp_longitude = target_long;
  aboat.in_race      = &arace;
  aboat.polar        = polar;
  time(&(aboat.last_vac_time));

  t_heading = get_heading_vbvmg_context(context, &aboat, 0);
  *heading = radToDeg(t_heading);
  *vmg = find_speed(&aboat, aboat.wind.speed, aboat.wind.angle - t_heading);
}

/**
 * Get the speed boat based on wind speed and wind angle
 * @param polar_name, a <code>char *</code>, the polar name
 * @param wind_speed, a <code>double</code>, in <em>kts</em>
 * @param angle_diff, a <code>double</code>, in <em>degrees</em>
 * @return a double, the boat speed in <em>kts</em>
 */
double VLM_find_boat_speed(char *polar_name, double wind_speed, 
			   double angle_diff) {
  return VLM_find_boat_speed_context(global_vlmc_context, polar_name,
				     wind_speed, angle_diff);
}

/**
 * Get the speed boat based on wind speed and wind angle
 * @param context, a <code>vlmc_context *</code> pointer to a vlmc_context.
 * @param polar_name, a <code>char *</code>, the polar name
 * @param wind_speed, a <code>double</code>, in <em>kts</em>
 * @param angle_diff, a <code>double</code>, in <em>degrees</em>
 * @return a double, the boat speed in <em>kts</em>
 */
double VLM_find_boat_speed_context(vlmc_context *context,
				   char *polar_name, double wind_speed, 
				   double angle_diff) {
  char *real_polar_name;
  boat fakeboat;

  /* if no polar are defined, bail out */
  if (!polar_name) {
    return 0.0;
  }
  
  if (!strncmp(polar_name, "boat_", 5)) {
    real_polar_name = &polar_name[5];
  } else {
    real_polar_name = polar_name;
  }

  fakeboat.polar = NULL;
  associate_polar_boat_context(context, &fakeboat, real_polar_name);
  /* round things */
  angle_diff = degToRad(fmod(angle_diff, 360.0));

  return find_speed(&fakeboat, wind_speed, angle_diff);
}

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
void VLM_raw_move_loxo(double latitude, double longitude, 
		       double distance, double heading,
		       double *new_latitude, double*new_longitude) {
  double t_lat, t_long, t_new_lat, t_new_long;
  
  // normalization
  heading = degToRad(fmod(heading, 360.0));
  t_lat  = degToRad(latitude / 1000.0);
  t_long = degToRad(fmod((longitude / 1000.0), 360.0));
  // sanity check
  if ((t_lat < -PI_2) || (t_lat > PI_2)) {
    *new_latitude = latitude;
    *new_longitude = longitude;
    return;
  }
  raw_move_loxo(t_lat, t_long, distance, heading, 
		&t_new_lat, &t_new_long);
  // sanity check on result
  if ((t_new_lat < -PI_2) || (t_new_lat > PI_2)) {
    *new_latitude = latitude;
    *new_longitude = longitude;
    return;
  }
  // aligning longitude to -PI / +PI
  if (t_new_long < -PI) {
    t_new_long += TWO_PI;
  } else if (t_new_long > PI) {
    t_new_long -= TWO_PI;
  }
  *new_latitude = radToDeg(t_new_lat*1000.0);
  *new_longitude = radToDeg(t_new_long*1000.0);
}

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
void VLM_init_waypoint(waypoint *wp, int wp_type, int id,
		       double lat1, double long1,
		       double lat2, double long2,
		       double leave_at, double gate_length) {
  double t_lat1, t_long1;
  double t_lat2, t_long2;

  leave_at = degToRad(fmod(leave_at, 360.0));
  t_lat1   = degToRad(lat1 / 1000.0);
  t_long1  = degToRad(fmod((long1 / 1000.0), 360.0));
  t_lat2   = degToRad(lat2 / 1000.0);
  t_long2  = degToRad(fmod((long2 / 1000.0), 360.0));
  
  init_waypoint(wp, wp_type, id, t_lat1, t_long1, t_lat2, t_long2, 
		leave_at, gate_length);
}

/**
 * Check if the waypoint is crossed.
 * We check if we are in the range where approximation is valid,
 * otherwise we compute a clipped part of the gate, then use the
 * approximation method of checking the gate crossing
 * order of parameters is:
 * start lat/long of boat
 * end lat/long of boat
 * Gate pointer
 * result:
 * crossing lat/long
 * ratio from the start to end of boat
 * @param latitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param longitude, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_lat, a <code>double</code>, in <em>milli-degrees</em>
 * @param new_long, a <code>double</code>, in <em>milli-degrees</em> 
 * @param wp, a pointer to a <code>waypoint struct</code>
 * NOTE, the wp struct must contains long/lat in radians.
 * @param xing_lat, a pointer to a <code>double</code>, 
 *                  in <em>milli-degrees</em>
 * @param xing_long, a pointer to a <code>double</code>, 
 *                   in <em>milli-degrees</em> 
 * @param ratio, a pointer to  a <code>double</code>, the ratio of 
 *        the intersection, 0 (boat start) < ratio < 1 (boat end)
 * @return 1 if crossing occured, 0 otherwise
 */
int VLM_check_WP(double latitude, double longitude, 
		 double new_lat, double new_long,
		 waypoint *wp,
		 double *xing_lat, double *xing_long,
		 double *ratio) { 
  double t_prev_lat, t_prev_long, t_new_lat, t_new_long;
  double t_xing_lat, t_xing_long;
  int crossed;
  
  t_prev_lat  = degToRad(latitude / 1000.0);
  t_prev_long = degToRad(fmod((longitude / 1000.0), 360.0));
  t_new_lat   = degToRad(new_lat / 1000.0);
  t_new_long  = degToRad(fmod((new_long / 1000.0), 360.0));
  
  crossed = check_waypoint(t_prev_lat, t_prev_long, t_new_lat, t_new_long,
			   wp, ratio, &t_xing_lat, &t_xing_long);
  if (crossed) {
    if (t_xing_long > PI) {
      t_xing_long -= TWO_PI;
    } else if (t_xing_long < -PI) {
      t_xing_long += TWO_PI;
    }
    *xing_lat  = 1000.0 * radToDeg(t_xing_lat);
    *xing_long = 1000.0 * radToDeg(t_xing_long);
  }
  return crossed;
}
