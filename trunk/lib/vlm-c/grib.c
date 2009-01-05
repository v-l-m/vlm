/**
 * $Id: grib.c,v 1.24 2009-01-05 09:20:47 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *
 * Parts of this code are taken from wgrib-c v1.8.0.12o (5-07) by Wesley Ebisuzaki
 * and adapted to fit our data structures.
 * See http://www.cpc.ncep.noaa.gov/products/wesley/wgrib.html
 *
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
#include <stdlib.h>
#ifndef OLD_C_COMPILER
#include <complex.h>
#endif /* OLD_C_COMPILER */

#include "defs.h"
#include "types.h"
#include "grib.h"
#include "wgrib/grib.h"
#include "wgrib/bms.h"
#include "wgrib/bds.h"
#include "wgrib/gds.h"
#include "wgrib/pds4.h"

extern vlmc_context *global_vlmc_context;

/* local definition */
winds **read_gribs PARAM1(int *);

#define MSEEK 4096
#define BUFF_ALLOC0  1048576

void set_grib_offset(long time_offset) {
  global_vlmc_context->windtable.time_offset = time_offset;
}

long get_grib_offset() {
  return global_vlmc_context->windtable.time_offset;
}

void init_grib() {
  init_grib_offset(GRIB_TIME_OFFSET);
}

void init_grib_offset(long time_offset) {
  winds **w, **oldw;
  int nb_prevs, oldcount, i;

  w = read_gribs(&nb_prevs);
  /* no error, cleanup old data */
  if (w) {
    oldcount = global_vlmc_context->windtable.nb_prevs;
    oldw = global_vlmc_context->windtable.wind;
    global_vlmc_context->windtable.nb_prevs    = nb_prevs; 
    global_vlmc_context->windtable.wind        = w;
    global_vlmc_context->windtable.time_offset = time_offset;
    if (oldw != NULL) {
      for (i=0; i<oldcount; i++) {
	free(oldw[i]);
      }
      free(oldw);
    }
  }
}

/**
 * remove old gribs (compared to _now_)
 */
void purge_gribs() {
  time_t now;
  int i, gribidx, nb_prevs;
  winds_prev *windtable;
  winds **w, **oldw;

  windtable = &global_vlmc_context->windtable;
  if (!windtable) {
    return;
  }
  nb_prevs = windtable->nb_prevs; 
  if (nb_prevs < 3) {
    /* no need to work on this */
    return;
  }

  time(&now);
  for (i=0; i< nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > now) {
      /* the first one is already ahead of now, or we are between 0 and 1,
	 then skip */
      if (!(i>>1)) {
	return;
      }
      i--;
      /* allocate and copy */
      w = calloc (nb_prevs - i, sizeof(winds *));
      for (gribidx = i; i<nb_prevs; i++) {
	w[i-gribidx] = windtable->wind[i];
      }
      /* assign then purge old structures */
      oldw                 = windtable->wind;
      windtable->wind      = w;
      windtable->nb_prevs -= gribidx;
#ifdef DEBUG
      printf("Purging, from 0 to %d\n", gribidx);
#endif /* DEBUG */
      for (i=0; i<gribidx; i++) {
	free(oldw[i]);
      }
      free(oldw);
      /* we did our job, abort the outer loop */
      break;
    }
  }
}

/**
 * merge gribs, then purge if 'purge' is non-zero 
 */
