/**
 * $Id: winds.c,v 1.24 2009-05-12 22:10:47 ylafon Exp $
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
#ifndef OLD_C_COMPILER
#include <complex.h>
#endif /* OLD_C_COMPILER */
#include <stdio.h>
#include <time.h>
#include <sys/time.h>
#include <stdlib.h>

#include "defs.h"
#include "types.h"
#include "winds.h"

extern vlmc_context *global_vlmc_context;

void get_wind_info(boat *aboat, wind_info *wind) {
  time_t vac_time;
  
  vac_time = aboat->last_vac_time + aboat->in_race->vac_duration;

  get_wind_info_latlong(aboat->latitude, aboat->longitude, vac_time, wind);
}

wind_info *get_wind_info_latlong(double latitude, double longitude,
				 time_t vac_time, wind_info *wind) {
#ifdef DEFAULT_INTERPOLATION_UV
  return get_wind_info_latlong_UV(latitude, longitude, vac_time, wind);
#else
  return get_wind_info_latlong_TWSA(latitude, longitude, vac_time, wind);
#endif /* DEFAULT_INTERPOLATION_UV */
}

wind_info *get_wind_info_latlong_now(double latitude, double longitude,
				     wind_info *wind) {
  time_t vac_time;
  
  time(&vac_time);
  return get_wind_info_latlong(latitude, longitude, vac_time, wind);
}

wind_info *get_wind_info_latlong_UV(double latitude, double longitude, 
				    time_t vac_time, wind_info *wind) {
  winds *prev, *next;
  int i, t_long, t_lat;
  double u0prev, u0next, v0prev, v0next;
  double u1prev, u1next, v1prev, v1next;
  double u2prev, u2next, v2prev, v2next;
  double u3prev, u3next, v3prev, v3next;
  double u01next, u23next, u01prev, u23prev;
  double v01next, v23next, v01prev, v23prev;
  double uprev, unext, vprev, vnext;
  double u,v;
  double d_long, d_lat, t_ratio, angle;
#ifdef OLD_C_COMPILER
  double t_speed;
#else
  double complex c;
#endif /* OLD_C_COMPILER */
  winds_prev *windtable;
#ifdef DEBUG
  char buff[64];
#endif /* DEBUG */
  
  windtable = &global_vlmc_context->windtable;
  /* if the windtable is not there, return NULL */
  if (windtable->wind == NULL) {
    wind->speed = 0.0;
    wind->angle = 0.0;
    return NULL;
  }

  d_long = radToDeg(longitude); /* is there a +180 drift? see grib */
  if (d_long < 0) {
    d_long += 360;
  } else if (d_long >= 360) {
    d_long -= 360;
  }
  d_lat = radToDeg(latitude) + 90; /* is there a +90 drift? see grib*/
    
  prev = next = NULL;

  /* correct the grib time, currently variable in VLM */
  vac_time -= windtable->time_offset;

  for (i=0; i< windtable->nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > vac_time) {
      if (i) {
	next = windtable->wind[i];
	prev = windtable->wind[i-1];
      } else {
	prev = windtable->wind[i];
      }
      break;
    }
  }
  /* none found the two are the last ones */
  if (!next && !prev) {
    prev = windtable->wind[windtable->nb_prevs-1];
  } 
#ifdef GRIB_RESOLUTION_0_5
  d_long = d_long*2.0;
  d_lat = d_lat*2.0;
#endif /* GRID_RESOLUTION_0_5 */
  t_long = (int)floor(d_long);
  t_lat = (int)floor(d_lat);

  u0prev = prev->wind_u[t_long][t_lat];
  v0prev = prev->wind_v[t_long][t_lat];
  u1prev = prev->wind_u[t_long][t_lat+1];
  v1prev = prev->wind_v[t_long][t_lat+1];
  u2prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
  v2prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];
  u3prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
  v3prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];    

