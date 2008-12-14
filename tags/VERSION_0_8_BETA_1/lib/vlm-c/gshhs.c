/**
 * $Id: gshhs.c,v 1.12 2008/08/11 12:59:33 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *
 * Parts of code Copyright (c) 1996-2007 by P. Wessel and W. H. F. Smith
 * Released under GPLv2 License.
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
#include <stdio.h>
#include <string.h>
#include <assert.h>
#include "defs.h"
#include "types.h"
#include "dist_gshhs.h"
#include "gshhs.h"


extern vlmc_context *global_vlmc_context;

void internal_init_partial_coastline PARAM4(int, int, int ,int);

void init_coastline() {
  internal_init_partial_coastline(0,0,1799,3600);
}

/* it is important to get the order right when calling, as rounding will
   take place parameters in radians */
void init_partial_coastline(double minlat, double minlong, 
			    double maxlat, double maxlong) {
  int iminlat, imaxlat, iminlong, imaxlong;

  minlong = fmod(minlong, TWO_PI);
  maxlong = fmod(maxlong, TWO_PI);
  if (minlong < 0) {
    minlong += TWO_PI;
  }
  if (maxlong < 0) {
    maxlong += TWO_PI;
  }
  iminlat  = (int)floor(10.0*radToDeg(minlat)) + 900;
  imaxlat  = (int)ceil(10.0*radToDeg(maxlat)) + 900;
  iminlong = (int)floor(10.0*radToDeg(minlong));
  imaxlong = (int)ceil(10.0*radToDeg(maxlong));
  /* extra sanity checks */
  if (imaxlat>1799) {
    imaxlat = 1799;
  }
  if (imaxlong>3600) {
    imaxlong = 3600;
  }
  internal_init_partial_coastline(iminlat, iminlong, imaxlat, imaxlong);
}
			    
