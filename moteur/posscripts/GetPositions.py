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

import re, sys, VlmHttp, math, time

class GetPositions(VlmHttp.VlmHttp):
    #attributs à surcharger
    
    #url de récupération
    url = None
    #expression régulière pour récupèrer tout ce qui constitue une "voile" (l'ensemble des infos sur un boat)
    reSailDatas = None
    #pour faire le mapping avec un helper
    reSailDatasMap = None
    #identifiant vlm de la course
    vlmRaceId = None
    #prochain wp
    vlmNextWp = 1
    #comment on calcule le loch s'il faut
    vlmPrevWpLat = 0.
    vlmPrevWpLon = 0.
    #comment on calcule le dnm s'il faut
    vlmNextWpLat = 0.
    vlmNextWpLon = 0.
    #base du calcul de l'iduser
    vlmBaseId = 0
    #depart de la course (pour engager l'user virtuel)
    vlmDepTime = 0
    
    def __init__(self, *argv, **kargv):
        super(GetPositions, self).__init__(*argv, **kargv)
        self.sailsData = None
        if self.url == None :
            print "ERREUR : no url"
            sys.exit()

    #Méthodes à surcharger optionnellement

    def sailid(self, dat):
        return self.vlmBaseId-dat['sailid']
    
    def loch(self, dat):
        if not dat.has_key('loch'):
            return self.distanceOrtho(dat['lat'], dat['lon'], self.vlmPrevWpLat, self.vlmPrevWpLon)
        else :
            return dat['loch']
            
    def dnm(self, dat):
        if not dat.has_key('dnm'):
            return self.distanceOrtho(dat['lat'], dat['lon'], self.vlmNextWpLat, self.vlmNextWpLon)
        else :
            return dat['dnm']
    
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
        if self.sailsData != None :
            return self.sailsData
        #Récupérer les données
        data = self.getUrl()
        #Matcher les positions et obtenir une liste d'arguments
        data = self.matchSails(data)
        #Nettoyage html des arguments
        data = map(self.cleanSail, data)
        #Conversion des arguments en dico
        data = map(self.convertSail, data)
        self.sailsData = data
        return data

    #Output
    def outPositions(self):
        data = self.getSails()
        for r in data:
            self.outSail(r)
            
    def outSail(self, r):
        print "%s|%s|%s|%i|%s|%s|%f|%f|%f|%f" % (self.vlmRaceId, self.vlmNextWp, self.vlmDepTime,\
                                              self.sailid(r), r['boatname'], r['skipper'], \
                                              r['lat'], r['lon'], \
                                              self.loch(r), self.dnm(r)
                                              )

    def sqlPositions(self):
        data = self.getSails()
        currenttime=int(time.time())
        #init bdd
        import MySQLdb

        conn = MySQLdb.connect (host = "localhost",
                                user = "vlmtest",
                                passwd = "vlmtest",
                                db = "vlm")
        cursor = conn.cursor ()

        for r in data:
            self.outSail(r)
            a, b, c = self.sqlSail(r, currenttime)
            cursor.execute(a)
            cursor.execute(b)
            cursor.execute(c)
        cursor.close ()
        conn.close()
        
    def sqlSail(self, r, currenttime):
          sqlengage = \
              "replace into users (idusers,password,username,engaged,nextwaypoint,userdeptime,loch) values (%s, 'xxxxxxxx', '%s', %s, %s, %s, %s) ;"\
              % (self.sailid(r), r['boatname'], self.vlmRaceId, self.vlmNextWp, self.vlmDepTime, self.loch(r)) 
          sqlranking = \
              "replace into races_ranking (idraces,idusers,latitude,longitude,loch,nwp,dnm) values (%s, %s, %f, %f, %f, %s, %f) ;"\
              % (self.vlmRaceId, self.sailid(r), r['lat']*1000., r['lon']*1000., self.loch(r), self.vlmNextWp, r['dnm'])
          sqlpositions = \
              "insert into positions values ( %i , %f, %f, %s, %s, '' ) ;"\
              % (currenttime, r['lat']*1000., r['lon']*1000., self.sailid(r), self.vlmRaceId)
          return sqlengage, sqlranking, sqlpositions 

    #toolbox
    def _cleanSail(self, data):
        return map(self._htmlentities, data)

    def _matchSails(self, data, rexp):
        return self._reGetAll(rexp, data)

    def _convertSail(self, listdat, mapping):
        #informations de base attendus
        dico = {'sailid' :0, 'boatname': 'boatname', 'skipper': 'skipper', 'lat':0., 'lon':0.,}
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

    # Fonction pour le calcul de la distance orthodromique en miles
    # Arguments en degres
    # Retourne une distance en miles
    def distanceOrtho(self, lat_bat, long_bat, lat_wp, long_wp) :
        lat_bat=math.radians(float(lat_bat))
        long_bat=math.radians(float(long_bat))
        lat_wp=math.radians(float(lat_wp))
        long_wp=math.radians(float(long_wp))

        dlong = abs(long_bat - long_wp)
        if dlong > math.pi :
            dlong = 2*math.pi - dlong
        x = math.sin(lat_bat) * math.sin(lat_wp) + (math.cos(lat_bat) * math.cos(lat_wp) * math.cos(dlong))
        if ((lat_bat == 0) and (long_bat == 0) and (lat_wp == 0) and (long_wp == 0) or (lat_bat == lat_wp) and (long_bat == long_wp)) :
            return (0)
        else :
            return (10800. / math.pi * math.acos(x))


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
        l = self._reGetOne(r'(\d+)\s*[^\d.,]*\s*([\d,.]+)[\s\']*([N|S|E|W])', dat)
        if l[2] in ['S', 'W']:
            sign = -1.
        else :
            sign = 1.
        return sign*(float(l[0])+self._float(l[1])/60.)

