#!/usr/bin/env python
# -*- coding: utf-8 -*-

# auteur : paparazzia@gmail.com

import urllib
import zipfile, os
import time
import xml.etree.ElementTree as ElementTree
import sys
import VlmHttp
import json

def vlm_get_tmp():
    """Utilise le VLMTEMP s'il est défini"""
    if os.environ.has_key('VLMTEMP'):
        return os.environ['VLMTEMP']
    else :
        return '.'

def unzip_file_into_dest(src, dest):
    """Décompresse un fichier"""
    try :
        zfobj = zipfile.ZipFile(src)
    except :
        print "ERROR when unzipping ressource %s" % src
        sys.exit()

    for name in zfobj.namelist():
        outfile = open(dest, 'wb')
        outfile.write(zfobj.read(name))
        outfile.close()

def unzipurl(url, basefilename, suffix = 'static'):
    """Récupère une url, décompresse et renvoie le nom temporaire"""
    vlmtmp = vlm_get_tmp()
    statichwz = os.path.join(vlmtmp, basefilename+"."+suffix+".tmp.zip")
    staticxml = os.path.join(vlmtmp, basefilename+"."+suffix+".tmp.xml")

    urllib.urlretrieve(url, statichwz)
    unzip_file_into_dest(statichwz, staticxml)
    return staticxml

def geturl(url, basefilename, suffix = 'static'):
    """Récupère une url et renvoie le nom temporaire"""
    vlmtmp = vlm_get_tmp()
    tmpxml = os.path.join(vlmtmp, basefilename+"."+suffix+".tmp.xml")

    urllib.urlretrieve(url, tmpxml)
    return tmpxml


def sqlusers(boats, engaged):
    for rid in boats.keys() :
        print "INSERT INTO `users` (idusers, username, boatname, engaged) VALUES (%d, \"%s\", \"%s\", %d);" % (-boats[rid]['vlmid'], boats[rid]['vlmusername'], boats[rid]['vlmboatname'], engaged)

def basedatas(boats, firstid):
    text = ""
    c = 0
    for rid in boats.keys() :
        text += "%d : {'vlmid' : %d}, " % (int(rid), firstid+c)
        c += 1

    print "def baseboat(rid):"
    print "    vlmboatidfirst = %d" % firstid
    print "    matchid = {%s}" % text
    print """
    if rid in matchid.keys():
        return matchid[rid]
    return {'vlmid' : -vlmboatidfirst-int(rid)}
    """

