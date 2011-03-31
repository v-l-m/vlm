/**
 * $Id$
 *
 * (c) 2010 by Yves Lafon
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
#include <stdlib.h>
#include <string.h>
#include <arpa/inet.h>

#define GSHHS_MAX_DETAILS 3
#define GSHHS_SCL 1.0e-6
#ifdef USE_GSHHS_20
struct GSHHS {  /* Global Self-consistent Hierarchical High-resolution 
		   Shorelines */
  int id;         /* Unique polygon id number, starting at 0 */
  int n;          /* Number of points in this polygon */
  int flag;       /* = level + version << 8 + 
		     greenwich << 16 + source << 24 + river << 25 */
  /* flag contains 5 items, as follows:
   * low byte:    level = flag & 255: Values: 1 land, 2 lake, 
   3 island_in_lake, 4 pond_in_island_in_lake
   * 2nd byte:    version = (flag >> 8) & 255: Values: 
   Should be 7 for GSHHS release 7
   * 3rd byte:    greenwich = (flag >> 16) & 1: Values: Greenwich is 1 
   if Greenwich is crossed
   * 4th byte:    source = (flag >> 24) & 1: Values: 0 = CIA WDBII, 
   1 = WVS
   * 4th byte:    river = (flag >> 25) & 1: Values: 0 = not set, 
   1 = river-lake and level = 2
  */
  int west, east, south, north;   /* min/max extent in micro-degrees */
  int area;       /* Area of polygon in 1/10 km^2 */
  int area_full;  /* Area of original full-resolution polygon in 
		     1/10 km^2 */
  int container;  /* Id of container polygon that encloses this 
		     polygon (-1 if none) */
  int ancestor;   /* Id of ancestor polygon in the full resolution 
		     set that was the source of this polygon 
		     (-1 if none) */
};
#else /* USE_GSHHS_20 */
struct GSHHS {	/* Global Self-consistent Hierarchical High-resolution 
		   Shorelines */
  int id;				/* Unique polygon id number, 
					   starting at 0 */
  int n;				/* Number of points in this polygon */
  int flag;			/* = level + version << 8 + 
				   greenwich << 16 + source << 24 */
  /* flag contains 4 items, one in each byte, as follows:
   * low byte:	level = flag & 255: Values: 1 land, 2 lake, 
   3 island_in_lake, 4 pond_in_island_in_lake
   * 2nd byte:	version = (flag >> 8) & 255: Values: 
   Should be 4 for GSHHS version 1.4
   * 3rd byte:	greenwich = (flag >> 16) & 255: Values: 
   Greenwich is 1 if Greenwich is crossed
   * 4th byte:	source = (flag >> 24) & 255: Values: 0 = CIA WDBII, 
   1 = WVS
  */
  int west, east, south, north;	/* min/max extent in micro-degrees */
  int area;			/* Area of polygon in 1/10 km^2 */
};
#endif /* USE_GSHHS_20 */

struct	POINT {	/* Each lon, lat pair is stored in micro-degrees in 
		   4-byte integer format */
  int	x;
  int	y;
};

int main (int argc, char **argv) {
  FILE *coastfile;
  struct GSHHS poly;
  struct POINT *plist, *p;
  int level, greenwich;
  int n, max_n, i;
  int px, py;
  double latitude, longitude;
  unsigned long idpoint;
  int nb_read;

  if (argc != 2) {
    printf("Usage: %s <coastfile>\n", argv[0]);
    exit (-1);
  }
  coastfile = fopen(argv[1], "r");
  if (coastfile == NULL) {
    printf("Fatal error trying to read %s\n", argv[1]);
    exit(-1);
  }
  
  max_n = 0;
  plist = NULL;
  idpoint = 1;
  
  nb_read = fread ((void *)&poly, (size_t)sizeof (struct GSHHS), 
		   (size_t)1, coastfile);
  
  while (nb_read == 1) {
    poly.id     = ntohl(poly.id);
    n           = ntohl(poly.n);
    poly.flag      = ntohl(poly.flag);
    poly.west      = ntohl(poly.west);
    poly.east      = ntohl(poly.east);
    poly.south     = ntohl(poly.south);
    poly.north     = ntohl(poly.north);
    poly.area      = ntohl(poly.area);
#ifdef USE_GSHHS_20
    poly.area_full = ntohl(poly.area_full);
    poly.container = ntohl(poly.container);
    poly.ancestor  = ntohl(poly.ancestor);
#endif /* USE_GSHHS_20 */

    level     = poly.flag & 0xff;
#ifdef USE_GSHHS_20
    /* the previous version should work, but be ready for 
       future releases */
    greenwich = (poly.flag >>16) & 0x01;
#else
    greenwich = (poly.flag >>16) & 0xff;
#endif /* USE_GSHHS_20 */
    if (level > GSHHS_MAX_DETAILS) { 
      /* keep only land ?, not lake, island in lake, 
	 pond in island in lake => take everything now */
      if (fseek(coastfile, n*sizeof(struct POINT), SEEK_CUR)) {
	printf ("Fatal error reading Error reading file %s\n",
		argv[1]);
	exit(-1);
      }
    } else {
      if (n > max_n) {
	if (max_n) {
	  free(plist);
	}
	plist = malloc(n*sizeof(struct POINT));
	max_n = n;
      }
      /* now read all the points from the polygon */
      if (fread ((void *)plist, (size_t)sizeof(struct POINT), 
		 (size_t)n, coastfile) != n) {
	printf ("Fatal error reading Error reading file %s\n", argv[1]);
	exit(2);
      }
      p = plist;
      /* and iterate on all points */
      for (i=0; i<n; i++) {
	px = ntohl(p->x);
	py = ntohl(p->y);
	longitude = ((double)px * GSHHS_SCL);
	// in the db we don't need to keep the continuity, as
	// the php code will take care of that if needed.
	// so stick strictly to -180 / +180
	if (longitude > 180.0) {
	  longitude -= 360.0;
	}
	latitude  = ((double)py * GSHHS_SCL);
	printf("%lu,%u,%.7g,%.7g\n",  idpoint++, poly.id, 
	       latitude, longitude);
	p++;
      }
    }
      nb_read = fread ((void *)&poly, (size_t)sizeof (struct GSHHS), 
		       (size_t)1, coastfile);
  }
  exit(1);
}

	
