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

# Copyright (C) 2012 Free Software Fundation

# auteur : paparazzia@gmail.com

import urllib
import zipfile, os
import time
import xml.etree.ElementTree as ElementTree

def vlm_get_tmp():
    """Utilise le VLMTEMP s'il est défini"""
    if os.environ.has_key('VLMTEMP'):
        return os.environ['VLMTEMP']
    else :
        return '.'

def unzip_file_into_dest(src, dest):
    """Décompresse un fichier"""
    zfobj = zipfile.ZipFile(src)
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

def sqlusers(boats, engaged):
    for rid in boats.keys() :
        print "INSERT INTO `users` (idusers, username, boatname, engaged) VALUES (%d, '%s', '%s', %d);" % (boats[rid]['vlmid'], boats[rid]['vlmusername'], boats[rid]['vlmboatname'], engaged)

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
        elif tree.find("./factors") != None:
            return float(self.tree.find("./factors").attrib['coord'])
        else :
            return 1.

    def boats(self, boats = {}):
        for outline in self.tree.findall("./boats/boat"):
            rid = int(outline.attrib['id'])
            boats[rid] = outline.attrib
            try :
                boats[rid]['name'] = outline.find("name").text.encode('utf-8')
            except :
                boats[rid]['name'] = "%d" % rid
        return boats
    
    def timezero(self, offset = 0):
        try:
           return int(self.tree.getroot().attrib['timezero'])+offset
        except :
           return 0

    def tracks(self, path = ".//track"):
        tracks = []
        for outline in self.tree.findall(path):
            l = outline.text.split(';')
            lat, lon, t = 0, 0, 0
            rid = int(outline.attrib['boatid'])
            for i in l:
                tup = i.split(',')
                lat += float(tup[0])
                lon += float(tup[1])
                t += int(tup[2])
                pos = [rid, t, lat, lon]
                tracks.append(pos)
        return tracks
