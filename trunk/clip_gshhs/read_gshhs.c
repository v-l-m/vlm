/**
 * $Id: read_gshhs.c,v 0.0 2008/06/18 $
 *
 * (c) 2008 by Stephane PENOT
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
 * Contact: <stephpen@gmail.com>
 */

#include "gshhs.h"
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <math.h>
#include "read_gshhs.h"

void GshhsToGpcFile(FILE *gpc_file, gshhs_polygons *polygons, int id_poly)
{
    int i;
    for (i = 0; i < polygons->contour[id_poly].nb_point - 1; i++) {
        fprintf(gpc_file, "%10d %10d\n", polygons->contour[id_poly].vertex[i].x,
                                         polygons->contour[id_poly].vertex[i].y);
    }
}

void read_gshhs(FILE *gshhs_file, gshhs_polygons *polygons, int debug)
{
    /* declaration des variables */
    double  lon, lat;
    int     k, max_east = 270000000, n_read, flip;
    int     version;
    int     i = 0;
    /*Polygone *tableau;*/
    gshhs_vertex_list *temp_polygons;
    
    struct POINT p;
    struct GSHHS h;
       
    n_read = fread ((void *)&h, (size_t)sizeof (struct GSHHS), (size_t)1, gshhs_file);
    version = (h.flag >> 8) & 255;
    flip = (version != GSHHS_DATA_RELEASE);    /* Take as sign that byte-swabbing is needed */
    
    while (n_read == 1) 
    {
        if (flip) 
        {
            h.id = swabi4 ((unsigned int)h.id);
            h.n  = swabi4 ((unsigned int)h.n);
            h.west  = swabi4 ((unsigned int)h.west);
            h.east  = swabi4 ((unsigned int)h.east);
            h.south = swabi4 ((unsigned int)h.south);
            h.north = swabi4 ((unsigned int)h.north);
            h.area  = swabi4 ((unsigned int)h.area);
            h.flag  = swabi4 ((unsigned int)h.flag);
        }
        
        if (h.id == 0)
        {
            polygons->contour = NULL;
            i = 1024;
            polygons->contour = malloc(i * sizeof(gshhs_vertex_list));
            if ( polygons->contour == NULL )
            {
                fprintf(stderr,"Allocation impossible \n");
                exit(EXIT_FAILURE);
            }
        }
        if (h.id == i)
        {
            /*Polygone *temp_tableau;*/
            i = i + 1024;
            temp_polygons = NULL;
            temp_polygons = realloc(polygons->contour, i * sizeof(gshhs_vertex_list));
            if (temp_polygons == NULL)
            {
                fprintf(stderr,"Reellocation impossible \n");
                free(polygons->contour);
                exit(EXIT_FAILURE);
            }
            else
            {
                polygons->contour = temp_polygons;
            }
        }
        
        polygons->contour[h.id].id = h.id;
        if (h.south == -90000000) {
            h.n = h.n + 2;
        }
        
        polygons->contour[h.id].nb_point    = h.n;
        polygons->contour[h.id].level       = h.flag & 255;
        polygons->contour[h.id].version     = (h.flag >> 8) & 255;
        polygons->contour[h.id].greenwich   = (h.flag >> 16) & 255;
        polygons->contour[h.id].source      = (h.flag >> 24) & 255; /* Either =1 = WVS or <>1 = CIA (WDBII) pedigree */
        polygons->contour[h.id].long_min    = h.west;
        polygons->contour[h.id].long_max    = h.east;
        polygons->contour[h.id].lat_min     = h.south;
        polygons->contour[h.id].lat_max     = h.north;
        polygons->contour[h.id].type        = (h.area) ? 0 : 1;        /* Either Polygon (0) or Line (1) (if no area) */
        polygons->contour[h.id].area        = h.area;
        
        polygons->contour[h.id].vertex = NULL;
        polygons->contour[h.id].vertex = malloc(h.n * sizeof(gshhs_vertex));
        if ( polygons->contour[h.id].vertex == NULL )
        {
            fprintf(stderr,"Allocation impossible \n");
            exit(EXIT_FAILURE);
        }

        if (debug==1)
        {
            printf("ID polygone= %6d\n", polygons->contour[h.id].id);
            printf("Type= %3d\n", polygons->contour[h.id].type);
            printf("Nombre de point: %8d\n", polygons->contour[h.id].nb_point);
            printf("Niveau: %8d\n", polygons->contour[h.id].level);
            printf("Version: %8d\n", polygons->contour[h.id].version);
            printf("Greenwich: %8d\n", polygons->contour[h.id].greenwich);
            printf("Source: %8d\n", polygons->contour[h.id].source);
            printf("Surface: %8d\n", polygons->contour[h.id].area);
            printf("Longitude min: %8d\n", polygons->contour[h.id].long_min);
            printf("Longitude max: %8d\n", polygons->contour[h.id].long_max);
            printf("Latitude min: %8d\n", polygons->contour[h.id].lat_min);
            printf("Latitude max: %8d\n", polygons->contour[h.id].lat_max);
        }
        
        for (k = 0; k < h.n; k++) 
        {
            if (fread ((void *)&p, (size_t)sizeof(struct POINT), (size_t)1, gshhs_file) != 1) 
            {
                fprintf (stderr, "gshhs:  Error reading file\n");
                exit (EXIT_FAILURE);
            }
            if (flip) 
            {
                p.x = swabi4 ((unsigned int)p.x);
                p.y = swabi4 ((unsigned int)p.y);
            }
                
            if (h.south == -90000000 && k == (h.n - 3) ) {
                polygons->contour[h.id].vertex[k].x = 0;
                polygons->contour[h.id].vertex[k].y = -90000000;
                lon = polygons->contour[h.id].vertex[k].x * GSHHS_SCL;
                lat = polygons->contour[h.id].vertex[k].y * GSHHS_SCL;
                k++;
                
                if (debug==1)
                {
                    printf ("lon(%2d)=%10d ,%10.6f , lat(%2d)=%10d ,%10.6f\n",  sizeof(p.x), polygons->contour[h.id].vertex[k].x, lon,
                                                                                sizeof(p.y), polygons->contour[h.id].vertex[k].y, lat);
                }
                polygons->contour[h.id].vertex[k].x = 360000000;
                polygons->contour[h.id].vertex[k].y = -90000000;
                lon = polygons->contour[h.id].vertex[k].x * GSHHS_SCL;
                lat = polygons->contour[h.id].vertex[k].y * GSHHS_SCL;
                k++;
                if (debug==1)
                {
                    printf ("lon(%2d)=%10d ,%10.6f , lat(%2d)=%10d ,%10.6f\n",  sizeof(p.x), polygons->contour[h.id].vertex[k].x, lon,
                                                                                sizeof(p.y), polygons->contour[h.id].vertex[k].y, lat);
                }
            }
                
            if (polygons->contour[h.id].greenwich && p.x > max_east) p.x -= 360000000;
            lon = p.x * GSHHS_SCL;
            lat = p.y * GSHHS_SCL;
            polygons->contour[h.id].vertex[k].x = p.x;
            polygons->contour[h.id].vertex[k].y = p.y;

            if (debug==1)
            {
                printf ("lon(%2d)=%10d ,%10.6f , lat(%2d)=%10d ,%10.6f\n",  sizeof(p.x), polygons->contour[h.id].vertex[k].x, lon,
                                                                            sizeof(p.y), polygons->contour[h.id].vertex[k].y, lat);
            }
                
                
            }
        //}
        max_east = 180000000;    /* Only Eurasiafrica needs 270 */
        n_read = fread((void *)&h, (size_t)sizeof (struct GSHHS), (size_t)1, gshhs_file);
    }
    polygons->nb_poly = h.id;

}    

void free_gshhs(gshhs_polygons *polygons, int nb_poly)
{ 
    int k;
    /* vidage de la memoire des données GSHHS */
    for (k = 0; k <= nb_poly; k++)
    {
        free(polygons->contour[k].vertex);
        polygons->contour[k].vertex = NULL;
    }
    
    free(polygons->contour);
    polygons->contour = NULL;

}


