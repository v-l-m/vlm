/**
 *    Filename        : read_bd.c

 *    Created            : 07 January 2009 (23:13:57)
 *    Created by        : StephPen - stephpen@gmail.com

 *    Last Updated    : 23:24 21/11/2010
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
 *    Comments        : 
 *     
 *     
 *     
 *     
 *     
 *    Contact: <stephpen@gmail.com>
*/


#include <stdio.h>
#include "gpc.h"
#include "gshhs.h"

int get_gpc_path(char *gpc_path, char *bd_path, int x, int y, int *pas, int level);

int main (int argc, char **argv)
{
    
    struct st_01 {
        char file[256];
    };
    
    struct st_02 {
        FILE   *file;
    };
    
    struct header_01 {
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
    };
    
    struct st_02 file_end[4];
    
    char bd_path[256];
    char gpc_path[256];
    char gpc_file[256];
    
    struct st_01 bd_end[4];
    
    int pas[] = {45, 15, 5, 1};
    int level = 3;
    int i;
    long pos_data;
    long tab_data;
        
    int x, y;
    int c;
    
    struct header_01 header_end[4];
    
    FILE *polygon_file;
    gpc_polygon polygon;
    
    int n_contour;
    int n_point;
    
    
    if (argc < 2 || argc > 3) {
        fprintf (stderr, "Sorry !!\n");
        fprintf (stderr, "Usage:  read_bd [f|h|i|l|c]\n\n");
        
        exit (EXIT_FAILURE);
    }
    
    pos_data = 0;
    sprintf(bd_path, "./bd/bd_%s", argv[1]);
    
    for (i=0; i<=level; i++)
    {
        printf("i: %d\n", i);
        sprintf(bd_end[i].file, "%s/poly-%s-%d.dat", bd_path, argv[1], pas[i]);
        if ((file_end[i].file = fopen (bd_end[i].file, "wb")) == NULL )
        {
            fprintf (stderr, "Impossible d'ouvrir le fichier %s\n", bd_end[i].file);
            exit (EXIT_FAILURE);
        }
        header_end[i].version=  210;
        header_end[i].pasx=     pas[i];
        header_end[i].pasy=     pas[i];
        header_end[i].xmin=     0;
        header_end[i].ymin=     -90;
        header_end[i].xmax=     360;
        header_end[i].ymax=     90;
        header_end[i].p1=       1;
        header_end[i].p2=       2;
        header_end[i].p3=       3;
        header_end[i].p4=       4;
        header_end[i].p5=       5;
        
        fwrite(&header_end[i], sizeof(header_end[i]), 1, file_end[i].file);
        
        for (x=0; x<360; x=x+pas[i])
        {
            for (y=-90; y<90; y=y+pas[i])
            {
                fwrite(&pos_data, sizeof(long), 1, file_end[i].file);
            }
        }
        for (x=0; x<360; x=x+pas[i])
        {
            for (y=-90; y<90; y=y+pas[i])
            {
                if (get_gpc_path(gpc_path, bd_path, x, y, pas, i) != 0)
                {
                    fseek(file_end[i].file, 0L, SEEK_END);
                    pos_data = ftell(file_end[i].file);
                    printf("pos_data: %ld\n", pos_data);

                    for (c=1; c<=5; c=c+1) 
                    {
                        sprintf(gpc_file, "%s/%s%d.dat", gpc_path, argv[1], c);
                        printf("X: %i, Y: %i, File: %s\n", x, y, gpc_file);
                
                        if ((polygon_file = fopen(gpc_file, "r")) == NULL )
                        {
                            fprintf (stderr, "Impossible d'ouvrir le fichier %s\n", gpc_file);
                            exit (EXIT_FAILURE);
                        }
                
                        gpc_read_polygon(polygon_file, 1, &polygon);      /* Lecture du fichier polygone*/
                
                        printf("num_contour: %d\n", polygon.num_contours);
                        
                        fwrite(&polygon.num_contours, sizeof(int), 1, file_end[i].file);
                        for (n_contour=0; n_contour<polygon.num_contours; n_contour++)
                        {
                            fwrite(&polygon.hole[n_contour], sizeof(int), 1, file_end[i].file);
                        
                            fwrite(&polygon.contour[n_contour].num_vertices, sizeof(int), 1, file_end[i].file);
                            for (n_point=0; n_point<polygon.contour[n_contour].num_vertices; n_point++)
                            {
                                fwrite(&polygon.contour[n_contour].vertex[n_point].x, sizeof(double), 1, file_end[i].file);
                                fwrite(&polygon.contour[n_contour].vertex[n_point].y, sizeof(double), 1, file_end[i].file);
                            }
                        }
                    
                
                        fclose(polygon_file);
                        gpc_free_polygon(&polygon);
                    }
                    tab_data = (x/pas[i])*(180/pas[i]) + (y+90)/pas[i];
                    printf("tab_data: %ld\n\n", tab_data);
                    fseek(file_end[i].file, sizeof(header_end[i]) + tab_data*sizeof(int), SEEK_SET);
                    fwrite(&pos_data, sizeof(long), 1, file_end[i].file);

                }
            }
        }

    fclose(file_end[i].file);

    }
    
    return 0;
    
}

int get_gpc_path(char *gpc_path, char *bd_path, int x, int y, int *pas, int level)
{
    
    int xo, yo;
    int xe, ye;
    int nb_pas;
    char buffer[256];
    int i;
    
    nb_pas=(sizeof(pas) / sizeof(pas[0]));
    sprintf(buffer, "%s", bd_path);
    
    for (i=0; i<=level; i++)
    {
        xo=(x/pas[i])*pas[i];
        yo=((y+90)/pas[i]-(90/pas[i]))*pas[i];
        xe=xo+pas[i];
        ye=yo+pas[i];
        sprintf(gpc_path, "%s/%d_%d_to_%d_%d", buffer, xo, yo, xe, ye);
        sprintf(buffer, "%s", gpc_path);
    }
    
    return 1;

}


