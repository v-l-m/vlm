/**
 * $Id: shmem.c,v 1.10 2008/08/05 09:27:29 ylafon Exp $
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

#ifdef __linux__
union semun {
  int val;
  struct semid_ds *buf; 
  unsigned short *array; 
  struct seminfo *__buf; 
};
#endif /* __linux */

int create_semaphore() {
  int shmid, semvalset, semid;
  int *semarray;
  union semun semunion;

  /* create the shared memory segment int for the semaphore */
  shmid = shmget(VLM_SEMAPHORE_MEM_KEY, sizeof(int), IPC_CREAT|IPC_EXCL|0666);
  if (shmid == -1) {
    /* failed */
    fprintf(stderr, "Unable to create shared memory segment VLMKEY\n");
    exit(1);
  }

  /* attach the segment */
  semarray = (int *) shmat(shmid, NULL, 0);
  if (semarray  == (int *) -1 ) {
    /* failed */
    fprintf(stderr, "Unable to attach semaphore array\n");
    exit(1);
  }
  
  /* create the semaphores */
  *semarray = semget(VLM_SEMAPHORE_KEY, 2, IPC_CREAT|IPC_EXCL|0666);
  if (*semarray == -1) {
    fprintf(stderr, "Unable to create semaphore VLMSEM\n");
    exit(1);
  }

  semunion.val = 0;
  semvalset = semctl(*semarray, 0, SETVAL, semunion);
  if (semvalset == -1) {
    fprintf(stderr, "Unable to set semaphore 0 value\n");
    exit(1);
  }
  semvalset = semctl(*semarray, 1, SETVAL, semunion);
  if (semvalset == -1) {
    fprintf(stderr, "Unable to set semaphore 1 value\n");
    exit(1);
  }  

  semid = *semarray;
  shmdt(semarray);
  return semid;
}

int get_semaphore_id() {
  int shmid;
  int *semarray, semid;

  /* create the shared memory segment int for the semaphore */
  shmid = shmget(VLM_SEMAPHORE_MEM_KEY, sizeof(int), 0666);
  if (shmid == -1) {
    return -1;
  }
  /* attach the segment */
  semarray = (int *) shmat(shmid, NULL, 0);
  if (semarray  == (int *) -1 ) {
    return -1;
  }
  semid = *semarray;
  shmdt(semarray);
  return semid;
}
  

/**
 * create the shared memory entry in order to store a grib array
 * @param windtable, a pointer to a <code>winds_prev</code> structure
 * used to get the number of entries in the grib. If this parameter is NULL
 * the default  DEFAULT_NB_SHARED_GRIB_ENTRIES is used
 * @return an int, the shmid of the segment
 */
int create_grib_shmid(winds_prev *windtable) {
  long needed_bytes;
  int shmid;

  needed_bytes = sizeof(int);
  /* we align to 4 bytes */
  if (needed_bytes % 4) {
    needed_bytes = (((needed_bytes >> 2) + 1) << 2);
  }
  needed_bytes += sizeof(time_t); /* for offset, now unused but can be used
				   later for something else like date of
				   interpolation in case of smoothing */
  needed_bytes += sizeof(winds) * 
           ((windtable) ? windtable->nb_prevs : DEFAULT_NB_SHARED_GRIB_ENTRIES);
  
  /* create the shared memory segment */
  shmid = shmget(VLM_GRIB_MEM_KEY, needed_bytes, IPC_CREAT|0644);
  if (shmid == -1) {
    /* failed */
    fprintf(stderr, "Unable to create shared memory segment VLMGRB\n");
    exit(1);
  }
  return shmid;
}

/**
 * get the shared memory entry in order to store or read a grib array
 * @return an int, the shmid of the segment
 */
int get_grib_shmid(int readonly) {
  int shmid;
  /* FIXME do we need to read the size, then redo the shmget ? I guess not 
     but it is possible... */
  if (readonly) {
    shmid = shmget(VLM_GRIB_MEM_KEY, 0, 0444);
  } else {
    shmid = shmget(VLM_GRIB_MEM_KEY, 0, 0644);
  }
  return shmid;
}


void *get_grib_shmem(int shmid, int readonly) {
  void *addr;
  
  if (readonly) {
    addr = shmat(shmid, (void *) 0, SHM_RDONLY);
  } else {
    addr = shmat(shmid, (void *) 0, 0) ;
  }
  if (addr == (void *) -1) {
    return NULL;
  }
  return addr;
}
/**
 * Copy the wind previsions (in windtable) in the shared memory segment
 * If the segment is too small, it may be resized 
 * @param shmid an int, the id of the shared memory segment
 * @param windtable a pointer to a <code>winds_prev</code> structure
 * @param memseg a generic (void *) pointer representing the segment
 * @return an int, the shmid. If it changed, the user must check that
 * the memseg pointer is still valid
 */
