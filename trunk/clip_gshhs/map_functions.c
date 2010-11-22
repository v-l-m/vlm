/**
 *    Filename        : map_functions.c

 *    Created            : 07 January 2009 (23:08:16)
 *    Created by        : StephPen - stephpen@gmail.com

 *    Last Updated    : 23:23 21/11/2010
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

    
    
    if (DEBUG==1)
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
    fseek(polyfile, 0 , SEEK_SET);
    fread(header, sizeof(PolygonFileHeader), 1, polyfile);

    if (DEBUG==1)
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

void ReadPolygonFile (FILE *polyfile,
                        int x, int y,
                        int pas_x, int pas_y,
                        gpc_polygon *p1, gpc_polygon *p2, gpc_polygon *p3, gpc_polygon *p4, gpc_polygon *p5)
{

    long pos_data;
    long tab_data;
    int c, v;


    tab_data = (x/pas_x)*(180/pas_y) + (y+90)/pas_y;
    fseek(polyfile, sizeof(PolygonFileHeader) + tab_data*sizeof(int), SEEK_SET);
    fread(&pos_data, sizeof(long), 1, polyfile);
    
    //printf("tabdata: %d, posdata: %d\n", tab_data, pos_data);
    
    fseek(polyfile, pos_data, SEEK_SET);
    
    // Lecture du polygone 1 -> Terre
    fread(&(p1->num_contours), sizeof(int), 1, polyfile);
    //printf("p1->num_contours: %d\n", p1->num_contours);
    MALLOC(p1->hole, p1->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p1->contour, p1->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);
    
        for (c= 0; c < p1->num_contours; c++)
        {
            fread(&(p1->hole[c]), sizeof(int), 1, polyfile);
            fread(&(p1->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p1->contour[c].vertex, p1->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);
        
            for (v= 0; v < p1->contour[c].num_vertices; v++)
            {
                fread(&(p1->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                fread(&(p1->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }
    
    //printf("data1: %d, data2: %d, data3: %d\n", p1->num_contours, p1->hole[0], p1->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p1->contour[0].vertex[0].x, p1->contour[0].vertex[0].y);
    

    // Lecture du polygone 2 -> Lacs
    fread(&(p2->num_contours), sizeof(int), 1, polyfile);
    //printf("p2->num_contours: %d\n", p2->num_contours);
    MALLOC(p2->hole, p2->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p2->contour, p2->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);
    
        for (c= 0; c < p2->num_contours; c++)
        {
            fread(&(p2->hole[c]), sizeof(int), 1, polyfile);
            fread(&(p2->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p2->contour[c].vertex, p2->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);
        
            for (v= 0; v < p2->contour[c].num_vertices; v++)
            {
                fread(&(p2->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                fread(&(p2->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }
    
    //printf("data1: %d, data2: %d, data3: %d\n", p2->num_contours, p2->hole[0], p2->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p2->contour[0].vertex[0].x, p2->contour[0].vertex[0].y);

    // Lecture du polygone 3 -> Iles dans les Lacs
    fread(&(p3->num_contours), sizeof(int), 1, polyfile);
    //printf("p3->num_contours: %d\n", p3->num_contours);
    MALLOC(p3->hole, p3->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p3->contour, p3->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);
    
        for (c= 0; c < p3->num_contours; c++)
        {
            fread(&(p3->hole[c]), sizeof(int), 1, polyfile);
            fread(&(p3->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p3->contour[c].vertex, p3->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);
        
            for (v= 0; v < p3->contour[c].num_vertices; v++)
            {
                fread(&(p3->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                fread(&(p3->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }
    
    //printf("data1: %d, data2: %d, data3: %d\n", p3->num_contours, p3->hole[0], p3->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p3->contour[0].vertex[0].x, p3->contour[0].vertex[0].y);

    // Lecture du polygone 4 -> Flaques sur les Iles dans les Lacs
    fread(&(p4->num_contours), sizeof(int), 1, polyfile);
    //printf("p4->num_contours: %d\n", p4->num_contours);
    MALLOC(p4->hole, p4->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p4->contour, p4->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);
    
        for (c= 0; c < p4->num_contours; c++)
        {
            fread(&(p4->hole[c]), sizeof(int), 1, polyfile);
            fread(&(p4->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p4->contour[c].vertex, p4->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);
        
            for (v= 0; v < p4->contour[c].num_vertices; v++)
            {
                fread(&(p4->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                fread(&(p4->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }
    
    //printf("data1: %d, data2: %d, data3: %d\n", p4->num_contours, p4->hole[0], p4->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p4->contour[0].vertex[0].x, p4->contour[0].vertex[0].y);

    // Lecture du polygone 5 -> Lignes de Recif
    fread(&(p5->num_contours), sizeof(int), 1, polyfile);
    //printf("p5->num_contours: %d\n", p5->num_contours);
    MALLOC(p5->hole, p5->num_contours * sizeof(int), "hole flag array creation", int);
    MALLOC(p5->contour, p5->num_contours * sizeof(gpc_vertex_list), "contour creation", gpc_vertex_list);
    
        for (c= 0; c < p5->num_contours; c++)
        {
            fread(&(p5->hole[c]), sizeof(int), 1, polyfile);
            fread(&(p5->contour[c].num_vertices), sizeof(int), 1, polyfile);
            MALLOC(p5->contour[c].vertex, p5->contour[c].num_vertices * sizeof(gpc_vertex), "vertex creation", gpc_vertex);
        
            for (v= 0; v < p5->contour[c].num_vertices; v++)
            {
                fread(&(p5->contour[c].vertex[v].x), sizeof(double), 1, polyfile);
                fread(&(p5->contour[c].vertex[v].y), sizeof(double), 1, polyfile);
            }
        }
    
    //printf("data1: %d, data2: %d, data3: %d\n", p5->num_contours, p5->hole[0], p5->contour[0].num_vertices);
    //printf("xx: %lf, yy: %lf\n", p5->contour[0].vertex[0].x, p5->contour[0].vertex[0].y);

}

void FreePolygon(gpc_polygon *p)
{
  int c;

  for (c= 0; c < p->num_contours; c++)
    FREE(p->contour[c].vertex);
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
                    int x, int y,
                    int pas_x, int pas_y,
                    double X_Origine, double Y_Origine,
                    double Zoom,
                    int Contour_Color)
{

    int c;
    double r;
    double x1, y1, x2, y2;
    double long_max, lat_max, long_min, lat_min;
    
    r = 180.0*Zoom/M_PI;
    
    long_min=(double)x/GSHHS_SCL;
    lat_min=(double)y/GSHHS_SCL;
    long_max=((double)x+(double)pas_x)/GSHHS_SCL;
    lat_max=((double)y+(double)pas_y)/GSHHS_SCL;

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

    long pos_data;
    long tab_data;
    int c;


    tab_data = x*180 + (y+90);
    fseek(linefile, sizeof(PolygonFileHeader) + tab_data*sizeof(int), SEEK_SET);
    fread(&pos_data, sizeof(long), 1, linefile);
    
    fseek(linefile, pos_data, SEEK_SET);
    
    fread(&(contour->nb_line), sizeof(int), 1, linefile);
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
            fread(&(contour->line[c]), sizeof(gshhs_line), 1, linefile);
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