#ifdef DEBUG
  printf("u0prev: U=%.2f m/s, V=%.2f m/s\n", u0prev, v0prev);
  printf("u1prev: U=%.2f m/s, V=%.2f m/s\n", u1prev, v1prev);
  printf("u2prev: U=%.2f m/s, V=%.2f m/s\n", u2prev, v2prev);
  printf("u3prev: U=%.2f m/s, V=%.2f m/s\n", u3prev, v3prev);
#endif /* DEBUG */  

  /*
    simple bilinear interpolation, we might factor the cos(lat) in
    the computation to tackle the shape of the pseudo square
    
    Doing interpolation on angle/speed might be better 
  */
  u01prev = u0prev + (u1prev - u0prev) * (d_lat - floor(d_lat));
  v01prev = v0prev + (v1prev - v0prev) * (d_lat - floor(d_lat));
  u23prev = u2prev + (u3prev - u2prev) * (d_lat - floor(d_lat));
  v23prev = v2prev + (v3prev - v2prev) * (d_lat - floor(d_lat));

  uprev = u01prev + (u23prev - u01prev) * (d_long - floor(d_long));
  vprev = v01prev + (v23prev - v01prev) * (d_long - floor(d_long));

#ifdef DEBUG
  printf("-> u01prev: U=%.2f m/s, V=%.2f m/s\n", u01prev, v01prev);
  printf("-> u23prev: U=%.2f m/s, V=%.2f m/s\n", u23prev, v23prev);
  printf("=>   uprev: U=%.2f m/s, V=%.2f m/s\n", uprev, vprev);
#endif /* DEBUG */  

  if (next) {
    u0next = next->wind_u[t_long][t_lat];
    v0next = next->wind_v[t_long][t_lat];      
    u1next = next->wind_u[t_long][t_lat+1];
    v1next = next->wind_v[t_long][t_lat+1];
    u2next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
    v2next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];
    u3next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
    v3next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];
      
#ifdef DEBUG
    printf("u0next: U=%.2f m/s, V=%.2f m/s\n", u0next, v0next);
    printf("u1next: U=%.2f m/s, V=%.2f m/s\n", u1next, v1next);
    printf("u2next: U=%.2f m/s, V=%.2f m/s\n", u2next, v2next);
    printf("u3next: U=%.2f m/s, V=%.2f m/s\n", u3next, v3next);
#endif /* DEBUG */  

    /* simple bilinear interpolation, we might factor the cos(lat) in
       the computation to tackle the shape of the pseudo square */
      
    u01next = u0next + (u1next - u0next) * (d_lat - floor(d_lat));
    v01next = v0next + (v1next - v0next) * (d_lat - floor(d_lat));
    u23next = u2next + (u3next - u2next) * (d_lat - floor(d_lat));
    v23next = v2next + (v3next - v2next) * (d_lat - floor(d_lat));
      
    unext = u01next + (u23next - u01next) * (d_long - floor(d_long));
    vnext = v01next + (v23next - v01next) * (d_long - floor(d_long));
      
#ifdef DEBUG
    printf("-> u01next: U=%.2f m/s, V=%.2f m/s\n", u01next, v01next);
    printf("-> u23next: U=%.2f m/s, V=%.2f m/s\n", u23next, v23next);
    printf("=>   unext: U=%.2f m/s, V=%.2f m/s\n", unext, vnext);
#endif /* DEBUG */  

#ifdef DEBUG
    printf("vac_time %ld, prev prevision time %ld, next prevision time %ld\n", vac_time,
	   prev->prevision_time, next->prevision_time);
    ctime_r(&vac_time, buff);
    printf("vac_time %s", buff);
    ctime_r(&prev->prevision_time, buff);
    printf(" prev_time %s", buff);
    ctime_r(&next->prevision_time, buff);
    printf(" next_time %s\n", buff);
    printf("diff num %ld, diff_denom %ld\n", vac_time - prev->prevision_time,
	   next->prevision_time - prev->prevision_time);
