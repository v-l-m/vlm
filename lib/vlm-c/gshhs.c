/**
 * $Id: gshhs.c,v 1.27 2011-07-03 08:27:04 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *
 * Parts of code Copyright (c) 1996-2007 by P. Wessel and W. H. F. Smith
 * Released under GPLv2 License.
 *
 *      See COPYING file for copying and redistribution conditions.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Contact: <yves@raubacapeu.net>
 */
#include <stdio.h>
#include <string.h>
#include <assert.h>
#include <arpa/inet.h>
#include "defs.h"
#include "types.h"
#include "dist_gshhs.h"
#include "gshhs.h"

/* FIXME
   the structure should be...
   one 1800x3601 array of int, the starting and ending index
   of the segment array (for the record, in full we have
   10482169 segments. -> 159 Mo.
   total should be 184Mo (for the int version)
   way of accessing it:
   get the index from (latdeg+90)*10 long*10, and the subsequent one.
   first index is curseg = (segarray+index0)
   then for i= index0; i<index1; i++
   blahblah
   curseg++
*/

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
  struct POINT *plist, *p;
  int *segnum;
  int i, k, n, px, py, max_n, idx;
#ifdef USE_GSHHS_22
  int nb_read, level, greenwich, dateline, nb_seg;
#else
  int nb_read, level, greenwich, nb_seg;
#endif /* USE_GSHHS_22 */
  int x, y, prev_x, prev_y;
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
  int first_x, first_y;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */

#ifdef SAVE_MEMORY
  int longitude, latitude, prev_longitude, prev_latitude;
# if defined USE_GSHHS_20 || defined USE_GSHHS_22
  int first_longitude, first_latitude;
# endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#else
  double longitude, latitude, prev_longitude, prev_latitude;
# if defined USE_GSHHS_20 || defined USE_GSHHS_22
  double first_longitude, first_latitude;
# endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#endif /* SAVE_MEMORY */
  coast_seg *segment;
  coast *wholecoast;
  int zerocrossed;
  double min_longitude, max_longitude;
  min_longitude = 100000.0;
  max_longitude = -100000.0;
  max_n = 0;
  
  plist = NULL;
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
#ifdef SAVE_MEMORY
  prev_longitude = prev_latitude = 0;
# if defined USE_GSHHS_20 || defined USE_GSHHS_22
  first_longitude = first_latitude = 0;
# endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#else
  prev_longitude = prev_latitude = 0.0;
# if defined USE_GSHHS_20 || defined USE_GSHHS_22
  first_longitude = first_latitude = 0.0;
# endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#endif /* SAVE MEMORY */

#if defined USE_GSHHS_20 || defined USE_GSHHS_22
  first_x = 0;
  first_y = 0;
  x = 0;
  y = 0;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */

  coastfile = fopen(global_vlmc_context->gshhs_filename, "rb");
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

  // FIXME we might want to test the version before proceeding
  // but ntohl takes cares automagically of endianess 
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
  //  flip = (((h.flag >> 8) & 0xff) != GSHHS_DATA_RELEASE);
