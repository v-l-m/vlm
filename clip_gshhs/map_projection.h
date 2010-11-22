/**
 *    Filename          : map_projection.h

 *    Created           : 07 January 2009 (23:08:51)
 *    Created by        : StephPen - stephpen@gmail.com

 *    Last Updated      : 23:25 21/11/2010
 *    Updated by        : StephPen - stephpen@gmail.com

 *    (c) 2008 by Stephane PENOT
 *        See COPYING file for copying and redistribution conditions.
 *     
 *        This program is free software; you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation; version 2 of the License.
 *     
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *     
 *    Comments          : 
 *     
 *     
 *     
 *     
 *     
 *    Contact: <stephpen@gmail.com>
*/

#ifndef _MAP_PROJECTION_H_
#define _MAP_PROJECTION_H_
/*
===========================================================================
                               Constants
===========================================================================
*/

#define R_MAJOR         6378137.0
#define R_MINOR         6356752.3142

#ifndef M_PI
#define M_PI          3.14159265358979323846264338327950288
#endif


/*
===========================================================================
                           Public Data Types
===========================================================================
*/



/*
===========================================================================
                       Public Function Prototypes
===========================================================================
*/

double  MercatorLongitude               (double         lon);
double  MercatorLatitude                (double         lat);
double  MercatorLongitudeSimple         (double         lon);
double  MercatorLatitudeSimple          (double         lat);
double  MercatorInverseLongitudeSimple  (double         x);
double  MercatorInverseLatitudeSimple   (double         y);


#endif

/*
===========================================================================
                           End of file
===========================================================================
*/

