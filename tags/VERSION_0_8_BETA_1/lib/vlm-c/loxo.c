/**
 * $Id: loxo.c,v 1.15 2008-12-14 15:02:06 ylafon Exp $
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

#include <math.h>
#include <stdio.h>

#include "defs.h"
#include "types.h"
#include "loxo.h"
#include "polar.h"
#include "lines.h"
#include "winds.h"

/* vac_duration in seconds */
void move_boat_loxo(boat *aboat) {
  double speed;
  double latitude, t_lat;
  double vac_l, d;
  double longitude;
  int vac_duration;
  wind_info *wind;

  vac_duration = aboat->in_race->vac_duration;
  /* compute the heading based on the function used */
  aboat->set_heading_func(aboat);
  wind = &aboat->wind;

  speed = find_speed(aboat, wind->speed, wind->angle - aboat->heading);
    
  vac_l = speed*(vac_duration/3600.0);
    
  d = degToRad(vac_l/60.0);
  latitude = aboat->latitude + d*cos(aboat->heading);
    
  t_lat = (latitude + aboat->latitude) / 2;
  longitude = aboat->longitude + (d*sin(aboat->heading))/cos(t_lat);
    
  d = check_coast(aboat->latitude, aboat->longitude, latitude, longitude,
		  &t_lat, &speed);
#ifdef PARANOID_COAST_CHECK
  if (d>=COAST_INTER_MIN_LIMIT && d <=COAST_INTER_MAX_LIMIT) {
#else
  if (d>=INTER_MIN_LIMIT && d <=INTER_MAX_LIMIT) {
#endif /* PARANOID_COAST_CHECK */
    /* move right before the coast */
    vac_l = vac_l * 0.99 * d;
    d = degToRad(vac_l/60.0);
    latitude = aboat->latitude + d*cos(aboat->heading);
    
    t_lat = (latitude + aboat->latitude) / 2;
    longitude = aboat->longitude + (d*sin(aboat->heading))/cos(t_lat);
    aboat->landed = 1;
  }
  if (longitude > PI) {
    longitude -= TWO_PI;
  } else if (longitude < -PI ) {
    longitude += TWO_PI;
  }
  aboat->longitude = longitude;
  aboat->latitude  = latitude;
  aboat->loch += vac_l;
  aboat->last_vac_time += vac_duration;
}

/* used to estimate where the boat will go based on a specific heading
   returns longitude and latitude (double) */
void estimate_boat_loxo(boat *aboat, int vac_duration, double heading, 
			double *new_latitude, double *new_longitude) {
  double speed;
  double latitude, t_lat;
  double vac_l;
  wind_info *wind;

  /* BE CAREFUL, you must compute the wind before calling this function */
  wind = &aboat->wind;
  speed = find_speed(aboat, wind->speed, wind->angle - heading);

  vac_l = degToRad(speed*vac_duration/(3600.0*60.0));
  latitude = aboat->latitude + vac_l*cos(heading);
    
  t_lat = (latitude + aboat->latitude) / 2;
  *new_longitude = aboat->longitude + (vac_l*sin(heading))/cos(t_lat);
  if (*new_longitude > PI) {
    *new_longitude -= TWO_PI;
  } else if (*new_longitude < -PI ) {
    *new_longitude += TWO_PI;
  }
  *new_latitude  = latitude;
}

/* 
   used to estimate where the boat will go based on a specific heading
   returns longitude and latitude (double) and check for collision
   function returns 0 if the boat landed, 1 otherwise 
*/
int estimate_boat_loxo_coast(boat *aboat, int vac_duration, double heading, 
			     double *new_latitude, double *new_longitude) {
  double speed;
  double latitude, t_lat;
  double vac_l;
  wind_info *wind;

  /* BE CAREFUL, you must compute the wind before calling this function */
  wind = &aboat->wind;
  speed = find_speed(aboat, wind->speed, wind->angle - heading);

  vac_l = degToRad(speed*vac_duration/(3600.0*60.0));
  latitude = aboat->latitude + vac_l*cos(heading);
    
  t_lat = (latitude + aboat->latitude) / 2;
  *new_longitude = aboat->longitude + (vac_l*sin(heading))/cos(t_lat);
  *new_latitude  = latitude;
  /* we reuse old variable to save space */
  vac_l = check_coast(aboat->latitude, aboat->longitude,
		      *new_latitude, *new_longitude,
		      &t_lat, &speed);
#ifdef PARANOID_COAST_CHECK
  return(vac_l>=COAST_INTER_MIN_LIMIT && vac_l<=COAST_INTER_MAX_LIMIT);
#else
  return(vac_l>=INTER_MIN_LIMIT && vac_l <=INTER_MAX_LIMIT);
#endif /* PARANOID_COAST_CHECK */
}

void set_heading_loxo(boat *aboat) {
  get_wind_info(aboat, &aboat->wind);
  /* do nothing, as heading is already set */
}

void set_heading_direct(boat *aboat, double heading) {
  aboat->heading = heading;
}

/* set heading according to wanted heading */
void set_heading_constant(boat *aboat) {
  get_wind_info(aboat, &aboat->wind);
  set_heading_direct(aboat, aboat->wp_heading);
}

/* set heading according to wind angle */
void set_heading_wind_angle(boat *aboat) {
  double angle;

  get_wind_info(aboat, &aboat->wind);
  angle = fmod(aboat->wind.angle + aboat->wp_heading, TWO_PI);
  if (angle < 0) {
    angle += TWO_PI;
  }
  set_heading_direct(aboat, angle);
}
 
/**
 * compute coordinate from one point with one angle and distance 
 * @param latitude, the latitude or the starting point, in radians
 * @param longitude, the longitude of the starting point, in radians
 * @param distance, the distance in nautic miles
 * @param angle, the followed heading, in radians
 * @param target_lat, a pointer to the latitude at the arrival, in radians
 * @param target_long, a pointer to the longitude at the arrival, in radians
  */
void get_loxo_coord_from_dist_angle(double latitude, double longitude,
				    double distance, double angle,
				    double *target_lat, double *target_long) {
  double ld, la;
  *target_lat = latitude + degToRad( (cos(angle)*distance)/60.0 );
  if (fabs(*target_lat - latitude) > degToRad(0.001)) {
    ld = log(tan(M_PI_4 + (latitude/2.0)));
    la = log(tan(M_PI_4 + (*target_lat/2.0)));
    *target_long = longitude + (la-ld)*tan(angle);
  } else {
    *target_long = longitude+sin(angle)*degToRad(distance/(60.0*cos(latitude)));
  }
}

/**
 * compute coordinate from one point with one angle and distance 
 * @param latitude, the latitude or the starting point, in radians
 * @param longitude, the longitude of the starting point, in radians
 * @param target_lat, the latitude or the end point, in radians
 * @param target_long, the longitude of the end point, in radians
 * @param distance, the distance in nautic miles
 * @param angle, the followed heading, in radians
  */
void loxo_distance_angle(double latitude, double longitude, 
			 double target_lat, double target_long,
			 double *distance, double *angle) {
  double ld, la;
  double l, g, rfq;

  longitude = fmod(longitude+TWO_PI, TWO_PI);
  target_long = fmod(target_long+TWO_PI, TWO_PI);

  if (fabs(target_lat-latitude) < 0.000001) {
    /* clamp to horizontal */
    if (fabs(target_long-longitude) < M_PI) {
      *angle = ((target_long-longitude)>0) ? M_PI_2 : -M_PI_2;
      *distance = fabs(60.0 * cos((latitude+target_lat)/2) 
		       * radToDeg(target_long - longitude));
    } else {
      *angle = ((target_long-longitude)>0) ? -M_PI_2 : M_PI_2;
      *distance = fabs(60.0 * cos((latitude+target_lat)/2) 
		       * (360.0 - radToDeg(target_long - longitude)));
    }
    return;
  }
  if (fabs(target_long-longitude) < 0.000001) {
    /* clamp to vertical */
    *distance = fabs(60.0 * radToDeg(target_lat-latitude));
    *angle = ((target_lat-latitude) > 0) ? 0 : M_PI;
    return;
  }
  ld = log(tan(M_PI_4 + (latitude/2.0)));
  la = log(tan(M_PI_4 + (target_lat/2.0)));
  l = target_lat - latitude;
  g = target_long - longitude;
  rfq = atan(fabs(g/(la-ld)));
  if (l>0.0) {
    if (g > 0.0) {
      *angle = rfq;
    } else {
      *angle = TWO_PI - rfq;
    }
  } else {
    if (g > 0.0) {
      *angle = PI - rfq;
    } else {
      *angle = PI + rfq;
    }
  }
  if (degToRad(rfq) > 89.0) {
    *distance = (60*fabs(radToDeg(g))*cos((latitude+target_lat)/2)) / sin(rfq);
  } else {
    *distance = (60*fabs(radToDeg(l))) / cos(rfq);
  }
}
