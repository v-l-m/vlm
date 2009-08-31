/**
 * $Id: polar.c,v 1.19 2009-08-31 12:54:03 ylafon Exp $
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
#include <math.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>

#include "defs.h"
#include "types.h"
#include "polar.h"

#ifdef USE_SETLOCALE
#include <locale.h>
#endif /* USE_SETLOCALE */

extern vlmc_context *global_vlmc_context;

#define INITIAL_BUFFER_SIZE 65536; /* 64k */

void add_polar PARAM2(char *, char *);
void read_polars();

void add_polar(char *pname, char *fname) {
  boat_polar      *pol;
  boat_polar_list *plist;
  boat_polar      **p;
  FILE            *pfile;
  char   wbuff[16384], *token, *ptoken;
  int    p_i, p_j, i,j, wspeed, wangle, ok, nb_polar;
  int    idx, interp_idx, idx_diff;
  int    *polar_check_table;
  int    wspeedidx[61];
  double speed, p_speed;

  /* safety check */
  if ((pname == NULL) || (fname == NULL)) {
    return;
  }
  /* now check the file name */
  pfile = fopen(fname, "r");
  if (pfile == NULL) {
    printf("FATAL: unable to open \"%s\" for polar \"%s\"\n", fname, pname);
    return;
  }

  /* ok so far, process it */
  pol = calloc(1, sizeof(pol));
  pol->polar_tab = calloc(181*61, sizeof(double));
  polar_check_table = calloc(181*61, sizeof(int));
  for (i=0;i<61;i++) {
    wspeedidx[i] = -1;
  }
  idx = -1;

#ifdef USE_SETLOCALE
  setlocale(LC_NUMERIC, "C");
#endif /* USE_SETLOCALE */

  /* copy the name */
  pol->polar_name = calloc(strlen(pname)+1, sizeof(char));
  strcpy(pol->polar_name, pname);

  /* now cycle to read all the values */
  while (1) {
    ok = fscanf(pfile, "%s", wbuff);
    if (ok<=0) { 
      break;
    }
    if (idx == -1) { /* first line */
      token = strtok(wbuff, ";");
      idx = 0;
      while (token) {
	token = strtok(NULL, ";");
	if (token) {
	  wspeedidx[idx++] = atoi(token);
	}
      }
      continue;
    }
    /* other lines */
    /* first, read the angle _always there_ */
    token = wbuff;
    ptoken = wbuff;
    while (*token && *token!=';') {
      token++;
    }
    *token = 0;
    wangle = atoi(ptoken);
    idx = 0;
    while (1) {
      /* get the current speed */
      wspeed = wspeedidx[idx++];
      ptoken = ++token;
      if (*token == ';') { /* two ; in a row, it's empty */
	continue;
      }
      while (*token && *token!=';') {
	token++;
      }
      ok = *token;
      *token = 0;
      sscanf(ptoken, "%lf", &speed);
      pol->polar_tab[wangle*61+wspeed] = speed;
      polar_check_table[wangle*61+wspeed] = 1;
      if (!ok) {
	break;
      }
    }
  }
  fclose (pfile);

  /* now interpolate the missing bits */
  /* we assume (wrongly? :) ) that all polars uses
     0-180 inclusive and speed always starts at 0 */

  assert(wspeedidx[0] == 0);
  idx_diff = 0;
  j = 0;

  /* First, for all known angles, fill all speeds */
  for (i=0; i<=180; i++) {
    if (polar_check_table[i*61]) {
      p_j = 0;
      p_speed = pol->polar_tab[i*61];
      for (idx=1; wspeedidx[idx]>=0; idx++) {
	j = wspeedidx[idx];
	idx_diff = (j - p_j);
	speed =  pol->polar_tab[i*61+j];
	if (idx_diff > 1 ) { /* we have something to interpolate */
	  for (interp_idx = 1; interp_idx < idx_diff; interp_idx++ ) {
	    pol->polar_tab[i*61+p_j+interp_idx] = p_speed + 
	             (speed-p_speed)*((double)interp_idx) / ((double) idx_diff);
	    polar_check_table[i*61+p_j+interp_idx] = 1;
	  }
	}
	p_j = j;
	p_speed = speed;
      }
      if (j < 60) {
	assert((j!=0) && (idx_diff !=0));
	p_j = wspeedidx[idx-2];
	p_speed = pol->polar_tab[i*61+p_j];
	for (interp_idx = j - p_j +1 ; interp_idx < 61 - p_j ; interp_idx++ ) {
	  pol->polar_tab[i*61+p_j+interp_idx] = p_speed + 
	         (speed-p_speed)*((double)interp_idx) / ((double) idx_diff);
	  polar_check_table[i*61+p_j+interp_idx] = 1;
	}
      }
    }
  }

  /* The fill all the missing values for non-defined angles */
  for (j=0; j<=60; j++) {
    assert (polar_check_table[j] == 1);
    p_i = 0;
    p_speed = pol->polar_tab[j];
    for (i=1; i<=180; i++) {
      if (polar_check_table[i*61+j]) { /* got one value */
	idx_diff = i-p_i;
	speed = pol->polar_tab[i*61+j];
	if ( idx_diff > 1) { /* if we have something to interpolate */
	  for (interp_idx = 1; interp_idx < idx_diff; interp_idx++ ) {
	    pol->polar_tab[(p_i+interp_idx)*61+j] = p_speed + 
	             (speed-p_speed)*((double)interp_idx) / ((double) idx_diff);
	    polar_check_table[(p_i+interp_idx)*61+j] = 1;
	  }
	}
	p_i = i;
	p_speed = speed;
      }
    }
  }
 
  /* final check (might be removed later) */
  for (i=0; i<=180; i++) {
    for (j=0; j<=60; j++) {
      assert( polar_check_table[i*61+j] == 1);
    }
  }
  free(polar_check_table);

  plist = &global_vlmc_context->polar_list;
  if (plist->polars == NULL) {
    plist->nb_polars = 1;
    plist->polars = malloc(sizeof (boat_polar *));
    plist->polars[0] = pol;
  } else {
    nb_polar = plist->nb_polars;
    p = calloc(nb_polar+1, sizeof(boat_polar *));
    for (i=0; i<nb_polar; i++) {
      p[i] = plist->polars[i];
    }
    p[nb_polar] = pol;
    free(plist->polars);
    plist->polars = p;
    plist->nb_polars++;
  }
#ifdef USE_SETLOCALE
  setlocale(LC_NUMERIC, "");
#endif /* USE_SETLOCALE */
}

