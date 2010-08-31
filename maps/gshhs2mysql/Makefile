CC     = gcc 
AR     = ar
RM     = rm -f
DEPEND = makedepend
MAKE   = make

EXTRA_DEFINES = -DUSE_GSHHS_20 
COPTIMFLAGS   = -O3 
CFLAGS = $(COPTIMFLAGS) $(EXTRA_DEFINES)

all: gshhs2csv

gshhs2csv: gshhs2csv.o
	$(RM) $@
	$(CC) -o $@ $(CFLAGS) gshhs2csv.o

clean: 
	$(RM) *.o gshhs2csv


