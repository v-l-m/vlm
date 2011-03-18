/**
 *  Filename          : tiles_g.c
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


#include <stdio.h>
#include <stdlib.h>
#include <gd.h>
#include <math.h>
#include <getopt.h>
#include <sys/stat.h>

#include "read_gshhs.h"
#include "gpc.h"
#include "map_functions.h"
#include "map_projection.h"
#include "gshhs.h"

#define TILEMAXDIM 32768

#define TILESGENERATOR "\n \
 _____ _ _            ____                           _             \n \
|_   _(_) | ___  ___ / ___| ___ _ __   ___ _ __ __ _| |_ ___  _ __ \n \
  | | | | |/ _ \\/ __| |  _ / _ \\ '_ \\ / _ \\ '__/ _` | __/ _ \\| '__|\n \
  | | | | |  __/\\__ \\ |_| |  __/ | | |  __/ | | (_| | || (_) | |   \n \
  |_| |_|_|\\___||___/\\____|\\___|_| |_|\\___|_|  \\__,_|\\__\\___/|_|   \n \
                Tiles Generator V0.4 - 29/12/2010 \n \
                       stephpen@gmail.com\n\n"


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
 --topo          -a   file    Path to topo file \n \
                              Fill land with topography\n \
 --memory        -m           Mount all data file in RAM \n \
                              Please use this option only \n \
                              for -n 1 or when size > 1000 \n \
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




    FILE    *PolyFile = NULL;
    FILE    *RiversFile = NULL;
    FILE    *BordersFile = NULL;
    FILE    *EtopoFile = NULL;

    PolygonFileHeader     PolyHeader;
    PolygonFileHeader     RiversHeader;
    PolygonFileHeader     BordersHeader;

    gpc_polygon p1, p2, p3, p4, p5;
    gpc_polygon ***PolyRAM = NULL;  /* Tableau 3D pour mettre en RAM les polygones */
                                    /* Les 2 premieres  dim sont pour longitude et latitude */
                                    /* La 3eme pour les polygones [0] land */
                                    /*                            [1] lake */
                                    /*                            [2] island in lake */
                                    /*                            [3] pond in island in lake */
                                    /*                            [4] recif */



    gshhs_contour borders_contour, rivers_contour;
    gshhs_contour **RiversRAM = NULL, **BordersRAM = NULL;

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
    char TilePath[1024] = "tile.png";
    char BdPath[1024];
    char RiversPath[1024];
    char BordersPath[1024];
    char EtopoPath[1024];
    double zoom = 1;
    int bord = 10;

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
    int FlagAlpha = 0; // For DrawETOPO()
                       // 0 > No Alpha canal
                       // 1 > Water is alpha canal
                       // 2 > Land is alpha canal
                       // 3 > Water and land are alpha canal
    int flag_alti = 0;
    int flag_memory = 0;
    int flag_help = 0;
    int flag_verbose = 0;

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
	const char *short_options = "n:x:y:s:c:r:b:t:dfa:mhv";

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
        {"alti",            required_argument,  NULL, 'a'},
        {"memory",          no_argument,        NULL, 'm'},
        {"land_color",      required_argument,  NULL, -100},
        {"water_color",     required_argument,  NULL, -101},
        {"coast_color",     required_argument,  NULL, -102},
        {"rivers_color",    required_argument,  NULL, -103},
        {"borders_color",   required_argument,  NULL, -104},
        {"water_alpha",     required_argument,  NULL, -105},
        {"land_alpha",      required_argument,  NULL, -106},

        {"verbose",         no_argument,        NULL, 'v'},
        {"help",            no_argument,        NULL, 'h'},
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
                    if (flag_verbose) printf("Nb_Tiles: %d\n", Nb_Tiles);

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
                    if (flag_verbose) printf("X_Tiles: %d\n", X_Tile);
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
                    if (flag_verbose) printf("Y_Tiles: %d\n", Y_Tile);
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
                    if (flag_verbose) printf("Tile size: %d\n", TileDim);
                    if (TileDim < 0 || TileDim > TILEMAXDIM)
                    {
                        fprintf (stderr, TILESGENERATOR);
                        fprintf (stderr, USAGE);
                        fprintf (stderr, "Tiles_G Error!: \n");
                        fprintf (stderr, "0< tile_size <=%d\n", TILEMAXDIM);
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
                    sscanf(optarg, "%s", BdPath);
                    if (flag_verbose) printf("Coast filename: %s\n", BdPath);
                    flag_coast_file = 1;
                }
                break;

            // Rivers file
            case 'r':
                if (optarg)
                {
                    sscanf(optarg, "%s", RiversPath);
                    if (flag_verbose) printf("Rivers filename: %s\n", RiversPath);
                    flag_rivers = 1;
                }
                break;

            // Borders file
            case 'b':
                if (optarg)
                {
                    sscanf(optarg, "%s", BordersPath);
                    if (flag_verbose) printf("Borders filename: %s\n", BordersPath);
                    flag_borders = 1;
                }
                break;

            // Tile name
            case 't':
                if (optarg)
                {
                    sscanf(optarg, "%s", TilePath);
                    if (flag_verbose) printf("Tile filename: %s\n", TilePath);
                    flag_tile_name = 1;
                }
                break;

            // Draw polygons
            case 'd':
                if (flag_verbose) printf("Draw polygons\n");
                flag_draw_polygons = 1;
                break;

            // Fill polygons
            case 'f':
                if (flag_verbose) printf("Fill polygons\n");
                flag_fill_polygons = 1;
                break;

            // Fill topography
            case 'a':
                if (optarg)
                {
                    sscanf(optarg, "%s", EtopoPath);
                    if (flag_verbose) printf("ETOPO filename: %s\n", EtopoPath);
                    flag_alti = 1;
                }
                break;

            // Use memory
            case 'm':
                if (flag_verbose) printf("Memory\n");
                flag_memory = 1;
                break;

            // Land Color
            case -100:
                // printf("-100\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    LandFullColor.red = (color & 0xFF0000)>>16;
                    if (flag_verbose) printf (" rouge %x %d\n", LandFullColor.red, LandFullColor.red);
                    LandFullColor.green = (color & 0x00FF00)>>8;
                    if (flag_verbose) printf (" vert %x %d\n", LandFullColor.green, LandFullColor.green);
                    LandFullColor.blue = color & 0x0000FF;
                    if (flag_verbose) printf (" bleu %x %d\n", LandFullColor.blue, LandFullColor.blue);

                }
                flag_land_color = 1;
                break;

            //Water Color
            case -101:
                // printf("-101\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    WaterFullColor.red = (color & 0xFF0000)>>16;
                    if (flag_verbose) printf (" rouge %x %d\n", WaterFullColor.red, WaterFullColor.red);
                    WaterFullColor.green = (color & 0x00FF00)>>8;
                    if (flag_verbose) printf (" vert %x %d\n", WaterFullColor.green, WaterFullColor.green);
                    WaterFullColor.blue = color & 0x0000FF;
                    if (flag_verbose) printf (" bleu %x %d\n", WaterFullColor.blue, WaterFullColor.blue);

                }
                flag_water_color = 1;
                break;

            // Coast Color
            case -102:
                // printf("-102\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    CoastFullColor.red = (color & 0xFF0000)>>16;
                    if (flag_verbose) printf (" rouge %x %d\n", CoastFullColor.red, CoastFullColor.red);
                    CoastFullColor.green = (color & 0x00FF00)>>8;
                    if (flag_verbose) printf (" vert %x %d\n", CoastFullColor.green, CoastFullColor.green);
                    CoastFullColor.blue = color & 0x0000FF;
                    if (flag_verbose) printf (" bleu %x %d\n", CoastFullColor.blue, CoastFullColor.blue);

                }
                flag_coast_color = 1;
                break;

            // Rivers Color
            case -103:
                // printf("-103\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    RiversFullColor.red = (color & 0xFF0000)>>16;
                    if (flag_verbose) printf (" rouge %x %d\n", RiversFullColor.red, RiversFullColor.red);
                    RiversFullColor.green = (color & 0x00FF00)>>8;
                    if (flag_verbose) printf (" vert %x %d\n", RiversFullColor.green, RiversFullColor.green);
                    RiversFullColor.blue = color & 0x0000FF;
                    if (flag_verbose) printf (" bleu %x %d\n", RiversFullColor.blue, RiversFullColor.blue);

                }
                flag_rivers_color = 1;
                break;

            // Borders Color
            case -104:
                // printf("-104\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &color);
                    //printf (" Avec argument %d\n", color);

                    BordersFullColor.red = (color & 0xFF0000)>>16;
                    if (flag_verbose) printf (" rouge %x %d\n", BordersFullColor.red, BordersFullColor.red);
                    BordersFullColor.green = (color & 0x00FF00)>>8;
                    if (flag_verbose) printf (" vert %x %d\n", BordersFullColor.green, BordersFullColor.green);
                    BordersFullColor.blue = color & 0x0000FF;
                    if (flag_verbose) printf (" bleu %x %d\n", BordersFullColor.blue, BordersFullColor.blue);

                }
                flag_borders_color = 1;
                break;

            // Water Alpha
            case -105:
                // printf("-105\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &WaterAlpha);
                    //printf (" Avec argument %d\n", color);

                    if (WaterAlpha > 127) WaterAlpha = 127;
                    if (WaterAlpha < 0) WaterAlpha = 0;
                }
                flag_water_alpha = 1;
                FlagAlpha = FlagAlpha + 1;
                break;

            // Land Alpha
            case -106:
                // printf("-106\n");
                if (flag_verbose) printf ("Option %s", long_options[option_index].name);
                if (optarg)
                {
                    if (flag_verbose) printf (" Argument %s\n", optarg);
                    sscanf(optarg, "%x", &LandAlpha);
                    //printf (" Avec argument %d\n", color);

                    if (LandAlpha > 127) LandAlpha = 127;
                    if (LandAlpha < 0) LandAlpha = 0;
                }
                flag_land_alpha = 1;
                FlagAlpha = FlagAlpha + 2;
                break;

            case 'v':
                flag_verbose = 1;
                break;

            case 'h':
                printf (TILESGENERATOR);
                printf (USAGE);
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


    if (flag_draw_polygons || flag_fill_polygons || flag_coast_file)
    {
        if (flag_coast_file)
        {
            PolyFile = fopen(BdPath, "rb");
            if (PolyFile == NULL)
            {
                fprintf (stderr, TILESGENERATOR);
                fprintf (stderr, USAGE);
                fprintf (stderr, "Tiles_G Error!: \n");
                fprintf (stderr, "Could not open file: %s\n", BdPath);
                exit(EXIT_FAILURE);
            }

            ReadPolygonFileHeader (PolyFile, &PolyHeader);

            if (flag_memory)
            {
                PolyRAM=PolygonToMemory (PolyFile);
                //printf("fin %d\n", PolyRAM[120][38][0].num_contours);
            }
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
        RiversFile = fopen(RiversPath, "rb");
        if (RiversFile == NULL)
        {
            if (PolyFile != NULL) fclose(PolyFile);

            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "Could not open file: %s\n", RiversPath);
            exit(EXIT_FAILURE);
        }

        ReadPolygonFileHeader (RiversFile, &RiversHeader);

        if (flag_memory)
        {
            RiversRAM=LineToMemory (RiversFile);
            //printf("fin %d\n", PolyRAM[120][38][0].num_contours);
        }
    }

    if (flag_borders)
    {
        BordersFile = fopen(BordersPath, "rb");
        if (BordersFile == NULL)
        {
            if (PolyFile != NULL) fclose(PolyFile);
            if (RiversFile != NULL) fclose(RiversFile);

            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "Could not open file: %s\n", BordersPath);
            exit(EXIT_FAILURE);
        }

        ReadPolygonFileHeader (BordersFile, &BordersHeader);

        if (flag_memory)
        {
            BordersRAM=LineToMemory (BordersFile);
            //printf("fin %d\n", PolyRAM[120][38][0].num_contours);
        }

        }

    if (flag_alti)
    {
        if (flag_coast_file)
        {
            EtopoFile = fopen(EtopoPath, "rb");
            if (EtopoFile == NULL)
            {
                if (PolyFile != NULL) fclose(PolyFile);
                if (RiversFile != NULL) fclose(RiversFile);
                if (BordersFile != NULL) fclose(BordersFile);

                fprintf (stderr, TILESGENERATOR);
                fprintf (stderr, USAGE);
                fprintf (stderr, "Tiles_G Error!: \n");
                fprintf (stderr, "Could not open file: %s\n", EtopoPath);
                exit(EXIT_FAILURE);
            }
        }
        else
        {
            if (PolyFile != NULL) fclose(PolyFile);
            if (RiversFile != NULL) fclose(RiversFile);
            if (BordersFile != NULL) fclose(BordersFile);

            fprintf (stderr, TILESGENERATOR);
            fprintf (stderr, USAGE);
            fprintf (stderr, "Tiles_G Error!: \n");
            fprintf (stderr, "This option works with --coast_file -c option\n");
            fprintf (stderr, "Missing --coast_file -c option for coast file\n");
            exit(EXIT_FAILURE);
        }

    }

    // Facteur de zoom (pour le dessin)
    zoom = (double)Nb_Tiles * (double)TileDim  / 360.0;
    if (flag_verbose) printf("Zoom: %lf px/deg\n", zoom);

    //Détermination des longitudes mini, maxi, et origine image x
    long_min = (X_Tile * 360.0 / Nb_Tiles) - 180;
    long_max = ((X_Tile+1) * 360.0 / Nb_Tiles) -180;
    if (flag_verbose) printf("long_min: %lf, long_max: %lf\n", long_min, long_max);

    if (long_min>=0)    long_min_int=   floor(fabs(long_min));
    else                long_min_int=  -ceil(fabs(long_min));
    if (long_max>=0)    long_max_int=   ceil(fabs(long_max));
    else                long_max_int=  -floor(fabs(long_max));

    origine_x = -long_min * zoom;

    if (flag_verbose) printf("long_min: %d, long_max: %d, origine_x: %lf\n", long_min_int, long_max_int, origine_x);

    //Détermination des latitudes mini, maxi, et origine image y
    lat_min = MercatorInverseLatitudeSimple((((double)Nb_Tiles / 2) - (Y_Tile+1)) * (360.0 / Nb_Tiles) * M_PI / 180.0);
    lat_max = MercatorInverseLatitudeSimple((((double)Nb_Tiles / 2) - (Y_Tile)) * (360.0 / Nb_Tiles) * M_PI / 180.0);
    if (flag_verbose) printf("lat_min: %lf, lat_max: %lf\n", lat_min, lat_max);

    if (lat_min>=0)     lat_min_int=     floor(fabs(lat_min));
    else                lat_min_int=    -ceil(fabs(lat_min));
    if (lat_max>=0)     lat_max_int=     ceil(fabs(lat_max));
    else                lat_max_int=    -floor(fabs(lat_max));

    //origine_y = -MercatorLatitudeSimple(lat_min)*180.0*zoom/M_PI;
    origine_y = -((((double)Nb_Tiles / 2) - (Y_Tile + 1)) * (360.0 / Nb_Tiles)) * zoom;

    if (flag_verbose) printf("lat_min: %d, lat_max: %d, origine_y: %lf\n", lat_min_int, lat_max_int, origine_y);

    //printf("%lf\n", MercatorInverseLatitudeSimple(-1* (256-origine_y)*M_PI / (180.0*zoom)));
    // Création de l'image
    if (flag_verbose) printf("Map_Width: %d, Map_Height: %d\n", TileDim, TileDim);
    bord = 10;
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

    if (flag_alti)
    {
        if (flag_memory)
        {
            // Cas où tout va bien
            if (long_min_int>=0 && long_max_int<=360)
            {
                for (x=long_min_int; x<long_max_int; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }
            }

            // Cas où long_min <0
            if (long_min_int<0)
            {
                for (x=0; x<long_max_int; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                for (x=long_min_int+360; x<360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                }
            }

            // Cas où long_max >360
            if (long_max_int>360)
            {
                for (x=long_min_int; x<360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                for (x=0; x<long_max_int-360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                }
            }
        }
        else
        {
            // Cas où tout va bien
            if (long_min_int>=0 && long_max_int<=360)
            {
                for (x=long_min_int; x<long_max_int; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                        DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);

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
                for (x=0; x<long_max_int; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                        DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);

                        FreePolygon(&p1);
                        FreePolygon(&p2);
                        FreePolygon(&p3);
                        FreePolygon(&p4);
                        FreePolygon(&p5);
                    }
                }

                for (x=long_min_int+360; x<360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                       ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                        DrawPolygonFilled(image, &p1, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);

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
                for (x=long_min_int; x<360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                        DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);

                        FreePolygon(&p1);
                        FreePolygon(&p2);
                        FreePolygon(&p3);
                        FreePolygon(&p4);
                        FreePolygon(&p5);
                    }
                }

                for (x=0; x<long_max_int-360; x=x+PolyHeader.pasx)
                {
                    for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
                    {
                        ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                        DrawPolygonFilled(image, &p1, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);

                        FreePolygon(&p1);
                        FreePolygon(&p2);
                        FreePolygon(&p3);
                        FreePolygon(&p4);
                        FreePolygon(&p5);
                    }
                }
            }
        }
        DrawEtopo(image, EtopoFile, flag_memory, TileDim, bord, origine_x, origine_y, zoom, land_color, water_color, FlagAlpha);
    }

    // Cas où tout va bien
    if (long_min_int>=0 && long_max_int<=360)
    {
        for (x=long_min_int; x<long_max_int; x=x+PolyHeader.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
            {
                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                    ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    if (flag_memory)
                    {
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                    else
                    {
                        DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                        DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                        DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                if (flag_borders)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &BordersRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                    else
                    {
                        ReadLineFile(BordersFile, x, y, &borders_contour);
                        DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                }

                if (flag_rivers)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &RiversRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                    else
                    {
                        ReadLineFile(RiversFile, x, y, &rivers_contour);
                        DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                }

                if (flag_draw_polygons)
                {
                    if (flag_memory)
                    {
                        DrawPolygonContour(image, &PolyRAM[x][y+90][0], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][1], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][2], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][3], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][4], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                    else
                    {
                        DrawPolygonContour(image, &p1, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p2, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p3, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p4, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p5, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                }

                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders && flag_memory == 0)
                    FreeLine(&borders_contour);

                if (flag_rivers && flag_memory == 0)
                    FreeLine(&rivers_contour);

            }
        }
    }

    // Cas où long_min <0
    if (long_min_int<0)
    {
        for (x=0; x<long_max_int; x=x+PolyHeader.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
            {
                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                    ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                    else
                    {
                    DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                if (flag_borders)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &BordersRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                    else
                    {
                        ReadLineFile(BordersFile, x, y, &borders_contour);
                        DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                }

                if (flag_rivers)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &RiversRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                    else
                    {
                        ReadLineFile(RiversFile, x, y, &rivers_contour);
                        DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                }

                if (flag_draw_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonContour(image, &PolyRAM[x][y+90][0], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][1], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][2], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][3], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][4], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                    else
                    {
                    DrawPolygonContour(image, &p1, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                }

                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders && flag_memory == 0)
                    FreeLine(&borders_contour);

                if (flag_rivers && flag_memory == 0)
                    FreeLine(&rivers_contour);

            }
        }

        for (x=long_min_int+360; x<360; x=x+PolyHeader.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
            {
                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                    ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                    else
                    {
                    DrawPolygonFilled(image, &p1, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                if (flag_borders)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &BordersRAM[x][y+90], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                    }
                    else
                    {
                        ReadLineFile(BordersFile, x, y, &borders_contour);
                        DrawLine(image, &borders_contour, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                    }
                }

                if (flag_rivers)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &RiversRAM[x][y+90], origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                    else
                    {
                        ReadLineFile(RiversFile, x, y, &rivers_contour);
                        DrawLine(image, &rivers_contour, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                }

                if (flag_draw_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonContour(image, &PolyRAM[x][y+90][0], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][1], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][2], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][3], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][4], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    }
                    else
                    {
                    DrawPolygonContour(image, &p1, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord-360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    }
                }

                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders && flag_memory == 0)
                    FreeLine(&borders_contour);

                if (flag_rivers && flag_memory == 0)
                    FreeLine(&rivers_contour);

            }
        }
    }

    // Cas où long_max >360
    if (long_max_int>360)
    {
        for (x=long_min_int; x<360; x=x+PolyHeader.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
            {
                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                    ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                    else
                    {
                    DrawPolygonFilled(image, &p1, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                if (flag_borders)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &BordersRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                    else
                    {
                        ReadLineFile(BordersFile, x, y, &borders_contour);
                        DrawLine(image, &borders_contour, origine_x+bord, TileDim-origine_y+bord, zoom, borders_color);
                    }
                }

                if (flag_rivers)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &RiversRAM[x][y+90], origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                    else
                    {
                        ReadLineFile(RiversFile, x, y, &rivers_contour);
                        DrawLine(image, &rivers_contour, origine_x+bord, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                }

                if (flag_draw_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonContour(image, &PolyRAM[x][y+90][0], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][1], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][2], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][3], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &PolyRAM[x][y+90][4], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                    else
                    {
                    DrawPolygonContour(image, &p1, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p2, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p3, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p4, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    DrawPolygonContour(image, &p5, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord, TileDim-origine_y+bord, zoom, coast_color);
                    }
                }

                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders && flag_memory == 0)
                    FreeLine(&borders_contour);

                if (flag_rivers && flag_memory == 0)
                    FreeLine(&rivers_contour);

            }
        }

        for (x=0; x<long_max_int-360; x=x+PolyHeader.pasx)
        {
            for (y=lat_min_int; y<lat_max_int; y=y+PolyHeader.pasy)
            {
                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                    ReadPolygonFile(PolyFile, x, y, PolyHeader.pasx, PolyHeader.pasy, &p1, &p2, &p3, &p4, &p5);

                if (flag_fill_polygons)
                {
                    if (flag_memory)
                    {
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][0], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][1], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][2], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][3], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &PolyRAM[x][y+90][4], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                    else
                    {
                    DrawPolygonFilled(image, &p1, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p2, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p3, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    DrawPolygonFilled(image, &p4, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, water_color);
                    DrawPolygonFilled(image, &p5, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, land_color);
                    }
                }

                if (flag_borders)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &BordersRAM[x][y+90], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                    }
                    else
                    {
                        ReadLineFile(BordersFile, x, y, &borders_contour);
                        DrawLine(image, &borders_contour, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, borders_color);
                    }
                }

                if (flag_rivers)
                {
                    if (flag_memory)
                    {
                        DrawLine(image, &RiversRAM[x][y+90], origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                    else
                    {
                        ReadLineFile(RiversFile, x, y, &rivers_contour);
                        DrawLine(image, &rivers_contour, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, rivers_color);
                    }
                }

                if (flag_draw_polygons)
                {
                    if (flag_memory)
                    {
                        DrawPolygonContour(image, &PolyRAM[x][y+90][0], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][1], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][2], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][3], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &PolyRAM[x][y+90][4], x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    }
                    else
                    {
                        DrawPolygonContour(image, &p1, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p2, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p3, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p4, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                        DrawPolygonContour(image, &p5, x, y, PolyHeader.pasx, PolyHeader.pasy, origine_x+bord+360*zoom, TileDim-origine_y+bord, zoom, coast_color);
                    }
                }

                if ((flag_draw_polygons || flag_fill_polygons) && flag_memory == 0)
                {
                    FreePolygon(&p1);
                    FreePolygon(&p2);
                    FreePolygon(&p3);
                    FreePolygon(&p4);
                    FreePolygon(&p5);
                }

                if (flag_borders && flag_memory == 0)
                    FreeLine(&borders_contour);

                if (flag_rivers && flag_memory == 0)
                    FreeLine(&rivers_contour);

            }
        }
    }

    gdImageCopy(image_f, image, 0, 0, bord, bord, TileDim, TileDim);

    umask(S_IWOTH);
    image_png = fopen(TilePath, "w");
    gdImagePng(image_f, image_png);

    fclose(image_png);
    gdImageDestroy(image);
    gdImageDestroy(image_f);

    if (flag_draw_polygons || flag_fill_polygons || flag_coast_file)
        fclose(PolyFile);

    if (flag_rivers)
        fclose(RiversFile);

    if (flag_borders)
        fclose(BordersFile);

    if (flag_alti)
        fclose(EtopoFile);

    if (flag_memory)
    {
        if (flag_coast_file) FreePolygonToMemory (PolyRAM);
        if (flag_rivers) FreeLineToMemory (RiversRAM);
        if (flag_borders) FreeLineToMemory (BordersRAM);
    }

    exit(EXIT_SUCCESS);

}