#endif /* DEBUG */
    t_ratio = ((double)(vac_time - prev->prevision_time)) / 
      ((double)(next->prevision_time - prev->prevision_time));
      
    u = uprev + (unext - uprev) * t_ratio;
    v = vprev + (vnext - vprev) * t_ratio;
    /* check this from grib...
       WE -> NS V (west from east, north from south) and in m/s
       so U/V +/+ -> 270 to 360
       +/- -> 180 to 270
       -/- -> 90 to 180
       -/+ -> 0 to 90
       going clockwise.
       1m/s -> 1.9438445 kts
    */
#ifdef OLD_C_COMPILER
    t_speed = sqrt(u*u+v*v);
    angle = acos(-v / t_speed);
    if (u > 0.0) {
      angle = TWO_PI - angle;
    }
#else
    c = - v - _Complex_I * u;
#endif /* OLD_C_COMPILER */
#ifdef DEBUG
    printf("time stamps: prev %ld, boat_time %ld", prev->prevision_time,
	   vac_time);
    printf(", next %ld, time ratio %.3f\n", next->prevision_time, t_ratio);
#endif /* DEBUG */
  } else {
#ifdef OLD_C_COMPILER
    t_speed = sqrt(uprev*uprev+vprev*vprev);
    angle = acos(-vprev / t_speed);
    if (uprev > 0.0) {
      angle = TWO_PI - angle;
    }
#else
    c = - vprev - _Complex_I * uprev;
#endif /* OLD_C_COMPILER */
  }
#ifndef OLD_C_COMPILER
  angle = carg(c);
  if (angle < 0) {
    angle += TWO_PI;
  }
#endif /* !OLD_C_COMPILER */
#ifdef DEBUG
  printf("U component %.3f, V component %.3f, speed %.3f, angle %.3f\n",
	 -cimag(c), -creal(c), msToKts(cabs(c)), radToDeg(angle));
#endif /* DEBUG */
#ifdef OLD_C_COMPILER
  wind->speed = msToKts(t_speed);
#else
  wind->speed = msToKts(cabs(c));
#endif /* OLD_C_COMPILER */
  wind->angle = angle;
  return wind;
}

/* same as above, but with interpolation using True Wind Speed and Angle */
wind_info *get_wind_info_latlong_TWSA(double latitude, double longitude,
				      time_t vac_time, wind_info *wind) {
  winds *prev, *next;
  int i, t_long, t_lat;
  double u0prev, u0next, v0prev, v0next;
  double u1prev, u1next, v1prev, v1next;
  double u2prev, u2next, v2prev, v2next;
  double u3prev, u3next, v3prev, v3next;
  double u01next, u23next, u01prev, u23prev;
  double v01next, v23next, v01prev, v23prev;
  double uprev, unext, vprev, vnext;
  double u,v;
  double d_long, d_lat, t_ratio, angle;
#ifdef OLD_C_COMPILER
  double t_speed;
#else
  double complex c;
#endif /* OLD_C_COMPILER */
  winds_prev *windtable;
#ifdef DEBUG
  char buff[64];
#endif /* DEBUG */

  windtable = &global_vlmc_context->windtable;
  /* if the windtable is not there, return NULL */
  if (windtable->wind == NULL) {
    wind->speed = 0.0;
    wind->angle = 0.0;
    return NULL;
  }

  d_long = radToDeg(longitude); /* is there a +180 drift? see grib */
  if (d_long < 0) {
    d_long += 360;
  } else if (d_long >= 360) {
    d_long -= 360;
  }
  d_lat = radToDeg(latitude) + 90; /* is there a +90 drift? see grib*/
    
  prev = next = NULL;

  /* correct the grib time, currently variable in VLM */
  vac_time -= windtable->time_offset;

  for (i=0; i< windtable->nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > vac_time) {
      if (i) {
	next = windtable->wind[i];
	prev = windtable->wind[i-1];
      } else {
	prev = windtable->wind[i];
      }
      break;
    }
  }
  /* none found the two are the last ones */
  if (!next && !prev) {
    prev = windtable->wind[windtable->nb_prevs-1];
  } 
