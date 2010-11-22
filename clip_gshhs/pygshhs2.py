#!/usr/bin/env python
# -*- coding: utf-8 -*-

#
#    Filename           : pygshhs2.py
#
#    Created            : 07 January 2009 (23:08:51)
#    Created by         : StephPen - stephpen@gmail.com
#
#    Last Updated       : 23:32 21/11/2010
#    Updated by         : StephPen - stephpen@gmail.com
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
#    Comments           : 
#     
#     
#     
#     
#     
#    Contact: <stephpen@gmail.com>
#


# Découpe des polygones et classement dans l'arborescence
# Depart des carrés de 45° x 45°
# -> carrés de 15° x 15°
# -> carrés de 5° x 5°
# -> carrés de 1° x 1°


from Polygon import *
import os.path
import sys


path = '/home/spenot/gshhs/'
path_BD = path + 'bd/'
if len(sys.argv) < 2 :
    print "Use pygshhs2.py [c, l, i, h, f] !"
    print "By default it's resolution c"
    resol = 'c'
else :
    resol = sys.argv[1]

def clip_gshhs(path_init, x0, y0, x1, y1, pas_fin, resol, level) :
    poly_init_file = path_init + resol + level + '.dat'
    print poly_init_file
    poly_init = Polygon(poly_init_file)

    x = x0
    while x < x1  :
        y = y0
        while y < y1 :
            xc = x * 1000000
            yc = y * 1000000
            pas1c = pas_fin * 1000000
            
            path_fin = path_init + str(x) + '_' + str(y) + '_to_' + str(x + pas_fin) + '_' + str(y + pas_fin) + '/'
                
            if os.path.isdir(path_fin) == 0 :
                os.mkdir(path_fin)
            
            poly_finish_file = path_fin + resol + level + '.dat'
            print poly_finish_file
            
            poly_clip = Polygon(((xc, yc), (xc + pas1c, yc), (xc + pas1c, yc +pas1c), (xc, yc +pas1c)))
            poly_finish = poly_init & poly_clip

            poly_finish.write(poly_finish_file)
                
            if pas_fin == 15 :
                clip_gshhs(path_fin, x, y, x+pas_fin, y+pas_fin, 5, resol, level)



            if pas_fin == 5 :
                clip_gshhs(path_fin, x, y, x+pas_fin, y+pas_fin, 1, resol, level)
                
                

            y = y + pas_fin
        
            
        x = x + pas_fin




pas = 45        
x=0
while x<360 :
    y=-90
    while y<90 :
        path_poly_dir = path_BD + 'bd_' + resol + '/' + str(x) + '_' + str(y) + '_to_' + str(x + pas) + '_' + str(y + pas) + '/'
        
        level_all = ['1', '2', '3', '4', '5']
        for level in level_all :
            clip_gshhs(path_poly_dir, x, y, x+pas, y+pas, 15, resol, level)
            
        y=y+pas
            
    x=x+pas

