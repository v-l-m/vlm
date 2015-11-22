#!/usr/bin/env python
# -*- coding' : 'utf-8 -*-

import requests #pour l'injection http
import time
import json

url ="http://spindrift2-julesverne.addviso.org/data/tracking/sdt/leg1/locs.json"
#first = "http://www.whatusea.com/embed/70441830a8f41ba763c26b6e9f28ac81"
#second= "http://www.whatusea.com/positions/get/?assetId=1078&isSharingMode=1"
vlmidrace = 1581
realid = -3900
realname = "IDEC"

# headers = {
    # 'Host' : 'www.whatusea.com',
    # 'User-Agent' : 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0',
    # 'Accept' : 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    # 'Accept-Language' : 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
   # 'Accept-Encoding' : 'gzip, deflate',
    # 'Referer' :'http://www.hermione.com/voyage/localiser-l-hermione/',
    # 'Connection' : 'keep-alive'
# }


s = requests.Session()
#s.headers.update(headers)

# q = s.get(first)
# s.headers.update(
  # {
   # 'Accept' : 'application/json, text/javascript, */*; q=0.01',
# Accept-Encoding: gzip, deflate
   # 'X-Requested-With' : 'XMLHttpRequest',
   # 'Referer' : 'http://www.whatusea.com/embed/70441830a8f41ba763c26b6e9f28ac81'
  # }
  # )
# q = s.get(second)
q=s.get(url)
#print (q.text)
#print type(q)
#print type(q.json)
res = json.loads(q.text)
Positions = res['pollings']['p']

#for key in Positions:
#  value = Positions[key]
#  print("The key and value are ({}) = ({})".format(key, value))


for pos in Positions:
  epoch = pos['t'] * 60
  lat = float(pos['locs'][0]['A'])
  lon = float(pos['locs'][0]['B'])
  heading = float(pos['locs'][0]['D'])
  speed =  float(pos['locs'][0]['I'])
  print("%d|1|%d|%d|%s|IDEC|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
  
#print (Positions)
  
# ts = time.strptime(row['received_datestamp'], "%Y-%m-%d %H:%M:%S")
# epoc = int(time.mktime((ts.tm_year, ts.tm_mon, ts.tm_mday, ts.tm_hour, ts.tm_min, ts.tm_isdst, ts.tm_wday, ts.tm_yday, 0)))
#print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[rid]['vlmid'], boats[rid]['name'].encode('utf8'), lat/coordfactor, lon/coordfactor)
#print(row['received_datestamp'])