#ifdef GRIB_RESOLUTION_0_5
  d_long = d_long*2.0;
  d_lat = d_lat*2.0;
#endif /* GRID_RESOLUTION_0_5 */
  t_long = (int)floor(d_long);
  t_lat = (int)floor(d_lat);

  u0prev = prev->wind_u[t_long][t_lat];
  v0prev = prev->wind_v[t_long][t_lat];
  u1prev = prev->wind_u[t_long][t_lat+1];
  v1prev = prev->wind_v[t_long][t_lat+1];
  u2prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
  v2prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];
  u3prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
  v3prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];    

  /* we reuse u = speed v = angle after conversion */
#ifdef OLD_C_COMPILER
# define _transform_u_v(a, b)                   \
  t_speed = sqrt(a*a+b*b);                      \
  b = acos(-b/t_speed);                         \
  if (a > 0.0) {                                \
    b = TWO_PI - b;                             \
  }                                             \
  a = msToKts(t_speed);                         
#else
#  define _transform_u_v(a, b)			\
  c = -b - _Complex_I * a;			\
  a = msToKts(cabs(c));				\
  b = carg(c);					\
  if (b < 0) {					\
    b += TWO_PI;				\
  }                       
#endif /* OLD_C_COMPILER */

  _transform_u_v(u0prev, v0prev);
  _transform_u_v(u1prev, v1prev);
  _transform_u_v(u2prev, v2prev);
  _transform_u_v(u3prev, v3prev);
  
#ifdef DEBUG
  printf("u0prev: %.2f kts, %.2f deg\n", u0prev, radToDeg(v0prev));
  printf("u1prev: %.2f kts, %.2f deg\n", u1prev, radToDeg(v1prev));
  printf("u2prev: %.2f kts, %.2f deg\n", u2prev, radToDeg(v2prev));
  printf("u3prev: %.2f kts, %.2f deg\n", u3prev, radToDeg(v3prev));
#endif /* DEBUG */  
  /* speed interpolation */
  u01prev = u0prev + (u1prev - u0prev) * (d_lat - floor(d_lat));
  u23prev = u2prev + (u3prev - u2prev) * (d_lat - floor(d_lat));
  uprev = u01prev + (u23prev - u01prev) * (d_long - floor(d_long));

#define _check_angle_interp(a)			\
  if (a > PI) {					\
    a -= TWO_PI;				\
  } else if (a < -PI) {				\
    a += TWO_PI;				\
  }
  
#define _positive_angle(a)			\
  if (a < 0) {					\
    a += TWO_PI;				\
  } else if (a >= TWO_PI) {			\
    a -= TWO_PI;				\
  }

  angle = (v1prev - v0prev);
  _check_angle_interp(angle);
  v01prev = v0prev + (angle) * (d_lat - floor(d_lat));
  _positive_angle(v01prev);
  
  angle =  (v3prev - v2prev);
  _check_angle_interp(angle);
  v23prev = v2prev + (angle) * (d_lat - floor(d_lat));
  _positive_angle(v23prev);

  angle = (v23prev - v01prev);
  _check_angle_interp(angle);
  vprev = v01prev + (angle) * (d_long - floor(d_long));
  _positive_angle(vprev);

#ifdef DEBUG
  printf("-> u01prev: %.2f kts, %.2f deg\n", u01prev, radToDeg(v01prev));
  printf("-> u23prev: %.2f kts, %.2f deg\n", u23prev, radToDeg(v23prev));
  printf("=>   uprev: %.2f kts, %.2f deg\n", uprev, radToDeg(vprev));
