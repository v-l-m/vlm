#!/usr/bin/env python
# -*- coding: utf-8 -*-

#
#    Filename          : pygshhs1.py
#
#    Created           : 07 January 2009 (23:08:51)
#    Created by        : StephPen - stephpen@gmail.com
#
#    Last Updated      : 23:31 21/11/2010
#    Updated by        : StephPen - stephpen@gmail.com
#
#    (c) 2008 by Stephane PENOT
#        See COPYING file for copying and redistribution conditions.
#     
#        This program is free software; you can redistribute it and/or modify
#        it under the terms of the GNU General Public License as published by
#        the Free Software Foundation; version 2 of the License.
#     
#        This program is distributed in the hope that it will be useful,
#        but WITHOUT ANY WARRANTY; without even the implied warranty of
#        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#        GNU General Public License for more details.
#     
#    Comments        : 
#     
#     
#     
#     
#     
#    Contact: <stephpen@gmail.com>
#




from Polygon import *
import os.path
import sys



path = '/home/spenot/gshhs/'
path_BD = path + 'bd/'


if len(sys.argv) < 2 :
    print "Use pygshhs1.py [c, l, i, h, f] !"
    print "By default it's resolution c"
    level = 'c'
else :
    level = sys.argv[1]

path_BD_f = path_BD + 'bd_' + level + '/'
if os.path.isdir(path_BD_f) == 0 :
    os.mkdir(path_BD_f)




# Découpe des polygones de toutes les résolutions en carré de 45° de coté
# Création de l'arborescence correspondante
print "Passe 1"
i = 1
while i <= 5 :
    path_poly_start = path_BD + level + str(i) + '.dat'
    poly_start = Polygon(path_poly_start)
    
    # Départ en négatif pour inclure le polygone Eurasiafrica qui deborde en négatif
    x = -45
    pas1 = 45

    while x < 360 :
        y = -90
        while y < 90 :
            xc = x * 1000000
            yc = y * 1000000
            pas1c = pas1 * 1000000
            
            if x < 0 :
                path_poly_dir = path_BD_f + str(y) + '_to_' + str(y + pas1)
            else :
                path_poly_dir = path_BD_f + str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)
                
            if os.path.isdir(path_poly_dir) == 0 :
                os.mkdir(path_poly_dir)
            
            path_poly_finish = path_poly_dir+ '/' + level + str(i) + '.dat'
            print path_poly_finish
            
            poly_clip = Polygon(((xc, yc), (xc + pas1c, yc), (xc + pas1c, yc +pas1c), (xc, yc +pas1c)))
            poly_finish = poly_start & poly_clip

            if x < 0 :
                # Translation des polygones negatifs
                poly_finish.shift(360000000, 0)
        
            poly_finish.write(path_poly_finish)

            y = y + pas1
        
        x = x + pas1

    i = i + 1

# On vide la memoire
poly_start=0
poly_finish=0
poly_clip=0


# On additionne les polygones 
print "Passe 2"
i = 1
while i <= 5 :
    y=-90
    while y<90 :
        path_poly_o = path_BD_f + str(y) + '_to_' + str(y + pas1) + '/' + level + str(i) + '.dat'
        path_poly_f = path_BD_f + '315' + '_' + str(y) + '_to_' + '360' + '_' + str(y + pas1) + '/' + level + str(i) + '.dat'
        poly_start_o = Polygon(path_poly_o)
        poly_start_f = Polygon(path_poly_f)
        poly_f = poly_start_f + poly_start_o
        poly_f.write(path_poly_f)
        
        y = y + pas1
        
    i = i+1

# On vide la memoire    
poly_start_o = 0
poly_start_f = 0
poly_f = 0

"""
print "Passe 3"
path_BD_f = path_BD + 'bd_' + level
if os.path.isdir(path_BD_f) == 0 :
    os.mkdir(path_BD_f)
  
i=1
while i<4 :
    x=0
    while x<360 :
        y=-90
        while y<90 :
            path_poly_dir = path_BD + str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)
            path_poly_1 = path_poly_dir +'/' + level + str(i) + '.dat'
            poly_start_1 = Polygon(path_poly_1)
            path_poly_2 = path_poly_dir +'/' + level + str(i+1) + '.dat'
            poly_start_2 = Polygon(path_poly_2)
            
            path_poly_f_dir = path_BD_f +'/'+ str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)
            if os.path.isdir(path_poly_f_dir) == 0 :
                os.mkdir(path_poly_f_dir)

            path_poly_f = path_poly_f_dir+ '/' + level + str(i) + '.dat'
            print path_poly_f
            
            poly_f = poly_start_1 - poly_start_2
            poly_f.write(path_poly_f)
            
            y=y+pas1
            
        x=x+pas1
        
    i=i+2
"""
# On vide la memoire 
poly_start_1 = 0
poly_start_2 = 0
poly_f = 0

# On déplace le polygone n°5
i=5
x=0
while x<360 :
    y=-90
    while y<90 :
        path_poly_dir = path_BD_f + str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)
        path_poly_1 = path_poly_dir +'/' + level + str(i) + '.dat'
        poly_start_1 = Polygon(path_poly_1)
            
        path_poly_f_dir = path_BD_f +'/'+ str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)

        path_poly_f = path_poly_f_dir+ '/' + level + str(i) + '.dat'
        print path_poly_f
        poly_start_1.write(path_poly_f)
            
        y=y+pas1
            
    x=x+pas1

# On vide la memoire 
poly_start_1 = 0



