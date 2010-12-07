/**
 *    Filename        : map_projection.c

 *    Created            : 07 January 2009 (23:13:57)
 *    Created by        : StephPen - stephpen @at@ gmail . com

 *    Last Updated    : 23:23 21/11/2010
 *    Updated by        : StephPen - stephpen @at@ gmail . com

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
 *    Comments        : 
 *     
 *     
 *     
 *     
 *     
 *    Contact: <stephpen @at@ gmail . com>
*/

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <math.h>

#include "map_projection.h"


/*
 * Mercator transformation
 * accounts for the fact that the earth is not a sphere, but a spheroid
 */
#define D_R (M_PI / 180.0)
#define R_D (180.0 / M_PI)
#define RATIO (R_MINOR/R_MAJOR)
#define ECCENT (sqrt(1.0 - (RATIO * RATIO)))
#define COM (0.5 * ECCENT)
#define MERCATOR_LIMIT RadToDeg(atan(sinh(M_PI)))  //85.051129...

static double DegToRad (double deg)
{
        return deg * D_R;
}

static double RadToDeg (double rad)
{
        return rad * R_D;
}


double MercatorLongitude (double lon)
{
        return R_MAJOR * DegToRad(lon);
}

double MercatorLatitude (double lat)
{
        if (lat>MERCATOR_LIMIT) lat=MERCATOR_LIMIT;
        if (lat<-MERCATOR_LIMIT) lat=-MERCATOR_LIMIT; 
        
        double phi = DegToRad(lat);
        double sinphi = sin(phi);
        double con = ECCENT * sinphi;
        con = pow(((1.0 - con) / (1.0 + con)), COM);
        double ts = tan(0.5 * ((M_PI * 0.5) - phi)) / con;
        return 0 - R_MAJOR * log(ts);
}

double MercatorLongitudeSimple (double lon)
{
        return DegToRad(lon);
}

double MercatorLatitudeSimple (double lat)
{
        if (lat>MERCATOR_LIMIT) lat=MERCATOR_LIMIT;
        if (lat<-MERCATOR_LIMIT) lat=-MERCATOR_LIMIT; 
        
        double phi = DegToRad(lat);
        return log( tan( (M_PI/4) + (phi/2) ) );
}

double MercatorInverseLongitudeSimple (double x)
{
        return RadToDeg(x);
}

double MercatorInverseLatitudeSimple (double y)
{
        return RadToDeg( atan( sinh(y) ) );
}