boat_polar *get_polar_by_name(char *pname) {
  return get_polar_by_name_context(global_vlmc_context, pname);
}

/* get the pointer to the polar entry based on its name */
boat_polar *get_polar_by_name_context(vlmc_context *context, char *pname) {
  boat_polar_list *plist;
  int i, nb_polars;

  plist =  &context->polar_list;
  if ((pname == NULL) || (plist->polars == NULL)) {
    return NULL;
  }
  nb_polars = plist->nb_polars;
  for (i=0; i<nb_polars; i++) {
    if (!strcmp(pname, plist->polars[i]->polar_name)) {
      return plist->polars[i];
    }
  }
  return NULL;
}

/**
 * read polar file from the polar definition filename, then
 * generate all polars, they will be associated by their name
 * to races and boats
 */
void read_polars() {
  FILE *polar_definitions;
  char *buffer, *bufend, *t;
  char *polar_name, *polar_filename;
  int nb_read;
  int buffer_size;
  int remaining_size;
  int a;

  /* if we don't have a global context... bail out */
  if (!global_vlmc_context->polar_definition_filename) {
    printf("FATAL: unable to read polar, no definition name given\n");
    return;
  }
  buffer_size       = INITIAL_BUFFER_SIZE;
  remaining_size    = buffer_size;
  buffer            = calloc(buffer_size, sizeof(char)); 
  polar_definitions = fopen(global_vlmc_context->polar_definition_filename,"r");
  if (polar_definitions == NULL) {
    printf("FATAL: unable to open \"%s\"\n", 
	   global_vlmc_context->polar_definition_filename);
    return;
  }
  t = buffer;
  /* fill the buffer with the entire file */
  nb_read = fread((void *)t, (size_t) sizeof(char), remaining_size,
		  polar_definitions);
  while (nb_read) {
    remaining_size -= nb_read;
    t += nb_read;
    if (remaining_size < 8192) {
      a = (buffer_size / 2);
      buffer = realloc((void *)buffer, (size_t)(buffer_size+a)*sizeof(char));
      if (buffer == NULL) {
	printf("FATAL: unable to allocate memory while processing \"%s\"\n", 
	       global_vlmc_context->polar_definition_filename);
	return;
      }
      buffer_size    += a;
      remaining_size += a;
    }
    nb_read = fread((void *)t, (size_t) sizeof(char), remaining_size,
		    polar_definitions);
  }
  bufend = t;
  *bufend = 0; /* just to be sure */
  /* close file */
  fclose(polar_definitions);

  /* now parse quick and dirty */
  t = buffer;
  polar_name = NULL;
  polar_filename = NULL;
  while (*t && t < bufend) {
    /* first get the name */
    if ((polar_name == NULL) &&
	(((*t >= 'A') && (*t <= 'Z')) || ((*t >= 'a') && (*t <= 'z')))) {
      polar_name = t++;
    } 
    /* then the filename */
    if (polar_name && (polar_filename == NULL) && (*t == ':')) {
      *t++ = 0;
      polar_filename = t;
    }
    /* go to the end of line */
    if ((*t == '\n') || (*t == '\r')) {
      *t++ = 0;
      add_polar(polar_name, polar_filename);
      polar_name = NULL;
      polar_filename = NULL;
      continue;
    }
    /* nothing special, loop */
    t++;
  }
  if (polar_name != NULL && polar_filename != NULL) {
    add_polar(polar_name, polar_filename);
  }
  free(buffer);
}

