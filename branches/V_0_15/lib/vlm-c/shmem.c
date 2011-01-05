/**
 * $Id: shmem.c,v 1.23 2010-12-09 13:54:26 ylafon Exp $
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

/**
 * create the shared memory entry in order to store a grib array
 * @param windtable, a pointer to a <code>winds_prev</code> structure
 * used to get the number of entries in the grib. If this parameter is NULL
 * the default  DEFAULT_NB_SHARED_GRIB_ENTRIES is used
 * @return an int, the shmid of the segment
 */
int create_polar_shmid(boat_polar_list *polars) {
  long needed_bytes;
  int i, shmid;

  needed_bytes = sizeof(int);
  /* we align to 4 bytes */
  if (needed_bytes % 4) {
    needed_bytes = (((needed_bytes >> 2) + 1) << 2);
  }
  
  needed_bytes += 181*61*sizeof(double)*polars->nb_polars;
  for (i=0; i<polars->nb_polars; i++) {
    needed_bytes += sizeof(int); /* stirng length */
    needed_bytes += strlen(polars->polars[i]->polar_name)+1; /* string value */
    /* we align to 4 bytes */
    if (needed_bytes % 4) {
      needed_bytes = (((needed_bytes >> 2) + 1) << 2);
    }
  }

  /* create the shared memory segment */
  shmid = shmget(VLM_POLAR_MEM_KEY, needed_bytes, IPC_CREAT|0644);
  if (shmid == -1) {
    /* failed */
    fprintf(stderr, "Unable to create shared memory segment VLMPOL\n");
    exit(1);
  }
  return shmid;
}

/**
 * get the shared memory entry in order to store or read a grib array
 * @return an int, the shmid of the segment
 */
int get_polar_shmid(int readonly) {
  int shmid;
  if (readonly) {
    shmid = shmget(VLM_POLAR_MEM_KEY, 0, 0444);
  } else {
    shmid = shmget(VLM_POLAR_MEM_KEY, 0, 0644);
  }
  return shmid;
}

