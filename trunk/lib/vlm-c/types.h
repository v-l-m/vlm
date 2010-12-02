/**
 * $Id: types.h,v 1.22 2010-11-27 15:32:35 ylafon Exp $
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

#ifndef _VLMC_TYPES_H_
#define _VLMC_TYPES_H_

#include <time.h>

#include "defs.h"

typedef struct coast_seg_str {
#ifdef SAVE_MEMORY
  int longitude_a;
  int latitude_a;
  int longitude_b;
  int latitude_b;
#else
  double longitude_a;
  double latitude_a;
  double longitude_b;
  double latitude_b;
#endif /* SAVE_MEMORY */
} coast_seg;

typedef struct coast_zone_str {
  int                   nb_segments;
  struct coast_seg_str *seg_array;
} coast_zone;

typedef struct coast_str {
  int  nb_grid_x;
  int  nb_grid_y;
  /* FIXME add resolution, min_lat, max_lat, min_long, max_long */
  struct coast_zone_str *zone_array;
} coast;

typedef struct boat_polar_str {
  char *polar_name;
  double *polar_tab;
} boat_polar;

typedef struct boat_polar_list_str {
  int                   nb_polars;
  struct boat_polar_str **polars;
} boat_polar_list;

typedef struct waypoint_str {
  int    type      ;  /* type of waypoint                     */
  int    idwaypoint;  /* id in the race                       */
  double latitude1 ;  /* latitude of first point              */
  double longitude1;  /* longitude of first point             */
  double latitude2 ;  /* latitude of second point (if used)   */
  double longitude2;  /* longitude of second point (if used)  */
  double angle     ;  /* angle from the first point (if used) */
  char   *name     ;  /* (if needed)                          */
} waypoint;

typedef struct race_str {
  int                 idraces;
  char                *racename;
  int                 started;
  time_t              deptime;
  time_t              closetime;
  time_t              bobegin;
  time_t              boend;
  time_t              maxarrivaltime;
  long                startlong;
  long                startlat;
  boat_polar          *boattype;     /* point to the polar */
  int                 racetype;
  int                 firstpcttime;  
  int                 coastpenalty;
  struct boat_str     *boat_list;
  int                 nb_boats;
  struct waypoint_str *waypoint_list;
  int                 nb_waypoints;
  int                 vac_duration;
} race;

typedef struct winds_str {
  time_t prevision_time;           /* set origin time */
  double  wind_u[WIND_GRID_LONG][WIND_GRID_LAT]; 
  double  wind_v[WIND_GRID_LONG][WIND_GRID_LAT];
  /* u = horizontal W->E , v = vertical N->S ? */
} winds;

typedef struct wind_prev_str {
  long time_offset;
  int nb_prevs;
  struct winds_str **wind;
} winds_prev;

typedef struct wind_info_str {
  double speed;
  double angle; /* in rad */
} wind_info;

/* spf :
     is type part of boat, or taken from the race ?
   paparazzia :
     in vlm, it's "taken" from the race
     however, you have to read the type from the boat page
     thus, I suggest to simply proxy/copy the type from the race to the boat
     (you really don't need all the race infos for routing)
*/
 
typedef struct boat_str {
  int         racing;          /* has the boat started or not ? bool */
  int         landed;          /* did we reach the coast ? bool      */
  int         num;             /* boat number                        */
  char        *name;           /* boat name                          */
  double      latitude;        /* latitude  in rad                   */
  double      longitude;       /* longitude in rad                   */
  double      wp_latitude;     /* latitude  in rad                   */
  double      wp_longitude;    /* longitude in rad                   */
  double      wp_heading;      /* for fixed angle/wind_angle in rad  */
  double      wp_distance;     /* distance to the WP in nm           */
  double      heading;         /* actual heading in rad              */
  double      loch;            /* loch                               */
  int         pim;             /* pilot mode                         */
  int         nwp;             /* The target gate ID                 */
  double      pip;             /* pilot parameter                    */
  struct race_str *in_race;    /* the race it belongs to             */
  struct boat_polar_str *polar;/* The polar in use                   */
  time_t      last_vac_time;   /* time of last move                  */
  time_t      last_update;     /* time of last pilot change          */
  time_t      departure_time;  /* departure time in the race         */
  time_t      release_time;    /* release time (after a penalty)     */
  struct wind_info_str wind;   /* the computed wind                  */
  void   (*set_heading_func)();
} boat;

typedef struct vlmc_context_str {
  char            *polar_definition_filename;
  char            *gshhs_filename;
  char            *grib_filename;
  coast           *shoreline;
  winds_prev      windtable;
  boat_polar_list polar_list;
  int             init_value;
  int             semid;     /* used for shared memory functions */
  void            *grib_segmaddr; /* used for shared memory functions */
  void            *polar_segmaddr; /* used for shared memory functions */
} vlmc_context;


#endif /* _VLMC_TYPES_H_ */
