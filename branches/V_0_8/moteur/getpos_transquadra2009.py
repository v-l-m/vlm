#!/usr/bin/env python
# -*- coding: utf-8 -*-

import GetPositions

class GetPosTransquadra2009(GetPositions.GetPositions):
    #l'utl de récupération
    url = 'http://transquadra.geovoile.com/rankings.asp'
    #l'expression régulière
    reSailDatas = r'<tr>\s*<td>(\d+)</td>\s*<td><i>(\d+)</i><br><b>(.*?)</b><br>(.*?)\s*/?\s*</td>\s*<td>\s*(.*?)\s*<br>(.*?)<br>(.*?)<br>\s*</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*</tr>'
    #le mapping de la regexp
    reSailDatasMap = ['rank:int', 'sailid:int', 'boatname', 'skipper', 'time_pos', 'lat:degminutes', 'lon:degminutes', 'speed:float', 'heading:float', 'dnm:float', 'dtof:float']
    #la course vlm correspondante
    vlmRaceId = '20090124'
    #le nextwp a mettre pour ces bateaux
    vlmNextWp = '1'
    #comment on calcule le loch
    vlmPrevWpLat = 33.05
    vlmPrevWpLon = -16.335
    #inutilisé, comment on calculerait le dnm s'il n'était pas fourni
    vlmNextWpLat = 14.386
    vlmNextWpLon = -60.874
    #la base du calcul de l'id
    vlmBaseId = -400
    vlmDepTime = 1232809200

if __name__ == '__main__':
    gp = GetPosTransquadra2009()
    #on veut du csv
    gp.outPositions()
    #on veut du sql
    #gp.sqlPositions()