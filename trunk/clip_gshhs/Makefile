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

# Map_G
map_g: map.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)

# Tiles_G
tiles_g: tiles.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)
	
# Map2GML
map2gml: map2.o map_functions.o map_projection.o
	$(CC) -o $@ $^ $(LDFLAGS)

%.o: %.c
	$(CC) -o $@ -c $< $(CFLAGS)



#Cleaner!
clean:
	rm -rf *.o
    
cleanall: clean
	rm -rf $(EXEC)
     

