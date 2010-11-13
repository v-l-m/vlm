#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# Copyright (C) 2007 Free Software Fundation

# auteur : Stephpen


# D�coupe des polygones et classement dans l'arborescence
# Depart des carr�s de 45� x 45�
# -> carr�s de 15� x 15�
# -> carr�s de 5� x 5�
# -> carr�s de 1� x 1�


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

