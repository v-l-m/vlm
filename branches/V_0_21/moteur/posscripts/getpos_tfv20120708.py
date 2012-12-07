#!/usr/bin/env python
# -*- coding: utf-8 -*-

import getposlib as gp

class Tfv2012(gp.DolinkPositions):
    defaults = {
        'idrace' : 20120708,
        'basefilename' : "tfv",
        'timezero' : 0,
        'firstidboat' : 1500,
        'suffix' : ".json",
        'course': 506962,
        'etape' : 509372,
        'url' : "http://www.dolink.fr/ajax/new/"
        }

    def __init__(self, **kvargs):
        super(Tfv2012, self).__init__(**kvargs)
        self.param['url'] = self.param['url'] + str(self.param['course'])

    def _fetch(self, url, **kvargs):
        return super(Tfv2012, self)._fetch(url, course = self.param['course'], etape = self.param['etape'], flotte = 0)


#506983">Dunkerque - Dieppe - Parcours 1
#509372">Dieppe - St Cast - Parcours 1
#509376">St Cast - Roscoff
#509378">Roscoff - Talmont - Parcours 1
#512088">Roscoff - Talmont - Parcours 2
#509380">Roses - Gruissan
#509382">Gruissan - La Seyne sur Mer - Parcours 1

#Instancie la classe
tfv = Tfv2012(etape = 509372) #509372">Dieppe - St Cast - Parcours 1
#Appelle toutes les fonctions fetch, read, boats, tracks
tfv.fill()

#Envoie les tracks
tfv.printTracks()
#tfv.printSqlBoats()