void merge_gribs(int purge) {
  winds **w, **oldw, **neww, *winds_t;
  int nb_prevs, oldcount, i, j, addit, idx;
  int totalcount, must_loop;
  
  w = read_gribs(&nb_prevs);
  /* error, fail silently */
  if (!w) {
    return;
  }
  
  oldcount = global_vlmc_context->windtable.nb_prevs;
  oldw = global_vlmc_context->windtable.wind;
  
  /* compute the size of the merged grib */
  totalcount = oldcount;
  for (i=0; i<nb_prevs; i++) {
    addit = 1;
    for (j=0; j<oldcount; j++) {
      /* same as an existing grib, it will be replaced, not added */
      if (w[i]->prevision_time == oldw[j]->prevision_time) {
	addit = 0;
	break;
      }
    }
    totalcount += addit;
  }

  /* allow the structure, copy then merge/replace */
  neww = calloc (totalcount, sizeof(winds *));
  for (i=0; i<oldcount; i++) {
    neww[i] = oldw[i];
  }
  idx = oldcount;
  for (i=0; i<nb_prevs; i++) {
    addit = 1;
    for (j=0; j<oldcount; j++) {
      /* same as an existing grib, clean up old data, and install
	 the most recent grib */
      if (w[i]->prevision_time == neww[j]->prevision_time) {
	free(neww[j]);
	neww[j] = w[i];
	addit = 0;
	break;
      }
    }
    /* not a replacement, add it at the end */
    if (addit) {
      neww[idx++] = w[i];
    }
  }
  /* we added new gribs at the end, but not sure that it has been done
     in order -> reorder this */
  must_loop = 1;
  while (must_loop) {
    must_loop = 0;
    for (i=1; i<totalcount; i++) {
      if (neww[i-1]->prevision_time > neww[i]->prevision_time) {
	winds_t   = neww[i];
	neww[i]   = neww[i-1];
	neww[i-1] = winds_t;
	must_loop = 1;
      }
    }
  }
  global_vlmc_context->windtable.nb_prevs    = totalcount; 
  global_vlmc_context->windtable.wind        = neww;
  /* we freed the old gribs, but not the containing structure, do it now */
  free(oldw);
  free(w);
  /* and finish by purging the old gribs */
  if (purge) {
    purge_gribs();
  }
}

/**
 * read grib form context->grib filename and return a an array
 * of newly allocated winds *, and fill the number of previsions
 */