void *get_shmem(int shmid, int readonly) {
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
  
  /*
    we are storing in the segment array the folloing thing:
    (int) nb_prevs
    (time_t) grib offset (should be 0, may be moved to something else
    nb_prevs * (wind) complete wind structure, aka
                      -> time_t prevision_time
                      -> double wind_u[[WIND_GRID_LONG][WIND_GRID_LAT]; 
                      -> double wind_v[WIND_GRID_LONG][WIND_GRID_LAT];

    As the types used are fixed, it's bery easy to process.
  */
  if (shmctl(shmid, IPC_STAT , &shminfo) == -1) {
    fprintf(stderr, "Unable to access information on GRIB segment\n");
    return -1;
  }

  maxprev = 0;
  nb_prevs = windtable->nb_prevs;
  /* be sure to lock the semaphore before using this function */
  nb_bytes = nb_prevs * sizeof(winds);
  printf("Got %d entries in windtable\n", windtable->nb_prevs);
  printf("Bytes used: %ld (winds struct size is %zu)\n",nb_bytes,sizeof(winds));
  ok = (shminfo.shm_segsz > nb_bytes);
  printf("Segment size: %zu %s\n",  shminfo.shm_segsz, 
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

/**
 * Copy the polar tables to the dedicated shared memory segment
 * @param shmid an int, the id of the shared memory segment
 * @param polars a pointer to a <code>boat_polar_list</code> structure
 * @param memseg a generic (void *) pointer representing the segment
 * @return an int, the shmid. If it changed, the user must check that
 * the memseg pointer is still valid
 */
int copy_polar_array_to_shmem(int shmid, boat_polar_list *polars, 
			      void *memseg) {
  long needed_bytes;
  int *intarray, i, nb_polars, ok, slen;
  double *darray;
  char *carray;
  long used_bytes;
  struct shmid_ds shminfo;
  
  /*
    we are storing in the segment array the folloing thing:
    (int) nb_polars (boat_polar_list->nb_polars)
    nb_polars* (181*61*sizeof(double)) 
                         (boat_polar_list->polars[0..nb_polars-1]->polar_tab)
    [0..nb_polars-1]
      (int) strlen (i)
      boat_polar_list->polars[0..nb_polars-1]->polar_tab) (aligned to 4 bytes)

    As the types used are fixed, it's very easy to process.
  */
  if (shmctl(shmid, IPC_STAT , &shminfo) == -1) {
    fprintf(stderr, "Unable to access information on POLAR segment\n");
    return -1;
  }

  needed_bytes = sizeof(int);
  /* we align to 4 bytes */
  if (needed_bytes % 4) {
    needed_bytes = (((needed_bytes >> 2) + 1) << 2);
  }
  needed_bytes += 181*61*sizeof(double)*polars->nb_polars;
  for (i=0; i<polars->nb_polars; i++) {
    needed_bytes += sizeof(int); /* stirng length */
    needed_bytes += strlen(polars->polars[i]->polar_name)+1; /* string value */
    /* we align to 4 bytes */
    if (needed_bytes % 4) {
      needed_bytes = (((needed_bytes >> 2) + 1) << 2);
    }
  }

  printf("Bytes used: %ld\n", needed_bytes);
  ok = (shminfo.shm_segsz >= needed_bytes);
  printf("Segment size: %zu %s\n",  shminfo.shm_segsz, 
	 (ok) ? "OK" : "NOT OK");
  
  if (!ok) {
    /* we need to reallocate... here, we should own the semaphore, so it is 
       safe to destroy everything and restart */
    shmdt(memseg);
    memseg = NULL;
    if (shmctl(shmid, IPC_RMID, &shminfo) == -1) {
      fprintf(stderr, "Unable to resize POLAR segment\n");
      exit(-1);
    } else {
      shmid = create_polar_shmid(polars);
      
      if (shmid == -1) {
	/* ok we are in trouble, we destroyed the previous existing segment
	   now we can't create a bigger new one */
	shmid = shmget(VLM_POLAR_MEM_KEY, shminfo.shm_segsz, IPC_CREAT|0644);
	if (shmid == -1) {
	  /* we are in deeeeeeep trouble, abort */
	  return -1;
	}
	return shmid;
      } else {
	if (shmctl(shmid, IPC_STAT , &shminfo) == -1) {
	  fprintf(stderr, "Unable to get information on new POLAR segment\n");
	  return -1;
	}
      }
      /* don't forget to reattach the new memory */
      memseg = shmat(shmid, (void *) 0, 0);
    }
  }
  if ( shminfo.shm_segsz < needed_bytes) {
    printf("Unable to request the needed size %ld\n", needed_bytes);
    exit(-1);
  }

  nb_polars = polars->nb_polars;
  /* dump the number of entries */
  intarray = (int *) memseg;
  *intarray =  nb_polars;
  used_bytes = sizeof(int);
  /* we are using a 4 bytes alignment, ensure that it's right (it should be) */
  if (used_bytes % 4) {
    used_bytes = (((used_bytes >> 2) + 1) << 2);
  }
  /* dump the polar tab values */
  for (i=0; i<nb_polars;i++) {
    darray = (double *) (((char *)memseg) + used_bytes);
    memcpy(darray, polars->polars[i]->polar_tab, 61*181*sizeof(double));
    used_bytes += 61*181*sizeof(double);
  }
  /* dump the size+value of polar names */
  for (i=0; i<nb_polars; i++) {
    intarray = (int *) (((char *)memseg) + used_bytes);
    slen = strlen(polars->polars[i]->polar_name);
    *intarray = slen;
    used_bytes += sizeof(int);
    carray = (char *) (((char *)memseg) + used_bytes);
    strcpy(carray, polars->polars[i]->polar_name);
    used_bytes += (slen+1)*sizeof(char);
    if (used_bytes % 4) {
      used_bytes = (((used_bytes >> 2) + 1) << 2);
    }
  }
  /* done! */
  printf("Updated %ld bytes\n", used_bytes);
  return shmid;
}

/**
 * Construct a local polar array (in the global context) based on
 * what is in the memory segment.
 * Only references are used to make it faster so:
 * The segment should be used read-only
 * @param polars, a <code>boat_polar_list *</code> pointer, where the 
 * associated data will be stored.
 * @param shmaddr, a <code>void *</code> pointer, the address of the attached
 * shared memory segment
 * NOTE that it will allocate an array of pointer, which must be freed when
 * the polar table is no longer needed
 */
void construct_polar_array_from_shmem(boat_polar_list *polars, void *memseg) {
  int *intarray, nb_polars, i, slen;
  long used_bytes;
  double *darray;
  char *carray;

  /* get the number of polars */
  intarray = (int *)memseg;
  nb_polars = *intarray;
  used_bytes = sizeof(int);
  /* we are using a 4 bytes alignment, ensure that it's right (it should be) */
  if (used_bytes % 4) {
    used_bytes = (((used_bytes >> 2) + 1) << 2);
  }
  /* allocate the polar list structure */
  polars->nb_polars = nb_polars;
  polars->polars = calloc(nb_polars, sizeof (boat_polar *));
  for (i=0; i<nb_polars; i++) {
    polars->polars[i] = calloc(1, sizeof(boat_polar));
  }
  /* now point from the boat_polar struct to the tab in the segment */
  for (i=0; i<nb_polars; i++) {
    darray = (double *) (((char *)memseg) + used_bytes);
    polars->polars[i]->polar_tab = darray;
    used_bytes += 61*181*sizeof(double);
  }
  /* and same for the polar names */
    for (i=0; i<nb_polars; i++) {
    intarray = (int *) (((char *)memseg) + used_bytes);
    slen = *intarray;
    used_bytes += sizeof(int);
    carray = (char *) (((char *)memseg) + used_bytes);
    polars->polars[i]->polar_name = carray;
    used_bytes += (slen+1)*sizeof(char);
    if (used_bytes % 4) {
      used_bytes = (((used_bytes >> 2) + 1) << 2);
    }
  }
}

