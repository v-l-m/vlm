#!/usr/bin/env python
# -*- coding: utf-8 -*-

#
#    Filename           : pygshhs1bis.py
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




from Polygon import *
import os.path
import sys



path = '/home/spenot/gshhs/'
path_BD = path + 'bd/'

if len(sys.argv) < 2 :
    print "Use pygshhs.py [c, l, i, h, f] !"
    print "By default it's resolution c"
    level = 'c'
else :
    level = sys.argv[1]

path_BD_f = path_BD + 'bd_' + level
if os.path.isdir(path_BD_f) == 0 :
    os.mkdir(path_BD_f)

pas1 = 45

i=1
while i<=5 :
    x=0
    while x<360 :
        y=-90
        while y<90 :
            path_poly_dir = path_BD + str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)

            path_poly_1 = path_poly_dir +'/' + level + str(i) + '.dat'
            poly_start_1 = Polygon(path_poly_1)
            
            path_poly_f_dir = path_BD_f +'/'+ str(x) + '_' + str(y) + '_to_' + str(x + pas1) + '_' + str(y + pas1)
            if os.path.isdir(path_poly_f_dir) == 0 :
                os.mkdir(path_poly_f_dir)


            path_poly_f = path_poly_f_dir+ '/' + level + str(i) + '.dat'
            print path_poly_f
            poly_start_1.write(path_poly_f)
            
            y=y+pas1
            
        x=x+pas1
        
    i=i+1

# On vide la memoire 
poly_start_1 = 0