class BasePositions(VlmHttp.VlmHttp):
    """Class abstraite pour (tenter de) définir une API générique pour les positions"""
    # On veut pouvoir faire les étapes suivantes :
    #- Initialiser un objet
    #- (fetch) Récupérer le contenu d'une url et le mettre dans un fichier temporaire predictible
    #- (read) Lire le fichier et le parser dans un format exploitable
    #- (boats) Créer un index de bateaux avec le plus d'infos possibles
    #- (tracks) Créer une liste de traces
    defaults = {
        'idrace' :0,
        'basename' : "base",
        'timezero' : 0,
        'firstidboat' : 0,
        'suffix' : ".xml",
        'url' : ""
        }
            
    def __init__(self, **kvargs):
        super(BasePositions, self).__init__()
        self.param = {}
        self.param.update(self.defaults)
        self.param.update(kvargs)
        if self.param['idrace'] != 0:
            self.baseFileName = "%s%d" % (self.param['basename'], self.param['idrace'])
        else :
            self.baseFileName = self.param['basename']
        self.boats = {}
        self.positions = []

    #Helpers
        
    def vlmTmp(self):
        """Utilise le VLMTEMP s'il est défini"""
        if os.environ.has_key('VLMTEMP'):
            return os.environ['VLMTEMP']
        else :
            return '.'        

    def csv(self, fname, delimiter, quotechar):
        """Récupère un fichier et charge le csv"""
        import csv
        return csv.reader(open(fname, 'rb'), delimiter=delimiter, quotechar=quotechar)

    def geturl(self, url, basefilename, suffix = '.xml'):
        """Récupère une url dans un fichier ad-hoc"""
        tmpfile = self.tmpFileName(basefilename, suffix)
        urllib.urlretrieve(url, tmpfile)
        return tmpfile

    def tmpFileName(self, basefilename, suffix):
        vlmtmp = self.vlmTmp()
        tmpfile = os.path.join(vlmtmp, basefilename+suffix)
        self.lastFileName = tmpfile
        return tmpfile

    def strptime(self, strtime):
        """Convert string time to epoch"""
        pass


    #API, keep like this
        
    def fetch(self, url = None, **kvargs):
        """Récupère une url et renvoie le nom temporaire"""
        if url == None:
            url = self.param['url']
        self.lastFileName = self._fetch(url, basefilename = self.baseFileName, **kvargs)
        return self.lastFileName
        
    def read(self, fname = None):
        """Read datas from file and return object"""
        if fname == None:
            fname = self.lastFileName
        self.datas = self._read(fname)
        return self.datas
        
    def tracks(self, datas = None):
        self.positions = []
        if datas == None :
            datas = self.datas
        return self._tracks(datas)

    def opponents(self, datas = None):
        self.boats = {}
        if datas == None :
            datas = self.datas
        return self._opponents(datas)
        
    def fill(self):
        self.fetch()
        self.read()
        self.opponents()
        self.tracks()
        
    def printTracks(self, tinf = None, tsup = None):
        #tracks : time, lat, lon, rid
        if tinf == None :
            tinf = time.time() - 48*3600
            tsup = time.time()
        for p in self.positions:
            t = p['time']
            if t > tinf and t < tsup:
                #FIXME : output should be simpler
                bid = p['rid']
                print("%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (
                    self.param['idrace'], p['time'], -self.boats[bid]['vlmid'], self.boats[bid]['vlmboatname'].encode('utf8'), float(p['lat']), float(p['lon'])
                    ) )

    def printSqlBoats(self, tinf = None, tsup = None):
        boats = self.boats
        for bid in boats:
            txt = "INSERT INTO users SET idusers = %d, username = '%s', boatname = '%s', engaged = %d" % (
                -boats[bid]['vlmid'],
                boats[bid]['vlmusername'],
                boats[bid]['vlmboatname'].replace("'", " "),
                self.param['idrace']
                )
            if boats[bid].has_key('color'):
                txt += ", color = '%s'" % boats[bid]['color']
            txt += " ;"
            print(txt)

    def _timezero(self, offset = 0):
        return offset


    #sub API : customize here
    def _fetch(self, url, **kvargs):
        self.lastFileName = self.geturl(url, **kvargs)
        return self.lastFileName
    
    def _read(self, fname):
        return self.datas
      
    def _tracks(self, datas):
        return self.positions
        
    def _opponents(self, datas):
        return self.boats
        
    def _rid2vlmid(self, rid):
        return int(rid)

class DolinkPositions(BasePositions):
    defaults = {
        'idrace' : 0,
        'basename' : "dolink",
        'timezero' : 0,
        'firstidboat' : 0,
        'suffix' : ".json",
        'url' : "http://www.dolink.fr/ajax/new/"
        }
    def __init__(self, **kvargs):
        super(DolinkPositions, self).__init__(**kvargs)
    
    def _fetch(self, url, **post):
        """Récupère une url et renvoie le nom temporaire (using POST)"""
        vlmtmp = self.vlmTmp()
        tmpfile = self.tmpFileName(self.baseFileName, self.param['suffix'])
        datas = self._getUrl(url, {}, post)
        outfile = open(tmpfile, 'wb')
        outfile.write(datas)
        outfile.close()
        return tmpfile
        
    def _read(self, fname):
        fp = open(fname, 'rb')
        o = json.load(fp)
        fp.close()
        return o

    def _tracks(self, datas):
        for ranking in datas['classements']:
            ts = int(ranking['created'])
            for rid in ranking['positions']:
                track = ranking['positions'][rid]['positions'][0]
                track['time'] = ts
                track['hdg'] = float(track['cap'])
                track['rid'] = int(rid)
                self.positions.append(track)
        return self.positions
        
    def _opponents(self, datas):
        for m in datas['markers'] :
            if m.has_key("mobile"):
                boat = m['mobile']
                if boat['nid'] == None :
                    continue
                boat['rid'] = int(boat['nid'])
                boat['color'] = boat['color'].replace('#', '')
                boat['rank'] = int(m['classement']['rang'])
                boat['sailid'] = m['voile'].replace(' ', '')
                boat['vlmid'] = self._rid2vlmid(boat['rid'])
                boat['vlmboatname'] = "%s - %s" % (boat['sailid'], boat['title'])
                boat['vlmusername'] = "%s%03d" % (self.param['basename'].upper(), boat['vlmid'] - self.param['firstidboat']+1)

                self.boats[boat['rid']] = boat
        return self.boats

    def _rid2vlmid(self, rid):
        return rid - 505465

