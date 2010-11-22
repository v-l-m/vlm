/**
 *    Filename        : readcontour.c

 *    Created            : 07 May 2009 (23:08:51)
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

#include "gshhs.h"
#include "read_gshhs.h"
#include <stdlib.h>
#include <stdio.h>

#include <string.h>
#include <math.h>




int main (int argc, char **argv)
{
    FILE *gshhs_file;
    char gshhs_name[256];
    gshhs_polygons *polygons;
    
    int n;
    int n1, n2, n3, n4, n5;
    
    FILE  *gpc_file_1;
    char path_gpc_file_1[256];

    FILE  *gpc_file_2;
    char path_gpc_file_2[256];
    
    FILE  *gpc_file_3;
    char path_gpc_file_3[256];
    
    FILE  *gpc_file_4;
    char path_gpc_file_4[256];
    
    FILE  *gpc_file_5;
    char path_gpc_file_5[256];
    
    gshhs_contour *contour[360][180];
    gshhs_line *temp_contour;
    int u, v, i, j, k;
    int xmin, xmax, x1min, x1max, x2min, x2max;
    int ymin, ymax, y1min, y1max, y2min, y2max;
    
    typedef struct {
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
    } header;
    header header_out;
    
    FILE  *out_file;
    char path_out_file[256];
        
    char bd_path[256];
    
    long pos_data;
    long tab_data;
    
    
    
    printf("Passe1\n");
    
    /* gestion de la ligne de commande */
    if (argc < 2 || argc > 3) 
    {
        fprintf (stderr, "gshhs v. %s ASCII export tool\n", GSHHS_PROG_VERSION);
        fprintf (stderr, "usage:  readgshhs [f|h|i|l|c] \n");
        fprintf (stderr, "Thanks\n");
        exit (EXIT_FAILURE);
    }
    
    printf("Passe2\n");
    /* Ouverture du fichier GSHHS */
    sprintf(gshhs_name, "./gshhs/%s.b", argv[1]);
    if ((gshhs_file = fopen (gshhs_name, "rb")) == NULL ) {
        fprintf (stderr, "gshhs:  Could not find file %s\n", gshhs_name);
        exit (EXIT_FAILURE);
    }
    
    printf("Passe3\n");
    
    /*Allocation de la mémoire pour stocker toutes les données du GSHHS */
    polygons = NULL;
    polygons = malloc(1 * sizeof(gshhs_polygons));
    if ( polygons == NULL )
    {
        fprintf(stderr,"Allocation impossible \n");
        exit(EXIT_FAILURE);
    }
    printf("Passe4\n");
    /*Allocation de la mémoire pour stocker toutes les données du GSHHS par tranche de 1deg x 1deg*/
    for (u=0; u<360; u++)
    {
        for (v=0; v<180; v++)
        {
            contour[u][v] = NULL;
            contour[u][v] = malloc(1 * sizeof(gshhs_contour));
            if ( contour[u][v] == NULL )
            {
                fprintf(stderr,"Allocation impossible %d, %d\n", u, v);
                exit(EXIT_FAILURE);
            }
            contour[u][v]->nb_line=0;
        }
    }
    
    printf("Passe5\n");
    
    /* Lecture et mise en memoire des données */
    read_gshhs(gshhs_file, polygons, 0);
    
    /* Affichage du nombre de polygones contenu dans le GSHHS */
    printf("Nb polygone= %8d\n", polygons->nb_poly );
    //printf("ID polygone N° 1865= %8d\n", polygons->contour[1865].id );
    //printf("Lat_max polygone N° 1865= %8d\n", polygons->contour[1865].lat_max );
    //printf("P1x polygone N° 1865= %8d\n", polygons->contour[1865].vertex[1].x );
        
    
    /* fermeture du fichier GSHHS */
    fclose (gshhs_file);
    
    sprintf(path_gpc_file_1, "./bd/%s1.dat", argv[1]);
    if ((gpc_file_1 = fopen (path_gpc_file_1, "w")) == NULL ) {
        fprintf (stderr, "Echec 1\n");
        exit (EXIT_FAILURE);
    }
    sprintf(path_gpc_file_2, "./bd/%s2.dat", argv[1]);
    if ((gpc_file_2 = fopen (path_gpc_file_2, "w")) == NULL ) {
        fprintf (stderr, "Echec 1\n");
        exit (EXIT_FAILURE);
    }
    sprintf(path_gpc_file_3, "./bd/%s3.dat", argv[1]);
    if ((gpc_file_3 = fopen (path_gpc_file_3, "w")) == NULL ) {
        fprintf (stderr, "Echec 1\n");
        exit (EXIT_FAILURE);
    }
    sprintf(path_gpc_file_4, "./bd/%s4.dat", argv[1]);
    if ((gpc_file_4 = fopen (path_gpc_file_4, "w")) == NULL ) {
        fprintf (stderr, "Echec 1\n");
        exit (EXIT_FAILURE);
    }
    sprintf(path_gpc_file_5, "./bd/%s5.dat", argv[1]);
    if ((gpc_file_5 = fopen (path_gpc_file_5, "w")) == NULL ) {
        fprintf (stderr, "Echec 1\n");
        exit (EXIT_FAILURE);
    }
    
    n1 = 0;
    n2 = 0;
    n3 = 0;
    n4 = 0;
    n5 = 0;
    for(n = 0; n <= polygons->nb_poly; n++ ) {
        if (polygons->contour[n].type == 0) {
            if (polygons->contour[n].level == 1) n1++;
            if (polygons->contour[n].level == 2) n2++;
            if (polygons->contour[n].level == 3) n3++;
            if (polygons->contour[n].level == 4) n4++;
        }
        else {
            n5++;
        }
    }
    
    fprintf(gpc_file_1, "%d\n", n1);
    fprintf(gpc_file_2, "%d\n", n2);
    fprintf(gpc_file_3, "%d\n", n3);
    fprintf(gpc_file_4, "%d\n", n4);
    fprintf(gpc_file_5, "%d\n", n5);
    
    for(n = 0; n <= polygons->nb_poly; n++ ) {
        if (polygons->contour[n].type == 0) {
            if (polygons->contour[n].level == 1){
                fprintf(gpc_file_1, "%d\n", polygons->contour[n].nb_point - 1);
                fprintf(gpc_file_1, "%d\n", 0);
                GshhsToGpcFile(gpc_file_1, polygons, n);
            }
            if (polygons->contour[n].level == 2){
                fprintf(gpc_file_2, "%d\n", polygons->contour[n].nb_point - 1);
                fprintf(gpc_file_2, "%d\n", 0);
                GshhsToGpcFile(gpc_file_2, polygons, n);
            }
            if (polygons->contour[n].level == 3){
                fprintf(gpc_file_3, "%d\n", polygons->contour[n].nb_point - 1);
                fprintf(gpc_file_3, "%d\n", 0);
                GshhsToGpcFile(gpc_file_3, polygons, n);
            }
            if (polygons->contour[n].level == 4){
                fprintf(gpc_file_4, "%d\n", polygons->contour[n].nb_point - 1);
                fprintf(gpc_file_4, "%d\n", 0);
                GshhsToGpcFile(gpc_file_4, polygons, n);
            }
        }
        else
        {
            fprintf(gpc_file_5, "%d\n", polygons->contour[n].nb_point - 1);
            fprintf(gpc_file_5, "%d\n", 0);
            GshhsToGpcFile(gpc_file_5, polygons, n);
            
            for (k=0; k<polygons->contour[n].nb_point-2; k++)
            {
                x1min=floor(polygons->contour[n].vertex[k].x * GSHHS_SCL);
                x1max=ceil(polygons->contour[n].vertex[k].x * GSHHS_SCL);
                x2min=floor(polygons->contour[n].vertex[k+1].x * GSHHS_SCL);
                x2max=ceil(polygons->contour[n].vertex[k+1].x * GSHHS_SCL);
                y1min=floor(polygons->contour[n].vertex[k].y * GSHHS_SCL + 90);
                y1max=ceil(polygons->contour[n].vertex[k].y * GSHHS_SCL + 90);
                y2min=floor(polygons->contour[n].vertex[k+1].y * GSHHS_SCL + 90);
                y2max=ceil(polygons->contour[n].vertex[k+1].y * GSHHS_SCL + 90);
                
                if (x1min<x2min) xmin=x1min; else xmin=x2min;
                if (x1max<x2max) xmax=x2max; else xmax=x1max;
                
                if (y1min<y2min) ymin=y1min; else ymin=y2min;
                if (y1max<y2max) ymax=y2max; else ymax=y1max;
                
                //printf("Xmin: %d\tXmax: %d\tYmin: %d\tYmax: %d\n", xmin, xmax, ymin, ymax);
                
                for (i=xmin; i<xmax; i++)
                {
                    for (j=ymin; j<ymax; j++)
                    {
                        contour[i][j]->nb_line = contour[i][j]->nb_line + 1;
                        //printf("X: %d\tY: %d\tNb line %d\n", i, j, contour[i][j]->nb_line);
                        
                        if (contour[i][j]->nb_line==1)
                        {
                            
                            contour[i][j]->line = NULL;
                            contour[i][j]->line = malloc(sizeof(gshhs_line));
                            if ( contour[i][j]->line == NULL )
                            {
                                fprintf(stderr,"Allocation impossible %d, %d\n", u, v);
                                exit(EXIT_FAILURE);
                            }
                        }
                        else
                        {
                            temp_contour = NULL;
                            temp_contour = realloc(contour[i][j]->line, contour[i][j]->nb_line * sizeof(gshhs_line)); 
                            if (temp_contour == NULL)
                            {
                                fprintf(stderr,"Reellocation impossible \n");
                                free(contour[j][j]->line);
                                exit(EXIT_FAILURE);
                            }
                            else
                            {
                                contour[i][j]->line = temp_contour;
                            }
                        }
                        
                        
                        contour[i][j]->line[contour[i][j]->nb_line - 1].x1=polygons->contour[n].vertex[k].x;
                        contour[i][j]->line[contour[i][j]->nb_line - 1].y1=polygons->contour[n].vertex[k].y;
                        contour[i][j]->line[contour[i][j]->nb_line - 1].x2=polygons->contour[n].vertex[k+1].x;
                        contour[i][j]->line[contour[i][j]->nb_line - 1].y2=polygons->contour[n].vertex[k+1].y;
                        
                        
                    }
                }
            }
        }
    }


    
    pos_data = 0;
    sprintf(bd_path, "./bd");
    
    sprintf(path_out_file, "%s/%s.dat", bd_path, argv[1]);
    if ((out_file = fopen (path_out_file, "wb")) == NULL )
    {
        fprintf (stderr, "Impossible d'ouvrir le fichier %s\n", path_out_file);
        exit (EXIT_FAILURE);
    }
    header_out.version=  111;
    header_out.pasx=     1;
    header_out.pasy=     1;
    header_out.xmin=     0;
    header_out.ymin=     -90;
    header_out.xmax=     360;
    header_out.ymax=     90;
    header_out.p1=       1;
    header_out.p2=       2;
    header_out.p3=       3;
    header_out.p4=       4;
    header_out.p5=       5;
    
    // Ecriture du header        
    fwrite(&header_out, sizeof(header), 1, out_file);

    // Initialisation de la table
    //printf("passe\n");
    for (i=0; i<360; i=i+1)
    {
        for (j=-90; j<90; j=j+1)
        {
            fwrite(&pos_data, sizeof(long), 1, out_file);
        }
    }
    
    // Ecriture des données
    for (i=0; i<360; i=i+1)
    {
        
        for (j=0; j<180; j=j+1)
        {
            printf("i: %d j: %d\n", i, j);
            if (contour[i][j]->nb_line == 0)
            {
                fseek(out_file, 0L, SEEK_END);
                pos_data = ftell(out_file);
                fwrite(&(contour[i][j]->nb_line), sizeof(int), 1, out_file);
                
                tab_data = i*180 + j;
                //printf("tab_data: %ld\n\n", tab_data);
                fseek(out_file, sizeof(header) + tab_data*sizeof(int), SEEK_SET);
                fwrite(&pos_data, sizeof(long), 1, out_file);

                
            }
            else
            {
                fseek(out_file, 0L, SEEK_END);
                pos_data = ftell(out_file);
                fwrite(&(contour[i][j]->nb_line), sizeof(int), 1, out_file);
                for (k=0; k<contour[i][j]->nb_line; k++)
                {
                    fwrite(&(contour[i][j]->line[k]), sizeof(gshhs_line), 1, out_file);   
                }
                
                tab_data = i*180 + j;
                //printf("tab_data: %ld\n\n", tab_data);
                fseek(out_file, sizeof(header) + tab_data*sizeof(int), SEEK_SET);
                fwrite(&pos_data, sizeof(long), 1, out_file);
            }
        }
    }

    fclose(out_file);

    fclose (gpc_file_1);
    fclose (gpc_file_2);
    fclose (gpc_file_3);
    fclose (gpc_file_4);
    fclose (gpc_file_5);
    
    /* vidage de la memoire des données GSHHS */
    free_gshhs(polygons, polygons->nb_poly);
    
    free(polygons);
    polygons = NULL;
    
    /* libération de la mémoire des tranches */
    
    for (u=0; u<360; u++)
    {
        for (v=0; v<180; v++)
        {
            printf("u: %d v: %d\n", u, v);
            if (contour[u][v]->nb_line>0)
            {
                free(contour[u][v]->line);
                contour[u][v]->line=NULL;
            }
            
            free(contour[u][v]);
            contour[u][v] = NULL;
        }
    }
    


    exit (EXIT_SUCCESS);
}
