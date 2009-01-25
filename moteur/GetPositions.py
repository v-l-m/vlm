#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# Copyright (C) 2008 Free Software Fundation

# auteur : paparazzia@gmail.Com

import re, sys, VlmHttp

class GetPositions(VlmHttp.VlmHttp):
    #attributs à surcharger
    
    #url de récupération
    url = None
    #expression régulière pour récupèrer tout ce qui constitue une "voile" (l'ensemble des infos sur un boat)
    reSailDatas = None
    #pour faire le mapping avec un helpe
    reSailDatasMap = None
    #identifiantvlm de la course
    vlmRaceId = None
    vlmNextWp = None
    
    def __init__(self, *argv, **kargv):
        if kargv.has_key('url'):
            self.url = kargv['url']
        if kargv.has_key('reSailDatas'):
            self.url = kargv['reSailDatas']
        if kargv.has_key('reSailDatasMap'):
            self.url = kargv['reSailDatasMap']
        super(GetPositions, self).__init__(*argv, **kargv)

        if self.url == None :
            print "ERREUR : no url"
            sys.exit()

        self.outPositions()

    #Méthodes à surcharger

    def sailid2vlm(self, sailid):
        return 400-sailid    
    
    def getUrl(self, url = None):
        if url == None :
            url = self.url
        return self._getUrl(url)
        
    def convertSail(self, listdat):
        return self._convertSail(listdat, self.reSailDatasMap)

    def matchSails(self, data):
        return self._matchSails(data, self.reSailDatas)

    def cleanSail(self, data):
        return self._cleanSail(data)
    
    #Ordonnance les étapes du parsing
    def getSails(self):
        #Récupérer les données
        data = self.getUrl()
        #Matcher les positions et obtenir une liste d'arguments
        data = self.matchSails(data)
        #Nettoyage html des arguments
        data = map(self.cleanSail, data)
        #Conversion des arguments en dico
        data = map(self.convertSail, data)
        return data

    #Output
    def outPositions(self):
        data = self.getSails()
        for r in data:
            print "%s|%s|%i|%s|%s|%f|%f" % (self.vlmRaceId, self.vlmNextWp, self.sailid2vlm(r['sailid']), r['boatname'], r['skipper'], r['lat'], r['lon'])


    #toolbox
    def _cleanSail(self, data):
        return map(self._htmlentities, data)

    def _matchSails(self, data, rexp):
        return self._reGetAll(rexp, data)

    def _convertSail(self, listdat, mapping):
        #informations de base attendus
        dico = {'sailid' :0, 'loch' : 0., 'boatname': 'boatname', 'skipper': 'skipper', 'lat':0., 'lon':0., 'dnm':0.}
        ind = 0
        for m in mapping:
            l = m.split(':')
            namevar = l[0]
            if len(l) < 2:
                typevar = 'string'
            else:
                typevar = l[1]
            if typevar == 'int':
                dico[namevar] = self._int(listdat[ind])
            elif typevar == 'float':
                dico[namevar] = self._float(listdat[ind])
            elif typevar == 'degminutes':
                dico[namevar] = self._degminutes(listdat[ind])
            else :
                dico[namevar] = self._cleantype(listdat[ind], r'|')
            ind += 1
        return dico

    #Transtypages

    def _float(self, dat):
        dat = self._cleantype(dat, r'[^\d,.]')
        dat = dat.replace(',', '.')
        return float(dat)

    def _int(self, dat):
        dat = self._cleantype(dat, r'[^\d]')
        return int(dat)    

    def _cleantype(self, dat, rexp):
        return re.compile(rexp).sub('', dat)
 
    def _degminutes(self, dat):
        l = self._reGetOne(r'(\d+)\s*[^\d.,]*\s*([\d,.]+)\s*([N|S|E|W])', dat)
        if l[2] in ['S', 'W']:
            sign = -1.
        else :
            sign = 1.
        return sign*(float(l[0])+self._float(l[1])/60.)

