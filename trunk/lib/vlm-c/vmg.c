/**
 * $Id: vmg.c,v 1.6 2009-05-02 16:56:44 ylafon Exp $
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
#include "ortho.h"
#include "winds.h"

#define REACH_WP_LIMIT 1.0 /* in nm */

/* the algorith used is to maximize the speed vector projection
   on the orthodromic vector */
void set_heading_bvmg(boat *aboat) {
  double speed, maxspeed;
  double angle, maxangle, t, t_max, t_max2;
  double wanted_heading;
  double w_speed, w_angle;
  int i;
  
  get_wind_info(aboat, &aboat->wind);
  set_heading_ortho_nowind(aboat);

  wanted_heading = aboat->heading;
  maxangle = wanted_heading;
    
  w_speed = aboat->wind.speed;
  w_angle = aboat->wind.angle;

  /* FIXME, this can be optimized a lot */
  maxspeed = -100.0;
  t_max = -100;
  t_max2 = -100;

  /* -90 to +90 form desired diretion */
  for (i=0; i<900; i++) {
    angle = wanted_heading + degToRad(((double)i)/10.0);
    speed = find_speed(aboat, w_speed, w_angle - angle);
    t = speed * cos(wanted_heading - angle);
    if (t > t_max) {
      t_max = t;
      maxangle = angle;
      maxspeed = speed;
    } else if ( t_max - t > (t_max/20.0)) { 
      break;  /* cut if lower enough from current maximum */
    }
  }

  for (i=0; i<900; i++) {
    angle = wanted_heading - degToRad(((double)i)/10.0);
    speed = find_speed(aboat, w_speed, w_angle - angle);
    t = speed * cos(wanted_heading - angle);
    if (t > t_max2) {
      t_max2 = t;
      if (t > t_max) {
	maxangle = angle;
	maxspeed = speed;
	t_max = t;
      }
    } else if (t_max2 - t > (t_max2/20.0)) {
      break;
    }
  }
  /* fixme save speed, and t_max (= bvmg) somewhere ? */
  angle = fmod(maxangle, TWO_PI);
  if (angle < 0) {
    angle += TWO_PI;
  }
  set_heading_direct(aboat, angle);
}


/* the algorith used is to minimize the distance to the WP */
void set_heading_bvmg2(boat *aboat) {
  double angle, maxangle, t, t_min;
  double wanted_heading;
  double w_speed, w_angle;
  double t_long, t_lat;
  int i;
  
  get_wind_info(aboat, &aboat->wind);
  set_heading_ortho_nowind(aboat);

  wanted_heading = aboat->heading;
  maxangle = wanted_heading;

  w_speed = aboat->wind.speed;
  w_angle = aboat->wind.angle; 

  /* FIXME, this can be optimized a lot */
  t_min = aboat->wp_distance;

  /* -90 to +90 form desired diretion */
  for (i=0; i<900; i++) {
    angle = wanted_heading + degToRad(((double)i)/10.0);
    estimate_boat_loxo(aboat, aboat->in_race->vac_duration, 
		       angle, &t_lat, &t_long);
    t = ortho_distance(t_lat, t_long, 
		       aboat->wp_latitude, aboat->wp_longitude);
    if (t < t_min) {
      t_min = t;
      maxangle = angle;
    }
  }
    
  for (i=0; i<900; i++) {
    angle = wanted_heading - degToRad(((double)i)/10.0);
    aboat->heading = angle;
    estimate_boat_loxo(aboat, 600, angle, &t_lat, &t_long);
    t = ortho_distance(t_lat, t_long, 
		       aboat->wp_latitude, aboat->wp_longitude);
    if (t < t_min) {
      t_min = t;
      maxangle = angle;
    }
  }
  /* fixme save speed, and t_max (= bvmg) somewhere ? */
  angle = fmod(maxangle, TWO_PI);
  if (angle < 0) {
    angle += TWO_PI;
  }
  set_heading_direct(aboat, angle);
}

