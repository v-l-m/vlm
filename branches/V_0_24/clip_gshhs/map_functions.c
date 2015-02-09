/**
 *  Filename          : map_functions.c
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


#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <math.h>
#include <gd.h>

#include "read_gshhs.h"
#include "gpc.h"
#include "map_functions.h"
#include "map_projection.h"
#include "gshhs.h"

#include <gdfontt.h> /*on va utiliser la police gdFontTiny */
#include <gdfonts.h> /*on va utiliser la police gdFontSmall */
#include <gdfontmb.h> /*on va utiliser la police gdFontMediumBold */
#include <gdfontl.h> /*on va utiliser la police gdFontLarge */
#include <gdfontg.h> /*on va utiliser la police gdFontGiant */




// Fonction pour lire le fichier de commande
void ReadCmdFile (FILE *cmdfile, CmdOrder *cmd)
{
    char    order[256];
    char    startcmd[3] = "[";     //Caractere de debut de commande
    //char    startcom[3] = "#";     //Caractere de debut de commentaire
    int     read;
    //double   x_start, y_start;
    //double   x_center, y_center;
    //double   x_extent, y_extent;
    //int     greenwich;


    do
    {
        read = fscanf(cmdfile, "%s", order);

        if (read != -1)
        {
            if (order[0] == startcmd[0])
            {
                if (strcmp(order, "[Map_Center]") == 0)
                {
                    read = fscanf(cmdfile, "%lf %lf %d %d", &(cmd->LongCenter), &(cmd->LatCenter), &(cmd->MapWidth), &(cmd->MapHeight));
                }
                /*
                if (strcmp(order, "[Map_Start]") == 0)
                {
                    read = fscanf(cmdfile, "%lf %lf %lf %lf", &x_start, &y_start, &x_extent, &y_extent);
                    cmd->long_start = x_start;
                    cmd->lat_start = y_start;
                    cmd->long_extent = x_extent;
                    cmd->lat_extent = y_extent;
                }
                */
                if (strcmp(order, "[Zoom]") == 0)
                {
                    read = fscanf(cmdfile, "%lf", &(cmd->Zoom));
                }
                if (strcmp(order, "[Resol]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->resolution);
                }
                if (strcmp(order, "[Projection]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->projection);
                }
                if (strcmp(order, "[Water_Color]") == 0)
                {
                    read = fscanf(cmdfile, "%d %d %d", &(cmd->WaterColorR), &(cmd->WaterColorG), &(cmd->WaterColorB));
                }
                if (strcmp(order, "[Coast_Color]") == 0)
                {
                    read = fscanf(cmdfile, "%d %d %d", &(cmd->CoastColorR), &(cmd->CoastColorG), &(cmd->CoastColorB));
                }
                if (strcmp(order, "[Land_Color]") == 0)
                {
                    read = fscanf(cmdfile, "%d %d %d", &(cmd->LandColorR), &(cmd->LandColorG), &(cmd->LandColorB));
                }
                if (strcmp(order, "[Grib_Color]") == 0)
                {
                    read = fscanf(cmdfile, "%d %d %d", &(cmd->GridColorR), &(cmd->GridColorG), &(cmd->GridColorB));
                }
                if (strcmp(order, "[Grib_Space]") == 0)
                {
                    read = fscanf(cmdfile, "%lf", &(cmd->grid_space));
                }
                if (strcmp(order, "[BD_Path]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->bd_path);
                }
                if (strcmp(order, "[BD_Name]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->bd_name);
                }
                if (strcmp(order, "[Map_Path]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->map_path);
                }
                if (strcmp(order, "[Map_Name]") == 0)
                {
                    read = fscanf(cmdfile, "%s", cmd->map_name);
                }
            }
        }
    } while(read != -1);



    if (DEBUG)
    {
        printf("\n");
        printf("##### Function read_cmd_file #####\n");
        printf("#--------------------------------#\n");
        //printf("# Long_Start:   %lf\n", cmd->long_start);
        //printf("# Lat_Start:    %lf\n", cmd->lat_start);
        //printf("# Long_Extent:  %lf\n", cmd->long_extent);
        //printf("# Lat_Extent:   %lf\n", cmd->lat_extent);
        printf("# Long_Center:  %lf\n", cmd->LongCenter);
        printf("# Lat_Center:   %lf\n", cmd->LatCenter);
        printf("# Map_Width:    %d\n", cmd->MapWidth);
        printf("# Map_Height:   %d\n", cmd->MapHeight);

        printf("# Zoom:         %lf\n", cmd->Zoom);
        printf("# Resol:        %s\n", cmd->resolution);
        printf("# Projection:   %s\n", cmd->projection);
        printf("# Water_Color:  R %d, V %d, B %d\n", cmd->WaterColorR,  cmd->WaterColorG,   cmd->WaterColorB);
        printf("# Coast_Color:  R %d, V %d, B %d\n", cmd->CoastColorR,  cmd->CoastColorG,   cmd->CoastColorB);
        printf("# Land_Color:   R %d, V %d, B %d\n", cmd->LandColorR,   cmd->LandColorG,    cmd->LandColorB);
        printf("# Grid_Color:   R %d, V %d, B %d\n", cmd->GridColorR,   cmd->GridColorG,    cmd->GridColorB);
        printf("# Grid_Space:   %lf\n", cmd->grid_space);
        printf("# BD_Path:      %s\n", cmd->bd_path);
        printf("# BD_Name:      %s\n", cmd->bd_name);
        printf("# Map_Path:     %s\n", cmd->map_path);
        printf("# Map_Name:     %s\n", cmd->map_name);
        printf("#--------------------------------#\n");
        printf("##### Function read_cmd_file #####\n");
        printf("\n");
    }
}



void ReadPolygonFileHeader (FILE *polyfile, PolygonFileHeader *header)
{
    int FReadResult = 0;

    fseek(polyfile, 0 , SEEK_SET);
    FReadResult = fread(header, sizeof(PolygonFileHeader), 1, polyfile);

    if (DEBUG)
    {
        printf("\n");
        printf("##### Function read_polygon_file_header #####\n");
        printf("#-------------------------------------------#\n");
        printf("# Header version:   %d\n", header->version);
        printf("# Header pasx:      %d\n", header->pasx);
        printf("# Header pasy:      %d\n", header->pasy);
        printf("# Header xmin:      %d\n", header->xmin);
        printf("# Header ymin:      %d\n", header->ymin);
        printf("# Header xmax:      %d\n", header->xmax);
        printf("# Header ymax:      %d\n", header->ymax);
        printf("# Header p1:        %d\n", header->p1);
        printf("# Header p2:        %d\n", header->p2);
        printf("# Header p3:        %d\n", header->p3);
        printf("# Header p4:        %d\n", header->p4);
        printf("# Header p5:        %d\n", header->p5);
        printf("#-------------------------------------------#\n");
        printf("##### Function read_polygon_file_header #####\n");
        printf("\n");
    }


}

gshhs_contour **LineToMemory(FILE *linefile)
{

    gshhs_contour **LineRAM = NULL;
    int x, y;
    int c;
    int FReadResult = 0;

    fseek(linefile, sizeof(PolygonFileHeader) + 360 * 180 * sizeof(int) , SEEK_SET);
    LineRAM = malloc(360 * sizeof(gshhs_contour *));
    if (LineRAM == NULL)
    {
        fprintf(stderr,"Erreur lors de l'allocation\n" );
        exit(EXIT_FAILURE);
    }

    for ( x = 0 ; x < 360 ; x ++ )
    {
        /* allocation d'un tableau de tableau */
        LineRAM[x] = malloc ( 180 * sizeof(gshhs_contour) );
        if (LineRAM[x] == NULL)
        {
            fprintf(stderr,"Erreur lors de l'allocation\n" );
            exit(EXIT_FAILURE);
        }
    }


    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            FReadResult = fread(&(LineRAM[x][y].nb_line), sizeof(int), 1, linefile);
            LineRAM[x][y].line = NULL;
            LineRAM[x][y].line = malloc(LineRAM[x][y].nb_line * sizeof(gshhs_line));
            if ( LineRAM[x][y].line == NULL )
            {
                fprintf(stderr,"Allocation impossible \n");
                exit(EXIT_FAILURE);
            }

            //printf("nb line: %d\n", LineRAM[x][y].nb_line);
            if (LineRAM[x][y].nb_line>0)
            {
                for (c= 0; c <LineRAM[x][y].nb_line; c++)
                {
                    FReadResult = fread(&(LineRAM[x][y].line[c]), sizeof(gshhs_line), 1, linefile);
                }
            }
        }
    }
    return LineRAM;
}

void FreeLineToMemory (gshhs_contour **LineRAM)
{
    int x, y;

    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            FreeLine(&LineRAM[x][y]);
        }
    }

    for ( x = 0 ; x < 360 ; x ++ )
    {
        free(LineRAM[x]);
        LineRAM[x] = NULL;
    }

    free(LineRAM);
    LineRAM = NULL;

}


gpc_polygon ***PolygonToMemory (FILE *polyfile)
{
    int i;
    int x, y;
    int c, v;
    gpc_polygon ***PolyRAM = NULL;
    int FReadResult = 0;


    fseek(polyfile, sizeof(PolygonFileHeader) + 360 * 180 * sizeof(int) , SEEK_SET);
    PolyRAM = malloc(360 * sizeof(gpc_polygon **));
    if (PolyRAM == NULL)
    {
        fprintf(stderr,"Erreur lors de l'allocation\n" );
        exit(EXIT_FAILURE);
    }

    for ( x = 0 ; x < 360 ; x ++ )
    {
        /* allocation d'un tableau de tableau */
        PolyRAM[x] = malloc ( 180 * sizeof(gpc_polygon *) );
        if (PolyRAM[x] == NULL)
        {
            fprintf(stderr,"Erreur lors de l'allocation\n" );
            exit(EXIT_FAILURE);
        }
    }

    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            PolyRAM[x][y] = malloc ( 5 * sizeof(gpc_polygon) );
            if (PolyRAM[x][y] == NULL)
            {
                fprintf(stderr,"Erreur lors de l'allocation\n" );
                exit(EXIT_FAILURE);
            }
        }
    }

    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            for (i=0; i<5; i++)
            {
                FReadResult = fread(&(PolyRAM[x][y][i].num_contours), sizeof(int), 1, polyfile);
                //PolyRAM[x][y][i].num_contours=util;
                //printf("PolyRAM[%d][%d][%d].num_contours: %d\n", x, y, i, PolyRAM[x][y][i].num_contours);
                MALLOC(PolyRAM[x][y][i].hole, PolyRAM[x][y][i].num_contours * sizeof(int), "hole flag array creation", int);
                MALLOC(PolyRAM[x][y][i].contour, PolyRAM[x][y][i].num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

                for (c= 0; c < PolyRAM[x][y][i].num_contours; c++)
                {
                    FReadResult = fread(&(PolyRAM[x][y][i].hole[c]), sizeof(int), 1, polyfile);
                    FReadResult = fread(&(PolyRAM[x][y][i].contour[c].num_vertices), sizeof(int), 1, polyfile);
                    MALLOC(PolyRAM[x][y][i].contour[c].vertex, PolyRAM[x][y][i].contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

                    for (v= 0; v < PolyRAM[x][y][i].contour[c].num_vertices; v++)
                    {
                        FReadResult = fread(&(PolyRAM[x][y][i].contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                        FReadResult = fread(&(PolyRAM[x][y][i].contour[c].vertex[v].y), sizeof(double), 1, polyfile);
                        //printf("xx: %lf, yy: %lf\n", p1->contour[c].vertex[v].x, p1->contour[c].vertex[v].y);
                    }
                }
            }
        }
    }

    //printf("Passe %d\n", PolyRAM[0][0][0].num_contours);
    return PolyRAM;

}

void FreePolygonToMemory (gpc_polygon ***PolyRAM)
{
    int i;
    int x, y;

    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            for (i=0; i<5; i++)
            {
                FreePolygon(&PolyRAM[x][y][i]);
            }
        }
    }

    for (x=0; x<360; x++)
    {
        for (y=0; y<180; y++)
        {
            free(PolyRAM[x][y]);
            PolyRAM[x][y] = NULL;
        }
    }

    for ( x = 0 ; x < 360 ; x ++ )
    {
        free(PolyRAM[x]);
        PolyRAM[x] = NULL;
    }

    free(PolyRAM);
    PolyRAM = NULL;

}


