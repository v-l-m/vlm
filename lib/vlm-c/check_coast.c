/**
 * $Id: check_coast.c,v 1.1 2010-10-03 17:09:40 ylafon Exp $
 *
 * (c) 2010 by Yves Lafon
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
#include "lines.h"
#include "context.h"
#include "gshhs.h"
#ifdef SAVE_MEMORY
#  include "dist_gshhs.h"
#endif /* SAVE_MEMORY */

vlmc_context *global_vlmc_context;

#define min(a,b) ((a<b)?a:b)
#define max(a,b) ((a<b)?b:a)

int main(int argc, char **argv) {
  double lat_a, lat_b, long_a, long_b, c_ratio, r_lat, r_long;
  int ilat_min, ilat_max, ilong_min, ilong_max;
  
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

  c_ratio = check_coast(degToRad(lat_a), degToRad(long_a), 
			degToRad(lat_b), degToRad(long_b),
			&r_lat, &r_long);
  if (c_ratio > -1.0) {
    printf("Coast crossed ! ratio= %.5lf\n", c_ratio);
    if (r_long > PI) {
      r_long -= TWO_PI;
    } else if (r_long < -PI) {
      r_long += TWO_PI;
    }
    printf("Encounter Coordinates: %.12lf %.12lf\n", radToDeg(r_lat),
	   radToDeg(r_long));
  } else {
    printf("No coast crossed\n");
  }
}
