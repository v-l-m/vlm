/**
 * $Id: shmem.h,v 1.12 2010-11-16 07:08:00 ylafon Exp $
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

#ifndef _VLMC_SHMEM_H_
#define _VLMC_SHMEM_H_

#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/shm.h>

#include "defs.h"
#include "types.h"

/* The value is VLM SEM (a=1 z=26) encoded by blocks of 5 bits */
#define VLM_SEMAPHORE_KEY ((22<<25)|(12<<20)|(13<<15)|(19<<10)|(5<<5)|13)

/* The value is VLM KEY (a=1 z=26) encoded by blocks of 5 bits */
#define VLM_SEMAPHORE_MEM_KEY ((22<<25)|(12<<20)|(13<<15)|(11<<10)|(5<<5)|25)

/* The value is VLM GSH (a=1 z=26) encoded by blocks of 5 bits */
#define VLM_COAST_MEM_KEY ((22<<25)|(12<<20)|(13<<15)|(7<<10)|(19<<5)|8)

/* The value is VLM GRB (a=1 z=26) encoded by blocks of 5 bits */
#define VLM_GRIB_MEM_KEY ((22<<25)|(12<<20)|(13<<15)|(7<<10)|(18<<5)|2)

/* The value is VLM POL (a=1 z=26) encoded by blocks of 5 bits */
#define VLM_POLAR_MEM_KEY ((22<<25)|(12<<20)|(13<<15)|(16<<10)|(15<<5)|12)

#define DEFAULT_NB_SHARED_GRIB_ENTRIES 61

/**
 * create the semaphore, and associated shared memory segment to store it
 * @return an int, the id of the semaphore 
 */
int create_semaphore ();

/**
 * get the semaphore id (from the shared memory segment used to store it)
 * @return an int, the id of the semaphore 
 */
int get_semaphore_id ();

/**
 * create the shared memory entry in order to store a grib array
 * @param windtable, a pointer to a <code>winds_prev</code> structure
 * used to get the number of entries in the grib. If this parameter is NULL
 * the default  DEFAULT_NB_SHARED_GRIB_ENTRIES is used
 * @return an int, the shmid of the segment
 */
int create_grib_shmid PARAM1(winds_prev *);

/**
 * get the grib memory segment id 
 * @param readonly, an int, if 1, the segment is searched using read-only
 * permissions, otherwise, rw for user read for others
 * @return an int, the shmid of the segment
 */
int get_grib_shmid PARAM1(int);

/**
 * create the shared memory entry in order to store a polar array
 * @param polars, a pointer to a <code>boat_polar_list</code> structure
 * @return an int, the shmid of the segment
 */
int create_polar_shmid PARAM1(boat_polar_list *);

/**
 * get the polar memory segment id 
 * @param readonly, an int, if 1, the segment is searched using read-only
 * permissions, otherwise, rw for user read for others
 * @return an int, the shmid of the segment
 */
int get_polar_shmid PARAM1(int);

/**
 * get the attached memory address of a shmid
 * @param shmid, an int, the segment id of the segment (see get_grib_shmid 
 * or others)
 * @param readonly, an int, if 1, the segment is attached using read-only
 * 0 if read-write.
 * @return a void *, the address of the attached segment
 */
void *get_shmem PARAM2(int, int);

/**
 * Copy the wind previsions (in windtable) in the shared memory segment
 * If the segment is too small, it may be resized 
 * @param shmid an int, the id of the shared memory segment
 * @param windtable a pointer to a <code>winds_prev</code> structure
 * @param memseg a generic (void *) pointer representing the segment
 * @return an int, the shmid. If it changed, the user must check that
 * the memseg pointer is still valid
 */
int copy_grib_array_to_shmem PARAM3(int, winds_prev *, void *);

/**
 * Construct a local grib array (in the global context) based on
 * what is in the memory segment.
 * Only references are used to make it faster so:
 * The segment should be used read-only
 * You should not use grib_merge/purge/free with it
 * @param windtable, a <code>winds_prev *</code> pointer, where the associated 
 * data will be stored.
 * @param shmaddr, a <code>void *</code> pointer, the address of the attached
 * shared memory segment
 */
void construct_grib_array_from_shmem PARAM2(winds_prev *, void *);

/**
 * Construct a local grib array (in the global context) based on
 * what is in the memory segment.
 * It copies entirely the data locally
 * The segment should be used read-only or read-write
 * You may use grib_merge/purge/free with it
 * (Typically, you can use this to do modification to the shared grib and 
 * save it back, or if you want a local copy and will work only on that
 * without risking an interference with an update)
 * @param windtable, a <code>winds_prev *</code> pointer, where the associated 
 * data will be stored.
 * @param shmaddr, a <code>void *</code> pointer, the address of the attached
 * shared memory segment
 */
void allocate_grib_array_from_shmem PARAM2(winds_prev *, void *);

/**
 * Copy the polar tables to the dedicated shared memory segment
 * @param shmid an int, the id of the shared memory segment
 * @param polars a pointer to a <code>boat_polar_list</code> structure
 * @param memseg a generic (void *) pointer representing the segment
 * @return an int, the shmid. If it changed, the user must check that
 * the memseg pointer is still valid
 */
int copy_polar_array_to_shmem PARAM3(int, boat_polar_list *, void *);

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
void construct_polar_array_from_shmem PARAM2(boat_polar_list *, void *);

#endif /* _VLMC_SHMEM_H_ */
