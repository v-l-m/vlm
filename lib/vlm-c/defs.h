/**
 * $Id: defs.h,v 1.25 2010-08-16 16:03:07 ylafon Exp $
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

#ifndef _DEFS_H_
#define _DEFS_H_

#include <math.h>

#if 0
#  define PI     M_PIl
#  define PI_2   M_PI_2l
#  define PI_4   M_PI_4l
#  define TWO_PI M_PIl * 2
#else
#  define PI     M_PI
#  define PI_2   M_PI_2
#  define PI_4   M_PI_4
#  define TWO_PI M_PI * 2
#endif /* 0 */  


#define degToRad(angle) (((angle)/180.0) * PI)
#define radToDeg(angle) (((angle)*180.0) / PI)
#define msToKts(speed) (1.9438445*(speed))
#define ktsToMs(speed) (0.51444444*(speed))

#define latToY(lat) (log(tan(PI_4+(lat/2.0))))
#define yToLat(y) ((2.0*atan(exp(y)) - PI_2))

#define GRIB_TIME_OFFSET 0 /* VLM used to have 37/38mn offset, now 0 */

#ifdef VLM_COMPAT
# define VLM_MAJOR_VERSION 0
# define VLM_MINOR_VERSION 7
#endif /* VLM_COMPAT */

#ifndef VLM_MAJOR_VERSION
# define VLM_MAJOR_VERSION 0
#endif /* VLM_MAJOR_VERSION */

#ifndef VLM_MINOR_VERSION
# define VLM_MINOR_VERSION 9
#endif /* VLM_MINOR_VERSION */

#if VLM_MAJOR_VERSION == 0
# if VLM_MINOR_VERSION == 7
#  define GRIB_RESOLUTION_1
#  define DEFAULT_INTERPOLATION_UV
#  define ROUND_WIND_ANGLE_IN_POLAR
# elif VLM_MINOR_VERSION == 8
#  define GRIB_RESOLUTION_1
#  define DEFAULT_INTERPOLATION_UV
#  define ROUND_WIND_ANGLE_IN_POLAR
# elif  VLM_MINOR_VERSION == 9
#  define GRIB_RESOLUTION_0_5
#  define DEFAULT_INTERPOLATION_TWSA
#  define ROUND_WIND_ANGLE_IN_POLAR
# elif  VLM_MINOR_VERSION == 10
#  define GRIB_RESOLUTION_0_5
#  define DEFAULT_INTERPOLATION_SELECTIVE_TWSA
#  define ROUND_WIND_ANGLE_IN_POLAR
# else /* default */
#  define GRIB_RESOLUTION_0_5
#  define DEFAULT_INTERPOLATION_SELECTIVE_TWSA
# endif /* VLM_MINOR_VERSION */
#endif /* VLM_MAJOR_VERSION */

/* for now, no default when major != 0 */

#ifdef GRIB_RESOLUTION_1
# define WIND_GRID_LONG   360
# define WIND_GRID_LAT    181
#else
# define WIND_GRID_LONG   720
# define WIND_GRID_LAT    361
# define GRIB_RESOLUTION_0_5
#endif /* GRIB_RESOLUTION_1 */

/* 1 land, 2 lake, 3 island_in_lake, 4 pond_in_island_in_lake */
#define GSHHS_MAX_DETAILS 3

#define MAX_LAT_GSHHS 88 /* in degrees */

/**
 * Strict check will check intersection only between 0 and 1 (inclusive)
 * However, to ensure that no rounding issues happens, we can widen this
 * and extend by a 1%o factor, in practise, if we take into account that
 * the boat is not a single pixel, it makes sense :)
 */
#ifdef SAFE_LINE_CHECK
#  define INTER_MAX_LIMIT 1.0000001
#  define INTER_MIN_LIMIT -0.0000001
#else
#  define INTER_MAX_LIMIT 1.0
#  define INTER_MIN_LIMIT 0.0
# endif /* SAFE_LINE_CHECK */

/** 
 * if Paranoid mode is enabled, get something higher than
 * PHP's default rounding
 */
#ifdef PARANOID_COAST_CHECK
#  define COAST_INTER_MAX_LIMIT  1.001
#  define COAST_INTER_MIN_LIMIT -0.001
#endif /* PARANOID_COAST_CHECK */

#define WP_ARRIVAL_DISTANCE 0.001

#define WP_TWO_BUOYS 0
#define WP_ONE_BUOY  1
#define WP_GATE_BUOY_MASK 0x000F
/* leave space for 0-15 types of gates using buoys
   next is bitmasks */
#define WP_DEFAULT              0
#define WP_ICE_GATE_N           (1 <<  4)
#define WP_ICE_GATE_S           (1 <<  5)
#define WP_ICE_GATE_E           (1 <<  6)
#define WP_ICE_GATE_W           (1 <<  7)
#define WP_GATE_KIND_MASK       0x00F0
/* allow crossing in one direction only */
#define WP_CROSS_CLOCKWISE      (1 <<  8)
#define WP_CROSS_ANTI_CLOCKWISE (1 <<  9)
/* for future releases */
#define WP_CROSS_ONCE           (1 << 10)

#ifndef VLM_BUILD_DATE
# define VLM_BUILD_DATE "\"Unknown\""
#endif /* VLM_BUILD_DATE */

#ifdef PARAM1
#  undef PARAM1
#endif /* PARAM1 */
#ifdef PARAM2
#  undef PARAM2
#endif /* PARAM2 */
#ifdef PARAM3
#  undef PARAM3
#endif /* PARAM3 */
#ifdef PARAM4
#  undef PARAM4
#endif /* PARAM4 */

#ifdef __STDC__
#  define PARAM1(a) (a)
#  define PARAM2(a,b) (a,b)
#  define PARAM3(a,b,c) (a,b,c)
#  define PARAM4(a,b,c,d) (a,b,c,d)
#  define PARAM5(a,b,c,d,e) (a,b,c,d,e)
#  define PARAM6(a,b,c,d,e,f) (a,b,c,d,e,f)
#  define PARAM7(a,b,c,d,e,f,g) (a,b,c,d,e,f,g)
#  define PARAM8(a,b,c,d,e,f,g,h) (a,b,c,d,e,f,g,h)
#  define PARAM9(a,b,c,d,e,f,g,h,i) (a,b,c,d,e,f,g,h,i)
#  define PARAM10(a,b,c,d,e,f,g,h,i,j) (a,b,c,d,e,f,g,h,i,j)
#  define PARAM11(a,b,c,d,e,f,g,h,i,j,k) (a,b,c,d,e,f,g,h,i,j,k)
#else
#  define PARAM1(a) ()
#  define PARAM2(a,b) ()
#  define PARAM3(a,b,c) ()
#  define PARAM4(a,b,c,d) () 
#  define PARAM5(a,b,c,d,e) ()
#  define PARAM6(a,b,c,d,e,f) ()
#  define PARAM7(a,b,c,d,e,f,g) ()
#  define PARAM8(a,b,c,d,e,f,g,h) ()
#  define PARAM9(a,b,c,d,e,f,g,h,i) ()
#  define PARAM10(a,b,c,d,e,f,g,h,i,j) ()
#  define PARAM11(a,b,c,d,e,f,g,h,i,j,k) ()
#  define const
#endif /* __STDC__ */

#endif /* _DEFS_H_ */