#endif /* DEBUG */  

  if (next) {
    u0next = next->wind_u[t_long][t_lat];
    v0next = next->wind_v[t_long][t_lat];      
    u1next = next->wind_u[t_long][t_lat+1];
    v1next = next->wind_v[t_long][t_lat+1];
    u2next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
    v2next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];      
    u3next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
    v3next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];
      
    _transform_u_v(u0next, v0next);
    _transform_u_v(u1next, v1next);
    _transform_u_v(u2next, v2next);
    _transform_u_v(u3next, v3next);

#ifdef DEBUG
    printf("u0next: %.2f kts, %.2f deg\n", u0next, radToDeg(v0next));
    printf("u1next: %.2f kts, %.2f deg\n", u1next, radToDeg(v1next));
    printf("u2next: %.2f kts, %.2f deg\n", u2next, radToDeg(v2next));
    printf("u3next: %.2f kts, %.2f deg\n", u3next, radToDeg(v3next));
#endif /* DEBUG */  

    /* simple bilinear interpolation, we might factor the cos(lat) in
       the computation to tackle the shape of the pseudo square */
      
    u01next = u0next + (u1next - u0next) * (d_lat - floor(d_lat));
    u23next = u2next + (u3next - u2next) * (d_lat - floor(d_lat));
    unext = u01next + (u23next - u01next) * (d_long - floor(d_long));

    angle = (v1next - v0next);
    _check_angle_interp(angle);
    v01next = v0next + (angle) * (d_lat - floor(d_lat));
    _positive_angle(v01next);
    
    angle =  (v3next - v2next);
    _check_angle_interp(angle);
    v23next = v2next + (angle) * (d_lat - floor(d_lat));
    _positive_angle(v23next);
    
    angle = (v23next - v01next);
    _check_angle_interp(angle);
    vnext = v01next + (angle) * (d_long - floor(d_long));
    _positive_angle(vnext);
    
#ifdef DEBUG
    printf("-> u01next: %.2f kts, %.2f deg\n", u01next, radToDeg(v01next));
    printf("-> u23next: %.2f kts, %.2f deg\n", u23next, radToDeg(v23next));
    printf("=>   unext: %.2f kts, %.2f deg\n", unext, radToDeg(vnext));
#endif /* DEBUG */  
   
#ifdef DEBUG
    printf("vac_time %ld, prev prevision time %ld, next prevision time %ld\n", vac_time,
	   prev->prevision_time, next->prevision_time);
    ctime_r(&vac_time, buff);
    printf("vac_time %s", buff);
    ctime_r(&prev->prevision_time, buff);
    printf(" prev_time %s", buff);
    ctime_r(&next->prevision_time, buff);
    printf(" next_time %s\n", buff);
    printf("diff num %ld, diff_denom %ld\n", vac_time - prev->prevision_time,
	   next->prevision_time - prev->prevision_time);
#endif /* DEBUG */
    t_ratio = ((double)(vac_time - prev->prevision_time)) / 
      ((double)(next->prevision_time - prev->prevision_time));
      
    u = uprev + (unext - uprev) * t_ratio;
    angle = (vnext - vprev);
    _check_angle_interp(angle);
    v = vprev + (angle) * t_ratio;
    _positive_angle(v);
  } else {
    u = uprev;
    v = vprev;
  }
#ifdef DEBUG
  printf("Speed %.3f, angle %.3f\n", u, radToDeg(v));
#endif /* DEBUG */
  wind->speed = u;
  wind->angle = v;
  return wind;
}

/* same as above, but with interpolation using True Wind Speed and Angle 
 In selective mode (trying to avoid diverging rotations) */