class CsvPositions(object):
    def __init__(self, idrace, url, basefilename, delimiter=';', quotechar='"'):
        super(CsvPositions, self).__init__()
        self.csv = self.url2csv(url, basefilename, delimiter, quotechar)

    def url2csv(self, url, basefilename, delimiter, quotechar):
        """Récupère une url et charge le csv"""
        import csv
        vlmtmp = vlm_get_tmp()
        pathcsv = os.path.join(vlmtmp, basefilename+".tmp.csv")
        urllib.urlretrieve(url, pathcsv)

        return csv.reader(open(pathcsv, 'rb'), delimiter=delimiter, quotechar=quotechar)

    def strptime(self, strtime):
        """Convert string time to epoch"""
        from calendar import timegm
        return int(timegm(time.strptime(strtime, "%Y/%m/%d %H:%M:%S")))


class GeovoileTree(object):
    def __init__(self, url, basefilename, suffix = 'static'):
        super(GeovoileTree, self).__init__()
        self.tree = self.url2xml(url, basefilename, suffix)
    
    def url2xml(self, url, basefilename, suffix = 'static'):
        """Récupère une url compressée et charge le treexml"""
        fn = unzipurl(url, basefilename, suffix)
        return ElementTree.parse(fn)
    
    def factors(self):
        """Récupère le facteur de conversion des coordonnées"""
        #<factors coord="1000" speed="10" distance="10" timecode="1" coef="1000"/>
        if self.tree.find("./factor") != None:
            return float(self.tree.find("./factor").attrib['coord'])
        elif self.tree.find("./factors") != None:
            return float(self.tree.find("./factors").attrib['coord'])
        else :
            return 1.

    def boats(self, boats = {}):
        for outline in self.tree.findall("./boats/boat"):
            rid = int(outline.attrib['id'])
            boats[rid] = outline.attrib
            if not boats[rid].has_key('sail') :
                boats[rid]['sail'] = rid
	    elif boats[rid]['sail'] == "" :
		 boats[rid]['sail'] = rid

            boats[rid]['sail'] = int(boats[rid]['sail'])
            if not boats[rid].has_key('name') :
                try :
                    boats[rid]['name'] = outline.find("name").text.replace('\'','').encode('utf-8')
                except :
                    boats[rid]['name'] = "%s" % boats[rid]['sail']
        return boats

    def strptime(self, strtime):
        """Convert geovoile string time to epoch"""
        from calendar import timegm
        return int(timegm(time.strptime(strtime, "%Y/%m/%d %H:%M:%SZ")))
    
    def timezero(self, offset = 0):
        """Try to compute timezero (for tracks)"""
        timezero = 0
        try:
            if self.tree.getroot().attrib.has_key('timezero'):
                timezero = int(self.tree.getroot().attrib['timezero'])+offset
            if self.tree.getroot().attrib.has_key('date'):
                timezero = self.strptime(self.tree.getroot().attrib['date'])+offset
        except :
           print "oops"
        return offset+timezero

    def tracks(self, path = ".//track", tagid = 'boatid', liveData = 1):
        tracks = []
        for outline in self.tree.findall(path):
            l = outline.text.split(';')
            lat, lon, t = 0., 0., 0
            rid = int(outline.attrib[tagid])
            for i in l:
                if liveData == 1:
                  tup = i.split(',')
                  lat = float(tup[0])
                  lon = float(tup[1])
                  t = int(tup[2])
                  pos = [rid, t, lat, lon]
                  tracks.append(pos)
                else:
                  tup = i.split(',')
                  lat += float(tup[0])
                  lon += float(tup[1])
                  t += int(tup[2])
                  pos = [rid, t, lat, lon]
                  tracks.append(pos)
        return tracks

    def tracks_live(self, path = ".//track", tagid = 'boatid'):
        tracks_live = []
        for outline in self.tree.findall(path):
            l = outline.text.split(';')
            lat, lon, t = 0., 0., 0
            rid = int(outline.attrib[tagid])
            for i in l:
                tup = i.split(',')
                lat = float(tup[0])
                lon = float(tup[1])
                t = int(tup[2])
                pos = [rid, t, lat, lon]
                tracks_live.append(pos)
        return tracks_live
        
