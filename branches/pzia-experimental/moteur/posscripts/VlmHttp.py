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

# auteur : paparazzia
# Source pour la gestion des cookies : http://aspn.activestate.com/ASPN/Cookbook/Python/Recipe/302930

import os, sys
import re


### Librement copié de http://aspn.activestate.com/ASPN/Cookbook/Python/Recipe/302930
cj = None
ClientCookie = None
cookielib = None

try:                                    # Let's see if cookielib is available
    import cookielib            
except ImportError:
    pass
else:
    import urllib2    
    urlopen = urllib2.urlopen
    cj = cookielib.LWPCookieJar()       # This is a subclass of FileCookieJar that has useful load and save methods
    Request = urllib2.Request

if not cookielib:                   # If importing cookielib fails let's try ClientCookie
    try:                                            
        import ClientCookie 
    except ImportError:
        import urllib2
        urlopen = urllib2.urlopen
        Request = urllib2.Request
    else:
        urlopen = ClientCookie.urlopen
        cj = ClientCookie.LWPCookieJar()
        Request = ClientCookie.Request
        
####################################################
# We've now imported the relevant library - whichever library is being used urlopen is bound to the right function for retrieving URLs
# Request is bound to the right function for creating Request objects
# Let's load the cookies, if they exist. 
    
if cj != None:                                  # now we have to install our CookieJar so that it is used as the default CookieProcessor in the default opener handler
    if cookielib:
        opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(cj))
        urllib2.install_opener(opener)
    else:
        opener = ClientCookie.build_opener(ClientCookie.HTTPCookieProcessor(cj))
        ClientCookie.install_opener(opener)
### / Fin de la copie du python cookbook

from VlmObject import VlmObject

class VlmHttp(VlmObject):

    def __init__(self):
        super(VlmHttp, self).__init__()

        #construction du reste de l'objet
        self.httpContext() #proxy & cookies

    def httpContext(self):
        #On fixe le proxy
        self.setProxy()
        #on fixe la fonction pour ouvrir une url
        self._urlopen = urlopen

    def setProxy(self):
        if os.environ.has_key('http_proxy'):
            proxy_url = os.environ['http_proxy']
        else :
            proxy_url = ""
        res = re.search('http://([^:]+):([0-9]+)', proxy_url)
        if res != None:
            phost, pport = res.group(1,2)
            self.proxy = "%s:%s" % (phost, pport)
        else :
            self.proxy = None

    def _makeExtra(self, extra, current = '') :
        #FIXME : utiliser urllib.encode .... (mieux que quote_plus manuel)
        import urllib
        exlist = []
        if current != '' :
            exlist = current.split('&')
        for ex in extra.keys():
            exlist.append( urllib.quote_plus(str(ex))+'='+urllib.quote_plus(str(extra[ex])) )
        return '&'.join(exlist)

    def _getUrl(self, url, extraget = {}, extrapost = {}, debug = 0):
        """Recupere l'url donnée avec les elements extra (qui doit être un dico)"""
        post = self._makeExtra(extrapost)
        get = self._makeExtra(extraget)
        if get != "":
            get = "?"+get
        if debug :
            print url
            print get
        page = self._urlopen(url+get,post)
        data = page.readlines()
        data = ' '.join(data)
        return data

