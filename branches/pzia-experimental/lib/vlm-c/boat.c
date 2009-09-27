/**
 * $Id: boat.c,v 1.5 2009-08-31 11:39:28 ylafon Exp $
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

#include <string.h>
#include <stdlib.h>

#include "types.h"
#include "loxo.h"
#include "boat.h"
#include "polar.h"

extern vlmc_context *global_vlmc_context;

void associate_polar_boat(boat *aboat, char *polar_name) {
  associate_polar_boat_context(global_vlmc_context, aboat, polar_name);
}

/* associate a polar by its name to a boat */
void associate_polar_boat_context(vlmc_context *context, boat *aboat, 
			  char *polar_name) {
  boat_polar *p;
  p = get_polar_by_name_context(context, polar_name);
  
  if (p != NULL) {
    aboat->polar = p;
  }
}

boat *init_boat(boat *aboat, int num, char *name, 
		double latitude, double longitude, double heading) {
  aboat->num = num;
  aboat->name = (char *)calloc(strlen(name)+1, sizeof(char));
  strcpy(aboat->name, name);
  aboat->latitude = latitude;
  aboat->longitude = longitude;
  aboat->heading = heading;
  aboat->set_heading_func = &set_heading_loxo;
  /* FIXME fill this as well */
  aboat->loch = 0.0;
  aboat->wp_latitude = 0.0;
  aboat->wp_longitude = 0.0;
  aboat->wp_distance = 0.0;
  /* FIXME : initialise the polar ??? */
  return aboat;
}

boat *set_wp(boat *aboat, double latitude, double longitude, double heading) {
  aboat->wp_latitude = latitude;
  aboat->wp_longitude = longitude;
  aboat->wp_heading = heading;
  return aboat;
}
