/**
 * $Id: move.c,v 1.3 2008/05/20 17:08:41 ylafon Exp $
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
#include "defs.h"
#include "types.h"
#include "loxo.h"
#include "vmg.h"
#include "ortho.h"

/* vac_duration in seconds */
void move_boat(boat *aboat) {
  move_boat_loxo(aboat);
}

/*
 * move the boat during 'n' vacations 
 * input the boat structure, properly filled
 * return an int, result of boat->landed (false, is still on water)
 */
int move_boat_n_vac(boat *aboat, int nb_vacs) {
  int i;
  
  for (i=0; !aboat->landed && i<nb_vacs; i++) {
    move_boat(aboat);
  }
  return aboat->landed;
}
