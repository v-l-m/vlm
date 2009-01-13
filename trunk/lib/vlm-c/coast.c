/**
 * $Id: coast.c,v 1.10 2009-01-13 06:18:26 ylafon Exp $
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
#include <sys/time.h>
#include <string.h>
#include <stdlib.h>

#include "defs.h"
#include "types.h"
#include "ortho.h"
#include "lines.h"
#include "winds.h"
#include "boat.h"
#include "vmg.h"
#include "loxo.h"
#include "grib.h"
#include "context.h"
#include "optim.h"
#include "gshhs.h"

vlmc_context *global_vlmc_context;

#define min(a,b) ((a<b)?a:b)
#define max(a,b) ((a<b)?b:a)

void print_position_ge(double latitude, double longitude) {
  longitude = radToDeg(longitude);
  if (longitude > 180) {
    longitude -= 360;
  } else if (longitude < -180) {
    longitude += 360;
  }
  latitude = radToDeg(latitude);
  printf("%.12g,%.12g,0 ", longitude, latitude); 
}

int main(int argc, char **argv) {
  double lat_a, lat_b, long_a, long_b;
  int ilat_min, ilat_max, ilong_min, ilong_max;
  int i,j,k, nb_segments, segnum, color, idx;
  coast *wholecoast;
  coast_zone *c_zone;
  coast_seg *seg_array;
  
  global_vlmc_context = calloc(1, sizeof(vlmc_context));
  
  if (argc < 5) {
    printf("%s usage:\n %s <lat,long> <lat,long>\n", *argv, *argv);
    exit(2);
  }
  lat_a  = fmod(atof(argv[1]), 180.0);
  long_a = fmod(atof(argv[2]), 360.0);
  lat_b  = fmod(atof(argv[3]), 180.0);
  long_b = fmod(atof(argv[4]), 360.0);
  
  if (long_a < 0.0) {
    long_a += 360.0;
  }
  if (long_b < 0.0) {
    long_b += 360.0;
  }

  /* sounds strange? yes it is ;) */
  if (fabs(long_b - long_a) > 180.0) {
    if (long_b > long_a) {
      long_b -= 360.0;
    } else {
      long_a -= 360.0;
    }
  }

  ilat_min  = min((int)floor(lat_a*10.0), (int)floor(lat_b*10.0)) + 900;
  ilat_max  = max((int)ceil(lat_a*10.0), (int)ceil(lat_b*10.0)) + 900;
  ilong_min = min((int)floor(long_a*10.0), (int)floor(long_b*10.0));
  ilong_max = max((int)ceil(long_a*10.0), (int)ceil(long_b*10.0));

  init_context_default(global_vlmc_context);
  init_partial_coastline(degToRad((float)ilat_min/10.0 - 90.),degToRad((float)ilong_min/10.0),degToRad((float)ilat_max/10.0-90.),degToRad((float)ilong_max/10.0));
//  init_partial_coastline(degToRad(48.), degToRad(-65.), degToRad(49.), degToRad(-66));

  printf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\
<kml xmlns=\"http://earth.google.com/kml/2.2\">\n\
  <Document>\n\
    <name>VLMC Sample</name>\n\
   <open>1</open>\n\
   <description>Used for debug only</description>\n\
    <Style id=\"fooStyle\">\n\
      <LineStyle>\n\
        <color>7fff00ff</color>\n\
        <width>4</width>\n\
      </LineStyle>\n\
    </Style>\n\
    <Style id=\"barStyle\">\n\
      <LineStyle>\n\
        <color>7f00ffff</color>\n\
        <width>4</width>\n\
      </LineStyle>\n\
    </Style>\n\
    <Folder>\n\
      <name>coast segments</name>\n\
");
  segnum = 0;
  color = 0;
  wholecoast = global_vlmc_context->shoreline;
  for (i=ilong_min; i<=ilong_max; i++) {
    for (j=ilat_min; j<= ilat_max; j++) {
      idx = ((i<0)?i+3600:i)*wholecoast->nb_grid_y+j;
      c_zone=&wholecoast->zone_array[idx];
      nb_segments = c_zone->nb_segments;      
      seg_array = c_zone->seg_array;
      for (k=0; k<nb_segments; k++) {
	printf("\
      <Placemark>\n\
        <name>Segment %d</name>\n\
        <visibility>1</visibility>\n\
        <styleUrl>#%s</styleUrl>\n\
        <LineString>\n\
          <tessellate>1</tessellate>\n\
          <coordinates>\n", segnum++, (color ? "fooStyle" : "barStyle"));
#ifdef SAVE_MEMORY
	print_position_ge(degToRad((double)seg_array->latitude_a * GSHHS_SCL),
			  degToRad((double) seg_array->longitude_a *GSHHS_SCL));
	print_position_ge(degToRad((double)seg_array->latitude_b * GSHHS_SCL),
			  degToRad((double) seg_array->longitude_b *GSHHS_SCL));
#else
	print_position_ge(seg_array->latitude_a, seg_array->longitude_a);
	print_position_ge(seg_array->latitude_b, seg_array->longitude_b);
#endif /* SAVE_MEMORY */
	seg_array++;
	color = !color;
	printf("\n\
          </coordinates>\n\
        </LineString>\n\
      </Placemark>\n");
      }
    }
  }
  printf("\
    </Folder>\n\
  </Document>\n\
</kml>\n");

}
