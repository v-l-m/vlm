/**
 * $Id: useshmem.c,v 1.6 2009-08-26 14:54:32 ylafon Exp $
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
#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/shm.h>
#include <sys/sem.h>

#include "defs.h"
#include "types.h"
#include "shmem.h"
#include "winds.h"
#include "useshmem.h"

extern vlmc_context *global_vlmc_context;

void shm_safe_get_wind_info_lat_long(double latitude, double longitude, 
				     time_t when, wind_info *windinfos) {
  shm_lock_sem_construct_grib(1);
  get_wind_info_latlong(latitude, longitude, when, windinfos);
  shm_unlock_sem_destroy_grib(1);
}

void shm_get_wind_info_lat_long(double latitude, double longitude, 
				time_t when, wind_info *windinfos) {
  shm_lock_sem_construct_grib(0);
  get_wind_info_latlong(latitude, longitude, when, windinfos);
  shm_unlock_sem_destroy_grib(0);
}

void shm_lock_sem_construct_grib(int do_construct) {
  int shmid, nbops;
  int *semid;
  void **segmaddr;
  struct sembuf sem_op[2];

  semid    = &global_vlmc_context->semid;
  segmaddr = &global_vlmc_context->grib_segmaddr;

  *semid   = get_semaphore_id();
  if (*semid == -1) {
    fprintf(stderr, "Unable to get the semaphore\n");
    exit(1);
  }
  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = 0;
#ifdef SAFE_SHM_READ
  sem_op[0].sem_flg = SEM_UNDO;
  sem_op[1].sem_num = 0;
  sem_op[1].sem_op  = 1;
  sem_op[1].sem_flg = SEM_UNDO|IPC_NOWAIT;
  nbops = 2;
#else
  sem_op[0].sem_flg = 0;
  nbops = 1;
#endif /* SAFE_SHM_READ */
  if (semop(*semid, sem_op, nbops) == -1) {
    fprintf(stderr, "Fail to lock the semaphore\n");
    exit(1);
  }

  if (do_construct) {
    shmid =  get_grib_shmid(1);
    if (shmid == -1) {
      /* not there, we create it */
      fprintf(stderr, "Cannot find GRIB shared segment\n");
      exit(1);
    }
    
    *segmaddr = get_shmem(shmid, 1);
    if (*segmaddr) {
      construct_grib_array_from_shmem(&global_vlmc_context->windtable,
				      *segmaddr);
    }
  }
}

void shm_unlock_sem_destroy_grib(int do_destroy) {
#ifdef SAFE_SHM_READ
  struct sembuf sem_op[2];
  int semid;
#endif /* SAFE_SHM_READ */
  winds_prev *windtable;
  void *segmaddr;

  if (do_destroy) {
    windtable = &global_vlmc_context->windtable;
    free(windtable->wind);
    windtable->wind = NULL;
    
    segmaddr = global_vlmc_context->grib_segmaddr;
    shmdt(segmaddr);
  }
#ifdef SAFE_SHM_READ
  semid    = global_vlmc_context->semid;
  /* and release the semaphore */
  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = -1;
  sem_op[0].sem_flg = SEM_UNDO|IPC_NOWAIT;
  if (semop(semid, sem_op, 1) == -1) {
    fprintf(stderr, "Fail to unlock the semaphore\n");
    exit(1);
  }
#endif /* SAFE_SHM_READ */
}

void shm_lock_sem_construct_polar(int do_construct) {
  int shmid, nbops;
  int *semid;
  void **segmaddr;
  struct sembuf sem_op[2];

  semid    = &global_vlmc_context->semid;
  segmaddr = &global_vlmc_context->polar_segmaddr;

  *semid   = get_semaphore_id();
  if (*semid == -1) {
    fprintf(stderr, "Unable to get the semaphore\n");
    exit(1);
  }
  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = 0;
#ifdef SAFE_SHM_READ
  sem_op[0].sem_flg = SEM_UNDO;
  sem_op[1].sem_num = 0;
  sem_op[1].sem_op  = 1;
  sem_op[1].sem_flg = SEM_UNDO|IPC_NOWAIT;
  nbops = 2;
#else
  sem_op[0].sem_flg = 0;
  nbops = 1;
#endif /* SAFE_SHM_READ */
  if (semop(*semid, sem_op, nbops) == -1) {
    fprintf(stderr, "Fail to lock the semaphore\n");
    exit(1);
  }

  if (do_construct) {
    shmid =  get_polar_shmid(1);
    if (shmid == -1) {
      /* not there, we create it */
      fprintf(stderr, "Cannot find Polar shared segment\n");
      exit(1);
    }
    
    *segmaddr = get_shmem(shmid, 1);
    if (*segmaddr) {
      construct_polar_array_from_shmem(&global_vlmc_context->polar_list,
				       *segmaddr);
    }
  }
}

void shm_unlock_sem_destroy_polar(int do_destroy) {
#ifdef SAFE_SHM_READ
  struct sembuf sem_op[2];
  int semid;
#endif /* SAFE_SHM_READ */
  int i;
  boat_polar_list *polars;
  void *segmaddr;

  if (do_destroy) {
    polars = &global_vlmc_context->polar_list;
    for (i=0; i<polars->nb_polars; i++) {
      free(polars->polars[i]);
    }
    free(polars->polars);
    polars->polars = NULL;
    
    segmaddr = global_vlmc_context->polar_segmaddr;
    shmdt(segmaddr);
  }
#ifdef SAFE_SHM_READ
  semid    = global_vlmc_context->semid;
  /* and release the semaphore */
  sem_op[0].sem_num = 0;
  sem_op[0].sem_op  = -1;
  sem_op[0].sem_flg = SEM_UNDO|IPC_NOWAIT;
  if (semop(semid, sem_op, 1) == -1) {
    fprintf(stderr, "Fail to unlock the semaphore\n");
    exit(1);
  }
#endif /* SAFE_SHM_READ */
}
