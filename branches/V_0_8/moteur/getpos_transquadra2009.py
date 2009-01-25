#!/usr/bin/env python
# -*- coding: utf-8 -*-

import GetPositions

class GetPosTransquadra2009(GetPositions.GetPositions):
    url = 'http://transquadra.geovoile.com/rankings.asp'
    reSailDatas = r'<tr>\s*<td>(\d+)</td>\s*<td><i>(\d+)</i><br><b>(.*?)</b><br>(.*?)\s*/?\s*</td>\s*<td>\s*(.*?)\s*<br>(.*?)<br>(.*?)<br>\s*</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*</tr>'
    reSailDatasMap = ['rank:int', 'sailid:int', 'boatname', 'skipper', 'time_pos', 'lat:degminutes', 'lon:degminutes', 'speed:float', 'heading:float', 'dnm:float', 'dtof:float']
    vlmRaceId = '20090124'
    vlmNextWp = '1'


if __name__ == '__main__':
    gp = GetPosTransquadra2009()
