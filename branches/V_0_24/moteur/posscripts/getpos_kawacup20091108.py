#!/usr/bin/env python
# -*- coding: utf-8 -*-

import GetPositions

class GetPosKawacup2009(GetPositions.GetPositions):
    #l'url de récupération
    url = 'http://www.jacques-vabre.com/fr/s10_classement/s10p01_class_general.php'
    #l'expression régulière
    reSailDatas = r'<tr.*?cellA">(\d+)</td>.*?bateau=(229|208|207|238|203|240|239|231|241|228|188|187|232|230)">(.*?)<span>(.*?)</span>.*?cellC">(.*?)</td>.*?cellC">(.*?)</td>.*?cellA">(.*?)</td>.*?cellA">(.*?)</td>.*?cellC">(.*?)</td>.*?cellC">(.*?)</td>.*?</tr>'

    #le mapping de la regexp
    reSailDatasMap = ['rank:int', 'sailid:int', 'boatname', 'skipper', 'lat:degminutes', 'lon:degminutes', 'speed:float', 'heading:float', 'dnm:float', 'dtof:float']
    #la course vlm correspondante
    vlmRaceId = '20091108'
    #le nextwp a mettre pour ces bateaux
    vlmNextWp = '1'
    #comment on calcule le loch
    vlmPrevWpLat = 49.34
    vlmPrevWpLon = 0.04
    #inutilisé, comment on calculerait le dnm s'il n'était pas fourni
    vlmNextWpLat = 17.903
    vlmNextWpLon = -76.196
    #la base du calcul de l'id
    vlmBaseId = -500
    vlmDepTime = 1257681600

if __name__ == '__main__':
    gp = GetPosKawacup2009()
    #on veut du csv
    gp.outPositions()
    #on veut du sql
    #gp.sqlPositions() #not fonctionnal