wind_info *get_wind_info_latlong_selective_TWSA(double latitude, 
						double longitude,
						time_t vac_time, 
						wind_info *wind) {
  winds *prev, *next;
  int i, t_long, t_lat;
  double u0prev, u0next, v0prev, v0next;
  double u1prev, u1next, v1prev, v1next;
  double u2prev, u2next, v2prev, v2next;
  double u3prev, u3next, v3prev, v3next;
  double u01next, u23next, u01prev, u23prev;
  double v01next, v23next, v01prev, v23prev;
  double uprev, unext, vprev, vnext;
  double u,v;
  double d_long, d_lat, t_ratio, angle;
  int rot_step1a, rot_step1b, rot_step2a, rot_step2b;
#ifdef OLD_C_COMPILER
  double t_speed;
#else
  double complex c, c01, c23;
#endif /* OLD_C_COMPILER */
  winds_prev *windtable;
#ifdef DEBUG
  char buff[64];
#endif /* DEBUG */

  windtable = &global_vlmc_context->windtable;
  /* if the windtable is not there, return NULL */
  if (windtable->wind == NULL) {
    wind->speed = 0.0;
    wind->angle = 0.0;
    return NULL;
  }

  d_long = radToDeg(longitude); /* is there a +180 drift? see grib */
  if (d_long < 0) {
    d_long += 360;
  } else if (d_long >= 360) {
    d_long -= 360;
  }
  d_lat = radToDeg(latitude) + 90; /* is there a +90 drift? see grib*/
    
  prev = next = NULL;

  /* correct the grib time, currently variable in VLM */
  vac_time -= windtable->time_offset;

  for (i=0; i< windtable->nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > vac_time) {
      if (i) {
	next = windtable->wind[i];
	prev = windtable->wind[i-1];
      } else {
	prev = windtable->wind[i];
      }
      break;
    }
  }
  /* none found the two are the last ones */
  if (!next && !prev) {
    prev = windtable->wind[windtable->nb_prevs-1];
  } 
#ifdef GRIB_RESOLUTION_0_5
  d_long = d_long*2.0;
  d_lat = d_lat*2.0;
#endif /* GRID_RESOLUTION_0_5 */
  t_long = (int)floor(d_long);
  t_lat = (int)floor(d_lat);

  u0prev = prev->wind_u[t_long][t_lat];
  v0prev = prev->wind_v[t_long][t_lat];
  u1prev = prev->wind_u[t_long][t_lat+1];
  v1prev = prev->wind_v[t_long][t_lat+1];
  u2prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
  v2prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];
  u3prev = prev->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
  v3prev = prev->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];    

  _transform_u_v(u0prev, v0prev);
  _transform_u_v(u1prev, v1prev);
  _transform_u_v(u2prev, v2prev);
  _transform_u_v(u3prev, v3prev);
  
#ifdef DEBUG
  printf("u0prev: %.2f kts, %.2f deg\n", u0prev, radToDeg(v0prev));
  printf("u1prev: %.2f kts, %.2f deg\n", u1prev, radToDeg(v1prev));
  printf("u2prev: %.2f kts, %.2f deg\n", u2prev, radToDeg(v2prev));
  printf("u3prev: %.2f kts, %.2f deg\n", u3prev, radToDeg(v3prev));
#endif /* DEBUG */  
  /* speed interpolation */
  u01prev = u0prev + (u1prev - u0prev) * (d_lat - floor(d_lat));
  u23prev = u2prev + (u3prev - u2prev) * (d_lat - floor(d_lat));

  angle = (v1prev - v0prev);
  _check_angle_interp(angle);
  rot_step1a = (angle > 0.0);
  v01prev = v0prev + (angle) * (d_lat - floor(d_lat));
  _positive_angle(v01prev);
  
  angle =  (v3prev - v2prev);
  _check_angle_interp(angle);
  rot_step1b = (angle > 0.0);
  v23prev = v2prev + (angle) * (d_lat - floor(d_lat));
  _positive_angle(v23prev);

#ifdef OLD_C_COMPILER
# define _transform_back_u_v(a,b)		\
  t_speed = a * sin(b);				\
  a = a * cos(b);				\
  b = t_speed;