winds **read_gribs(int *nb_prevs) {
  struct tm     gribtime_tm;
  time_t        gribtime;
  FILE          *gribfile;
  unsigned char *buffer, *msg;
  unsigned char *pds, *gds, *bms, *bds, *pointer;
  long          len_grib, pos, buffer_size;
  int           nx, ny;
  long          nxny;
  int           count, wpos;
  int           scan, mode;
  int           i,x,y;
  winds         **w;
  winds         *winds_t;
  float         *array;
  double        temp;
  int           in_error, gribtype, must_loop;

  /* to make the compiler happy */
  winds_t = NULL;
  array = NULL;
  gribtime = 0;
  in_error = 0;
  /**
   * first we read the file, find the number or records, 
   * alloc memory for structures
   * then reread the file to fill them
   */
  if (!global_vlmc_context->grib_filename) {
    printf("FATAL: global_vlmc_context not initialized\n");
    return NULL;
  }
  
  gribfile = fopen(global_vlmc_context->grib_filename, "r");
  if (gribfile == NULL) {
    printf("FATAL: unable to open \"%s\"\n",global_vlmc_context->grib_filename);
    return NULL;
  }

  if ((buffer = (unsigned char *) calloc(BUFF_ALLOC0, sizeof(char))) == NULL) {
    printf("FATAL: not enough memory while allocating buffer to read grib files\n");
    return NULL;
  }
  buffer_size = BUFF_ALLOC0;
  pos = 0;
  count = 0;
  do {
    msg = seek_grib(gribfile, &pos, &len_grib, buffer, MSEEK);
    if (msg) {
      count++;
      pos += len_grib;
    }
  } while (msg);
#ifdef DEBUG
  printf("Found %d records, %d wind previsions\n", count, count>>1);
#endif /* DEBUG */
  if (count & 1) {
    printf("Error reading GRIB file, found odd number or records\n");
    return NULL;
  }

  /* 
     here we assume we have only u/v in the grib, we could be stricter
     and really enforce the number of previsions before allocating the thing
     in low priority todo (FIXME)
  */
  w = calloc (count/2, sizeof(winds *));

  /* now reread the file, and dump stuff one by one */
  pos  = 0;
  wpos = 0;
  for (i=0; i<count; i++) {
    msg = seek_grib(gribfile, &pos, &len_grib, buffer, MSEEK);
    /* ensure buffer is enough (should be ok for two records by default */
    if (len_grib + msg - buffer > buffer_size) {
      buffer_size = len_grib + msg - buffer + 1000;
      buffer = (unsigned char *) realloc((void *) buffer, buffer_size);
      if (buffer == NULL) {
	printf("INIT GRIB ran out of memory\n");
	return NULL;
      }
    }
    if (read_grib(gribfile, pos, len_grib, buffer) == 0) {
      printf("INIT GRIB error, could not read to end of record %d\n",i*2);
      in_error = 1;
      break;
    }
    msg = buffer;
    pds = msg+8;
    pointer = pds + PDS_LEN(pds);
    if (PDS_HAS_GDS(pds)) {
      gds = pointer;
      pointer += GDS_LEN(gds);
    } else {
      gds = NULL;
    }
    if (PDS_HAS_BMS(pds)) {
      bms = pointer;
      pointer += BMS_LEN(bms);
    } else {
      bms = NULL;
    }
    bds = pointer;
    pointer += BDS_LEN(bds);
    if (pointer-msg+4 != len_grib) {
      printf("INIT GRIB: Len of grib message is inconsistent.\n");
      in_error = 1;
      break;
    }
    
    if (pointer[0] != 0x37 || pointer[1] != 0x37 ||
	pointer[2] != 0x37 || pointer[3] != 0x37) {
      printf("INIT GRIB: missing end section\n");
      in_error = 1;
      break;
    }
    if (gds == NULL) {
      /* we check only full grids */
      printf("INIT GRIB: wind grid invalid\n");
      in_error = 1;
      break;
    }
    GDS_grid(gds, bds, &nx, &ny, &nxny);
#ifdef DEBUG
    printf("GRID: %d by %d\n", nx, ny);
#endif /* DEBUG */
    if (GDS_LatLon(gds)) {
      scan = GDS_LatLon_scan(gds);
      mode = GDS_LatLon_mode(gds);
    }

    if ((array = (float *) calloc(nxny, sizeof(float))) == NULL) {
      printf("Unable to alloc float array\n");
      in_error = 1;
      break;
    }

    temp = int_power(10.0, - PDS_DecimalScale(pds));
    BDS_unpack(array, bds, BMS_bitmap(bms), BDS_NumBits(bds), nxny,
	       temp*BDS_RefValue(bds),temp*int_power(2.0, BDS_BinScale(bds)));
    
    /* ok, now we have the GDS, let's work on it */
#ifdef DEBUG
    for (x=0; x<10; x++) {
      printf("%.3f ", array[x]);
    }
    printf("\n");
#endif /* DEBUG */
    /**
     * we have two passes, one for U, one for V, we alloc only the first time
     * as well as the prevision time 
     * FIXME: should we _verify_ that the time is the same ? 
     */
    gribtype = PDS_PARAM(pds); /* 33 is U, 34 is V */
    gribtime_tm.tm_year   = PDS_Year4(pds) - 1900;
    gribtime_tm.tm_mon    = PDS_Month(pds) - 1; /* As January is 0 and not 1 */
    gribtime_tm.tm_mday   = PDS_Day(pds); 
    gribtime_tm.tm_hour   = PDS_Hour(pds);
    gribtime_tm.tm_min    = 0;
    gribtime_tm.tm_sec    = 0;
    gribtime_tm.tm_isdst  = 0;
#ifndef __CYGWIN
    gribtime_tm.tm_gmtoff = 0;
#endif /* __CYGWIN */
    gribtime = timegm(&gribtime_tm);
#ifdef DEBUG
    printf("Time: %ld", gribtime);
#endif /* DEBUG */
    if (PDS_ForecastTimeUnit(pds) == HOUR) {
      gribtime += (PDS_P1(pds)*256 + PDS_P2(pds)) * 3600;
    } else {
      printf("Unknown forecat time unit %d, contact maintainer\n", 
	     PDS_ForecastTimeUnit(pds));
      in_error = 1;
      break;
    }
#ifdef DEBUG
    printf(" -> %ld\n", gribtime);
    printf("Time Unit: %d\n", PDS_ForecastTimeUnit(pds));
    printf("Time P1: %d\n", PDS_P1(pds));
    printf("Time P2: %d\n", PDS_P2(pds));
    printf("Time Range: %d\n", PDS_TimeRange(pds));
#endif /* DEBUG */
    /* get or create a new winds structure */
    winds_t = NULL;
    /* first try to locate an entry with the same timestamp */
    if (wpos) {
      for (x=0; x<wpos; x++) {
	if (w[x]->prevision_time == gribtime) {
	  winds_t = w[x];
	  break;
	}
      }
    } 
    /* if not, allocate the structure */
    if (!winds_t) {
      winds_t = calloc(1, sizeof(winds));
      winds_t->prevision_time = gribtime;
      w[wpos] = winds_t;
#ifdef DEBUG
      printf("Prev time: %ld, %s\n", winds_t->prevision_time, 
	     ctime(&winds_t->prevision_time));
#endif /* DEBUG */
      wpos++;
    } 
    /* fill depending on grid type */
    if (gribtype == 34) { /* VGRD */
#ifdef GRIB_RESOLUTION_1
      for (y=0; y<ny; y+=2) {
	for (x=0; x<nx; x+=2) {
	  winds_t->wind_v[x/2][180 - y/2] = (double) array[y*nx+x];
	}
      }
#else
      for (y=0; y<ny; y++) {
	for (x=0; x<nx; x++) {
	  winds_t->wind_v[x][360 - y] = (double) array[y*nx+x];
	}
      }
#endif /* GRIB_RESOLUTION_1 */
      winds_t = NULL;
    } else if (gribtype == 33) { /* UGRD */
#ifdef GRIB_RESOLUTION_1
      for (y=0; y<ny; y+=2) {	
	for (x=0; x<nx; x+=2) {
	  winds_t->wind_u[x/2][180 - y/2] = (double) array[y*nx+x];
	}
      }
#else /* should we check we match GRIB_REOLUTION_0_5 ? */
      for (y=0; y<ny; y++) {	
	for (x=0; x<nx; x++) {
	  winds_t->wind_u[x][360 - y] = (double) array[y*nx+x];
	}
      }
#endif /* GRIB_RESOLUTION_1 */
    } else {
      in_error = 1;
      break;
    }
    free(array);
    array = NULL;
    pos += len_grib;
  }
  /* free structures */
  free(buffer);
  fclose(gribfile);
  /* in case of error, do some cleanup */
  if ((i < count) || in_error) {
    if (array) {
      free(array);
    }
    count = i;
    for (i=0; i<count; i++) {
      free(w[i]);
    }
    free(w);
    return NULL;
  }
  /* and reorder the gribs, if the source file is not in ascending order */
  must_loop = 1;
  while (must_loop) {
    must_loop = 0;
    for (x=1; x<wpos; x++) {
      if (w[x-1]->prevision_time > w[x]->prevision_time) {
	winds_t = w[x];
	w[x]    = w[x-1];
	w[x-1]  = winds_t;
	must_loop = 1;
      }
    }
  }
  /* populate data and exit */
  *nb_prevs = wpos;
  return w;
}

