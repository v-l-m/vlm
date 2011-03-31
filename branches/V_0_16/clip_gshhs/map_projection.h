/**
 *  Filename          : map_projection.h
 *  Created by        : StephPen - stephpen@gmail.com
 *  Update            : 11:14 02/01/2011

 *  (c) 2008 by Stephane PENOT
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
 *  Comments          :
 *
 *
 *
 *
 *
 *  Contact: <stephpen@gmail.com>
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

