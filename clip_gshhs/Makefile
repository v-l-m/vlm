#
#    Filename           : Makefile
#
#    Created            : 07 January 2009 (23:08:51)
#    Created by         : StephPen - stephpen@gmail.com
#
#    Last Updated       : 23:27 21/11/2010
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


CC = gcc
CFLAGS = -Wall -O3 -std=gnu99
LDFLAGS = -lm -lgd -lpng

EXEC=readgshhs read_bd map_g tiles_g readcontour map2gml

all: $(EXEC)

# ReadGSHHS    
readgshhs: readgshhs.o read_gshhs.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Readcontour
readcontour: readcontour.o read_gshhs.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Read_BD
read_bd: read_bd.o gpc.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Map_Generator
map_g: map_g.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Tiles_Generator
tiles_g: tiles_g.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Map2GML
map2gml: map2gml.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)



#Cleaner!
clean:
	rm -rf *.o
    
cleanall: clean
	rm -rf $(EXEC)
     