#else
# define _transform_back_u_v(a,b,c)		\
  c = a * cos(b) + _Complex_I * a * sin(b)
#endif /* OLD_C_COMPILER */

  if (rot_step1a == rot_step1b) {
    uprev = u01prev + (u23prev - u01prev) * (d_long - floor(d_long));

    angle = (v23prev - v01prev);
    _check_angle_interp(angle);
    rot_step2a = (angle > 0.0);
    vprev = v01prev + (angle) * (d_long - floor(d_long));
    _positive_angle(vprev);
  } else {
    rot_step2a = -1;
    /* rotations are in contrary motion, let's use UV for this */
#ifdef OLD_C_COMPILER
    _transform_back_u_v(u01prev, v01prev);
    _transform_back_u_v(u23prev, v23prev);
    uprev = u01prev + (u23prev - u01prev) * (d_long - floor(d_long));
    vprev = v01prev + (v23prev - v01prev) * (d_long - floor(d_long));
    _transform_u_v(uprev, vprev);
#else
    _transform_back_u_v(u01prev, v01prev, c01);
    _transform_back_u_v(u23prev, v23prev, c23);
    c = c01 + (c23 - c01) * (d_long - floor(d_long));
    uprev = cabs(c);
    vprev = carg(c);
#endif /* OLD_C_COMPILER */
  }

#ifdef DEBUG
  printf("-> u01prev: %.2f kts, %.2f deg\n", u01prev, radToDeg(v01prev));
  printf("-> u23prev: %.2f kts, %.2f deg\n", u23prev, radToDeg(v23prev));
  printf("=>   uprev: %.2f kts, %.2f deg\n", uprev, radToDeg(vprev));
#endif /* DEBUG */  

  if (next) {
    u0next = next->wind_u[t_long][t_lat];
    v0next = next->wind_v[t_long][t_lat];      
    u1next = next->wind_u[t_long][t_lat+1];
    v1next = next->wind_v[t_long][t_lat+1];
    u2next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat];
    v2next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat];      
    u3next = next->wind_u[(t_long+1)%WIND_GRID_LONG][t_lat+1];
    v3next = next->wind_v[(t_long+1)%WIND_GRID_LONG][t_lat+1];
      
    _transform_u_v(u0next, v0next);
    _transform_u_v(u1next, v1next);
    _transform_u_v(u2next, v2next);
    _transform_u_v(u3next, v3next);

#ifdef DEBUG
    printf("u0next: %.2f kts, %.2f deg\n", u0next, radToDeg(v0next));
    printf("u1next: %.2f kts, %.2f deg\n", u1next, radToDeg(v1next));
    printf("u2next: %.2f kts, %.2f deg\n", u2next, radToDeg(v2next));
    printf("u3next: %.2f kts, %.2f deg\n", u3next, radToDeg(v3next));
#endif /* DEBUG */  

    /* simple bilinear interpolation, we might factor the cos(lat) in
       the computation to tackle the shape of the pseudo square */
      
    u01next = u0next + (u1next - u0next) * (d_lat - floor(d_lat));
    u23next = u2next + (u3next - u2next) * (d_lat - floor(d_lat));

    angle = (v1next - v0next);
    _check_angle_interp(angle);
    rot_step1a = (angle > 0.0);
    v01next = v0next + (angle) * (d_lat - floor(d_lat));
    _positive_angle(v01next);
    
    angle =  (v3next - v2next);
    _check_angle_interp(angle);
    rot_step1b = (angle > 0.0);
    v23next = v2next + (angle) * (d_lat - floor(d_lat));
    _positive_angle(v23next);

    if (rot_step1a == rot_step1b) {
      unext = u01next + (u23next - u01next) * (d_long - floor(d_long));
      
      angle = (v23next - v01next);
      _check_angle_interp(angle);
      rot_step2b = (angle > 0.0);
      vnext = v01next + (angle) * (d_long - floor(d_long));
      _positive_angle(vnext);
    } else {
    rot_step2b = -1;
    /* rotations are in contrary motion, let's use UV for this */
#ifdef OLD_C_COMPILER
    _transform_back_u_v(u01next, v01next);
    _transform_back_u_v(u23next, v23next);
    unext = u01next + (u23next - u01next) * (d_long - floor(d_long));
    vnext = v01next + (v23next - v01next) * (d_long - floor(d_long));
    _transform_u_v(unext, vnext);
#else
    _transform_back_u_v(u01next, v01next, c01);
    _transform_back_u_v(u23next, v23next, c23);
    c = c01 + (c23 - c01) * (d_long - floor(d_long));
    unext = cabs(c);
    vnext = carg(c);
#endif /* OLD_C_COMPILER */
    }    
