/**
 * $Id: vlm.c,v 1.6 2008/08/08 08:24:45 ylafon Exp $
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

wind_info *get_wind_info_latlong_deg(double latitude, double longitude,
				     time_t vac_time, wind_info *wind) {
  get_wind_info_latlong(degToRad(latitude), degToRad(longitude),
			vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

wind_info *get_wind_info_latlong_deg_UV(double latitude, double longitude, 
					time_t vac_time, wind_info *wind) {
  get_wind_info_latlong_UV(degToRad(latitude), degToRad(longitude),
			   vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

wind_info *get_wind_info_latlong_deg_TWSA(double latitude, double longitude,
					  time_t vac_time, wind_info *wind) {
  get_wind_info_latlong_TWSA(degToRad(latitude), degToRad(longitude),
			     vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

wind_info *get_wind_info_latlong_millideg(double latitude, double longitude,
				     time_t vac_time, wind_info *wind) {
  get_wind_info_latlong(degToRad(latitude/1000.0), degToRad(longitude/1000.0),
			vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

wind_info *get_wind_info_latlong_millideg_UV(double latitude, double longitude, 
					     time_t vac_time, wind_info *wind) {
  get_wind_info_latlong_UV(degToRad(latitude/1000.0), 
			   degToRad(longitude/1000.0),
			   vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

wind_info *get_wind_info_latlong_millideg_TWSA(double latitude,double longitude,
					     time_t vac_time, wind_info *wind) {
  get_wind_info_latlong_TWSA(degToRad(latitude/1000.0), 
			     degToRad(longitude/1000.0),
			     vac_time, wind);
  wind->angle = radToDeg(wind->angle);
  return wind;
}