class AddvisoPositions(BasePositions):
    defaults = {
        'idrace' : 0,
        'basename' : "addviso",
        'timezero' : 0,
        'firstidboat' : 0,
        'suffix' : ".xml",
        'url' : None
        }
    def __init__(self, **kvargs):
        super(AddvisoPositions, self).__init__(**kvargs)
            
    def _read(self, fname):
        return ElementTree.parse(fname)
        
    def _opponents(self, datas):
        for outline in datas.findall("./pollings/sk"):
            rid = outline.attrib['sp']
            b = outline.attrib
            b['boatname'] = b['bat']
            b['rid'] = rid
            b['vlmid'] = self._rid2vlmid(rid)
            b['vlmboatname'] = "%s - %s" % (rid[0:3].upper(), b['boatname'])
            b['vlmusername'] = "%s%s" % (self.param['basename'].upper(), rid[0:3].upper())
            self.boats[rid] = b
        return self.boats

    def strptime(self, strtime):
        """Convert string time to epoch"""
        ts = time.strptime(strtime, "%A %d %b %H:%M")
        return int(time.mktime((2012, ts.tm_mon, ts.tm_mday, ts.tm_hour, ts.tm_min, ts.tm_isdst, ts.tm_wday, ts.tm_yday, 0)))
    
    def _timezero(self, offset = 0):
        """Try to compute timezero (for tracks)"""
        p = self.datas.findall("./pollings")
        return offset + self.strptime(p[0].attrib['dt'])

    def _tracks(self, datas):
        ts = self._timezero()
        for bid in self.boats:
            b = self.boats[bid]
            tr = {}
            tr['time'] = ts
            tr['lat'] = b['lt']
            tr['lon'] = b['ln']
            tr['rid'] = bid
            self.positions.append(tr)
        return self.positions

class YellowBrickTree(object):
    def __init__(self, url, basefilename, suffix = 'static'):
        super(YellowBrickTree, self).__init__()
        self.tree = self.url2xml(url, basefilename, suffix)
    
    def url2xml(self, url, basefilename, suffix = 'static'):
        """Récupère une url et charge le treexml"""
        fn = geturl(url, basefilename, suffix)
        return ElementTree.parse(fn)

    def boats(self, boats = {}):
        for outline in self.tree.findall("./teams/team"):
            rid = int(outline.attrib['id'])
            boats[rid] = outline.attrib
            boats[rid]['rid'] = rid
            sk = outline.find('skipper')
            if sk != None :
                boats[rid]['skipper'] = sk.attrib
            else :
                boats[rid]['skipper'] = {'name' : outline.attrib['owner']}
            image = outline.find('img')
            if image != None :
                boats[rid]['image'] = image.attrib
            else :
                boats[rid]['image'] = {'url' : ''}
        return boats
    
    def tracks(self):
        tracks = []
        for outline in self.tree.findall('./team'):
            rid = int(outline.attrib['id'])
            for p in outline.findall('./pos'):
                d = p.attrib
                pos = [rid, int(d['w']) , float(d['a']), float(d['o'])]
                #We could have cap and speed
                tracks.append(pos)
        return tracks

