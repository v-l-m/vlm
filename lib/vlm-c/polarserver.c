/**
 * $Id: polarserver.c,v 1.1 2009-08-25 14:48:47 ylafon Exp $
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
#include <sys/shm.h>
#include <sys/sem.h>

#include "defs.h"
#include "types.h"
#include "polar.h"
#include "context.h"
#include "shmem.h"

vlmc_context *global_vlmc_context;


void usage(char *argv0) {
  printf("Usage: %s <polar list filename>\n", argv0);
  exit(1);
}

int main(int argc, char **argv) {
  int shmid, semid;
  void *segmaddr;
  struct sembuf sem_op[2];
  
  /* TODO add options like -merge -interp -replace */
  global_vlmc_context = calloc(1, sizeof(vlmc_context));
  init_context_default(global_vlmc_context);

  if (argc != 2) {
    usage(*argv);
  }
  
  set_polar_definition_filename(global_vlmc_context, argv[1]);
  init_polar();
  
  if ( global_vlmc_context->polar_list.nb_polars == 0) {
    printf("Polar initialization failed\n");
    exit(-1);
  }

  shmid = -1;
  semid = -1;
  segmaddr = NULL;

  /* 
   *  lock it (we use the same semaphore as the grib, but it's not a big issue
   */
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
    shmid = get_polar_shmid(0);
  }
  if (shmid == -1) {
    /* not there, we create it */
    shmid = create_polar_shmid(&global_vlmc_context->polar_list);
    if (shmid == -1) {
      fprintf(stderr, "Fail to create the Polar memory segment\n");
      exit(1);
    }
  }

  /* copy the grib */
  if (!segmaddr) { /* did we got it from a merge ? */
    segmaddr = get_shmem(shmid, 0);
  }
  copy_polar_array_to_shmem(shmid, &global_vlmc_context->polar_list, segmaddr);
  shmdt(segmaddr);

  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = -1;
  sem_op[0].sem_flg = SEM_UNDO|IPC_NOWAIT;
  if (semop(semid, sem_op, 1) == -1) {
    fprintf(stderr, "Fail to unlock the semaphore\n");
    exit(1);
  }
  printf("Polar segment successfully updated\n");
  return 0;
}