/* return a winds entry interpolated in the time domain */
winds *generate_interim_grib(time_t gribtime) {
#ifdef VLM_COMPAT
  return generate_interim_grib_UV(gribtime);
#else
  return generate_interim_grib_TWSA(gribtime);
#endif /* VLM_COMPAT */
}

/* return a winds entry interpolated in the time domain using UV */
winds *generate_interim_grib_UV(time_t grib_time) {
  winds_prev *windtable;
  winds *prev, *next, *winds_t;
  double *uprev, *unext, *vprev, *vnext;
  double *ures, *vres;
  double t_ratio;
  time_t corrected_time;
  int i;

  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    return NULL;
  }
  /* correct the time according to the offset */
  corrected_time = grib_time - windtable->time_offset;
  /* find the surrounding grib entries */
  prev = next = NULL;
  for (i=0; i< windtable->nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > corrected_time) {
      if (i) {
	next = windtable->wind[i];
	prev = windtable->wind[i-1];
      } else {
	prev = windtable->wind[i];
      }
      break;
    }
  }
  /* we are before the first of after the last, no work needed */
  if (!next || !prev) {
    return NULL;
  }
  winds_t = (winds *)calloc(1, sizeof(winds));
  if (!winds_t) {
    /* error while allocating the structure -> fail silently */
    return NULL;
  }
  uprev = &prev->wind_u[0][0];
  vprev = &prev->wind_v[0][0];
  unext = &next->wind_u[0][0];
  vnext = &next->wind_v[0][0];
  ures = &winds_t->wind_u[0][0];
  vres = &winds_t->wind_v[0][0];

  t_ratio = ((double)(corrected_time - prev->prevision_time)) / 
    ((double)(next->prevision_time - prev->prevision_time));
  for (i=0; i<WIND_GRID_LONG*WIND_GRID_LAT; i++) {
    *ures++ = (1-t_ratio) * (*uprev++) + t_ratio*(*unext++);
    *vres++ = (1-t_ratio) * (*vprev++) + t_ratio*(*vnext++);
  }
  winds_t->prevision_time = corrected_time;
  return winds_t;
}

