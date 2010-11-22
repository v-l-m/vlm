/**
 *    Filename          : map_functions.h

 *    Created           : 07 January 2009 (23:08:51)
 *    Created by        : StephPen - stephpen@gmail.com

 *    Last Updated      : 23:24 21/11/2010
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


#ifndef _MAP_FUNCTIONS_H_
#define _MAP_FUNCTIONS_H_

/*
===========================================================================
                               Constants
===========================================================================
*/

#define DEBUG           1

#ifndef M_PI
#define M_PI          3.14159265358979323846264338327950288
#endif


/*
===========================================================================
                               Macro
===========================================================================
*/

#define MALLOC(p, b, s, t) {if ((b) > 0) { \
                            p= (t*)malloc(b); if (!(p)) { \
                            fprintf(stderr, "gpc malloc failure: %s\n", s); \
                            exit(0);}} else p= NULL;}

#define FREE(p)            {if (p) {free(p); (p)= NULL;}}

/*
===========================================================================
                           Public Data Types
===========================================================================
*/

typedef struct
    {
        double  long_start;         // Longitude start      -> degrees [0, 360]
        double  lat_start;          // Latitude start       -> degrees [0, 360]
        double  long_extent;        // Longitude extend     -> degrees [-90, 90]
        double  lat_extent;         // Latitude extend      -> degrees [-90, 90]
        double  Zoom;               // Zoom                 -> pixel/degrees equator
        double  grid_space;         // Grid spacing         -> degrees [0, 360]
                                    //                          0 = No grid
        double  LongCenter;         // Longitude center     -> degrees [0, 360]
        double  LatCenter;          // Latitude center      -> degrees [0, 360]
        int     MapWidth;           // Map Width            -> pixel
        int     MapHeight;          // Map Height           -> pixel
        
        int     WaterColorR;        // Water color red      -> [0-255]
        int     WaterColorG;        // Water color green    -> [0-255]
        int     WaterColorB;        // Water color blue     -> [0-255]
        
        int     CoastColorR;        // Coast color red      -> [0-255]
        int     CoastColorG;        // Coast color green    -> [0-255]
        int     CoastColorB;        // Coast color blue     -> [0-255]
        
        int     LandColorR;         // Land color red       -> [0-255]
        int     LandColorG;         // Land color green     -> [0-255]
        int     LandColorB;         // Land color blue      -> [0-255]
        
        int     GridColorR;         // Grid color red       -> [0-255]
        int     GridColorG;         // Grid color green     -> [0-255]
        int     GridColorB;         // Grid color blue      -> [0-255]
        
        char    resolution[10];     // GSHHS resolution     -> [c, l, i, h, f]
        char    projection[256];    // Map projection       -> [mercator,
                                    //                          plate_carre,
                                    //                          ...,                                    
                                    //                          ...]                                    
        
        char    bd_path[256];       // Path to bd file
        char    bd_name[256];       // BD file name

        char    map_path[256];      // Path to map file
        char    map_name[256];      // Map file name
        
        
    } CmdOrder;

typedef struct
     {
        int version;
        int pasx;
        int pasy;
        int xmin;
        int ymin;
        int xmax;
        int ymax;
        int p1;
        int p2;
        int p3;
        int p4;
        int p5;
    } PolygonFileHeader;
    


/*
===========================================================================
                       Public Function Prototypes
===========================================================================
*/
void    ReadCmdFile             (FILE *cmdfile,
                                CmdOrder *cmd);

void    ReadPolygonFileHeader   (FILE   *polyfile,
                                PolygonFileHeader *header);

void    ReadPolygonFile         (FILE *polyfile,
                                int x, int y,
                                int pas_x, int pas_y,
                                gpc_polygon *p1, gpc_polygon *p2, gpc_polygon *p3, gpc_polygon *p4, gpc_polygon *p5);

void    FreePolygon             (gpc_polygon *p);

void    DegToHMS                (char *hms, double deg, char *type);

void    DrawPolygonFilled       (gdImagePtr Image, gpc_polygon *p1,
                                double X_Origine, double Y_Origine, double Zoom,
                                int Fill_Color);

void    DrawPolygonContour      (gdImagePtr Image, gpc_polygon *p,
                                int x, int y,
                                int pas_x, int pas_y,
                                double X_Origine, double Y_Origine, double Zoom,
                                int Contour_Color);

void    DrawGrid                (gdImagePtr Image, int MapWidth, int MapHeight,
                                double long_min, double long_max, double lat_min, double lat_max,
                                double X_Origine, double Y_Origine, double Zoom,
                                double Grid_Space, int Grid_Color, int Text_Color);

void    DrawLine                (gdImagePtr Image, gshhs_contour *p,
                                int x, int y,
                                int pas_x, int pas_y,
                                double X_Origine, double Y_Origine, double Zoom,
                                int Contour_Color);

void    ReadLineFile            (FILE *linefile,
                                int x, int y, 
                                gshhs_contour *contour);
                                
void    FreeLine                (gshhs_contour *p);

void    PolygonToGML            (gpc_polygon *p,
                                FILE *gmlfile,
                                int translate);


#endif

/*
===========================================================================
                           End of file: 
===========================================================================
*/
