obj=seekgrib.o ibm2flt.o readgrib.o intpower.o cnames.o BDSunpk.o \
  flt2ieee.o wrtieee.o levels.o PDStimes.o missing.o  \
  nceptable_reanal.o nceptable_opn.o ensemble.o \
  ec_ext.o gribtable.o gds_grid.o PDS_date.o ectable_128.o \
  ectable_129.o ectable_130.o ectable_131.o ectable_140.o ectable_150.o \
  ectable_151.o ectable_160.o ectable_170.o ectable_172.o ectable_180.o ectable_190.o nceptab_129.o \
  dwdtable_002.o dwdtable_201.o dwdtable_202.o dwdtable_203.o dwdtable_204.o dwdtable_205.o \
  cptectable_254.o nceptab_128.o nceptab_130.o nceptab_131.o BDS_NValues.o

all:: wgrib

CC=gcc
EXTRADEFINES=
EXTRAOPTIONS=-pipe -fPIC
OPTIMFLAGS=-O3 -g 

libgrib.a: $(obj)
	ar -cr libgrib.a $(obj)

wgrib: $(obj) libgrib.a wgrib_main.o
	$(CC) -o wgrib wgrib_main.o libgrib.a -lm 

.c.o:	$(*).c
	$(CC) -c $(OPTIMFLAGS) $(EXTRAOPTIONS) $(EXTRADEFINES) $*.c

clean:
	rm -f *.o *~ wgrib libgrib.a
