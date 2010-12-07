/**
 *    Filename          : tiles_g.c

 *    Created           : 07 May 2009 (23:08:51)
 *    Created by        : StephPen - stephpen @at@ gmail . com

 *    Last Updated      : 23:24 21/11/2010
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


#include <stdio.h>
#include <stdlib.h>
#include <gd.h>
#include <math.h>
#include <getopt.h>

#include "read_gshhs.h"
#include "gpc.h"
#include "map_functions.h"
#include "map_projection.h"
#include "gshhs.h"

#define TILESGENERATOR "\n \
 _____ _ _            ____                           _             \n \
|_   _(_) | ___  ___ / ___| ___ _ __   ___ _ __ __ _| |_ ___  _ __ \n \
  | | | | |/ _ \\/ __| |  _ / _ \\ '_ \\ / _ \\ '__/ _` | __/ _ \\| '__|\n \
  | | | | |  __/\\__ \\ |_| |  __/ | | |  __/ | | (_| | || (_) | |   \n \
  |_| |_|_|\\___||___/\\____|\\___|_| |_|\\___|_|  \\__,_|\\__\\___/|_|   \n \
                Tiles Generator V0.3 - 07/12/2010 \n \
                    stephpen @at@ gmail . com\n\n"


#define USAGE   "\n \
Usage:  tiles_g [options]\n \
 --long_option -short_option [value] explain \n \
                                     limit \n \
                                     default \n \
 --n_tiles       -n   value   Number of tiles for 360 deg \n \
                              n_tiles must be even and >=1 \n \
                              default=1 \n \
 --x_tile        -x   value   X coord of tile \n \
                              0 <= x_tiles < nb_tiles-1 \n \
                              default=0 \n \
 --y_tile        -y   value   Y coord of tile \n \
                              0 <= y_tiles < nb_tiles-1 \n \
                              default=0 \n \
 --tile_size     -s   value   Size of the tile in pixel \n \
                              0< tile_size <4096 \n \
                              default=256 \n \
 --coast_file    -c   file    Path to polygon file \n \
                              (ex: poly-f-1.dat) \n \
                              Work with: --draw_polygons \n \
                                         --fill_polygons \n \
                              no default \n \
 --rivers        -r   file    Path to rivers file \n \
                              Active draw rivers \n \
                              (ex: river-f-1.dat) \n \
                              no default \n \
 --borbers       -b   file    Path to borders file \n \
                              Active draw borders \n \
                              (ex: borders-f-1.dat) \n \
                              no default \n \
 --tile_name     -t   file    Path to final tile file \n \
                              only .png format \n \
                              default tile.png \n \
 --draw_polygons -d           Draw only coastlines \n \
 --fill_polygons -f           Fill only land \n \
 --water_color        value   Color of the sea \n \
                              Format: 0xRRGGBB \n \
                              default 0xC6ECFF \n \
 --land_color         value   Color of the land \n \
                              Format: 0xRRGGBB \n \
                              default 0xF6E1B9 \n \
 --coast_color        value   Color of the coastlines \n \
                              Format: 0xRRGGBB \n \
                              default 0x0978AB \n \
 --rivers_color       value   Color of the rivers \n \
                              Format: 0xRRGGBB \n \
                              default 0x0978AB \n \
 --borders_color      value   Color of the borders \n \
                              Format: 0xRRGGBB \n \
                              default 0x646464 \n \
 --water_alpha        value   Transparency of water \n \
                              Format: 0xAA (max: 0x7F) \n \
                              default 0x00 \n \
 --land_alpha         value   Transparency of land \n \
                              Format: 0xAA (max: 0x7F) \n \
                              default 0x00 \n \
 --help          -h           This Help ! \n \
\n\n"



int main (int argc, char **argv)
{

    FILE    *polyfile = NULL;
    FILE    *riversfile = NULL;
    FILE    *bordersfile = NULL;

    //char    poly_file_name[256];
    PolygonFileHeader     header;
    gpc_polygon p1, p2, p3, p4, p5;
    gshhs_contour borders_contour, rivers_contour;

    int x, y;

    gdImagePtr image = NULL; /* Pointeur vers notre image */
    gdImagePtr image_f = NULL;  /* Pointeur vers notre image */
    FILE *image_png; /* Fichier image PNG */
    int water_color = 0, coast_color = 0, land_color = 0;
    int borders_color = 0, rivers_color = 0;
    //int grid_color;
    //int text_color;
    double long_max, long_min, lat_max, lat_min;
    int long_max_int, long_min_int, lat_max_int, lat_min_int;
    double origine_x, origine_y;

    int Nb_Tiles = 1;
    int X_Tile = 0, Y_Tile =0;
    int TileDim = 256; // Dimension des tiles (pixel)
    char TileName[256] = "tile.png";
    char bd_file[256];
    char rivers_file[256];
    char borders_file[256];
    double zoom;
    int bord;

    int flag_n_tiles = 0;
    int flag_x_tile = 0;
    int flag_y_tile = 0;
    int flag_tile_size = 0;
    int flag_coast_file = 0;
    int flag_rivers = 0;
    int flag_borders = 0;
    int flag_tile_name = 0;
    int flag_draw_polygons = 0;
    int flag_fill_polygons = 0;
    int flag_land_color = 0;
    int flag_water_color = 0;
    int flag_coast_color = 0;
    int flag_rivers_color = 0;
    int flag_borders_color = 0;
    int flag_water_alpha = 0;
    int flag_land_alpha = 0;
    int flag_help = 0;

    typedef struct
    {
        int red;
        int green;
        int blue;
    } FullColor;

    int color;
    FullColor WaterFullColor, CoastFullColor, LandFullColor;
    FullColor RiversFullColor, BordersFullColor;

    // Defaults Colors
    WaterFullColor.red      = 198;
    WaterFullColor.green    = 236;
    WaterFullColor.blue     = 255;

    LandFullColor.red       = 246;
    LandFullColor.green     = 225;
    LandFullColor.blue      = 185;

    CoastFullColor.red      = 9;
    CoastFullColor.green    = 120;
    CoastFullColor.blue     = 171;

    RiversFullColor.red     = 9;
    RiversFullColor.green   = 120;
    RiversFullColor.blue    = 171;

    BordersFullColor.red    = 100;
    BordersFullColor.green  = 100;
    BordersFullColor.blue   = 100;

    // GridFullColor.red       = 250;
    // GridFullColor.green     = 250;
    // GridFullColor.blue      = 250;

    // TextFullColor.red       = 0;
    // TextFullColor.green     = 0;
    // TextFullColor.blue      = 0;

    int WaterAlpha = 0;
    int LandAlpha = 0;


    int next_option;
	/* A string listing the valid short options letters */
	const char *short_options = "n:x:y:s:c:r:b:t:dfh";

    /* An array listing valid long options */
    static struct option long_options[] =
    {
        {"n_tiles",         required_argument,  NULL, 'n'},
        {"x_tile",          required_argument,  NULL, 'x'},
        {"y_tile",          required_argument,  NULL, 'y'},
        {"tile_size",       required_argument,  NULL, 's'},
        {"coast_file",      required_argument,  NULL, 'c'},
        {"rivers",          required_argument,  NULL, 'r'},
        {"borbers",         required_argument,  NULL, 'b'},
        {"tile_name",       required_argument,  NULL, 't'},
        {"draw_polygons",   no_argument,        NULL, 'd'},
        {"fill_polygons",   no_argument,        NULL, 'f'},
        {"land_color",      required_argument,  NULL, -100},
        {"water_color",     required_argument,  NULL, -101},
        {"coast_color",     required_argument,  NULL, -102},
        {"rivers_color",    required_argument,  NULL, -103},
        {"borders_color",   required_argument,  NULL, -104},
        {"water_alpha",     required_argument,  NULL, -105},
        {"land_alpha",      required_argument,  NULL, -106},

        {"help",            required_argument,  NULL, 'h'},
        {NULL, 0, NULL, 0}
    };

    int option_index = 0;
    
    if (argc == 1)
    {
        fprintf (stderr, TILESGENERATOR);
        fprintf (stderr, USAGE);
        exit(EXIT_SUCCESS);
    }
    
    printf(TILESGENERATOR);

    do
    {
        next_option = getopt_long ( argc, argv,
                                    short_options, long_options,
                                    &option_index);

        switch (next_option)
        {
            // Nombre de tiles pour 360 deg
            case 'n':
                if (optarg)
                {
                    sscanf(optarg, "%d", &Nb_Tiles);
                    printf("Nb_Tiles: %d\n", Nb_Tiles);

                    // test si nb_tiles est pair ou impair, uniquement les nombres pair sont acceptés
                    if (((Nb_Tiles & 1) && (Nb_Tiles != 1)) || (Nb_Tiles < 1))
                    {
                        fprintf (stderr, TILESGENERATOR);
                        fprintf (stderr, USAGE);
                        fprintf (stderr, "Tiles_G Error!: \n");
                        fprintf (stderr, "n_tiles must be even and >=1\n");
                        fprintf (stderr, "End!\n");
                        exit (EXIT_FAILURE);
                    }
                    flag_n_tiles = 1;
                }
                break;

            // la position x du tiles [0-(nb_tiles-1)]
            case 'x':
                if (optarg)
                {
                    sscanf(optarg, "%d", &X_Tile);
                    printf("X_Tiles: %d\n", X_Tile);
                    if (X_Tile<0 || X_Tile>=Nb_Tiles)
                    {
                        fprintf (stderr, TILESGENERATOR);
                        fprintf (stderr, USAGE);
                        fprintf (stderr, "Tiles_G Error!: \n");
                        fprintf (stderr, "0 <= x_tiles < nb_tiles-1\n");
                        fprintf (stderr, "End!\n");
                        exit (EXIT_FAILURE);
                    }
                    flag_x_tile = 1;
                }
                break;

            // la position y du tiles [0-(nb_tiles-1)]
            case 'y':
                if (optarg)
                {
                    sscanf(optarg, "%d", &Y_Tile);
                    printf("Y_Tiles: %d\n", Y_Tile);
                    if (Y_Tile<0 || Y_Tile>=Nb_Tiles)
                    {
                        fprintf (stderr, TILESGENERATOR);
                        fprintf (stderr, USAGE);
                        fprintf (stderr, "Tiles_G Error!: \n");
                        fprintf (stderr, "0 <= y_tiles < nb_tiles-1\n");
                        fprintf (stderr, "End!\n");
                        exit (EXIT_FAILURE);
                    }
                    flag_y_tile = 1;
                }
                break;

            // Taille du tile (default = 256) (max=4096)
            case 's':
                if (optarg)
                {
                    sscanf(optarg, "%d", &TileDim);
                    printf("Tile size: %d\n", TileDim);
                    if (TileDim < 0 || TileDim > 4096)
                    {
                        fprintf (stderr, TILESGENERATOR);
                        fprintf (stderr, USAGE);
                        fprintf (stderr, "Tiles_G Error!: \n");
                        fprintf (stderr, "0< tile_size <4096\n");
                        fprintf (stderr, "End!\n");
                        exit (EXIT_FAILURE);
                    }
                    flag_tile_size = 1;
                }
                break;

            // Coast file
            case 'c':
                if (optarg)
                {
                    sscanf(optarg, "%s", bd_file);
                    printf("Coast filename: %s\n", bd_file);
                    flag_coast_file = 1;
                }
                break;

            // Rivers file
            case 'r':
                if (optarg)
                {
                    sscanf(optarg, "%s", rivers_file);
                    printf("Rivers filename: %s\n", rivers_file);
                    flag_rivers = 1;
                }
                break;

            // Borders file
            case 'b':
                if (optarg)
                {
                    sscanf(optarg, "%s", borders_file);
                    printf("Borders filename: %s\n", borders_file);
                    flag_borders = 1;
                }
                break;

            // Tile name
            case 't':
                if (optarg)
                {
                    sscanf(optarg, "%s", TileName);
                    printf("Tile filename: %s\n", TileName);
                    flag_tile_name = 1;
                }
                break;

            // Land Color
            case -100:
                // printf("-100\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    LandFullColor.red = (color & 0xFF0000)>>16;
                    // printf (" rouge %x %d\n", red, red);
                    LandFullColor.green = (color & 0x00FF00)>>8;
                    // printf (" vert %x %d\n", green, green);
                    LandFullColor.blue = color & 0x0000FF;
                    // printf (" bleu %x %d\n", blue, blue);

                }
                flag_land_color = 1;
                break;

            //Water Color
            case -101:
                // printf("-101\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    WaterFullColor.red = (color & 0xFF0000)>>16;
                    // printf (" rouge %x %d\n", red, red);
                    WaterFullColor.green = (color & 0x00FF00)>>8;
                    // printf (" vert %x %d\n", green, green);
                    WaterFullColor.blue = color & 0x0000FF;
                    // printf (" bleu %x %d\n", blue, blue);

                }
                flag_water_color = 1;
                break;

            // Coast Color
            case -102:
                // printf("-102\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    CoastFullColor.red = (color & 0xFF0000)>>16;
                    // printf (" rouge %x %d\n", red, red);
                    CoastFullColor.green = (color & 0x00FF00)>>8;
                    // printf (" vert %x %d\n", green, green);
                    CoastFullColor.blue = color & 0x0000FF;
                    // printf (" bleu %x %d\n", blue, blue);

                }
                flag_coast_color = 1;
                break;

            // Rivers Color
            case -103:
                // printf("-103\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    RiversFullColor.red = (color & 0xFF0000)>>16;
                    // printf (" rouge %x %d\n", red, red);
                    RiversFullColor.green = (color & 0x00FF00)>>8;
                    // printf (" vert %x %d\n", green, green);
                    RiversFullColor.blue = color & 0x0000FF;
                    // printf (" bleu %x %d\n", blue, blue);

                }
                flag_rivers_color = 1;
                break;

            // Borders Color
            case -104:
                // printf("-104\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    BordersFullColor.red = (color & 0xFF0000)>>16;
                    // printf (" rouge %x %d\n", red, red);
                    BordersFullColor.green = (color & 0x00FF00)>>8;
                    // printf (" vert %x %d\n", green, green);
                    BordersFullColor.blue = color & 0x0000FF;
                    // printf (" bleu %x %d\n", blue, blue);

                }
                flag_borders_color = 1;
                break;

            // Water Alpha
            case -105:
                // printf("-105\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &WaterAlpha);
                    //printf (" Avec argument %d\n", color);

                    if (WaterAlpha > 127) WaterAlpha = 127;
                    if (WaterAlpha < 0) WaterAlpha = 0;
                }
                flag_water_alpha = 1;
                break;

            // Land Alpha
            case -106:
                // printf("-106\n");
                printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &LandAlpha);
                    //printf (" Avec argument %d\n", color);

                    if (LandAlpha > 127) LandAlpha = 127;
                    if (LandAlpha < 0) LandAlpha = 0;
                }
                flag_land_alpha = 1;
                break;

            case 'd':
                printf("Draw polygons\n");
                flag_draw_polygons = 1;
                break;

            case 'f':
                printf("Fill polygons\n");
                flag_fill_polygons = 1;
                break;

            case 'h':
                fprintf (stderr, TILESGENERATOR);
                fprintf (stderr, USAGE);
                flag_help = 1;
                exit(EXIT_SUCCESS);
                break;

            case '?':
                    printf("Unknown option !\n");
               break;

        }
    }
    while(next_option !=-1);

    if (optind < argc)
	{
		printf ("this arguments are not reconized: ");
		while (optind < argc)
		printf ("%s ", argv[optind++]);
		printf ("\n");
	}

    printf("\n\n");
    
    if (flag_draw_polygons || flag_fill_polygons)
    {
        if (flag_coast_file)
        {
            polyfile = fopen(bd_file, "rb");
            if (polyfile == NULL)
            {
                fprintf (stderr, TILESGENERATOR);
                fprintf (stderr, USAGE);
                fprintf (stderr, "Tiles_G Error!: \n");
                fprintf (stderr, "Could not open file: %s\n", bd_file);
                exit(EXIT_FAILURE);
            }

            ReadPolygonFileHeader (polyfile, &header);
            //printf("Header_Pasx: %d\n", header.pasx);
        }
        else
        {
            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "Missing --coast_file -c option for coast file\n");
            exit(EXIT_FAILURE);
        }
    }

    if (flag_rivers)
    {
        riversfile = fopen(rivers_file, "rb");
        if (riversfile == NULL)
        {
            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "Could not open file: %s\n", rivers_file);
            exit(EXIT_FAILURE);
        }
        if ((flag_coast_file == 0) || (flag_draw_polygons == 0) || (flag_fill_polygons ==0))
            ReadPolygonFileHeader (riversfile, &header);
            //printf("Header_Pasx: %d\n", header.pasx);
    }

    if (flag_borders)
    {
        bordersfile = fopen(borders_file, "rb");
        if (bordersfile == NULL)
        {
            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "Could not open file: %s\n", borders_file);
            exit(EXIT_FAILURE);
        }
        if (((flag_coast_file == 0) || (flag_draw_polygons == 0) || (flag_fill_polygons ==0)) && (flag_rivers == 0))
            ReadPolygonFileHeader (bordersfile, &header);
            //printf("Header_Pasx: %d\n", header.pasx);
    }

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

    //printf("%lf\n", MercatorInverseLatitudeSimple(-1* (256-origine_y)*M_PI / (180.0*zoom)));
    // Création de l'image
    printf("Map_Width: %d, Map_Height: %d\n", TileDim, TileDim);
    bord=10;
    image   = gdImageCreate(TileDim + 2 * bord, TileDim + 2 * bord);
    image_f = gdImageCreate(TileDim, TileDim);
    //image = gdImageCreate(TileDim, TileDim);

    // Création des couleurs
    if (flag_water_alpha) water_color = gdImageColorAllocateAlpha(image, WaterFullColor.red, WaterFullColor.green, WaterFullColor.blue, WaterAlpha);
    else                  water_color = gdImageColorAllocate(image, WaterFullColor.red, WaterFullColor.green, WaterFullColor.blue);
    if (flag_land_alpha)  land_color  = gdImageColorAllocateAlpha(image, LandFullColor.red, LandFullColor.green, LandFullColor.blue, LandAlpha);
    else                  land_color  = gdImageColorAllocate(image, LandFullColor.red, LandFullColor.green, LandFullColor.blue);
    coast_color     = gdImageColorAllocate(image, CoastFullColor.red,   CoastFullColor.green,   CoastFullColor.blue);
    borders_color   = gdImageColorAllocate(image, BordersFullColor.red, BordersFullColor.green, BordersFullColor.blue);
    rivers_color    = gdImageColorAllocate(image, RiversFullColor.red,  RiversFullColor.green,  RiversFullColor.blue);
    // grid_color      = gdImageColorAllocate(image, GridFullColor.red,    GridFullColor.green,    GridFullColor.blue);
    // text_color      = gdImageColorAllocate(image, TextFullColor.red,    TextFullColor.green,    TextFullColor.blue);


    // Cas où tout va bien
    if (long_min_int>=0 && long_max_int<=360)
    {
        for (x=long_min_int; x<long_max_int; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                if (flag_draw_polygons || flag_fill_polygons)
                    ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                }

                if (flag_borders)
                {
                    ReadLineFile(bordersfile, x, y, &borders_contour);
                    DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                }

                if (flag_rivers)
                {
                    ReadLineFile(riversfile, x, y, &rivers_contour);
                    DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                }

                if (flag_draw_polygons)
                {
                    DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                }

                if (flag_draw_polygons || flag_fill_polygons)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders)
                    FreeLine(&borders_contour);

                if (flag_rivers)
                    FreeLine(&rivers_contour);
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
                if (flag_draw_polygons || flag_fill_polygons)
                    ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                }

                if (flag_borders)
                {
                    ReadLineFile(bordersfile, x, y, &borders_contour);
                    DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                }

                if (flag_rivers)
                {
                    ReadLineFile(riversfile, x, y, &rivers_contour);
                    DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                }

                if (flag_draw_polygons)
                {
                    DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                }

                if (flag_draw_polygons || flag_fill_polygons)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders)
                    FreeLine(&borders_contour);

                if (flag_rivers)
                    FreeLine(&rivers_contour);
            }
        }

        for (x=long_min_int+360; x<360; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                if (flag_draw_polygons || flag_fill_polygons)
                    ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    DrawPolygonFilled(image, &p1, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                }

                if (flag_borders)
                {
                    ReadLineFile(bordersfile, x, y, &borders_contour);
                    DrawLine(image, &borders_contour, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                }

                if (flag_rivers)
                {
                    ReadLineFile(riversfile, x, y, &rivers_contour);
                    DrawLine(image, &rivers_contour, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                }

                if (flag_draw_polygons)
                {
                    DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                }

                if (flag_draw_polygons || flag_fill_polygons)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders)
                    FreeLine(&borders_contour);

                if (flag_rivers)
                    FreeLine(&rivers_contour);
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
                if (flag_draw_polygons || flag_fill_polygons)
                    ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                }

                if (flag_borders)
                {
                    ReadLineFile(bordersfile, x, y, &borders_contour);
                    DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                }

                if (flag_rivers)
                {
                    ReadLineFile(riversfile, x, y, &rivers_contour);
                    DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                }

                if (flag_draw_polygons)
                {
                    DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                }

                if (flag_draw_polygons || flag_fill_polygons)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders)
                    FreeLine(&borders_contour);

                if (flag_rivers)
                    FreeLine(&rivers_contour);
            }
        }

        for (x=0; x<long_max_int-360; x=x+header.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+header.pasy)
            {
                if (flag_draw_polygons || flag_fill_polygons)
                    ReadPolygonFile(polyfile, x, y, header.pasx, header.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    DrawPolygonFilled(image, &p1, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                }

                if (flag_borders)
                {
                    ReadLineFile(bordersfile, x, y, &borders_contour);
                    DrawLine(image, &borders_contour, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                }

                if (flag_rivers)
                {
                    ReadLineFile(riversfile, x, y, &rivers_contour);
                    DrawLine(image, &rivers_contour, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                }

                if (flag_draw_polygons)
                {
                    DrawPolygonContour(image, &p1, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, header.pasx, header.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                }

                if (flag_draw_polygons || flag_fill_polygons)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders)
                    FreeLine(&borders_contour);

                if (flag_rivers)
                    FreeLine(&rivers_contour);
            }
        }
    }

    gdImageCopy(image_f, image, 0, 0, bord, bord, TileDim, TileDim);

    image_png = fopen(TileName, "w");
    gdImagePng(image_f, image_png);

    fclose(image_png);
    gdImageDestroy(image);
    gdImageDestroy(image_f);

    if (flag_draw_polygons || flag_fill_polygons)
        fclose(polyfile);

    if (flag_rivers)
        fclose(riversfile);

    if (flag_borders)
        fclose(bordersfile);

    exit(EXIT_SUCCESS);

}



