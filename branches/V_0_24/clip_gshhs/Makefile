#
#    Filename           : Makefile
#
#    Created            : 07 January 2009 (23:08:51)
#    Created by         : StephPen - stephpen @at@ gmail . com
#
#    Last Updated       : 23:27 21/11/2010
#    Updated by         : StephPen - stephpen @at@ gmail . com
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
#    Contact: <stephpen @at@ gmail . com>
#


CC = gcc
CFLAGS = -Wall -O3 -std=gnu99
LDFLAGS = -lm -lgd -lpng

EXEC=tiles_g

all: $(EXEC)

# Tiles_Generator
tiles_g: tiles_g.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

#Cleaner!
clean:
	rm -rf *.o
    
cleanall: clean
	rm -rf $(EXEC)
     

