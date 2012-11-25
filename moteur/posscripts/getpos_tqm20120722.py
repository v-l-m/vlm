#!/usr/bin/env python
# -*- coding: utf-8 -*-

import getposlib as gp

# wget "http://transat.korem.com/data?mobile=true&action=full&antiCaching=1341780268414&callback=korem.initialDataLoaded"



class Kor2012(gp.AddvisoPositions):
    def _rid2vlmid(self, rid):
        vlmboatidfirst = self.param['firstidboat']
        sptoid = {
          'rfw' : 1,
          'foncia' : 2,
          'rothschild' : 3,
          'spindrift' : 4,
          'oman' : 5
          }
        return vlmboatidfirst+sptoid[rid]
        
    def _timezero(self, offset = 0):
        #Datafile use fr locale ! Stupid !
        import locale
        locale.setlocale(locale.LC_ALL, 'fr_FR.UTF-8')
        return super(Kor2012, self)._timezero(offset)


kor = Kor2012(idrace = 20120707, basename = "kor2012", url = "http://krysoceanrace.addviso.com/data/positions.xml", firstidboat = 1469)
kor.fill()

#Envoie les tracks
kor.printTracks()
#kor.printSqlBoats()
