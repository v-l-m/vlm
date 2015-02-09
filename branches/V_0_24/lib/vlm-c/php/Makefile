SWIG = swig
CC = gcc 
RM = rm -f
MAKE = make
DEPEND = makedepend
CDEBUGFLAGS =
#CDEBUGFLAGS = -Wall -pedantic -g
#COPTIMFLAGS = -O3  -ftree-vectorize -ffast-math 
#COPTIMFLAGS = -O3  -ftree-vectorize -ffast-math -march=nocona
COPTIMFLAGS = -O3  -ftree-vectorize
#COPTIMFLAGS = -O0
EXTRA_DEFINES = -std=gnu99 -fPIC -fno-stack-protector -pipe
CFLAGS = $(CDEBUGFLAGS) $(COPTIMFLAGS) $(EXTRA_DEFINES) #-DDEBUG
EXTRA_LIBRARIES = -lm
LD_FLAGS_OSX = -bundle -flat_namespace -undefined suppress
LD_FLAGS_LINUX = -shared

ifeq ($(OSTYPE),darwin)
       LD_FLAGS=$(LD_FLAGS_OSX)
else
       LD_FLAGS=$(LD_FLAGS_LINUX)
endif


all:: vlmc.so 

../wgrib/libgrib.a:
	cd ../wgrib && $(MAKE)

../libvlmc.a:
	cd .. && $(MAKE)

depend::
	$(DEPEND) -- $(EXTRA_DEFINES) -- $(SRCS)

vlmc.so: ../libvlmc.a ../wgrib/libgrib.a ../vlmc.i
	$(SWIG) -php5 -outdir . -o vlmc_wrap.c ../vlmc.i
	$(CC) `php-config --includes` -I.. $(CFLAGS) -c vlmc_wrap.c
	ld $(LD_FLAGS) vlmc_wrap.o ../libvlmc.a ../wgrib/libgrib.a -o vlmc.so $(EXTRA_LIBRARIES)

distclean:: 
	make clean

clean::
	$(RM) tst unittest *.CKP *.ln *.BAK *.bak *.o core errs ,* *~ *.a .emacs_* tags TAGS make.log MakeOut *.so *_wrap.c vlmc.php php_vlmc.h

