/**
 * $Id: vmg.c,v 1.20 2009-05-06 21:35:47 ylafon Exp $
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
#include "vmg.h"
#include "winds.h"

#define REACH_WP_LIMIT 1.0 /* in nm */

/**
 * get the heading according to the BVMG.
 * The boat structure needs to have its WP filled
 * @param aboat, a pointer to a <code>boat</code> structure
 * @param mode, an int, >0 for 0.1 degree precision, 0 for 1 degree precision
 * @return a double, the heading between 0 and 2*PI in radians
 */
double get_heading_bvmg(boat *aboat, int mode) {
  int imax;
  double anglediv;
  double speed, maxspeed;
  double angle, maxangle, t, t_max, t_max2;
  double wanted_heading;
  double w_speed, w_angle;
  int i;

  if (mode) {
    imax = 900;
    anglediv = 10.0;
  } else {
    imax = 90;
    anglediv = 1.0;
  }

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
  for (i=0; i<imax; i++) {
    angle = wanted_heading + degToRad(((double)i)/anglediv);
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

  for (i=0; i<imax; i++) {
    angle = wanted_heading - degToRad(((double)i)/anglediv);
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
  return angle;
}

/* the algorith used is to maximize the speed vector projection
   on the orthodromic vector */
void set_heading_bvmg(boat *aboat) {
  double angle;

  angle = get_heading_bvmg(aboat, 1);
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
 * get the best angle in close hauled mode (allure de pres)
 * @return a wind angle in radians
 */
double get_best_angle_close_hauled(boat *aboat, double speed, int mode) {
  double t, t_max;
  double maxangle;
  double t_speed, t_angle;
  int i;
  int istart, iend;
  double anglediv, comp;

  if (mode) {
    istart = 0;
    iend   = 900;
    anglediv = 10.0;
#ifdef ROUND_WIND_ANGLE_IN_POLAR
    comp = 0.001;
#else
    comp = 0.0;
#endif /* ROUND_WIND_ANGLE_IN_POLAR */
  } else {
    istart = 0;
    iend = 90;
    anglediv = 1.0;
    comp = 0.0;
  }
  t_max = -100.0;
  maxangle = 0.0;

  for (i=0; i<iend; i++) {
    t_angle =  degToRad(((double)i+comp)/anglediv);
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
double get_best_angle_broad_reach(boat *aboat, double speed, int mode) {
  double t, t_max;
  double maxangle;
  double t_speed, t_angle;
  int i;
  int istart, iend;
  double anglediv, comp;

  if (mode) {
    istart = 1800;
    iend   = 900;
    anglediv = 10.0;
#ifdef ROUND_WIND_ANGLE_IN_POLAR
    comp = 0.444;
#else
    comp = 0.0;
#endif /* ROUND_WIND_ANGLE_IN_POLAR */
  } else {
    istart = 180;
    iend = 90;
    anglediv = 1.0;
    comp = 0.0;
  }

  t_max = -100.0;
  maxangle = M_PI;

  for (i=istart; i>iend; i--) {
    t_angle =  degToRad(((double)i+comp)/anglediv);
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

/**
 * get the heading according to the Phavie's BVMG.
 * The boat structure needs to have its WP filled
 * @param aboat, a pointer to a <code>boat</code> structure
 * @param mode, an int, >0 for 0.1 degree precision, 0 for 1 degree precision
 * @return a double, the heading between 0 and 2*PI in radians
 */
void do_vbvmg(boat *aboat, int mode, 
	      double *heading1, double *heading2,
	      double *wangle1, double *wangle2, 
	      double *time1, double *time2,
	      double *dist1, double *dist2) {
  double alpha, beta;
  double speed, speed_t1, speed_t2, l1, l2, d1, d2;
  double angle, maxangle, t, t1, t2, t_min;
  double wanted_heading;
  double w_speed, w_angle;
  double dist, tanalpha, d1hypotratio;
  double b_alpha, b_beta, b_t1, b_t2, b_l1, b_l2;
  double b1_alpha, b1_beta;
  int i,j, min_i, min_j, max_i, max_j;
  
  b_t1 = b_t2 = b_l1 = b_l2 = b_alpha = b_beta = 0.0;

  dist = ortho_distance(aboat->latitude, aboat->longitude,
			aboat->wp_latitude, aboat->wp_longitude);

  get_wind_info(aboat, &aboat->wind);
  set_heading_ortho_nowind(aboat);

  wanted_heading = aboat->heading;
  maxangle = wanted_heading;
  
  w_speed = aboat->wind.speed;
  w_angle = aboat->wind.angle;
  
  /* first compute the time for the "ortho" heading */
  speed = find_speed(aboat, w_speed, w_angle - wanted_heading);
  t_min = dist / speed;
  
#if DEBUG
  printf("VBVMG Direct road: heading %.2f time %.2f\n", 
	 radToDeg(wanted_heading), t_min);
  printf("VBVMG Direct road: wind angle %.2f\n", 
	 radToDeg(w_angle-wanted_heading));
#endif /* DEBUG */

  angle = w_angle - wanted_heading;
  if (angle < -PI ) {
    angle += TWO_PI;
  } else if (angle > PI) {
    angle -= TWO_PI;
  }
  if (angle < 0.0) {
    min_i = 1;
    min_j = -89;
    max_i = 90;
    max_j = 0;
  } else {
    min_i = -89;
    min_j = 1;
    max_i = 0;
    max_j = 90;
  }

  for (i=min_i; i<max_i; i++) {
    alpha = degToRad((double)i);
    tanalpha = tan(alpha);
    d1hypotratio = hypot(1, tan(alpha));
    for (j=min_j; j<max_j; j++) {
      beta = degToRad((double)j);
      d1 = dist * (tan(-beta) / (tanalpha + tan(-beta)));
      speed_t1 = find_speed(aboat, w_speed, angle-alpha);
      l1 =  d1 * d1hypotratio;
      t1 = l1 / speed_t1;
      if ((t1 < 0.0) || (t1 > t_min)) {
	continue;
      }
      d2 = dist - d1; 
      speed_t2 = find_speed(aboat, w_speed, angle-beta);
      l2 =  d2 * hypot(1, tan(-beta));
      t2 = l2 / speed_t2;
      if (t2 < 0.0) {
	continue;
      }
      t = t1 + t2;
      if (t < t_min) {
	t_min = t;
	b_alpha = alpha;
	b_beta  = beta;
	b_l1 = l1;
	b_l2 = l2;
	b_t1 = t1;
	b_t2 = t2;
      }
    }
  }
#if DEBUG
  printf("VBVMG: alpha=%.2f, beta=%.2f\n", radToDeg(b_alpha), radToDeg(b_beta));
#endif /* DEBUG */
  if (mode) {
    b1_alpha = b_alpha;
    b1_beta = b_beta;
    for (i=-9; i<=9; i++) {
      alpha = b1_alpha + degToRad(((double)i)/10.0);
      tanalpha = tan(alpha);
      d1hypotratio = hypot(1, tan(alpha));
      for (j=-9; j<=9; j++) {
	beta = b1_beta + degToRad(((double)j)/10.0);
	d1 = dist * (tan(-beta) / (tanalpha + tan(-beta)));
	speed_t1 = find_speed(aboat, w_speed, angle-alpha);
	l1 =  d1 * d1hypotratio;
	t1 = l1 / speed_t1;
	if ((t1 < 0.0) || (t1 > t_min)) {
	  continue;
	}
	d2 = dist - d1; 
	speed_t2 = find_speed(aboat, w_speed, angle-beta);
	l2 =  d2 * hypot(1, tan(-beta));
	t2 = l2 / speed_t2;
	if (t2 < 0.0) {
	  continue;
	}
	t = t1 + t2;
	if (t < t_min) {
	  t_min = t;
	  b_alpha = alpha;
	  b_beta  = beta;
	  b_l1 = l1;
	  b_l2 = l2;
	  b_t1 = t1;
	  b_t2 = t2;
	}
      }    
    }
#if DEBUG
    printf("VBVMG: alpha=%.2f, beta=%.2f\n", radToDeg(b_alpha), 
	   radToDeg(b_beta));
#endif /* DEBUG */
  }
  if (fabs(alpha) < fabs(beta)) {
    *heading1 = fmod(wanted_heading + b_alpha, TWO_PI);
    *heading2 = fmod(wanted_heading + b_beta, TWO_PI);
    *time1 = b_t1;
    *time2 = b_t2;
    *dist1 = b_l1;
    *dist2 = b_l2;
  } else {
    *heading2 = fmod(wanted_heading + b_alpha, TWO_PI);
    *heading1 = fmod(wanted_heading + b_beta, TWO_PI);
    *time2 = b_t1;
    *time1 = b_t2;
    *dist2 = b_l1;
    *dist1 = b_l2;
  }
  if (*heading1 < 0 ) {
    *heading1 += TWO_PI;
  }
  if (*heading2 < 0 ) {
    *heading2 += TWO_PI;
  }
    
  *wangle1 = fmod(*heading1 - w_angle, TWO_PI);
  if (*wangle1 > PI ) {
    *wangle1 -= TWO_PI;
  }
  *wangle2 = fmod(*heading2 - w_angle, TWO_PI);
  if (*wangle2 > PI ) {
    *wangle2 -= TWO_PI;
  }
#if DEBUG
  printf("VBVMG: wangle1=%.2f, wangle2=%.2f\n", radToDeg(*wangle1),
	 radToDeg(*wangle2));
  printf("VBVMG: heading1 %.2f, heading2=%.2f\n", radToDeg(*heading1),
	 radToDeg(*heading2));
  printf("VBVMG: dist=%.2f, l1=%.2f, l2=%.2f, ratio=%.2f\n", dist, b_l1, b_l2,
	 (b_l1+b_l2)/dist);
  printf("VBVMG: t1 = %.2f, t2=%.2f, total=%.2f\n", b_t1, b_t2, t_min);
  printf("VBVMG: heading %.2f\n", radToDeg(*heading1));
  printf("VBVMG: wind angle %.2f\n", radToDeg(*wangle1));
#endif /* DEBUG */
}

/**
 * get the heading according to the Phavie's BVMG.
 * The boat structure needs to have its WP filled
 * @param aboat, a pointer to a <code>boat</code> structure
 * @param mode, an int, >0 for 0.1 degree precision, 0 for 1 degree precision
 * @return a double, the heading between 0 and 2*PI in radians
 */
double get_heading_vbvmg(boat *aboat, int mode) {
  double heading, heading2, wangle, wangle2, time1, time2, dist1, dist2;
  do_vbvmg(aboat, mode, &heading, &heading2, 
	   &wangle, &wangle2, &time1, &time2, &dist1, &dist2);
  return heading;
}

/**
 * get the heading according to the Phavie's BVMG.
 * The boat structure needs to have its WP filled
 * @param aboat, a pointer to a <code>boat</code> structure
 * @param mode, an int, >0 for 0.1 degree precision, 0 for 1 degree precision
 * @return a double, the heading between 0 and 2*PI in radians
 */
double get_wind_angle_vbvmg(boat *aboat, int mode) {
  double heading, heading2, wangle, wangle2, time1, time2, dist1, dist2;
  do_vbvmg(aboat, mode, &heading, &heading2, 
	   &wangle, &wangle2, &time1, &time2, &dist1, &dist2);
  return wangle;
}