#else
  //  flip = (((h.flag >> 8) & 0xff) != GSHHS_DATA_VERSION);
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */

  while (nb_read == 1) {
#ifndef OPTIMIZE_GSHHS_READ
    h.id    = ntohl(h.id);
#endif /* OPTIMIZE_GSHHS_READ */
    n       = ntohl(h.n);
    h.flag  = ntohl(h.flag);
#ifndef OPTIMIZE_GSHHS_READ
    h.west  = ntohl(h.west);
    h.east  = ntohl(h.east);
    h.south = ntohl(h.south);
    h.north = ntohl(h.north);
    h.area  = ntohl(h.area);
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
    h.area_full = ntohl(h.area_full);
    h.container = ntohl(h.container);
    h.ancestor  = ntohl(h.ancestor);
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#endif /* OPTIMIZE_GSHHS_READ */
    level = h.flag & 0xff;
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
    /* the previous version should work, but be ready for 
       future releases */
    greenwich = (h.flag >>16) & 0x01;
#ifdef USE_GSHHS_22
    dateline  = (h.flag >>16) & 0x02;
#endif /* USE_GSHHS_22 */
#else
    greenwich = (h.flag >>16) & 0xff;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
    if (level > GSHHS_MAX_DETAILS) { 
      /* keep only land ?, not lake, island in lake, 
	 pond in island in lake => take everything now */
      if (fseek(coastfile, n*sizeof(struct POINT), SEEK_CUR)) {
	printf ("Fatal error reading Error reading file %s\n",
		global_vlmc_context->gshhs_filename);
	exit(2);
      }
    } else {
      prev_x = -1;
      // if the polygon has more points, reallocate
      // but no need to copy
      if (n > max_n) {
	if (max_n) {
	  free(plist);
	}
	plist = malloc(n*sizeof(struct POINT));
	max_n = n;
      }
      // now read all the points from the polygon
      if (fread ((void *)plist, (size_t)sizeof(struct POINT), 
		 (size_t)n, coastfile) != n) {
	printf ("Fatal error reading Error reading file %s\n",
		global_vlmc_context->gshhs_filename);
	exit(2);
      }
      // and iterate on all points
      p = plist;
      for (i=0; i<n; i++) {
	px = ntohl(p->x);
	py = ntohl(p->y);
	x = floor((double)px * GSHHS_SCL * 10.0);
	y = floor((double)py * GSHHS_SCL * 10.0)+900;
#ifdef USE_GSHHS_22
	assert((x>=-1800 && x<=3600) && (y>=0 && y< 1800));
	if (x < 0) {
	  x += 3600;
	}
#else
	assert((x>=0 && x<=3600) && (y>=0 && y< 1800));
#endif /* USE_GSHHS_22 */
	if (prev_x == -1) {
	  prev_x = x;
	  prev_y = y;
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
	  first_x = x;
	  first_y = y;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
	  continue;
	}
#define _increment_array(x1,y1,x2,y2)		\
	if (x1 == x2) {				\
	  if (y1 == y2) {			\
	    segnum[x2*1800+y2]++;		\
	  } else {				\
	    segnum[x2*1800+y2]++;		\
	    segnum[x2*1800+y1]++;		\
	  }					\
	} else {				\
	  if (y1 == y2) {			\
	    segnum[x2*1800+y2]++;		\
	    segnum[x1*1800+y2]++;		\
	  } else {				\
	    segnum[x2*1800+y2]++;		\
	    segnum[x2*1800+y1]++;		\
	    segnum[x1*1800+y2]++;		\
	    segnum[x1*1800+y1]++;		\
	  }					\
	}
	_increment_array(prev_x, prev_y, x, y);
	prev_x = x;
	prev_y = y;
	p++;
      }
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
      if (n > 1) {
	_increment_array(x, y, first_x, first_y);
      }
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
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

#define _allocate_coast_entry(a,b)			\
  idx = a*wholecoast->nb_grid_y+b;			\
  nb_seg =  *(segnum+idx);				\
  if (nb_seg) {						\
    wholecoast->zone_array[idx].nb_segments = nb_seg;	\
    wholecoast->zone_array[idx].seg_array   =		\
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

  coastfile = fopen(global_vlmc_context->gshhs_filename, "rb");
  if (coastfile == NULL) {
    printf("Fatal error trying to read %s\n", 
	   global_vlmc_context->gshhs_filename);
    exit(2);
  }
  
  nb_read = fread ((void *)&h, (size_t)sizeof (struct GSHHS), 
		   (size_t)1, coastfile);
    
  while (nb_read == 1) {
#ifndef OPTIMIZE_GSHHS_READ
    h.id    = ntohl(h.id);
#endif /* OPTIMIZE_GSHHS_READ */
    n       = ntohl(h.n);
    h.flag  = ntohl(h.flag);
#ifndef OPTIMIZE_GSHHS_READ
    h.west  = ntohl(h.west);
    h.east  = ntohl(h.east);
    h.south = ntohl(h.south);
    h.north = ntohl(h.north);
    h.area  = ntohl(h.area);
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
    h.area_full = ntohl(h.area_full);
    h.container = ntohl(h.container);
    h.ancestor  = ntohl(h.ancestor);
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
#endif /* OPTIMIZE_GSHHS_READ */
    level = h.flag & 0xff;
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
    /* the previous version should work, but be ready for 
       future releases */
    greenwich = (h.flag >>16) & 0x01;
#ifdef USE_GSHHS_22
    dateline  = (h.flag >>16) & 0x02;
#endif /* USE_GSHHS_22 */
#else
    greenwich = (h.flag >>16) & 0xff;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
    if (level > GSHHS_MAX_DETAILS) {
      /* keep only land ?, not lake, island in lake, 
	 pond in island in lake => take everything now */
      if (fseek(coastfile, n*sizeof(struct POINT), SEEK_CUR)) {
	printf ("Fatal error reading Error reading file %s\n",
		global_vlmc_context->gshhs_filename);
	exit(2);
      }
    } else {
      prev_x = -1;
      // read all the points from the polygon
      if (fread ((void *)plist, (size_t)sizeof(struct POINT), 
		 (size_t)n, coastfile) != n) {
	printf ("Fatal error reading Error reading file %s\n",
		global_vlmc_context->gshhs_filename);
	exit(2);
      }
      // and iterate on all points
      p = plist;
      for (i=0; i<n; i++) {
	px = ntohl(p->x);
	py = ntohl(p->y);
	x = floor((double)px * GSHHS_SCL * 10.0);
	y = floor((double)py * GSHHS_SCL * 10.0)+900;
#ifdef SAVE_MEMORY
	longitude = px;
	latitude  = py;
#else
	longitude = degToRad((double)px * GSHHS_SCL);
	latitude  = degToRad((double)py * GSHHS_SCL);
#endif /* SAVE_MEMORY */
#ifdef USE_GSHHS_22
	assert((x>=-1800 && x<=3600) && (y>=0 && y< 1800));
	if (x < 0) {
	  x += 3600;
	}
#else
	assert((x>=0 && x<=3600) && (y>=0 && y< 1800));
#endif /* USE_GSHHS_22 */
	if (prev_x == -1) {
	  prev_x = x;
	  prev_y = y;
	  prev_longitude = longitude;
	  prev_latitude  = latitude;
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
	  first_x = x;
	  first_y = y;
	  first_longitude = longitude;
	  first_latitude = latitude;
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
	  continue;
	}

#define _add_segment(a,b)					\
	idx = a*wholecoast->nb_grid_y+b;			\
	if (wholecoast->zone_array[idx].nb_segments) {		\
          k = --*(segnum+idx);					\
	  segment = &wholecoast->zone_array[idx].seg_array[k];	\
	  segment->longitude_a = prev_longitude;		\
	  segment->longitude_b = longitude;			\
	  segment->latitude_a = prev_latitude;			\
	  segment->latitude_b = latitude;			\
	}

#define _add_segment_in_array(x1, y1, x2, y2)	\
	if (x1 == x2) {				\
	  if (y1 == y2) {			\
	    _add_segment(x2,y2);		\
	  } else {				\
	    _add_segment(x2,y2);		\
	    _add_segment(x2,y1);		\
	  }					\
	} else {				\
	  if (y1 == y2) {			\
	    _add_segment(x2,y2);		\
	    _add_segment(x1,y2);		\
	  } else {				\
	    _add_segment(x2,y2);		\
	    _add_segment(x2,y1);		\
	    _add_segment(x1,y2);		\
	    _add_segment(x1,y1);		\
	  }					\
	}

	_add_segment_in_array(prev_x, prev_y, x, y);

	prev_x = x;
	prev_y = y;
	prev_longitude = longitude;
	prev_latitude = latitude;
	p++;
      }
#if defined USE_GSHHS_20 || defined USE_GSHHS_22
      if (n>1) {
	// add the last one
	longitude = first_longitude;
	latitude  = first_latitude;
	_add_segment_in_array(first_x, first_y, x, y);
      }
#endif /* USE_GSHHS_20 || USE_GSHHS_22 */
    }
    nb_read = fread((void *)&h, (size_t)sizeof (struct GSHHS), (size_t)1, 
		    coastfile);
  }
  free(plist);
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