/**
 * finds current speed based on boat location,
 * wind_speed (in kts), wind_angle from boat's heading (in rad)
 * return boat speed (in kts)
 */
double find_speed(boat *aboat, double wind_speed, double wind_angle) {
  int intangle;
  int intspeed;
  double valfloor, valceil;
  double *polar_tab;
#ifdef ROUND_WIND_ANGLE_IN_POLAR
  /* in VLM compatibility mode, we interpolate only speed, not angle
     which is rounded to nearest integer */
  /* not using rint, as rint(0.5) = 0, while PHP round(0.5) = 1 */
  intangle = floor(radToDeg(fabs(fmod(wind_angle, TWO_PI)))+0.5);
  
  if (intangle > 180) {
    intangle = 360 - intangle;
  }
  intspeed  = floor(wind_speed);
  /* nothing set? return 0 */
  if (aboat->polar == NULL) {
    /* check if we can find the polar form the race */
    if (aboat->in_race) {
      if (aboat->in_race->boattype) {
	aboat->polar = aboat->in_race->boattype;
      } else {
	return 0.0;
      }
    } else {
      return 0.0;
    }
  }
  polar_tab = aboat->polar->polar_tab;
  valfloor  = polar_tab[intangle*61+intspeed];
  valceil   = polar_tab[intangle*61+intspeed+1];
#else
  /* higher reolution mode, where bilinear interpolation is performed
     (angle and speed) */
  double tvalfloor, tvalceil, tangle;
  int intangle_p1, intspeed_p1;
  tangle = radToDeg(fabs(fmod(wind_angle, TWO_PI)));
  if (tangle > 180.0) {
    tangle = 360.0 - tangle;
  }
  intangle = (int)floor(tangle);
  /* special case when we reach 180 */
  if (intangle == 180) {
    intangle_p1 = 179;
  } else {
    intangle_p1 = intangle+1;
  }
  intspeed  = floor(wind_speed);
  valfloor  = polar_tab[intangle*61+intspeed];
  tvalfloor = polar_tab[intangle_p1*61+intspeed];
  valfloor += (tvalfloor - valfloor)*(tangle - (double)intangle);
  /* if we reach the limit, return the right value now */
  if (intspeed == 60) {
    return valfloor;
  }
  valceil  = polar_tab[intangle*61+intspeed+1];
  tvalceil = polar_tab[intangle_p1*61+intspeed+1];
  valceil += (tvalceil - valceil)*(tangle - (double)intangle);
#endif /* ROUND_WIND_ANGLE_IN_POLAR */
  /* linear interpolation for wind speed */
  return (valfloor + (valceil-valfloor)*(wind_speed-(double)intspeed));
}


void init_polar() {
  read_polars();
}