#ifdef DEBUG
    printf("-> u01next: %.2f kts, %.2f deg\n", u01next, radToDeg(v01next));
    printf("-> u23next: %.2f kts, %.2f deg\n", u23next, radToDeg(v23next));
    printf("=>   unext: %.2f kts, %.2f deg\n", unext, radToDeg(vnext));
#endif /* DEBUG */  
   
#ifdef DEBUG
    printf("vac_time %ld, prev prevision time %ld, next prevision time %ld\n", vac_time,
	   prev->prevision_time, next->prevision_time);
    ctime_r(&vac_time, buff);
    printf("vac_time %s", buff);
    ctime_r(&prev->prevision_time, buff);
    printf(" prev_time %s", buff);
    ctime_r(&next->prevision_time, buff);
    printf(" next_time %s\n", buff);
    printf("diff num %ld, diff_denom %ld\n", vac_time - prev->prevision_time,
	   next->prevision_time - prev->prevision_time);
#endif /* DEBUG */
    t_ratio = ((double)(vac_time - prev->prevision_time)) / 
      ((double)(next->prevision_time - prev->prevision_time));
      
    if ((rot_step2a == rot_step2b) || (rot_step2a < 0) ||
	(rot_step2b < 0)) {
      u = uprev + (unext - uprev) * t_ratio;
      angle = (vnext - vprev);
      _check_angle_interp(angle);
      v = vprev + (angle) * t_ratio;
      _positive_angle(v);
    } else {
#ifdef OLD_C_COMPILER
      _transform_back_u_v(uprev, vprev);
      _transform_back_u_v(unext, vnext);
      u = uprev + (unext - uprev) * t_ratio;
      v = vprev + (vnext - vprev) * t_ratio;
      _transform_u_v(u, v);
#else
      _transform_back_u_v(uprev, vprev, c01);
      _transform_back_u_v(unext, vnext, c23);
      c = c01 + (c23 - c01) * t_ratio;
      u = cabs(c);
      v = carg(c);
#endif /* OLD_C_COMPILER */
    }
  } else {
    u = uprev;
    v = vprev;
  }
#ifdef DEBUG
  printf("Speed %.3f, angle %.3f\n", u, radToDeg(v));
#endif /* DEBUG */
  wind->speed = u;
  wind->angle = v;
  return wind;
}

time_t get_max_prevision_time() {
  winds_prev *windtable;

  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    return 0;
  }
  return windtable->wind[windtable->nb_prevs-1]->prevision_time;
}

time_t get_min_prevision_time() {
  winds_prev *windtable;

  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    return 0;
  }
  return windtable->wind[0]->prevision_time;
}

int get_prevision_count() {
  winds_prev *windtable;
  
  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    return 0;
  }
  return windtable->nb_prevs;
}

time_t get_prevision_time_index(int gribindex) {
  winds_prev *windtable;
  
  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL || (gribindex < 0)
      || (gribindex >= windtable->nb_prevs)) {
    return 0;
  }
  return windtable->wind[gribindex]->prevision_time;
}
