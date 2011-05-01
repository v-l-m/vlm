/**
 * $Id: windserver.c,v 1.10 2010-12-09 13:54:27 ylafon Exp $
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
#include "winds.h"
#include "grib.h"
#include "context.h"
#include "shmem.h"

vlmc_context *global_vlmc_context;


void usage(char *argv0) {
  printf("Usage: %s [-merge] [-purge] <grib filename>\n", argv0);
  exit(1);
}

int main(int argc, char **argv) {
  int merge, interp, purge, i, gotfile;
  int shmid, semid;
  void *segmaddr;
  struct sembuf sem_op[2];
  
  /* TODO add options like -merge -interp -replace */
  global_vlmc_context = calloc(1, sizeof(vlmc_context));
  init_context_default(global_vlmc_context);

  if (argc == 1) {
    usage(*argv);
  }
  
  gotfile = merge = interp = purge = 0;
  for (i=1; i<argc; i++) {
    if (!strncmp(argv[i], "-merge", 7)) {
      merge = 1;
      continue;
    }
    if (!strncmp(argv[i], "-purge", 7)) {
      purge = 1;
      continue;
    }
    /* FIXME, should we consume another token (time_t) to ensure replication
       on different servers? UNUSED FOR NOW */
    if (!strncmp(argv[i], "-interp", 8)) {
      interp = 1;
      continue;
    }
    /* unknown option */
    if (*argv[i] == '-') {
      usage(argv[0]);
    }
    set_grib_filename(global_vlmc_context, argv[i]);
    break;
  }

  shmid = -1;
  segmaddr = NULL;
  /* first we read the grib before locking things */
  if (merge) {
    /* first we need to read the grib from the segment */
    /* no need to lock as we are the one to lock it when doing the update */
    shmid = get_grib_shmid(0);
    if (shmid == -1) {
      fprintf(stderr, "Can't attach segment, impossible to merge data\n");
    } else {
      segmaddr = get_shmem(shmid, 0);
      allocate_grib_array_from_shmem(&global_vlmc_context->windtable, segmaddr);
      merge_gribs(purge);
    }
  } 
  
  if (!segmaddr) { /* no merge, or failed one */
    init_grib();
    if (purge) {
      purge_gribs();
    }
  }

  if (!global_vlmc_context->windtable.nb_prevs) {
    fprintf(stderr, "Invalid GRIB entry\n");
    exit(1);
  }

  semid = get_semaphore_id();
  if (semid == -1) {
    semid = create_semaphore();
    if (semid == -1) {
      fprintf(stderr, "Unable to create the semaphore\n");
      exit(1);
    }
  }
  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = 0;
  sem_op[0].sem_flg = SEM_UNDO;
  sem_op[1].sem_num = 0;
  sem_op[1].sem_op  = 1;
  sem_op[1].sem_flg = SEM_UNDO|IPC_NOWAIT;
  if (semop(semid, sem_op, 2) == -1) {
    fprintf(stderr, "Fail to lock the semaphore\n");
    exit(1);
  }
  
  if (shmid == -1) { /* uninitialized ? (we might have got it already) */
    shmid = get_grib_shmid(0);
  }
  if (shmid == -1) {
    /* not there, we create it */
    shmid = create_grib_shmid(&global_vlmc_context->windtable);
    if (shmid == -1) {
      fprintf(stderr, "Fail to create the GRIB memory segment\n");
      exit(1);
    }
  }

  /* copy the grib */
  if (!segmaddr) { /* did we got it from a merge ? */
    segmaddr = get_shmem(shmid, 0);
  }
  copy_grib_array_to_shmem(shmid, &global_vlmc_context->windtable, segmaddr);
  shmdt(segmaddr);

  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = -1;
  sem_op[0].sem_flg = SEM_UNDO|IPC_NOWAIT;
  if (semop(semid, sem_op, 1) == -1) {
    fprintf(stderr, "Fail to unlock the semaphore\n");
    exit(1);
  }
  printf("Grib segment successfully updated\n");
  return 0;
}