int copy_grib_array_to_shmem(int shmid, winds_prev *windtable, void *memseg) {
  long nb_bytes;
  int *intarray, i, nb_prevs, ok, maxprev;
  time_t *tarray;
  long used_bytes;
  winds *windarray;
  struct shmid_ds shminfo;
  
  if (shmctl(shmid, IPC_STAT , &shminfo) == -1) {
    fprintf(stderr, "Unable to access information on GRIB segment\n");
    return -1;
  }

  maxprev = 0;
  nb_prevs = windtable->nb_prevs;
  /* be sure to lock the semaphore before using this function */
  nb_bytes = nb_prevs * sizeof(winds);
  printf("Got %d entries in windtable\n", windtable->nb_prevs);
  printf("Bytes used: %ld (winds struct size is %ld)\n",nb_bytes,sizeof(winds));
  ok = (shminfo.shm_segsz > nb_bytes);
  printf("Segment size: %ld %s\n",  (long)shminfo.shm_segsz, 
	 (ok) ? "OK" : "NOT OK");

  if (!ok) {
    /* we need to reallocate... here, we should own the semaphore, so it is 
       safe to destroy everything and restart */
    shmdt(memseg);
    memseg = NULL;
    if (shmctl(shmid, IPC_RMID, &shminfo) == -1) {
      fprintf(stderr, "Unable to resize GRIB segment\n");
      maxprev = shminfo.shm_segsz / sizeof(winds);
    } else {
      shmid = create_grib_shmid(windtable);
      if (shmid == -1) {
	/* ok we are in trouble, we destroyed the previous existing segment
	   now we can't create a bigger new one */
	shmid = shmget(VLM_GRIB_MEM_KEY, shminfo.shm_segsz, IPC_CREAT|0644);
	if (shmid == -1) {
	  /* we are in deeeeeeep trouble, abort */
	  return -1;
	}
	maxprev = shminfo.shm_segsz / sizeof(winds);
	return shmid;
      }
      /* don't forget to reattach the new memory */
      memseg = shmat(shmid, (void *) 0, 0);
    }
  }
  /* if we are in danger of an overflow... limit the amount copied */
  if (maxprev && nb_prevs > maxprev) {
    nb_prevs = maxprev;
  }
  intarray = (int *) memseg;
  *intarray =  nb_prevs;
  used_bytes = sizeof(int);
  /* we are using a 4 bytes alignment, ensure that it's right (it should be) */
  if (used_bytes % 4) {
    used_bytes = (((used_bytes >> 2) + 1) << 2);
  }

  tarray = (time_t *) (((char *)memseg) + used_bytes);
  *tarray = windtable->time_offset; /* might be changed to interpolation date */
  used_bytes += sizeof(time_t);

  /* now copy the relevant stuff */
  windarray = (winds *) (((char *)memseg) + used_bytes);
  for (i=0; i<nb_prevs; i++) {
    memcpy(windarray++, windtable->wind[i], sizeof(winds));
  }
  return shmid;
}
  
void construct_grib_array_from_shmem(winds_prev *windtable, void *memseg) {
  /* NOTE the previous winds array is freed if not NULL */
  /* NOTE this is not a _copy_ we need to keep the segment and NEVER free
     something in it */
  int *intarray, nb_prevs, i;
  long used_bytes;
  winds *windarray;
  time_t *tarray;

  intarray = (int *) memseg;
  nb_prevs = *intarray;
  used_bytes = sizeof(int);
  /* we are using a 4 bytes alignment, ensure that it's right (it should be) */
  if (used_bytes % 4) {
    used_bytes = (((used_bytes >> 2) + 1) << 2);
  }

  tarray = (time_t *) (((char *)memseg) + used_bytes);
  windtable->time_offset = *tarray; /* might be changed to interpolation date */
  used_bytes += sizeof(time_t);

  windarray = (winds *) (((char *)memseg) + used_bytes);
  if (windtable->wind != NULL) {
    free(windtable->wind);
    windtable->wind = NULL;
  }
  windtable->wind = calloc(nb_prevs, sizeof(winds *));
  for (i=0; i<nb_prevs; i++) {
    windtable->wind[i] = windarray++;
  }
  windtable->nb_prevs = nb_prevs;
}
 
void allocate_grib_array_from_shmem(winds_prev *windtable, void *memseg) {
  int *intarray, nb_prevs, i;
  long used_bytes;
  winds *windarray;
  time_t *tarray;

  intarray = (int *) memseg;
  nb_prevs = *intarray;
  used_bytes = sizeof(int);
  /* we are using a 4 bytes alignment, ensure that it's right (it should be) */
  if (used_bytes % 4) {
    used_bytes = (((used_bytes >> 2) + 1) << 2);
  }

  tarray = (time_t *) (((char *)memseg) + used_bytes);
  windtable->time_offset = *tarray; /* might be changed to interpolation date */
  used_bytes += sizeof(time_t);

  windarray = (winds *) (((char *)memseg) + used_bytes);
  /* free the internal structure completely */
  if (windtable->wind != NULL) {
    for (i=0; i<windtable->nb_prevs; i++) {
      free(windtable->wind[i]);
    }
    free(windtable->wind);
    windtable->wind = NULL;
  }
  windtable->wind = calloc(nb_prevs, sizeof(winds *));
  for (i=0; i<nb_prevs; i++) {
    windtable->wind[i] = calloc(1, sizeof(winds));
    memcpy(windtable->wind[i], windarray++, sizeof(winds));
  }
  windtable->nb_prevs = nb_prevs;
}

  


