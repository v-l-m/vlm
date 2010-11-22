/**
 *    Filename          : readgshhs.c

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

#include "gshhs.h"
#include "read_gshhs.h"
#include <stdlib.h>
#include <stdio.h>

#include <string.h>




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
    
    /* gestion de la ligne de commande */
    if (argc < 2 || argc > 3) 
    {
        fprintf (stderr, "gshhs v. %s ASCII export tool\n", GSHHS_PROG_VERSION);
        fprintf (stderr, "usage:  readgshhs [f|h|i|l|c] \n");
        fprintf (stderr, "Thanks\n");
        exit (EXIT_FAILURE);
    }
    
    /* Ouverture du fichier GSHHS */
    sprintf(gshhs_name, "./gshhs/gshhs_%s.b", argv[1]);
    if ((gshhs_file = fopen (gshhs_name, "rb")) == NULL ) {
        fprintf (stderr, "gshhs:  Could not find file %s\n", gshhs_name);
        exit (EXIT_FAILURE);
    }
    
    /*Allocation de la mémoire pour stocker toutes les données du GSHHS */
    polygons = NULL;
    polygons = malloc(1 * sizeof(gshhs_polygons));
    if ( polygons == NULL )
    {
        fprintf(stderr,"Allocation impossible \n");
        exit(EXIT_FAILURE);
    }
    
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
        else {
            fprintf(gpc_file_5, "%d\n", polygons->contour[n].nb_point - 1);
            fprintf(gpc_file_5, "%d\n", 0);
            GshhsToGpcFile(gpc_file_5, polygons, n);
        }
    }
    
    

    






    fclose (gpc_file_1);
    fclose (gpc_file_2);
    fclose (gpc_file_3);
    fclose (gpc_file_4);
    fclose (gpc_file_5);
    
    /* vidage de la memoire des données GSHHS */
    free_gshhs(polygons, polygons->nb_poly);
    
    free(polygons);
    polygons = NULL;


    exit (EXIT_SUCCESS);
}
