/**
 * $Id: useshmem.h,v 1.6 2009-08-31 13:34:20 ylafon Exp $
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

#ifndef _USESHMEM_H_
#define _USESHMEM_H_

#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/shm.h>

#include "defs.h"
#include "types.h"

void shm_safe_get_wind_info_lat_long         PARAM4(double, double, time_t, 
						    wind_info *);
void shm_safe_get_wind_info_lat_long_context PARAM5(vlmc_context *, 
						    double, double, time_t, 
						    wind_info *);

void shm_get_wind_info_lat_long              PARAM4(double, double, time_t, 
						    wind_info *);
void shm_get_wind_info_lat_long_context      PARAM5(vlmc_context *, 
						    double, double, time_t, 
						    wind_info *);

void shm_lock_sem_construct_grib             PARAM1(int);
void shm_lock_sem_construct_grib_context     PARAM2(vlmc_context *, int);
void shm_unlock_sem_destroy_grib             PARAM1(int);
void shm_unlock_sem_destroy_grib_context     PARAM2(vlmc_context *, int);
void shm_lock_sem_construct_polar            PARAM1(int);
void shm_lock_sem_construct_polar_context    PARAM2(vlmc_context *, int);
void shm_unlock_sem_destroy_polar            PARAM1(int);
void shm_unlock_sem_destroy_polar_context    PARAM2(vlmc_context *, int);

void create_and_fill_polar_shm ();

#endif /* _USESHMEM_H_ */
