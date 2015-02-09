#!/usr/bin/env python
# -*- coding: utf-8 -*-

import getposlib as gp
import sys, time

#Generate with basedatas
def baseboat(rid):
    vlmboatidfirst = 1330
    matchid = {2 : {'vlmid' : 1330}, 3 : {'vlmid' : 1331}, 4 : {'vlmid' : 1332}, 5 : {'vlmid' : 1333}, 6 : {'vlmid' : 1334}, 7 : {'vlmid' : 1335}, 8 : {'vlmid' : 1336}, 9 : {'vlmid' : 1337}, 10 : {'vlmid' : 1338}, 11 : {'vlmid' : 1339}, 12 : {'vlmid' : 1340}, 14 : {'vlmid' : 1341}, 15 : {'vlmid' : 1342}, 16 : {'vlmid' : 1343}, 17 : {'vlmid' : 1344}, 18 : {'vlmid' : 1345}, 20 : {'vlmid' : 1346}, 21 : {'vlmid' : 1347}, 23 : {'vlmid' : 1348}, 24 : {'vlmid' : 1349}, 25 : {'vlmid' : 1350}, 26 : {'vlmid' : 1351}, 28 : {'vlmid' : 1352}, 29 : {'vlmid' : 1353}, 51 : {'vlmid' : 1354}, 52 : {'vlmid' : 1355}, 55 : {'vlmid' : 1356}, 57 : {'vlmid' : 1357}, 201 : {'vlmid' : 1358}, 203 : {'vlmid' : 1359}, 204 : {'vlmid' : 1360}, 205 : {'vlmid' : 1361}, 206 : {'vlmid' : 1362}, 207 : {'vlmid' : 1363}, 208 : {'vlmid' : 1364}, 209 : {'vlmid' : 1365}, 210 : {'vlmid' : 1366}, 211 : {'vlmid' : 1367}, 213 : {'vlmid' : 1368}, 215 : {'vlmid' : 1369}, 216 : {'vlmid' : 1370}, 217 : {'vlmid' : 1371}, 218 : {'vlmid' : 1372}, 219 : {'vlmid' : 1373}, 220 : {'vlmid' : 1374}, 222 : {'vlmid' : 1375}, 225 : {'vlmid' : 1376}, 226 : {'vlmid' : 1377}, 228 : {'vlmid' : 1378}, 230 : {'vlmid' : 1379}, 233 : {'vlmid' : 1380}, 234 : {'vlmid' : 1381}, 236 : {'vlmid' : 1382}, 237 : {'vlmid' : 1383}, 238 : {'vlmid' : 1384}, 239 : {'vlmid' : 1385}, 240 : {'vlmid' : 1386}, 241 : {'vlmid' : 1387}, 242 : {'vlmid' : 1388}, 243 : {'vlmid' : 1389}, 246 : {'vlmid' : 1390}, 247 : {'vlmid' : 1391}, 250 : {'vlmid' : 1392}, 252 : {'vlmid' : 1393}, 253 : {'vlmid' : 1394}, 254 : {'vlmid' : 1395}, 255 : {'vlmid' : 1396}, 256 : {'vlmid' : 1397}, 257 : {'vlmid' : 1398}, 259 : {'vlmid' : 1399}, 260 : {'vlmid' : 1400}, 262 : {'vlmid' : 1401}, 264 : {'vlmid' : 1402}, 265 : {'vlmid' : 1403}, 266 : {'vlmid' : 1404}, 267 : {'vlmid' : 1405}, 268 : {'vlmid' : 1406}, 269 : {'vlmid' : 1407}, 270 : {'vlmid' : 1408}, 272 : {'vlmid' : 1409}, 273 : {'vlmid' : 1410}, 274 : {'vlmid' : 1411}, 277 : {'vlmid' : 1412}, 281 : {'vlmid' : 1413}, 283 : {'vlmid' : 1414}, 286 : {'vlmid' : 1415}, 287 : {'vlmid' : 1416}, 288 : {'vlmid' : 1417}, 289 : {'vlmid' : 1418}, 291 : {'vlmid' : 1419}, 292 : {'vlmid' : 1420}, 293 : {'vlmid' : 1421}, 294 : {'vlmid' : 1422}, 295 : {'vlmid' : 1423}, 296 : {'vlmid' : 1424}, 298 : {'vlmid' : 1425}, 299 : {'vlmid' : 1426}, 972 : {'vlmid' : 1427}, }

    if rid in matchid.keys():
        return matchid[rid]
    return {'vlmid' : -vlmboatidfirst-int(rid)}
    
vlmidrace = 20120128
vlmusernameprefix = "TSQ2012_"
basefilename = "transquadra%d" % vlmidrace
geotree = gp.GeovoileTree("http://transquadra.geovoile.com/2011/data/reports/leg2.gvz", basefilename)

coordfactor = geotree.factors()
timezero = geotree.timezero()
boats = geotree.boats()

for rid in boats.keys() :
    bb = baseboat(int(rid))
    boats[rid]['vlmid'] = -bb['vlmid']
    boats[rid]['vlmboatname'] = "%03d - %s" % (rid, boats[rid]['name'])
    boats[rid]['vlmusername'] = "%s%03d" % (vlmusernameprefix, rid)

if len(sys.argv) > 1 and sys.argv[1] == 'sqlusers' :
        gp.sqlusers(boats, vlmidrace)
        sys.exit();
if len(sys.argv) > 2 and sys.argv[1] == 'basedatas' :
        gp.basedatas(boats, int(sys.argv[2]))
        sys.exit()

for track in geotree.tracks(".//location"):
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
     rid, t, lat, lon = track
     t += timezero
     if time.time() - t < 48*3600:
         print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, boats[rid]['vlmid'], boats[rid]['name'], lat/coordfactor, lon/coordfactor)