void internal_init_partial_coastline(int minlat, int minlong,
				     int maxlat, int maxlong) {
  FILE *coastfile;
  struct GSHHS h;
  struct POINT p;
  int *segnum;
  int nb_read, level, greenwich,i,k, nb_seg, idx;
  int x,y, prev_x, prev_y;
  double longitude, latitude, prev_longitude, prev_latitude;
  coast_seg *segment;
  coast *wholecoast;
  int flip, zerocrossed;
  double min_longitude, max_longitude;
  min_longitude = 100000.0;
  max_longitude = -100000.0;

  segnum = calloc(3601*1800, sizeof(int)); /* FIXME resolution + min/max lat */
  
  zerocrossed = 0;
  /* sanity check on given values */
  if (minlat < 0) {
    minlat = 0;
  }
  if (maxlat > 1799) {
    maxlat = 1799;
  }
  minlong = minlong % 3601;
  if (minlong < 0 ) {
    minlong += 3600;
  }
  minlong = minlong % 3601;
  if (maxlong < 0 ) {
    maxlong += 3600;
  }

  if (minlat > maxlat) {
    /* latitude in wrong orders... reset everything */
    minlat  = 0;
    maxlat  = 1799;
    minlong = 0;
    maxlong = 3600;
    zerocrossed = 0;
  }

  if (minlong > maxlong) {
    zerocrossed = 1;
  }

  /* to make the compiler happy */
  prev_y = 0;
  prev_longitude = prev_latitude = 0.0;
  
  coastfile = fopen(global_vlmc_context->gshhs_filename, "r");
  if (coastfile == NULL) {
    printf("Fatal error trying to read %s\n", 
	   global_vlmc_context->gshhs_filename);
    exit(2);
  }
  /* we read the file twice: once for finding the number of segments per
     0.1 deg square, then alloc the structures, and another time to fill
     the structure. this is to avoid building linked lists that are slower
     to traverse during the processing of the boats */

  nb_read = fread ((void *)&h, (size_t)sizeof (struct GSHHS), 
		   (size_t)1, coastfile);
  flip = (((h.flag >> 8) & 0xff) != GSHHS_DATA_VERSION);

  while (nb_read == 1) {
    if (flip) {
      h.id = swabi4 ((unsigned int)h.id);
      h.n  = swabi4 ((unsigned int)h.n);
      h.west  = swabi4 ((unsigned int)h.west);
      h.east  = swabi4 ((unsigned int)h.east);
      h.south = swabi4 ((unsigned int)h.south);
      h.north = swabi4 ((unsigned int)h.north);
      h.area  = swabi4 ((unsigned int)h.area);
      h.flag  = swabi4 ((unsigned int)h.flag);
    }
    level = h.flag & 0xff;
    greenwich = (h.flag >>16) & 0xff;
    if (level > GSHHS_MAX_DETAILS) { 
      /* keep only land ?, not lake, island in lake, 
	 pond in island in lake => take everything now */
      for (i=0; i<h.n; i++) {
	if (fread ((void *)&p, (size_t)sizeof(struct POINT), 
		   (size_t)1, coastfile) != 1) {
	  printf ("Fatal error reading Error reading file %s\n",
		  global_vlmc_context->gshhs_filename);
	  exit(2);
	}
      }
    } else {
      prev_x = -1;
      for (i=0; i<h.n; i++) {
	if (fread ((void *)&p, (size_t)sizeof(struct POINT), 
		   (size_t)1, coastfile) != 1) {
	  printf ("Fatal error reading Error reading file %s\n",
		  global_vlmc_context->gshhs_filename);
	  exit(2);
	}
	if (flip) {
	  p.x = swabi4 ((unsigned int)p.x);
	  p.y = swabi4 ((unsigned int)p.y);
	}
	x = floor((double)p.x * GSHHS_SCL * 10.0);
	y = floor((double)p.y * GSHHS_SCL * 10.0)+900;
	assert((x>=0 && x<=3600) && (y>=0 && y< 1800));
	if (prev_x == -1) {
	  prev_x = x;
	  prev_y = y;
	  continue;
	}
	if (prev_x == x) {
	  if (prev_y == y) {
	    segnum[x*1800+y]++;
	  } else {
	    segnum[x*1800+y]++;
	    segnum[x*1800+prev_y]++;
	  } 
	} else {
	  if (prev_y == y) {
	    segnum[x*1800+y]++;
	    segnum[prev_x*1800+y]++;
	  } else {
	    segnum[x*1800+y]++;
	    segnum[x*1800+prev_y]++;
	    segnum[prev_x*1800+y]++;
	    segnum[prev_x*1800+prev_y]++;
	  }
	}
	prev_x = x;
	prev_y = y;
      }
    }
    nb_read = fread((void *)&h, (size_t)sizeof (struct GSHHS), (size_t)1, 
		    coastfile);
  }
  fclose (coastfile);

  /* now allocate the structures */
  if (!global_vlmc_context->shoreline) {
    wholecoast = calloc(1, sizeof(coast));
    wholecoast->nb_grid_x  = 3601; /* FIXME use resolution (+min/max?) */
    wholecoast->nb_grid_y  = 1800;
    wholecoast->zone_array = calloc(wholecoast->nb_grid_x*wholecoast->nb_grid_y,
				    sizeof(coast_zone));
    global_vlmc_context->shoreline = wholecoast;
  } else {
    wholecoast = global_vlmc_context->shoreline;
    memset(wholecoast->zone_array, 0, 
	   wholecoast->nb_grid_x*wholecoast->nb_grid_y*sizeof(coast_zone));
  }

#define _allocate_coast_entry(a,b)					\
  idx = a*wholecoast->nb_grid_y+b;					\
  nb_seg =  *(segnum+idx);						\
  if (nb_seg) {								\
    wholecoast->zone_array[idx].nb_segments = nb_seg;			\
    wholecoast->zone_array[idx].seg_array   =				\
                        calloc(nb_seg, sizeof(struct coast_seg_str));  	\
  }

  /* now allocate the structures */
  if (zerocrossed) {
    for (y=minlat; y<=maxlat; y++) {
      for (x=minlong; x<3601; x++) {
	_allocate_coast_entry(x,y);
      }
      for (x=0; x<=maxlong; x++) {
	_allocate_coast_entry(x,y);
      }
    }
  } else {
    for (x=minlong; x<=maxlong; x++) {
      for (y=minlat; y<=maxlat; y++) {
	_allocate_coast_entry(x,y);
      }
    }
  }

  coastfile = fopen(global_vlmc_context->gshhs_filename, "r");
  if (coastfile == NULL) {
    printf("Fatal error trying to read %s\n", 
	   global_vlmc_context->gshhs_filename);
    exit(2);
  }
  
  nb_read = fread ((void *)&h, (size_t)sizeof (struct GSHHS), 
		   (size_t)1, coastfile);
    
  while (nb_read == 1) {
    if (flip) {
      h.id = swabi4 ((unsigned int)h.id);
      h.n  = swabi4 ((unsigned int)h.n);
      h.west  = swabi4 ((unsigned int)h.west);
      h.east  = swabi4 ((unsigned int)h.east);
      h.south = swabi4 ((unsigned int)h.south);
      h.north = swabi4 ((unsigned int)h.north);
      h.area  = swabi4 ((unsigned int)h.area);
      h.flag  = swabi4 ((unsigned int)h.flag);
    }
    level = h.flag & 0xff;
    greenwich = (h.flag >>16) & 0xff;
    if (level > GSHHS_MAX_DETAILS) {
      /* keep only land ?, not lake, island in lake, 
	 pond in island in lake => take everything now */
      for (i=0; i<h.n; i++) {
	if (fread ((void *)&p, (size_t)sizeof(struct POINT), 
		   (size_t)1, coastfile) != 1) {
	  printf ("Fatal error reading Error reading file %s\n",
		  global_vlmc_context->gshhs_filename);
	  exit(2);
	}
      }
    } else {
      prev_x = -1;
      for (i=0; i<h.n; i++) {
	if (fread ((void *)&p, (size_t)sizeof(struct POINT), 
		   (size_t)1, coastfile) != 1) {
	  printf ("Fatal error reading Error reading file %s\n",
		  global_vlmc_context->gshhs_filename);
	  exit(2);
	}
	if (flip) {
	  p.x = swabi4 ((unsigned int)p.x);
	  p.y = swabi4 ((unsigned int)p.y);
	}
	x = floor((double)p.x * GSHHS_SCL * 10.0);
	y = floor((double)p.y * GSHHS_SCL * 10.0)+900;
	longitude = degToRad((double)p.x * GSHHS_SCL);
	latitude = degToRad((double)p.y * GSHHS_SCL);
	assert((x>=0 && x<=3600) && (y>=0 && y< 1800));
	if (prev_x == -1) {
	  prev_x = x;
	  prev_y = y;
	  prev_longitude = longitude;
	  prev_latitude = latitude;
	  continue;
	}

#define _add_segment(a,b)                                               \
	idx = a*wholecoast->nb_grid_y+b;				\
	if (wholecoast->zone_array[idx].nb_segments) {			\
          k = --*(segnum+idx);						\
	  segment = &wholecoast->zone_array[idx].seg_array[k];		\
	  segment->longitude_a = prev_longitude;			\
	  segment->longitude_b = longitude;				\
	  segment->latitude_a = prev_latitude;				\
	  segment->latitude_b = latitude;				\
	}

	if (prev_x == x) {
	  if (prev_y == y) {
	    _add_segment(x,y);
	  } else {
	    _add_segment(x,y);
	    _add_segment(x, prev_y);
	  } 
	} else {
	  if (prev_y == y) {
	    _add_segment(x,y);
	    _add_segment(prev_x,y);
	  } else {
	    _add_segment(x,y);
	    _add_segment(x,prev_y);
	    _add_segment(prev_x,y);
	    _add_segment(prev_x,prev_y);
	  }
	}
	prev_x = x;
	prev_y = y;
	prev_longitude = longitude;
	prev_latitude = latitude;
      }
    }
    nb_read = fread((void *)&h, (size_t)sizeof (struct GSHHS), (size_t)1, 
		    coastfile);
  }
  free(segnum);
  fclose (coastfile);
}

void free_gshhs() {
  int i,j, maxi,maxj, idx;
  coast_seg *cs;
  coast *wholecoast;

  wholecoast = global_vlmc_context->shoreline;
  maxi = wholecoast->nb_grid_x;
  maxj = wholecoast->nb_grid_y;
  /* free the allocated arrays of coast_seg, then set everything to 0 */
  for (i=0; i<maxi; i++) {
    for (j=0; j<maxj; j++) {
      idx = i*maxj+j;
      cs = wholecoast->zone_array[idx].seg_array;
      if (cs) {
	free(cs);
      }
    }
  }
  free(wholecoast->zone_array);
  wholecoast->zone_array = NULL;
}

/* Helper for the bindings
   Return a coast_zone struct for a given square
   */
coast_zone *get_coastzone(int i, int j) {
  coast *wholecoast;

  wholecoast = global_vlmc_context->shoreline;
  return &(wholecoast->zone_array[i*wholecoast->nb_grid_y+j]);
}

/* Helper for the bindings
   return a segment struct for a given coast_zone and index 
   */
coast_seg *get_coastseg(coast_zone *cz, int idx) {
    coast_seg *cs;
    cs = &(cz->seg_array[idx]);
    return cs;
}
