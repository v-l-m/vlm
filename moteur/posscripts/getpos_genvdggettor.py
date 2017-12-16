#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
#import xml.etree.ElementTree as ElementTree
import json
#import requests
import getposlib as gp
import sys, time

trackfile = 'pos'
vlmtmp = gp.vlm_get_tmp()

racelist = ['20171104','171803']

for i,raceid in enumerate(racelist):
  gp.geturl("http://testing.v-l-m.org/jvlm/pos"+raceid,trackfile)
  with open(os.path.join(vlmtmp,trackfile+".static.tmp.xml")) as data_file:    
    print data_file.read()
    data_file.close()

