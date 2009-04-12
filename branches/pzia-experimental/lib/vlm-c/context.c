/**
 * $Id: context.c,v 1.12 2009-02-11 10:11:20 ylafon Exp $
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
#include <stdlib.h>
#include <string.h>

#include "defs.h"
#include "types.h"

void context_set PARAM2(char **, char *);

void context_set(char **structelem, char *fname) {
  char *c;
  int len;
  if (*structelem) {
    free(*structelem);
    *structelem = NULL;
  }
  if (fname) {
    len = strlen(fname);
    c = malloc((len+1)*sizeof(char));
    if (c) {
      strncpy(c, fname, len);
      *(c+len)=0; 
      *structelem = c;
    } else {
      printf("FATAL: while allocating memory for context initialization\n");
    }
  }
}

void set_grib_filename(vlmc_context *global_vlmc_context, char *fname) {
  context_set(&global_vlmc_context->grib_filename, fname);
}

void set_gshhs_filename(vlmc_context *global_vlmc_context, char *fname) {
  context_set(&global_vlmc_context->gshhs_filename, fname);
}

void set_polar_definition_filename(vlmc_context *global_vlmc_context,
				   char *fname) {
  context_set(&global_vlmc_context->polar_definition_filename, fname);
}

void init_context(vlmc_context *global_vlmc_context) {
  memset(global_vlmc_context, 0, sizeof(vlmc_context));
}

void init_context_default(vlmc_context *global_vlmc_context) {
  init_context(global_vlmc_context);
  set_grib_filename(global_vlmc_context, "latest.grb");
  set_gshhs_filename(global_vlmc_context, "gshhs.b");
  set_polar_definition_filename(global_vlmc_context, "../datas/polars.list");
}

/* return true if all the needed structures are filled */
int is_init_done(vlmc_context *global_vlmc_context) {
  /* we test only wind and polars, it is not mandatory
     to have the coastline filled */
  if ((global_vlmc_context->windtable.wind == NULL) ||
      (global_vlmc_context->polar_list.polars == NULL)) {
    return 0;
  }
  return 1;
}
      
  
