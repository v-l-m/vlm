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

# auteur : paparazzia@gmail.com

#import os, sys
import re
from time import *
import urllib2

def getUrlData(url):
        page = urllib2.urlopen(url)
        return page.readlines()

def getVoile(id):
    voilesImmoca = {'8' : 'Gitana Eighty', '11' : 'PRB',  '3' : 'Brit Air',
              '4' : 'BT', '6' : 'Foncia', '7' : 'Generali', '15' : 'Safran',
              '12' : 'Roxy', '5' : 'Cervin EnR', '10' : 'Pakea Biskaia', '2' : 'Aviva',
              '1' : 'Akena Veranda', '14' : 'Spirit of Weymouth',}
              
    voilesClass40 = {'21' : 'Fujifilm', '23' : 'Clarke Offshore Racing',
              '25' : 'Groupe Rover', '22' : 'Mistral Loisir','31' : 'Beluga Racer', 
              '24' : 'Groupe Partouche', '26': '40 Degrees', '27' : 'Custo Pol',
              '30' : 'Telecom Italia', '29' : 'Pre vie', '28' : 'Appart City'
              }
    if id in voilesImmoca.keys():
        return voilesImmoca[id], '2008051160'
    if id in voilesClass40.keys():
        return voilesClass40[id], '2008051140'
    return "id", "2008051140"

def racePositions():
        data = getUrlData("http://www.theartemistransat.com/ftp/leaderboard/posmaxseaterre.txt")
        for d in data :
            d = re.compile(r'(\d+\.\d+)[WS]').sub(r'-\1', d)
            d = re.compile(r'(\d+\.\d+)[NE]').sub(r'\1', d)
            d = d.replace('\r\n', '')
            liste = d.split(';')
            if len(liste) <5 :
                continue
            liste[4] = "%i" % long(mktime(strptime(liste[4], "%m/%d/%y %H:%M:%S")))
            liste[5], tmp = liste[5].split('/')
            liste.append(tmp)
            nom, course = getVoile(liste[1]) 
            liste += [nom, course]
            print ";".join(liste)

        return
        
      
if __name__ == '__main__':
    racePositions()

  