/* the algorith used is to minimize the distance to the WP */
void set_heading_bvmg2_coast(boat *aboat) {
  double angle, maxangle, t, t_min;
  double wanted_heading;
  double w_speed, w_angle;
  double t_long, t_lat;
  int i;

  get_wind_info(aboat, &aboat->wind);
  set_heading_ortho_nowind(aboat);

  wanted_heading = aboat->heading;
  maxangle = wanted_heading;

  w_speed = aboat->wind.speed;
  w_angle = aboat->wind.angle; 

  /* FIXME, this can be optimized a lot */
  t_min = aboat->wp_distance;

  /* -90 to +90 form desired diretion */
  for (i=0; i<900; i++) {
    angle = wanted_heading + degToRad(((double)i)/10.0);
    if (estimate_boat_loxo_coast(aboat, 600, angle, &t_lat, &t_long)) {
      t = ortho_distance(t_lat, t_long, 
			 aboat->wp_latitude, aboat->wp_longitude);
      if (t < t_min) {
	t_min = t;
	maxangle = angle;
      }
    }
  }
    
  for (i=0; i<900; i++) {
    angle = wanted_heading - degToRad(((double)i)/10.0);
    aboat->heading = angle;
    if (estimate_boat_loxo_coast(aboat, 600, angle, &t_lat, &t_long)) {
      t = ortho_distance(t_lat, t_long,
			 aboat->wp_latitude, aboat->wp_longitude);
      if (t < t_min) {
	t_min = t;
	maxangle = angle;
      }
    }
  }
  /* fixme save speed, and t_max (= bvmg) somewhere ? */
  angle = fmod(maxangle, TWO_PI);
  if (angle < 0) {
    angle += TWO_PI;
  }
  set_heading_direct(aboat, angle);
}

/**
 * the algorithm here is just to select between BVMG and ortho
 * based on the total time to destination
 */
void automatic_selection_heading(boat *aboat) {
  boat boatcopy;
  int orthovacs, bvmgvacs;
  double orthodist, bvmgdist;

  bvmgdist = 999999999.0;
  boatcopy = *aboat;
  boatcopy.set_heading_func=&set_heading_bvmg2_coast;
  bvmgvacs = 0;
  orthodist = 999999999.0; /* to cope with one weird case that should never 
			      happen */
  while (!boatcopy.landed && (bvmgdist = ortho_distance(boatcopy.latitude, 
							boatcopy.longitude, 
							boatcopy.wp_latitude, 
							boatcopy.wp_longitude)) > REACH_WP_LIMIT ) {
    move_boat_loxo(&boatcopy);
    bvmgvacs++;
  }
  if (boatcopy.landed) {
    bvmgvacs = 9999999;
  }
  boatcopy = *aboat;
  boatcopy.set_heading_func=&set_heading_ortho;
  orthovacs = 0;
  while (!boatcopy.landed && (orthovacs <= bvmgvacs) && 
	 (orthodist = ortho_distance(boatcopy.latitude, boatcopy.longitude, 
				     boatcopy.wp_latitude, 
				     boatcopy.wp_longitude)) > REACH_WP_LIMIT ) {
    move_boat_loxo(&boatcopy);
    orthovacs++;
  }
  if (boatcopy.landed) {
    orthovacs = 9999999;
  }
  printf("Ortho: %d vacs, %.2f final distance\n", orthovacs, orthodist);
  printf("BVMG: %d vacs, %.2f final distance\n", bvmgvacs, bvmgdist);
  if (orthovacs > bvmgvacs) {
    printf("Heading set to BVMG\n");
    set_heading_bvmg2_coast(aboat);
    return;
  }
  if (orthovacs < bvmgvacs) {
    printf("Heading set to Ortho\n");
    set_heading_ortho(aboat);
    return;
  }
  if (orthodist > bvmgdist) {
    printf("Heading set to BVMG\n");
    set_heading_bvmg2_coast(aboat);
    return;
  }
  printf("Heading set to ortho\n");
  set_heading_ortho(aboat);
}
  
/**
 * get the best angle in close hauled mode (allure de pres)
 * @return a wind angle in radians
 */
double get_best_angle_close_hauled(boat *aboat, double speed) {
  double t, t_max;
  double maxangle;
  double t_speed, t_angle;
  int i;
  
  t_max = -100.0;
  maxangle = 0.0;

  for (i=0; i<900; i++) {
    t_angle =  degToRad(((double)i)/10.0);
    t_speed = find_speed(aboat, speed, t_angle);
    t = t_speed * cos(t_angle);
    if (t > t_max) {
      t_max = t;
      maxangle = t_angle;
    } else if ( t_max - t > (t_max/20.0)) { 
      break;  /* cut if lower enough from current maximum */
    }
  }
  return maxangle;
}

/**
 * get the best angle in close hauled mode (grand largue)
 * @return a wind angle in radians
 */
double get_best_angle_broad_reach(boat *aboat, double speed) {
  double t, t_max;
  double maxangle;
  double t_speed, t_angle;
  int i;
  
  t_max = -100.0;
  maxangle = M_PI;

  for (i=1800; i>900; i--) {
    t_angle =  degToRad(((double)i)/10.0);
    t_speed = find_speed(aboat, speed, t_angle);
    t = t_speed * cos(M_PI - t_angle);
    if (t > t_max) {
      t_max = t;
      maxangle = t_angle;
    } else if ( t_max - t > (t_max/20.0)) { 
      break;  /* cut if lower enough from current maximum */
    }
  }
  return maxangle;
}
