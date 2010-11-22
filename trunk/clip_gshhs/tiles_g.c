/**
 *    Filename          : tiles_g.c

 *    Created           : 07 May 2009 (23:08:51)
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


#include <stdio.h>
#include <stdlib.h>
#include <gd.h>
#include <math.h>

#include "read_gshhs.h"
#include "gpc.h"
#include "map_functions.h"
#include "map_projection.h"
#include "gshhs.h"

#define USAGE   "\n \
Tiles Generator V0.2 - 00:37 13/11/2010\n \
stephpen@gmail.com\n \
Usage:  tiles_g Nb_Tiles X_Tile Y_Tile bd_file.dat abc.png\n \
Nb_Tiles:       nombre de tiles pour 360 deg\n \
X_Tile:         position x du tiles\n \
Y_Tile:         position y du tiles\n \
bd_file.dat:    chemin complet pour le fichier de cotes\n \
abc.png:        chemin complet pour le fichier image genere\n \
Origine des tiles:      latitude  = 85 deg\n \
                        longitude = -180 deg\n \
Amplitude des tiles:    latitude  = 85 deg > -85 deg\n \
                        longitude = -180 deg > 180 deg\n\n"


int main (int argc, char **argv)
{
    
    FILE    *polyfile;
    
    char    poly_file_name[256];
    PolygonFileHeader     header;
    gpc_polygon p1, p2, p3, p4, p5;
    
    int x, y;
    
    gdImagePtr image, image_f;  /* Pointeur vers notre image */
    FILE *image_png; /* Fichier image PNG */
    int water_color, coast_color, land_color;
    //int grid_color;
    //int text_color;
    double long_max, long_min, lat_max, lat_min;
    int long_max_int, long_min_int, lat_max_int, lat_min_int;
    double origine_x, origine_y;
    
    int Nb_Tiles;
    int X_Tile, Y_Tile;
    int TileDim;
    char TileName[256];
    char bd_file[256];
    double zoom;
    int bord;
        
    
    if (argc < 5 || argc > 6) {
        fprintf (stderr, USAGE);        
        exit (EXIT_FAILURE);
    }
    
    // Lecture de la ligne de commande
    // le nombre de tiles pour 360 deg
    sscanf(argv[1], "%d", &Nb_Tiles);
    printf("Nb_Tiles: %d\n", Nb_Tiles);
    // test si nb_tiles est pair ou impair, uniquement les nombres pair sont acceptés
    if (((Nb_Tiles & 1) && (Nb_Tiles != 1)) || (Nb_Tiles < 1))
    {
        fprintf (stderr, USAGE);
        fprintf (stderr, "Error!\n");
        fprintf (stderr, "nb_tiles must be pair\n");
        fprintf (stderr, "End!\n");
        exit (EXIT_FAILURE);
    }

    // la position x du tiles [0-(nb_tiles-1)]
    sscanf(argv[2], "%d", &X_Tile);
    printf("X_Tiles: %d\n", X_Tile);
    if (X_Tile>=Nb_Tiles)
    {
        fprintf (stderr, USAGE);
        fprintf (stderr, "Error!\n");
        fprintf (stderr, "x_tiles > nb_tiles\n");
        fprintf (stderr, "End!\n");
        exit (EXIT_FAILURE);
    }

    // la position y du tiles [0-(nb_tiles-1)]
    sscanf(argv[3], "%d", &Y_Tile);
    printf("Y_Tiles: %d\n", Y_Tile);
    if (Y_Tile>=Nb_Tiles)
    {
        fprintf (stderr, USAGE);
        fprintf (stderr, "Error!\n");
        fprintf (stderr, "y_tiles > nb_tiles\n");
        fprintf (stderr, "End!\n");
        exit (EXIT_FAILURE);
    }

    // le nom du fichier de cotes
    sscanf(argv[4], "%s", bd_file);
    printf("Coast_name: %s\n", bd_file);
    
    // le nom du fichier du tiles abc.png
    sscanf(argv[5], "%s", TileName);
    printf("Tiles_name: %s\n", TileName);
    
    sprintf(poly_file_name, "%s", bd_file);
    polyfile = fopen(poly_file_name, "rb");
    if (polyfile == NULL)
    {
        fprintf (stderr, USAGE);
        fprintf (stderr, "Impossible d'ouvrir le fichier: %s\n", poly_file_name);
        exit(EXIT_FAILURE);
    }
    
    ReadPolygonFileHeader (polyfile, &header);
    //printf("Header_Pasx: %d\n", header.pasx);
    
    // Initialisation de quelques variables
    // Dimension des tiles (pixel)
    TileDim = 256; // 256x256
    // Facteur de zoom (pour le dessin)
    zoom = (double)Nb_Tiles * (double)TileDim  / 360.0;
    printf("Zoom: %lf px/deg\n", zoom);

    //Détermination des longitudes mini, maxi, et origine image x
    long_min = (X_Tile * 360.0 / Nb_Tiles) - 180;
    long_max = ((X_Tile+1) * 360.0 / Nb_Tiles) -180;
    printf("long_min: %lf, long_max: %lf\n", long_min, long_max);
    
    if (long_min>=0)    long_min_int=   floor(fabs(long_min));
    else                long_min_int=  -ceil(fabs(long_min));
    if (long_max>=0)    long_max_int=   ceil(fabs(long_max));
    else                long_max_int=  -floor(fabs(long_max));

    origine_x = -long_min * zoom;

    printf("long_min: %d, long_max: %d, origine_x: %lf\n", long_min_int, long_max_int, origine_x);
    
    //Détermination des latitudes mini, maxi, et origine image y
    lat_min = MercatorInverseLatitudeSimple((((double)Nb_Tiles / 2) - (Y_Tile+1)) * (360.0 / Nb_Tiles) * M_PI / 180.0);
    lat_max = MercatorInverseLatitudeSimple((((double)Nb_Tiles / 2) - (Y_Tile)) * (360.0 / Nb_Tiles) * M_PI / 180.0);
    printf("lat_min: %lf, lat_max: %lf\n", lat_min, lat_max);
    
    if (lat_min>=0)     lat_min_int=     floor(fabs(lat_min));
    else                lat_min_int=    -ceil(fabs(lat_min));
    if (lat_max>=0)     lat_max_int=     ceil(fabs(lat_max));
    else                lat_max_int=    -floor(fabs(lat_max));

    //origine_y = -MercatorLatitudeSimple(lat_min)*180.0*zoom/M_PI;
    origine_y = -((((double)Nb_Tiles / 2) - (Y_Tile + 1)) * (360.0 / Nb_Tiles)) * zoom;

    printf("lat_min: %d, lat_max: %d, origine_y: %lf\n", lat_min_int, lat_max_int, origine_y);
    
    printf("%lf\n", MercatorInverseLatitudeSimple(-1* (256-origine_y)*M_PI / (180.0*zoom)));
    // Création de l'image
    printf("Map_Width: %d, Map_Height: %d\n", TileDim, TileDim);
    bord=10;
    image = gdImageCreate(TileDim + 2 * bord, TileDim + 2 * bord);
    image_f= gdImageCreate(TileDim, TileDim);
    //image = gdImageCreate(TileDim, TileDim);
    
    // Création des couleurs
    water_color =   gdImageColorAllocate(image, 99, 164, 255);
    land_color  =   gdImageColorAllocate(image, 255, 213, 162);
    coast_color =   gdImageColorAllocate(image, 9, 120, 171);
    //grid_color  =   gdImageColorAllocate(image, 250, 250, 250);
    //text_color  =   gdImageColorAllocate(image, 0, 0, 0); 
    
    // Cas où tout va bien
    if (long_min_int>=0 && long_max_int<=360)
    {
        for (x=long_min_int; x<long_max_int; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                
                DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                
                FreePolygon(&p1);
                FreePolygon(&p2);
                FreePolygon(&p3);
                FreePolygon(&p4);
                FreePolygon(&p5);
            }
        }
    }

    // Cas où long_min <0
    if (long_min_int<0)
    {
        for (x=0; x<long_max_int; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);
            
                DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                
                DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                
                FreePolygon(&p1);
                FreePolygon(&p2);
                FreePolygon(&p3);
                FreePolygon(&p4);
                FreePolygon(&p5);
            }
        }
        
        for (x=long_min_int+360; x<360; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                DrawPolygonFilled(image, &p1, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p2, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p3, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p4, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p5, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                
                DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
               
                FreePolygon(&p1);
                FreePolygon(&p2);
                FreePolygon(&p3);
                FreePolygon(&p4);
                FreePolygon(&p5);
            }
        }
    }

    // Cas où long_max >360
    if (long_max_int>360)
    {
        for (x=long_min_int; x<360; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                
                DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                
                FreePolygon(&p1);
                FreePolygon(&p2);
                FreePolygon(&p3);
                FreePolygon(&p4);
                FreePolygon(&p5);
            }
        }
        
        for (x=0; x<long_max_int-360; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                DrawPolygonFilled(image, &p1, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p2, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p3, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                DrawPolygonFilled(image, &p4, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                DrawPolygonFilled(image, &p5, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                
                DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                
                FreePolygon(&p1);
                FreePolygon(&p2);
                FreePolygon(&p3);
                FreePolygon(&p4);
                FreePolygon(&p5);
            }
        }
    }

    gdImageCopy(image_f, image, 0, 0, bord, bord, TileDim, TileDim);

     
    image_png = fopen(TileName, "w");
    gdImagePng(image_f, image_png);

    fclose(image_png);
    gdImageDestroy(image);
    gdImageDestroy(image_f);
    
    fclose(polyfile);
    
    return 0;
    
}