void ReadPolygonFile (FILE *polyfile,
                        int x, int y,
                        int pas_x, int pas_y,
                        gpc_polygon *p1, gpc_polygon *p2, gpc_polygon *p3, gpc_polygon *p4, gpc_polygon *p5)
{

    int pos_data;
    int tab_data;
    int c, v;
    int FReadResult = 0;


    tab_data = (x/pas_x)*(180/pas_y) + (y+90)/pas_y;
    fseek(polyfile, sizeof(PolygonFileHeader) + tab_data*sizeof(int), SEEK_SET);
    FReadResult = fread(&pos_data, sizeof(int), 1, polyfile);

    //printf("tabdata: %d, posdata: %d\n", tab_data, pos_data);

    fseek(polyfile, pos_data, SEEK_SET);

    // Lecture du polygone 1 -> Terre
    FReadResult = fread(&(p1->num_contours), sizeof(int), 1, polyfile);
    //printf("p1->num_contours: %d\n", p1->num_contours);
    MALLOC(p1->hole, p1->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p1->contour, p1->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

        for (c= 0; c < p1->num_contours; c++)
        {
            FReadResult = fread(&(p1->hole[c]), sizeof(int), 1, polyfile);
            FReadResult = fread(&(p1->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p1->contour[c].vertex, p1->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

            for (v= 0; v < p1->contour[c].num_vertices; v++)
            {
                FReadResult = fread(&(p1->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                FReadResult = fread(&(p1->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
                //printf("xx: %lf, yy: %lf\n", p1->contour[c].vertex[v].x, p1->contour[c].vertex[v].y);
            }
        }

    //printf("data1: %d, data2: %d, data3: %d\n", p1->num_contours, p1->hole[0], p1->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p1->contour[0].vertex[0].x, p1->contour[0].vertex[0].y);


    // Lecture du polygone 2 -> Lacs
    FReadResult = fread(&(p2->num_contours), sizeof(int), 1, polyfile);
    //printf("p2->num_contours: %d\n", p2->num_contours);
    MALLOC(p2->hole, p2->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p2->contour, p2->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

        for (c= 0; c < p2->num_contours; c++)
        {
            FReadResult = fread(&(p2->hole[c]), sizeof(int), 1, polyfile);
            FReadResult = fread(&(p2->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p2->contour[c].vertex, p2->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

            for (v= 0; v < p2->contour[c].num_vertices; v++)
            {
                FReadResult = fread(&(p2->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                FReadResult = fread(&(p2->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }

    //printf("data1: %d, data2: %d, data3: %d\n", p2->num_contours, p2->hole[0], p2->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p2->contour[0].vertex[0].x, p2->contour[0].vertex[0].y);

    // Lecture du polygone 3 -> Iles dans les Lacs
    FReadResult = fread(&(p3->num_contours), sizeof(int), 1, polyfile);
    //printf("p3->num_contours: %d\n", p3->num_contours);
    MALLOC(p3->hole, p3->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p3->contour, p3->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

        for (c= 0; c < p3->num_contours; c++)
        {
            FReadResult = fread(&(p3->hole[c]), sizeof(int), 1, polyfile);
            FReadResult = fread(&(p3->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p3->contour[c].vertex, p3->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

            for (v= 0; v < p3->contour[c].num_vertices; v++)
            {
                FReadResult = fread(&(p3->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                FReadResult = fread(&(p3->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }

    //printf("data1: %d, data2: %d, data3: %d\n", p3->num_contours, p3->hole[0], p3->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p3->contour[0].vertex[0].x, p3->contour[0].vertex[0].y);

    // Lecture du polygone 4 -> Flaques sur les Iles dans les Lacs
    FReadResult = fread(&(p4->num_contours), sizeof(int), 1, polyfile);
    //printf("p4->num_contours: %d\n", p4->num_contours);
    MALLOC(p4->hole, p4->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p4->contour, p4->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

        for (c= 0; c < p4->num_contours; c++)
        {
            FReadResult = fread(&(p4->hole[c]), sizeof(int), 1, polyfile);
            FReadResult = fread(&(p4->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p4->contour[c].vertex, p4->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

            for (v= 0; v < p4->contour[c].num_vertices; v++)
            {
                FReadResult = fread(&(p4->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                FReadResult = fread(&(p4->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }

    //printf("data1: %d, data2: %d, data3: %d\n", p4->num_contours, p4->hole[0], p4->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p4->contour[0].vertex[0].x, p4->contour[0].vertex[0].y);

    // Lecture du polygone 5 -> Lignes de Recif
    FReadResult = fread(&(p5->num_contours), sizeof(int), 1, polyfile);
    //printf("p5->num_contours: %d\n", p5->num_contours);
    MALLOC(p5->hole, p5->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p5->contour, p5->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);

        for (c= 0; c < p5->num_contours; c++)
        {
            FReadResult = fread(&(p5->hole[c]), sizeof(int), 1, polyfile);
            FReadResult = fread(&(p5->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p5->contour[c].vertex, p5->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);

            for (v= 0; v < p5->contour[c].num_vertices; v++)
            {
                FReadResult = fread(&(p5->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                FReadResult = fread(&(p5->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }

    //printf("data1: %d, data2: %d, data3: %d\n", p5->num_contours, p5->hole[0], p5->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p5->contour[0].vertex[0].x, p5->contour[0].vertex[0].y);

}

void FreePolygon(gpc_polygon *p)
{
    int c;

    for (c= 0; c < p->num_contours; c++)
    {
        FREE(p->contour[c].vertex);
    }
    FREE(p->hole);
    FREE(p->contour);
    p->num_contours= 0;
}

void DegToHMS(char *hms, double n, char *type)
{
    int deg, min, sec;

    if (n>180) n=n-360;

    if (n>=0)
    {
        deg=floor(n);
        min=floor((n-deg)*60);
        sec=((n-floor(n))*60-min)*60;
        if (strcmp(type, "long") == 0)  sprintf(hms, "%d°%.2d'%.2d\"E", deg, min, sec);
        if (strcmp(type, "lat") == 0)   sprintf(hms, "%d°%.2d'%.2d\"N", deg, min, sec);

    }

    else
    {
        n=-n;
        deg=floor(n);
        min=floor((n-deg)*60);
        sec=((n-floor(n))*60-min)*60;
        if (strcmp(type, "long") == 0)  sprintf(hms, "%d°%.2d'%.2d\"W", deg, min, sec);
        if (strcmp(type, "lat") == 0)   sprintf(hms, "%d°%.2d'%.2d\"S", deg, min, sec);
    }

}


void DrawPolygonFilled( gdImagePtr Image, gpc_polygon *p,
                        double X_Origine, double Y_Origine,
                        double Zoom,
                        int Fill_Color)
{

    int c, v;
    gdPoint *poly_pt;
    double r;

    r = 180.0*Zoom/M_PI;

    for (c= 0; c < p->num_contours; c++)
    {
        poly_pt= malloc(p->contour[c].num_vertices * 2 * sizeof(int));
        if ( poly_pt == NULL )
        {
            fprintf(stderr,"Allocation impossible \n");
            exit(EXIT_FAILURE);
        }

        for (v= 0; v < p->contour[c].num_vertices; v++)
        {
            poly_pt[v].x = (int)round(X_Origine + MercatorLongitudeSimple(p->contour[c].vertex[v].x * GSHHS_SCL) *r);
            poly_pt[v].y = (int)round(Y_Origine - MercatorLatitudeSimple(p->contour[c].vertex[v].y * GSHHS_SCL)  *r);
        }
        gdImageFilledPolygon(Image, poly_pt, p->contour[c].num_vertices , Fill_Color);

        free(poly_pt);
        poly_pt = NULL;

    }
}

void    DrawPolygonContour( gdImagePtr Image, gpc_polygon *p,
                            int x, int y,
                            int pas_x, int pas_y,
                            double X_Origine, double Y_Origine,
                            double Zoom,
                            int Contour_Color)
{

    int c, v;
    double r;
    double x1, y1, x2, y2;
    double long_max, lat_max, long_min, lat_min;

    r = 180.0*Zoom/M_PI;

    long_min=(double)x/GSHHS_SCL;
    lat_min=(double)y/GSHHS_SCL;
    long_max=((double)x+(double)pas_x)/GSHHS_SCL;
    lat_max=((double)y+(double)pas_y)/GSHHS_SCL;

    for (c= 0; c < p->num_contours; c++)
    {
        for (v= 0; v < (p->contour[c].num_vertices-1); v++)
        {
            x1=p->contour[c].vertex[v].x;
            y1=p->contour[c].vertex[v].y;
            x2=p->contour[c].vertex[v+1].x;
            y2=p->contour[c].vertex[v+1].y;

            // Elimination des traits verticaux et horizontaux
            if ((((x1==x2) && ((x1==long_min) || (x1==long_max))) || ((y1==y2) && ((y1==lat_min) || (y1==lat_max))))==0)
            {
                gdImageLine(Image,  (int)round(X_Origine + MercatorLongitudeSimple(x1 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y1 * GSHHS_SCL)  *r),
                                    (int)round(X_Origine + MercatorLongitudeSimple(x2 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y2 * GSHHS_SCL)  *r),
                                    Contour_Color);

            }
        }

        x1=p->contour[c].vertex[v].x;
        y1=p->contour[c].vertex[v].y;
        x2=p->contour[c].vertex[0].x;
        y2=p->contour[c].vertex[0].y;

        if ((((x1==x2) && ((x1==long_min) || (x1==long_max))) || ((y1==y2) && ((y1==lat_min) || (y1==lat_max))))==0)
        {
            gdImageLine(Image,  (int)round(X_Origine + MercatorLongitudeSimple(x1 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y1 * GSHHS_SCL)  *r),
                                (int)round(X_Origine + MercatorLongitudeSimple(x2 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y2 * GSHHS_SCL)  *r),
                                Contour_Color);

        }
    }
}

void    DrawGrid(   gdImagePtr Image, int MapWidth, int MapHeight,
                    double long_min, double long_max, double lat_min, double lat_max,
                    double X_Origine, double Y_Origine,
                    double Zoom,
                    double Grid_Space, int Grid_Color, int Text_Color)
{

    double x, y;
    double x1, y1, x2, y2;
    double LongGrid_Min, LatGrid_Min, LongGrid_Max, LatGrid_Max;
    double r;
    char   txt[50];


    r = 180*Zoom/M_PI;

    if (long_min>=0)    LongGrid_Min=   floor(fabs(long_min)/Grid_Space)*Grid_Space;
    else                LongGrid_Min=  -ceil(fabs(long_min)/Grid_Space)*Grid_Space;
    if (long_max>=0)    LongGrid_Max=   ceil(fabs(long_max)/Grid_Space)*Grid_Space;
    else                LongGrid_Max=  -floor(fabs(long_max)/Grid_Space)*Grid_Space;

    if (lat_min>=0)    LatGrid_Min=     floor(fabs(lat_min)/Grid_Space)*Grid_Space;
    else               LatGrid_Min=    -ceil(fabs(lat_min)/Grid_Space)*Grid_Space;
    if (lat_max>=0)    LatGrid_Max=     ceil(fabs(lat_max)/Grid_Space)*Grid_Space;
    else               LatGrid_Max=    -floor(fabs(lat_max)/Grid_Space)*Grid_Space;



    for (x=LongGrid_Min; x<=LongGrid_Max; x=x+Grid_Space)
    {
        x1=X_Origine + MercatorLongitudeSimple(x)*r;
        x2=x1;
        y1=Y_Origine - MercatorLatitudeSimple(lat_min)*r;
        y2=Y_Origine - MercatorLatitudeSimple(lat_max)*r;
        //printf("x1= %f, y1= %f, x2= %f, y2= %f\n", x1, y1, x2, y2);
        gdImageLine(Image, (int)round(x1), (int)round(y1), (int)round(x2), (int)round(y2), Grid_Color);

        DegToHMS(txt, x, "long");
        //printf("%d\n", gdFontGetSmall()->h);
        gdImageFilledRectangle(Image, (int)round(x1-(strlen(txt) * gdFontGetSmall()->w/2)), (int)round(Image->sy - gdFontGetSmall()->h), (int)round(x1+(strlen(txt) * gdFontGetSmall()->w /2)), (int)round(Image->sy), Grid_Color);
        gdImageString(Image, gdFontGetSmall(), (int)round(x1-(strlen(txt) * gdFontGetSmall()->w /2)), (int)round(Image->sy - gdFontGetSmall()->h), (unsigned char*)txt, Text_Color);
        gdImageFilledRectangle(Image, (int)round(x1-(strlen(txt) * gdFontGetSmall()->w/2)), 0, (int)round(x1+(strlen(txt) * gdFontGetSmall()->w /2)), (int)round(gdFontGetSmall()->h), Grid_Color);
        gdImageString(Image, gdFontGetSmall(), (int)round(x1-(strlen(txt) * gdFontGetSmall()->w /2)), 0, (unsigned char*)txt, Text_Color);
    }

    for (y=LatGrid_Min; y<=LatGrid_Max; y=y+Grid_Space)
    {
        x1=X_Origine + MercatorLongitudeSimple(long_min)*r;
        x2=X_Origine + MercatorLongitudeSimple(long_max)*r;
        y1=Y_Origine - MercatorLatitudeSimple(y)*r;
        y2=y1;
        //printf("x1= %f, y1= %f, x2= %f, y2= %f\n", x1, y1, x2, y2);
        gdImageLine(Image, (int)round(x1), (int)round(y1), (int)round(x2), (int)round(y2), Grid_Color);

        DegToHMS(txt, y, "lat");
        gdImageFilledRectangle(Image, 0, (int)round(y1 - gdFontGetSmall()->h/2), (int)round(strlen(txt) * gdFontGetSmall()->w), (int)round(y1 + gdFontGetSmall()->h/2), Grid_Color);
        gdImageString(Image, gdFontGetSmall(), 0, (int)round(y1 - gdFontGetSmall()->h/2), (unsigned char*)txt, Text_Color);
        gdImageFilledRectangle(Image, (int)round(Image->sx -(strlen(txt) * gdFontGetSmall()->w)), (int)round(y1 - gdFontGetSmall()->h/2), (int)round(Image->sx), (int)round(y1 + gdFontGetSmall()->h/2), Grid_Color);
        gdImageString(Image, gdFontGetSmall(), (int)round(Image->sx -(strlen(txt) * gdFontGetSmall()->w)), (int)round(y1 - gdFontGetSmall()->h/2), (unsigned char*)txt, Text_Color);

    }

}

void    DrawLine(   gdImagePtr Image, gshhs_contour *p,
                    double X_Origine, double Y_Origine,
                    double Zoom,
                    int Contour_Color)
{

    int c;
    double r;
    double x1, y1, x2, y2;

    r = 180.0*Zoom/M_PI;


    //printf("nb line: %d\n", p->nb_line);
    if (p->nb_line>0)
    {
        for (c= 0; c < p->nb_line; c++)
        {
            x1=p->line[c].x1;
            y1=p->line[c].y1;
            x2=p->line[c].x2;
            y2=p->line[c].y2;
            //printf("x1: %lf - y1: %lf - x2: %lf - y2: %lf\n", x1, y1, x2, y2);

            gdImageLine(Image,  (int)round(X_Origine + MercatorLongitudeSimple(x1 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y1 * GSHHS_SCL)  *r),
                                (int)round(X_Origine + MercatorLongitudeSimple(x2 * GSHHS_SCL) *r), (int)round(Y_Origine - MercatorLatitudeSimple(y2 * GSHHS_SCL)  *r),
                                Contour_Color);

        }
    }
}


void ReadLineFile(  FILE *linefile,
                    int x, int y,
                    gshhs_contour *contour)
{

    int pos_data;
    int tab_data;
    int c;
    int FReadResult = 0;


    tab_data = x*180 + (y+90);
    fseek(linefile, sizeof(PolygonFileHeader) + tab_data*sizeof(int), SEEK_SET);
    FReadResult = fread(&pos_data, sizeof(int), 1, linefile);

    fseek(linefile, pos_data, SEEK_SET);

    FReadResult = fread(&(contour->nb_line), sizeof(int), 1, linefile);
    contour->line = NULL;
    contour->line = malloc(contour->nb_line * sizeof(gshhs_line));
    if ( contour->line == NULL )
    {
        fprintf(stderr,"Allocation impossible \n");
        exit(EXIT_FAILURE);
    }

    //printf("nb line: %d\n", contour->nb_line);
    if (contour->nb_line>0)
    {
        for (c= 0; c <contour->nb_line; c++)
        {
            FReadResult = fread(&(contour->line[c]), sizeof(gshhs_line), 1, linefile);
        }
    }
}

void FreeLine(gshhs_contour *p)
{

    if (p->nb_line>0)
    {
        free(p->line);
        p->line=NULL;
    }

}

void PolygonToGML(gpc_polygon *p, FILE *gmlfile, int translate)
{

    int c, v;
    double x, y;

    fprintf(gmlfile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fprintf(gmlfile, "<dataset xmlns=\"http://www.safe.com/xml/schemas/FMEFeatures\" xmlns:fme=\"http://www.safe.com/xml/schemas/FMEFeatures\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.safe.com/xml/schemas/FMEFeatures FMEFeatures.xsd\">\n");
    fprintf(gmlfile, "<schemaFeatures>\n");
    fprintf(gmlfile, "<gml:featureMember>\n");
    fprintf(gmlfile, "<Feature>\n");
    fprintf(gmlfile, "<featureType>JCSOutput</featureType>\n");
    fprintf(gmlfile, "</Feature>\n");
    fprintf(gmlfile, "</gml:featureMember>\n");
    fprintf(gmlfile, "</schemaFeatures>\n");
    fprintf(gmlfile, "<dataFeatures>\n");
    fprintf(gmlfile, "\n");


    for (c= 0; c < p->num_contours; c++)
    {

        fprintf(gmlfile, "<gml:featureMember>\n");
        fprintf(gmlfile, "<Feature>\n");
        fprintf(gmlfile, "<featureType>JCSOutput</featureType>\n");
        fprintf(gmlfile, "<property name=\"gml2_coordsys\"></property>\n");
        fprintf(gmlfile, "<gml:PolygonProperty>\n");
        fprintf(gmlfile, "<gml:Polygon>\n");
        fprintf(gmlfile, "<gml:outerBoundaryIs>\n");
        fprintf(gmlfile, "<gml:LinearRing>\n");
        fprintf(gmlfile, "<gml:coordinates>\n");

        for (v= 0; v < p->contour[c].num_vertices; v++)
        {
            x=p->contour[c].vertex[v].x * GSHHS_SCL + translate;
            y=p->contour[c].vertex[v].y * GSHHS_SCL;
            fprintf(gmlfile, "%lf,%lf\n", x, y);
        }
        x=p->contour[c].vertex[0].x * GSHHS_SCL + translate;
        y=p->contour[c].vertex[0].y * GSHHS_SCL;
        fprintf(gmlfile, "%lf,%lf\n", x, y);


        fprintf(gmlfile, "</gml:coordinates>\n");
        fprintf(gmlfile, "</gml:LinearRing>\n");
        fprintf(gmlfile, "</gml:outerBoundaryIs>\n");
        fprintf(gmlfile, "</gml:Polygon>\n");
        fprintf(gmlfile, "</gml:PolygonProperty>\n");
        fprintf(gmlfile, "</Feature>\n");
        fprintf(gmlfile, "</gml:featureMember>\n");
        fprintf(gmlfile, "\n");
        fprintf(gmlfile, "\n");


    }

    fprintf(gmlfile, "</dataFeatures>\n");
    fprintf(gmlfile, "</dataset>\n");


}

void DrawEtopo     ( gdImagePtr Image,
                    FILE *EtopoFile,
                    int flag_memory,
                    int TileDim, int bord,
                    int origine_x, int origine_y,
                    double zoom,
                    int Land_Color, int Water_Color,
                    int FlagAlpha)
{
    typedef struct
     {
        double x;
        double y;
        double z;
    } Point;

    double cubicInterpolate (double p[4], double x) {
        return p[1] + (-0.5*p[0] + 0.5*p[2])*x + (p[0] - 2.5*p[1] + 2.0*p[2] - 0.5*p[3])*x*x + (-0.5*p[0] + 1.5*p[1] - 1.5*p[2] + 0.5*p[3])*x*x*x;
    }

    void bicubicInterpolate (double p[4][4], Point *P) {
	   double arr[4];

        arr[0] = cubicInterpolate(p[0], P->y);
        arr[1] = cubicInterpolate(p[1], P->y);
        arr[2] = cubicInterpolate(p[2], P->y);
        arr[3] = cubicInterpolate(p[3], P->y);
        P->z=cubicInterpolate(arr, P->x);
    }


    void Bilin(Point A, Point B, Point C, Point D, Point *P) {

        typedef struct
        {
            double a;
            double b;
        } Coef;


        Coef AB, CD;
        Point E, F;
        Coef EF;

        AB.a = (A.z-B.z)/(A.x-B.x);
        AB.b = A.z-AB.a*A.x;
        CD.a = (C.z-D.z)/(C.x-D.x);
        CD.b = C.z-CD.a*C.x;

        E.x = P->x;
        E.y = A.y;
        E.z = AB.a*E.x+AB.b;
        F.x = P->x;
        F.y = C.y;
        F.z = CD.a*F.x+CD.b;

        EF.a = (E.z-F.z)/(E.y-F.y);
        EF.b = E.z-EF.a*E.y;

        P->z = EF.a*P->y+EF.b;
    };


    ETOPO_Header Header;
    int PosData, PosData_Old;
    int PosDataE, PosDataE_Old;
    int PosDataW, PosDataW_Old;
    short int z;
    int i;
    int lon, lat;
    int lon_init, lat_init;
    double lat_img, lon_img;
    double lon_p, lat_p;
    double lon_s, lat_s;
    double pt[4][4];
    Point P;
    int u, v;
    int GetColor;

    int FReadResult = 0;

    short int * TestImg = NULL;
    TestImg = calloc ( TileDim * TileDim, sizeof(short int) );
    if ( TestImg == NULL )
    {
        fprintf(stderr, "Allocation tableau test tile impossible\n");
        exit(EXIT_FAILURE);
    }

    short int * ETOPO_Data = NULL;

    // definition de la palette de couleur pour les altitudes negatives
    int TabColor;
    #define NC  101
    int NegativeColor[NC];
    int xn[NC] =  {     -4500, -4455, -4410, -4365, -4320, -4275, -4230, -4185, -4140, -4095,
                        -4050, -4005, -3960, -3915, -3870, -3825, -3780, -3735, -3690, -3645,
                        -3600, -3555, -3510, -3465, -3420, -3375, -3330, -3285, -3240, -3195,
                        -3150, -3105, -3060, -3015, -2970, -2925, -2880, -2835, -2790, -2745,
                        -2700, -2655, -2610, -2565, -2520, -2475, -2430, -2385, -2340, -2295,
                        -2250, -2205, -2160, -2115, -2070, -2025, -1980, -1935, -1890, -1845,
                        -1800, -1755, -1710, -1665, -1620, -1575, -1530, -1485, -1440, -1395,
                        -1350, -1305, -1260, -1215, -1170, -1125, -1080, -1035,  -990,  -945,
                         -900,  -855,  -810,  -765,  -720,  -675,  -630,  -585,  -540,  -495,
                         -450,  -405,  -360,  -315,  -270,  -225,  -180,  -135,   -90,   -45,
                            0};
    int rn[NC] =  {       113,   113,   114,   114,   115,   116,   116,   117,   118,   119,
                          120,   121,   122,   123,   124,   125,   126,   127,   128,   129,
                          130,   131,   132,   133,   134,   134,   135,   136,   137,   138,
                          138,   139,   140,   141,   142,   142,   143,   144,   145,   145,
                          146,   147,   148,   149,   150,   151,   151,   152,   153,   154,
                          155,   156,   157,   158,   159,   160,   161,   162,   163,   164,
                          165,   166,   167,   168,   169,   170,   171,   172,   173,   175,
                          176,   177,   178,   179,   181,   182,   183,   184,   185,   186,
                          188,   189,   190,   191,   192,   193,   194,   196,   197,   198,
                          199,   201,   202,   204,   205,   207,   208,   210,   212,   214,
                          216};
    int gn[NC] =  {       171,   172,   172,   173,   174,   174,   175,   176,   176,   177,
                          177,   178,   179,   179,   180,   180,   181,   182,   182,   183,
                          184,   184,   185,   186,   186,   187,   188,   188,   189,   190,
                          191,   191,   192,   193,   193,   194,   195,   196,   196,   197,
                          198,   198,   199,   200,   201,   201,   202,   203,   204,   205,
                          205,   206,   207,   208,   209,   210,   210,   211,   212,   213,
                          214,   215,   215,   216,   217,   218,   218,   219,   220,   221,
                          221,   222,   223,   224,   224,   225,   226,   226,   227,   228,
                          229,   230,   230,   231,   232,   233,   234,   235,   235,   236,
                          237,   238,   238,   239,   240,   240,   241,   241,   241,   242,
                          242};
    int bn[NC] =  {       216,   217,   217,   218,   219,   219,   220,   220,   221,   221,
                          222,   222,   222,   223,   223,   224,   224,   224,   225,   225,
                          226,   226,   227,   227,   228,   229,   229,   230,   231,   231,
                          232,   233,   233,   234,   234,   235,   235,   236,   237,   237,
                          238,   238,   239,   239,   240,   240,   241,   242,   242,   243,
                          244,   244,   245,   246,   246,   247,   247,   248,   248,   248,
                          249,   249,   249,   250,   250,   250,   251,   251,   252,   252,
                          252,   253,   253,   254,   254,   254,   255,   255,   255,   255,
                          255,   255,   255,   255,   255,   255,   255,   255,   255,   255,
                          255,   255,   254,   254,   254,   254,   254,   254,   254,   254,
                          254};

    // definition de la palette de couleur pour les altitudes positive
    #define PC  121
    int PositiveColor[PC];
    int xp[PC] =  {         0,    45,    90,   135,   180,   225,   270,   315,   360,   405,
                          450,   495,   540,   585,   630,   675,   720,   765,   810,   855,
                          900,   945,   990,  1035,  1080,  1125,  1170,  1215,  1260,  1305,
                         1350,  1395,  1440,  1485,  1530,  1575,  1620,  1665,  1710,  1755,
                         1800,  1845,  1890,  1935,  1980,  2025,  2070,  2115,  2160,  2205,
                         2250,  2295,  2340,  2385,  2430,  2475,  2520,  2565,  2610,  2655,
                         2700,  2745,  2790,  2835,  2880,  2925,  2970,  3015,  3060,  3105,
                         3150,  3195,  3240,  3285,  3330,  3375,  3420,  3465,  3510,  3555,
                         3600,  3645,  3690,  3735,  3780,  3825,  3870,  3915,  3960,  4005,
                         4050,  4095,  4140,  4185,  4230,  4275,  4320,  4365,  4410,  4455,
                         4500,  4545,  4590,  4635,  4680,  4725,  4770,  4815,  4860,  4905,
                         4950,  4995,  5040,  5085,  5130,  5175,  5220,  5265,  5310,  5355,
                         5400};
    int rp[PC] =  {       172,   163,   156,   152,   149,   147,   147,   148,   150,   153,
                          156,   160,   163,   167,   170,   174,   177,   180,   183,   186,
                          189,   192,   195,   198,   201,   204,   207,   210,   212,   215,
                          217,   219,   222,   224,   227,   229,   232,   234,   237,   238,
                          239,   239,   239,   238,   236,   235,   233,   231,   230,   228,
                          227,   225,   224,   223,   221,   219,   218,   216,   214,   213,
                          211,   209,   208,   207,   205,   204,   203,   202,   201,   199,
                          198,   197,   196,   195,   194,   193,   192,   190,   189,   187,
                          185,   183,   180,   178,   175,   173,   171,   170,   169,   168,
                          169,   169,   170,   171,   173,   175,   177,   179,   181,   184,
                          186,   188,   191,   193,   195,   198,   200,   203,   206,   209,
                          212,   216,   219,   223,   226,   230,   233,   237,   240,   242,
                          245};
    int gp[PC] =  {       208,   202,   198,   195,   193,   191,   191,   191,   192,   193,
                          194,   195,   196,   198,   199,   200,   200,   201,   202,   203,
                          204,   205,   207,   208,   210,   212,   214,   216,   218,   220,
                          222,   224,   225,   227,   229,   231,   232,   234,   235,   235,
                          235,   234,   233,   232,   230,   228,   226,   224,   223,   221,
                          219,   218,   216,   215,   213,   211,   210,   208,   206,   204,
                          202,   200,   197,   195,   192,   190,   187,   184,   181,   178,
                          176,   173,   170,   168,   166,   164,   162,   159,   157,   155,
                          152,   149,   145,   142,   139,   137,   135,   135,   136,   138,
                          141,   145,   149,   153,   156,   160,   163,   166,   169,   171,
                          174,   177,   180,   183,   186,   189,   193,   196,   200,   204,
                          208,   212,   217,   221,   225,   229,   232,   236,   239,   242,
                          244};
    int bp[PC] =  {       165,   157,   151,   146,   143,   141,   139,   139,   139,   139,
                          140,   141,   142,   143,   143,   144,   145,   145,   147,   148,
                          150,   153,   156,   159,   163,   166,   169,   172,   174,   175,
                          177,   178,   179,   180,   182,   184,   186,   188,   190,   191,
                          192,   192,   192,   190,   189,   187,   184,   181,   178,   174,
                          171,   168,   166,   164,   162,   161,   161,   160,   160,   159,
                          157,   154,   151,   147,   142,   138,   133,   129,   124,   121,
                          117,   114,   111,   108,   105,   103,   100,    98,    95,    93,
                           90,    87,    85,    82,    81,    80,    81,    84,    88,    94,
                          101,   108,   115,   122,   128,   133,   138,   142,   146,   150,
                          154,   158,   162,   167,   171,   176,   181,   186,   191,   195,
                          200,   205,   210,   214,   219,   223,   227,   231,   235,   239,
                          242};
/*
    int xwhp[PC] =  {       0,    65,   130,   195,   260,   325,   390,   455,   520,   585,
                          650,   715,   780,   845,   910,   975,  1040,  1105,  1170,  1235,
                          300,  1365,  1430,  1495,  1560,  1625,  1690,  1755,  1820,  1885,
                         1950,  2015,  2080,  2145,  2210,  2275,  2340,  2405,  2470,  2535,
                         2600,  2665,  2730,  2795,  2860,  2925,  2990,  3055,  3120,  3185,
                         3250,  3315,  3380,  3445,  3510,  3575,  3640,  3705,  3770,  3835,
                         3900,  3965,  4030,  4095,  4160,  4225,  4290,  4355,  4420,  4485,
                         4550,  4615,  4680,  4745,  4810,  4875,  4940,  5005,  5070,  5135,
                         5200,  5265,  5330,  5395,  5460,  5525,  5590,  5655,  5720,  5785,
                         5850,  5915,  5980,  6045,  6110,  6175,  6240,  6305,  6370,  6435,
                         6500,  6565,  6630,  6695,  6760,  6825,  6890,  6955,  7020,  7085,
                         7150,  7215,  7280,  7345,  7410,  7475,  7540,  7605,  7670,  7735,
                         7800};
    int rwhp[PC] =  {     77,    86,    92,    96,    99,   101,   104,   107,   111,   115,
                          119,   124,   130,   135,   141,   147,   153,   159,   166,   172,
                          178,   184,   189,   195,   200,   204,   208,   211,   214,   216,
                          218,   220,   221,   221,   222,   222,   222,   221,   221,   220,
                          219,   218,   216,   215,   214,   212,   211,   210,   208,   207,
                          206,   205,   204,   204,   203,   203,   202,   202,   202,   202,
                          202,   202,   202,   202,   203,   203,   203,   204,   204,   204,
                          205,   205,   205,   206,   206,   206,   206,   206,   206,   206,
                          206,   206,   206,   206,   206,   206,   206,   206,   206,   205,
                          205,   205,   205,   205,   204,   204,   204,   204,   204,   204,
                          204,   204,   204,   204,   204,   204,   205,   205,   205,   205,
                          206,   206,   206,   207,   207,   208,   208,   208,   209,   209,
                          210};
    int gwhp[PC] =  {    141,   159,   169,   174,   175,   175,   175,   175,   175,   176,
                          177,   178,   179,   180,   182,   183,   184,   185,   186,   187,
                          188,   189,   189,   190,   190,   190,   190,   190,   189,   189,
                          188,   188,   187,   186,   185,   184,   183,   182,   181,   179,
                          178,   177,   176,   175,   174,   173,   172,   171,   170,   170,
                          169,   169,   168,   168,   168,   168,   168,   168,   168,   169,
                          169,   169,   170,   170,   171,   172,   172,   173,   174,   174,
                          175,   176,   177,   178,   178,   179,   180,   180,   181,   182,
                          182,   183,   184,   184,   185,   185,   186,   186,   187,   188,
                          188,   189,   189,   190,   190,   191,   192,   192,   193,   193,
                          194,   195,   195,   196,   197,   197,   198,   199,   200,   200,
                          201,   202,   203,   203,   204,   205,   206,   207,   207,   208,
                          209};
    int bwhp[PC] =  {    114,   127,   134,   137,   138,   138,   138,   138,   138,   139,
                          140,   141,   142,   144,   145,   147,   149,   151,   153,   154,
                          156,   158,   159,   161,   162,   163,   164,   164,   164,   164,
                          164,   163,   163,   162,   161,   160,   159,   158,   156,   155,
                          153,   152,   151,   149,   148,   147,   146,   144,   143,   143,
                          142,   142,   141,   141,   141,   141,   141,   142,   142,   143,
                          144,   144,   145,   146,   147,   149,   150,   151,   152,   154,
                          155,   156,   158,   159,   161,   162,   163,   165,   166,   167,
                          169,   170,   171,   173,   174,   175,   176,   178,   179,   180,
                          181,   182,   184,   185,   186,   187,   188,   189,   190,   191,
                          192,   193,   194,   195,   196,   197,   198,   199,   199,   200,
                          201,   202,   203,   204,   204,   205,   206,   207,   208,   208,
                          209};
*/
    #define IC  17
    int IceColor[IC];

    int xi[IC]   =  {       0,   250,   500,   750,  1000,  1250,  1500,  1750,  2000,  2250,
                         2500,  2750,  3000,  3250,  3500,  3750, 4000};
    int ri[IC]   =  {     207,210,213,216,219,222,225,228,231,234,237,240,243,246,249,252,255};
    int gi[IC]   =  {     207,210,213,216,219,222,225,228,231,234,237,240,243,246,249,252,255};
    int bi[IC]  =   {     207,210,213,216,219,222,225,228,231,234,237,240,243,246,249,252,255};


    PosData_Old  = -1;
    PosDataW_Old = -1;
    PosDataE_Old = -1;

    // Ouverture du fichier de données altimetrique et lecture du Header
    /*
    EtopoFile = fopen("./bd/etopo_060_ice.dat", "rb");
    if (EtopoFile == NULL)
    {
        fprintf (stderr, "Tiles_G Error!: \n");
        fprintf (stderr, "Could not open file: \n");
        exit(EXIT_FAILURE);
    }
    */

    fseek(EtopoFile, 0 , SEEK_SET);
    FReadResult = fread(&Header, sizeof(Header), 1, EtopoFile);
    if (DEBUG)
    {
        printf("Header.NCOLS=%d\n",         Header.NCOLS);
        printf("Header.NROWS=%d\n",         Header.NROWS);
        printf("Header.START_X=%lf\n",      Header.START_X);
        printf("Header.START_Y=%lf\n",      Header.START_Y);
        printf("Header.FINISH_X=%lf\n",     Header.FINISH_X);
        printf("Header.FINISH_Y=%lf\n",     Header.FINISH_Y);
        printf("Header.CELLSIZE=%lf\n",     Header.CELLSIZE);
        printf("Header.NODATA_VALUE=%d\n",  Header.NODATA_VALUE);
        printf("Header.NUMBERTYPE=%s\n",    Header.NUMBERTYPE);
        printf("Header.ZUNITS=%s\n",        Header.ZUNITS);
        printf("Header.MIN_VALUE=%d\n",     Header.MIN_VALUE);
        printf("Header.MAX_VALUE=%d\n",     Header.MAX_VALUE);
    }

    if (flag_memory)
    {
        //printf("Mise en memoire\n");
        ETOPO_Data = malloc ( Header.NCOLS * Header.NROWS * sizeof(short int) );
        if ( ETOPO_Data == NULL )
        {
            fprintf(stderr, "Allocation tableau ETOPO impossible\n");
            exit(EXIT_FAILURE);
        }
        FReadResult = fread(ETOPO_Data, sizeof(short int), Header.NCOLS * Header.NROWS, EtopoFile);
        //printf("Mise en memoire termine\n");
    }

    // Define colors
    for (i=0; i<NC; i++) NegativeColor[i] = gdImageColorAllocate(Image, rn[i], gn[i], bn[i]);
    //for (i=0; i<PC; i++) PositiveColor[i] = gdImageColorAllocate(Image, rwhp[i], gwhp[i], bwhp[i]);
    for (i=0; i<PC; i++) PositiveColor[i] = gdImageColorAllocate(Image, rp[i], gp[i], bp[i]);
    for (i=0; i<IC; i++) IceColor[i] = gdImageColorAllocate(Image, ri[i], gi[i], bi[i]);

    // Pour dessiner le tile, on le parcours pixel par pixel
    // comme ca on est sur de ne pas en oublier

    for (lat=0; lat<TileDim; lat++)
    {
        ////printf("Ligne: %d\n", lat);
        for (lon=0; lon < TileDim; lon++)
        {
            if (TestImg[lon + TileDim * lat] == 0)
            {
                ////printf("Lat: %d, Lon %d\n", lat, lon);
                // Latitude du pixel
                lat_img = MercatorInverseLatitudeSimple((-lat + TileDim - origine_y) * M_PI / (180.0 * zoom));
                // Longitude du pixel
                lon_img = (lon - origine_x) / zoom;
                ////printf("Longitude = %lf, Latitude = %lf\n", lon_img, lat_img);

                // Longitude et latitude du du point de donnée ETOPO le plus proche
                // inferieur dans le cas des longitudes
                // superieur dans le cas des latitudes
                // Dans le fichier ETOPO les donnee sont stockees
                // -180 <= longitudes =< 180
                //   90 >= latitudes  >= -90
                lon_p = (floor((lon_img + 180) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) - 180;
                lat_p = (ceil ((lat_img +  90) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) -  90;

                ////printf("lon_p = %lf, lat_p = %lf\n", lon_p, lat_p);

                // On prend le point directement inferieur, toujours dans le meme sens
                // Car on va interpoler les donnees en bicubique, donc sur 16 points
                lon_s = lon_p - (1 / Header.CELLSIZE);
                lat_s = lat_p + (1 / Header.CELLSIZE);
                ////printf("lon_s = %lf, lat_s = %lf\n", lon_s, lat_s);

                // Calcul des la position de depart de lecture dans le fichier ETOPO
                PosData = round(((Header.NCOLS - 1) / 2 + lon_s * Header.CELLSIZE)+((Header.NROWS - 1) / 2 - lat_s * Header.CELLSIZE) * Header.NCOLS);
                PosData_Old = PosData;

                if ((lon_s >= -180) && (lon_s <= (180 - 2 * (1 / Header.CELLSIZE))))
                {
                    if (flag_memory)
                    {
                        i=PosData;
                        // Lecture des donnees en memoire
                        for (v=0; v<4; v++)
                        {
                            for (u=0; u<4; u++)
                            {
                                pt[u][v] = (double)ETOPO_Data[i];
                                ////printf("pt[%d][%d] = %lf\n", v, u, pt[v][u]);
                                i++;
                            }
                            i = PosData + (Header.NCOLS - 4);
                        }
                    }
                    else
                    {
                        fseek(EtopoFile, sizeof(ETOPO_Header) + PosData * sizeof(short int), SEEK_SET);
                        // Lecture du fichier
                        for (v=0; v<4; v++)
                        {
                            for (u=0; u<4; u++)
                            {
                                FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                                pt[u][v] = (double)z;
                                ////printf("pt[%d][%d] = %lf\n", v, u, pt[v][u]);
                            }
                            fseek(EtopoFile, (Header.NCOLS - 4) * sizeof(short int), SEEK_CUR);
                        }
                    }
                }

                if (lon_s < -180)
                {
                    if (flag_memory)
                    {
                        PosDataW = PosData + 1;
                        i = PosDataW;
                        // Lecture des donnees en memoire
                        for (v=0; v<4; v++)
                        {
                            for (u=1; u<4; u++)
                            {
                                pt[u][v] = (double)ETOPO_Data[i];
                                i++;
                            }
                            i = PosDataW + (Header.NCOLS - 3);
                            pt[0][v] = (double)ETOPO_Data[i];
                        }
                    }
                    else
                    {
                        PosDataW = PosData + 1;
                        fseek(EtopoFile, sizeof(ETOPO_Header) + PosDataW * sizeof(short int), SEEK_SET);
                        // Lecture du fichier
                        for (v=0; v<4; v++)
                        {
                            for (u=1; u<4; u++)
                            {
                                FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                                pt[u][v] = (double)z;
                                ////printf("pt[%d][%d] = %lf\n", v, u, pt[v][u]);
                            }
                            fseek(EtopoFile, (Header.NCOLS - 3) * sizeof(short int), SEEK_CUR);
                            FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                            pt[0][v] = (double)z;
                        }
                    }
                }

                if (lon_s >= (180 - 2 * (1 / Header.CELLSIZE)))
                {
                    if (flag_memory)
                    {
                        PosDataE = PosData - Header.NCOLS + 3;
                        i = PosDataE;
                        // Lecture des donnees en memoire
                        for (v=0; v<4; v++)
                        {
                            pt[3][v] = (double)ETOPO_Data[i];
                            i = PosDataE + (Header.NCOLS - 3);
                            for (u=0; u<3; u++)
                            {
                                pt[u][v] = (double)ETOPO_Data[i];
                                i++;
                                ////printf("pt[%d][%d] = %lf\n", v, u, pt[v][u]);
                            }
                        }
                    }
                    else
                    {
                        PosDataE = PosData - Header.NCOLS + 3;
                        fseek(EtopoFile, sizeof(ETOPO_Header) + PosDataE * sizeof(short int), SEEK_SET);
                        // Lecture du fichier
                        for (v=0; v<4; v++)
                        {
                            FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                            pt[3][v] = (double)z;
                            fseek(EtopoFile, (Header.NCOLS - 3) * sizeof(short int), SEEK_CUR);
                            for (u=0; u<3; u++)
                            {
                                FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                                pt[u][v] = (double)z;
                                ////printf("pt[%d][%d] = %lf\n", v, u, pt[v][u]);
                            }
                        }
                    }
                }

                lon_init = lon;
                lat_init = lat;
                do
                {
                    do
                    {
                        // Interpolation Bicubique
                        P.x = (lon_img - lon_p) * Header.CELLSIZE;
                        P.y = (lat_p - lat_img) * Header.CELLSIZE;
                        P.z = 0;
                        bicubicInterpolate(pt, &P);
                        z=(short int)floor(P.z);
                        ////printf("z = %d\n", z);

                        /*
                        pos_data=((Header.NCOLS-1)/2+lon_p*Header.CELLSIZE)+((Header.NROWS-1)/2-lat_p*Header.CELLSIZE)*Header.NCOLS;
                        fseek(EtopoFile, sizeof(ETOPO_Header)+pos_data*sizeof(short int), SEEK_SET);
                        FReadResult = fread(&z, sizeof(short int), 1, EtopoFile);
                        */
                        ////printf("z = %d\n", z);

                        GetColor = gdImageGetPixel(Image, lon+bord, lat+bord);
                        if ((GetColor == Land_Color) && (z <= 0)) z = 1;
                        if ((GetColor == Water_Color) && (z >= 0)) z = -1;

                        ////printf("lon_min = %lf, lat_max = %lf, z = %d\n", lon_p, lat_p, z);
                        // Dessin du pixel avecla bonne couleur en fonction de l'altitude
                        if (lat_img >-60)
                        {
                            if (z>=0 && (FlagAlpha & 2)==0)
                            {
                                if (z<=4950)
                                {
                                    TabColor = ceil(z / (xp[1] - xp[0]));
                                    //TabColor=floor(log10((double)z+1))*31;
                                    if (TabColor > (PC -1)) TabColor = (PC -1);
                                    if (TabColor < 0)       TabColor = 0;

                                    gdImageSetPixel(Image, lon+bord, lat+bord, PositiveColor[TabColor]);
                                }
                                else
                                {
                                    TabColor = ceil((z-4950) / (xi[1] - xi[0]));
                                    //TabColor=floor(log10((double)z+1))*31;
                                    if (TabColor > (IC -1)) TabColor = (IC -1);
                                    if (TabColor < 0)       TabColor = 0;

                                    gdImageSetPixel(Image, lon+bord, lat+bord, IceColor[TabColor]);
                                }
                                
                            }
                        }
                        else
                        {
                            if (z>=0 && (FlagAlpha & 2)==0)
                            {
                                TabColor = ceil(z / (xi[1] - xi[0]));
                                //TabColor=floor(log10((double)z+1))*31;
                                if (TabColor > (IC -1)) TabColor = (IC -1);
                                if (TabColor < 0)       TabColor = 0;

                                gdImageSetPixel(Image, lon+bord, lat+bord, IceColor[TabColor]);
                            }
                        }


                        if (z<0 && (FlagAlpha & 1)==0)
                        {
                            TabColor = (NC - 1) - floor(z / (xn[0] - xn[1]));
                            if (TabColor < 0) TabColor = 0;
                            if (TabColor > (NC -1)) TabColor = (NC -1);

                            gdImageSetPixel(Image, lon+bord, lat+bord, NegativeColor[TabColor]);
                        }

                        TestImg[lon + TileDim * lat] = 1;

                        lon++;
                        if (lon < TileDim)
                        {
                            lat_img = MercatorInverseLatitudeSimple((-lat + TileDim - origine_y) * M_PI / (180.0 * zoom));
                            lon_img = (lon - origine_x) / zoom;

                            lon_p = (floor((lon_img + 180) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) - 180;
                            lat_p = (ceil ((lat_img +  90) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) -  90;

                            lon_s = lon_p - (1 / Header.CELLSIZE);
                            lat_s = lat_p + (1 / Header.CELLSIZE);

                            PosData = round(((Header.NCOLS - 1) / 2 + lon_s * Header.CELLSIZE)+((Header.NROWS - 1) / 2 - lat_s * Header.CELLSIZE) * Header.NCOLS);
                        }
                        else
                            break;

                    } while (PosData_Old == PosData);

                    lon = lon_init;
                    lat++;
                    if (lat < TileDim)
                    {
                        lat_img = MercatorInverseLatitudeSimple((-lat + TileDim - origine_y) * M_PI / (180.0 * zoom));
                        lon_img = (lon - origine_x) / zoom;

                        lon_p = (floor((lon_img + 180) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) - 180;
                        lat_p = (ceil ((lat_img +  90) / (1 / Header.CELLSIZE)) * (1 / Header.CELLSIZE)) -  90;

                        lon_s = lon_p - (1 / Header.CELLSIZE);
                        lat_s = lat_p + (1 / Header.CELLSIZE);

                        PosData = round(((Header.NCOLS - 1) / 2 + lon_s * Header.CELLSIZE)+((Header.NROWS - 1) / 2 - lat_s * Header.CELLSIZE) * Header.NCOLS);
                    }
                    else
                        break;

                } while (PosData_Old == PosData);

                lon = lon_init;
                lat = lat_init;
            }
        }
    }

    if (flag_memory)
    {
        free(ETOPO_Data);
        ETOPO_Data = NULL;
        free(TestImg);
        TestImg = NULL;
    }
}

unsigned int compute_outcode(Point p, Rectangle r)
{
    unsigned int oc = 0;

    if (p.y > r.p2.y)
	oc |= TOP;
    else if (p.y < r.p1.y)
	oc |= BOTTOM;


    if (p.x > r.p2.x)
	oc |= RIGHT;
    else if (p.x < r.p1.x)
	oc |= LEFT;

    return oc;
}

int cohen_sutherland (Line LineStart, Rectangle ClippingRectangle, Line *LineFinish)
{
    int accept;
    int done;
    unsigned int outcode1, outcode2;

    accept = FALSE;
    done = FALSE;

    double check;


    if (ClippingRectangle.p1.x > ClippingRectangle.p2.x)
    {
        check = ClippingRectangle.p1.x;
        ClippingRectangle.p1.x = ClippingRectangle.p2.x;
        ClippingRectangle.p2.x = check;
    }

    if (ClippingRectangle.p1.y > ClippingRectangle.p2.y)
    {
        check = ClippingRectangle.p1.y;
        ClippingRectangle.p1.y = ClippingRectangle.p2.y;
        ClippingRectangle.p2.y = check;
    }


    outcode1 = compute_outcode (LineStart.p1, ClippingRectangle);
    outcode2 = compute_outcode (LineStart.p2, ClippingRectangle);
    do
    {
        if (outcode1 == 0 && outcode2 == 0)
        {
            accept = TRUE;
            done = TRUE;
        }
        else if (outcode1 & outcode2)
        {
            done = TRUE;
        }
        else
        {
            double x, y;
            int outcode_ex = outcode1 ? outcode1 : outcode2;
            if (outcode_ex & TOP)
            {
                x = LineStart.p1.x + (LineStart.p2.x - LineStart.p1.x) * (ClippingRectangle.p2.y - LineStart.p1.y) / (LineStart.p2.y - LineStart.p1.y);
                y = ClippingRectangle.p2.y;
            }

            else if (outcode_ex & BOTTOM)
            {
                x = LineStart.p1.x + (LineStart.p2.x - LineStart.p1.x) * (ClippingRectangle.p1.y - LineStart.p1.y) / (LineStart.p2.y - LineStart.p1.y);
                y = ClippingRectangle.p1.y;
            }
            else if (outcode_ex & RIGHT)
            {
                y = LineStart.p1.y + (LineStart.p2.y - LineStart.p1.y) * (ClippingRectangle.p2.x - LineStart.p1.x) / (LineStart.p2.x - LineStart.p1.x);
                x = ClippingRectangle.p2.x;
            }
            else
            {
                y = LineStart.p1.y + (LineStart.p2.y - LineStart.p1.y) * (ClippingRectangle.p1.x - LineStart.p1.x) / (LineStart.p2.x - LineStart.p1.x);
                x = ClippingRectangle.p1.x;
            }
            if (outcode_ex == outcode1)
            {
                LineStart.p1.x = x;
                LineStart.p1.y = y;
                outcode1 = compute_outcode (LineStart.p1, ClippingRectangle);
            }
            else
            {
                LineStart.p2.x = x;
                LineStart.p2.y = y;
                outcode2 = compute_outcode (LineStart.p2, ClippingRectangle);
            }
        }
    } while (done == FALSE);

    if (accept == TRUE)
    {
        LineFinish->p1.x = LineStart.p1.x;
        LineFinish->p1.y = LineStart.p1.y;
        LineFinish->p2.x = LineStart.p2.x;
        LineFinish->p2.y = LineStart.p2.y;

        return TRUE;
    }
    else return FALSE;

}


