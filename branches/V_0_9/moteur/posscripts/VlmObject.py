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

# Copyright (C) 2007 Free Software Fundation

# auteur : paparazzia@gmail.com

import htmlentitydefs
import re

class VlmObject(object):
    """Objet de base pour certaines classes"""
    
    def __init__(self):
        pass
        
    def _htmlentities(self, dat):
        """Remplace les entitées html par leur équivalent unicode"""
        #FIXME : à défaut de connaitre la méthode idéale
        for ent in htmlentitydefs.entitydefs.keys() :
            dat = dat.replace('&'+ent+';',htmlentitydefs.entitydefs[ent])
        uniord = self._reGetAll('&#(\d+);', dat)
        for o in uniord :
            dat = dat.replace("&#%i;" % int(o), chr(int(o)) )
        return dat

    def _reGetOne(self, rexp, dat):
        """Helper pour récupèrer une info par regexp"""
        res = re.compile(rexp, re.MULTILINE | re.DOTALL).findall(dat)
        if len(res) != 0:
            return res[0]
        else :
            return None 

    def _reGetAll(self, rexp, dat):
        """Helper pur récupèrer une série d'info par regexp"""
        return re.compile(rexp, re.MULTILINE | re.DOTALL).findall(dat)

