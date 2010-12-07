/**
 *    Filename          : read_gshhs.h

 *    Created           : 07 January 2009 (23:08:51)
 *    Created by        : StephPen - stephpen @at@ gmail . com

 *    Last Updated      : 23:25 21/11/2010
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
 *    Comments          : 
 *     
 *     
 *     
 *     
 *     
 *    Contact: <stephpen @at@ gmail . com>
*/


#ifndef _READ_GSHHS_H_
#define _READ_GSHHS_H_
/*
===========================================================================
                               Constants
===========================================================================
*/

#ifndef M_PI
#define M_PI          3.14159265358979323846264338327950288
#endif


/*
==================================================================================
                           Public Data Types
==================================================================================
*/

typedef struct                              /* Polygon vertex structure         */
{
    int                 x;                  /* Vertex x component               */
    int                 y;                  /* Vertex y component               */
} gshhs_vertex;

typedef struct                              /* Structure du polygone            */
{
    int                 id;                 /* Unique polygon id number         */ 
                                            /*    starting at 0                 */
    int                 nb_point;           /* Number of points in this polygon */
    int                 type;               /* (0) > Polygon                    */    
                                            /* (1) > Line (if no area)          */
    int                 level;              /* 1 land                           */
                                            /* 2 lake                           */
                                            /* 3 island_in_lake                 */
                                            /* 4 pond_in_island_in_lake         */
    int                 version;            /* Set to 6 for GSHHS version 1.6   */
    int                 greenwich;          /* 1 if Greenwich is crossed        */
    int                 source;             /* 0 = CIA WDBII, 1 = WVS           */
    int                 long_max;           /* Longitude max in micro-degrees   */
    int                 long_min;           /* Longitude min in micro-degrees   */   
    int                 lat_max;            /* Latitude max in micro-degrees    */
    int                 lat_min;            /* Latitude min in micro-degrees    */
    int                 area;               /* Area of polygon in 1/10 km^2     */
    gshhs_vertex        *vertex;            /* Vertex array pointer             */
} gshhs_vertex_list;

typedef struct
{
    int                 nb_poly;            /* Nb de polygone dans le GSHHS     */
    gshhs_vertex_list   *contour;
} gshhs_polygons;

typedef struct
{
    double              x1;            /* xxx     */
    double              y1;
    double              x2;            /* xxx     */
    double              y2;
} gshhs_line;


typedef struct
{
    int              nb_line;            /* xxx     */
    gshhs_line       *line;
} gshhs_contour;



/*
===========================================================================
                       Public Function Prototypes
===========================================================================
*/

void GshhsToGpcFile         (FILE *gpc_file,
                            gshhs_polygons *polygons,
                            int id_poly);

void read_gshhs             (FILE               *gshhs_file,
                            gshhs_polygons      *polygons,
                            int                 debug);
                            
void free_gshhs             (gshhs_polygons     *polygons,
                            int                 nb_poly);



#endif

/*
===========================================================================
                           End of file: gpc.h
===========================================================================
*/ 






