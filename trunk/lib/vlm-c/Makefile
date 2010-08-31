SWIG   = swig
CC     = gcc 
AR     = ar
RM     = rm -f
DEPEND = makedepend
MAKE   = make


ifeq ($(OSTYPE),cygwin)
	OS_DEFINES = -D__CYGWIN -DOLD_C_COMPILER
else 
	OS_DEFINES = -std=gnu99 -fno-stack-protector
endif

CDEBUGFLAGS     = -Wall -pedantic -g
#COPTIMFLAGS     = -O3  -ftree-vectorize
#COPTIMFLAGS     = -O3  -ftree-vectorize -march=nocona
COPTIMFLAGS     = -O3
#COPTIMFLAGS     = -O0
EXTRA_CFLAGS    = -fPIC -pipe
EXTRA_DEFINES   = -DVLM_BUILD_DATE=\""`date -u +%Y-%m-%d\ T\ %T\ %Z`"\"
EXTRA_INCLUDES  =
VLM_DEFINES     = -DVLM_MINOR_VERSION=11 -DUSE_GSHHS_20 -DSAFE_LINE_CHECK -DSAVE_MEMORY -DOPTIMIZE_GSHHS_READ
#VLM_DEFINES     = -DVLM_COMPAT -DSAFE_LINE_CHECK -DPARANOID_COAST_CHECK
#VLM_DEFINES     = -DGRIB_TIME_OFFSET=2100 -DSAFE_LINE_CHECK

CFLAGS = $(CDEBUGFLAGS) $(COPTIMFLAGS) $(EXTRA_CFLAGS) $(OS_DEFINES) $(EXTRA_DEFINES) $(VLM_DEFINES) $(EXTRA_INCLUDES) #-DDEBUG
EXTRA_LIBRARIES = -lm

LD_FLAGS_OSX    = -bundle -flat_namespace -undefined suppress
LD_FLAGS_LINUX  = -shared

ARFLAGS = -rc

ifeq ($(OSTYPE),darwin)
       LD_FLAGS=$(LD_FLAGS_OSX)
else	
       LD_FLAGS=$(LD_FLAGS_LINUX)
endif

ifeq ($(OSTYPE),cygwin)

endif

SRCS = boat.c tst.c ortho.c loxo.c polar.c vmg.c lines.c gshhs.c winds.c grib.c util.c context.c vlm.c move.c waypoint.c shmem.c useshmem.c

LIBOBJS = boat.o ortho.o loxo.o polar.o vmg.o lines.o gshhs.o winds.o grib.o util.o context.o vlm.o move.o waypoint.o shmem.o useshmem.o

OBJS = $(LIBOBJS) 

 PROGRAM = tst

all:: wgrib/libgrib.a libvlmc.a unittest windserver polarserver unittest-shm

bindings:: php

php:: libvlmc.a
	cd php && $(MAKE)

wgrib/libgrib.a:
	cd wgrib && $(MAKE)

coast: coast.o libvlm.a wgrib/libgrib.a
	$(RM) $@
	$(CC) -o $@ $(CFLAGS) coast.o $(LIBOBJS) wgrib/libgrib.a $(EXTRA_LIBRARIES)

unittest: unittest.o libvlmc.a wgrib/libgrib.a
	$(RM) $@
	$(CC) -o $@ $(CFLAGS) unittest.o libvlmc.a wgrib/libgrib.a $(EXTRA_LIBRARIES)

windserver: windserver.o grib.o shmem.o context.o  wgrib/libgrib.a
	$(RM) $@
	$(CC) -o $@ $(CFLAGS)  windserver.o grib.o shmem.o context.o  wgrib/libgrib.a $(EXTRA_LIBRARIES)

polarserver: polarserver.o polar.o shmem.o context.o useshmem.o winds.o
	$(RM) $@
	$(CC) -o $@ $(CFLAGS)  polarserver.o polar.o shmem.o useshmem.o winds.o context.o $(EXTRA_LIBRARIES)

unittest-shm: unittest-shm.o libvlmc.a
	$(RM) $@
	$(CC) -o $@ $(CFLAGS) unittest-shm.o shmem.o libvlmc.a $(EXTRA_LIBRARIES)

libvlmc.a: $(LIBOBJS)
	$(AR) $(ARFLAGS) $@ $(LIBOBJS)

depend::
	$(DEPEND) -- $(EXTRA_DEFINES) -- $(SRCS)

distclean:: 
	cd wgrib && $(MAKE) clean
	cd php && $(MAKE) clean
	$(MAKE) clean

clean::
	$(RM) coast unittest unittest-shm polarserver windserver *.CKP *.ln *.BAK *.bak *.o core errs ,* *~ *.a .emacs_* tags TAGS make.log MakeOut *.so

# after a make depend, run this:
# mv Makefile Makefile.bak ; cat Makefile.bak | sed -e '/^[a-z]*\.o: / s,/[0-9A-Z_a-z/]*.h,,g' -e '^[a-z]*\.o: *$/ d' > Makefile

# DO NOT DELETE

boat.o:   types.h
boat.o:   defs.h
boat.o:   loxo.h
boat.o: boat.h polar.h
ortho.o:  defs.h types.h 
ortho.o:  winds.h 
loxo.o:  defs.h types.h 
loxo.o:  loxo.h polar.h lines.h winds.h
polar.o:   defs.h types.h
polar.o:   polar.h
vmg.o:  defs.h types.h 
vmg.o:  loxo.h polar.h ortho.h winds.h vmg.h
lines.o:   defs.h
lines.o: types.h ortho.h lines.h
gshhs.o:  defs.h 
gshhs.o:  types.h 
gshhs.o:  dist_gshhs.h 
winds.o:   defs.h
winds.o: types.h winds.h
grib.o:  defs.h types.h 
grib.o:  grib.h wgrib/grib.h wgrib/bms.h wgrib/bds.h wgrib/gds.h wgrib/pds4.h
util.o: defs.h  
util.o:  types.h 
context.o:  defs.h 
context.o:  types.h 
vlm.o: defs.h  
vlm.o:  types.h 
vlm.o:  loxo.h vmg.h ortho.h lines.h polar.h vlm.h winds.h boat.h
move.o: defs.h  
move.o:  types.h 
move.o:  loxo.h vmg.h ortho.h
waypoint.o:   defs.h
waypoint.o: types.h  
waypoint.o: lines.h waypoint.h
shmem.o: defs.h  
shmem.o: types.h   shmem.h
useshmem.o:  defs.h 
useshmem.o:  types.h 
useshmem.o:  shmem.h winds.h 
useshmem.o:  useshmem.h
