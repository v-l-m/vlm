/**
 * $Id: polarserver.c,v 1.5 2010-12-09 13:54:26 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
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
#include <stdlib.h>
#include <string.h>
#include <sys/shm.h>
#include <sys/sem.h>

#include "defs.h"
#include "types.h"
#include "polar.h"
#include "context.h"
#include "shmem.h"
#include "useshmem.h"

vlmc_context *global_vlmc_context;

void usage(char *argv0) {
  printf("Usage: %s <polar list filename>\n", argv0);
  exit(1);
}

int main(int argc, char **argv) {

  global_vlmc_context = calloc(1, sizeof(vlmc_context));
  init_context_default(global_vlmc_context);

  if (argc != 2) {
    usage(*argv);
  }
  
  set_polar_definition_filename(global_vlmc_context, argv[1]);
  init_polar();
  
  create_and_fill_polar_shm();

  printf("Polar segment successfully updated\n");
  return 0;
}