/* return a winds entry interpolated in the time domain using TWSA */
winds *generate_interim_grib_TWSA(time_t grib_time) {
  winds_prev *windtable;
  winds *prev, *next, *winds_t;
  double *uprev, *unext, *vprev, *vnext;
  double *ures, *vres;
  double t_ratio;
  double p_speed, p_angle, n_speed, n_angle;
  double speed, angle;
  double diff_angle;
  time_t corrected_time;
  int i;

  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    return NULL;
  }
  /* correct the time according to the offset */
  corrected_time = grib_time - windtable->time_offset;
  /* find the surrounding grib entries */
  prev = next = NULL;
  for (i=0; i< windtable->nb_prevs; i++) {
    if (windtable->wind[i]->prevision_time > corrected_time) {
      if (i) {
	next = windtable->wind[i];
	prev = windtable->wind[i-1];
      } else {
	prev = windtable->wind[i];
      }
      break;
    }
  }
  /* we are before the first of after the last, no work needed */
  if (!next || !prev) {
    return NULL;
  }
  winds_t = (winds *)calloc(1, sizeof(winds));
  /* error while allocating the structure, fail silently */
  if (!winds_t) {
    return NULL;
  }
  uprev = &prev->wind_u[0][0];
  vprev = &prev->wind_v[0][0];
  unext = &next->wind_u[0][0];
  vnext = &next->wind_v[0][0];
  ures = &winds_t->wind_u[0][0];
  vres = &winds_t->wind_v[0][0];

#define _check_angle_interp(a)			\
  if (a >= PI) {				\
    a -= TWO_PI;				\
  } else if (a <= -PI) {			\
    a += TWO_PI;				\
  }

#define _positive_angle(a)			\
  if (a < 0) {					\
    a += TWO_PI;				\
  } else if (a >= TWO_PI) {			\
    a -= TWO_PI;				\
  }

  t_ratio = ((double)(corrected_time - prev->prevision_time)) / 
    ((double)(next->prevision_time - prev->prevision_time));
  for (i=0; i<WIND_GRID_LONG*WIND_GRID_LAT; i++) {
    p_speed =sqrt((*uprev)*(*uprev)+(*vprev)*(*vprev));
    n_speed =sqrt((*unext)*(*unext)+(*vnext)*(*vnext));
    p_angle = acos((*uprev)/p_speed);
    if (*vprev < 0.0) {
      p_angle = TWO_PI - p_angle;
    }
    n_angle = acos((*unext)/n_speed);
    if (*vnext < 0.0) {
      n_angle = TWO_PI - n_angle;
    }
    diff_angle = (n_angle - p_angle);
    _check_angle_interp(diff_angle);
    angle = p_angle + (diff_angle) * t_ratio;
    speed = p_speed + (n_speed - p_speed) * t_ratio;
    *ures++ = speed*cos(angle);
    *vres++ = speed*sin(angle);
    uprev++; vprev++; unext++; vnext++;
  }
  winds_t->prevision_time = corrected_time;
  return winds_t;
}

/**
 * generate a snapshot of current time (offset corrected), then merge with
 * a new grib collection file, and purge the stale entries
 */
void interpolate_and_merge_grib() {
  winds *interpolated, *tw;
  time_t now;
  winds_prev *windtable;

  windtable = &global_vlmc_context->windtable;
  if (windtable->wind == NULL) {
    init_grib();
    return;
  }
  interpolated = generate_interim_grib(time(&now));
  if (interpolated) {
    /* the first one is more likely to be outdated, otherwise
       no interpolation would happen. reorder will appear later */
    tw = windtable->wind[0];
    windtable->wind[0] = interpolated;
    free(tw);
  }
  /* merge and purge. The merge should reorder things, prior to the purge */
  merge_gribs(1);
}
