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

# auteur : paparazzia

#import os, sys
import re
import urllib2

def racePositions():
        page = urllib2.urlopen("http://www.transatag2r.com/fr/s10_classement/s10p01_class_general.php")
        data = page.readlines()
        data = ' '.join(data)
        data = data.split("\r\n")
        data = ' '.join(data)
        regexptr = re.compile(r'(<tr.*?>?\s*<td class="cellA".*?<\/tr>)')
        regexp2 = re.compile(r'<td class="cell.">(.*?)<\/td>')
        regexp3 = re.compile(r'>(.*?)<span')  
        listetr = regexptr.findall(data, re.MULTILINE)
        for it in listetr :
            listelem = regexp2.findall(it)
            listelem = map(lambda x : x.replace('&nbsp;', ' '), listelem )
            elem1 = regexp3.findall(listelem[1])
            listelem[1] = elem1[0]
            print ";".join(listelem)
      
if __name__ == '__main__':
    racePositions()

