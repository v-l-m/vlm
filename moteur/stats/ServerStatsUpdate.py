import json
from subprocess import call
import time
import datetime
import os
import re
import sys

###
###
### Base class to hanlde stats loading, and updating
###
###

class ServerStats:
  def __init__(self,FileStat):
    self.start=time.time()
    self.FileName = FileStat    
    if (FileStat and os.path.isfile(FileStat)):
      self.DeSerialize()    
      self.CheckNewStatTypes() #if any(isinstance(obj, <type_you_want_to_check>) for obj in list_of_objects):..
    else:
      self.InitData()

  ##
  ## Check for new classes in the code to expand the current json file
  def CheckNewStatTypes(self):
    g = globals().copy()
    CurModule = sys.modules[__name__]
    for item in g:
        if item and str(item)[:5]=='Stat_':
          obj=getattr(CurModule,item)
          if not any(isinstance(o,obj) for o in self.DataSet):
            s=obj()
            self.DataSet.append(s)
      
        #if obj and type(obj) not in ['str','dict'] and 'class' in str(obj)  and issubclass(obj(),StatInstance):
        #  print (item)
      
  ##
  ## Get started if there is no stat file
  ##
  def InitData(self):
    self.DataSet=[]
    m=Stat_MailQStat()
    self.DataSet.append(m)
    d=Stat_VolumeSpace()
    self.DataSet.append(d)
    self.UpdateData()

  ##
  ## Update each stat in the stat data set
  ##
  def UpdateData(self):
    for item in  self.DataSet:
      item.GetNextStat()
      if (not hasattr(self,"C") ):
        self.C=[0,0,0]

      #Day Compression 1day 15' 
      item.Compress(self.C[0],3600*24,15*60)
      #Week Compression 7 days 60'    
      item.Compress(self.C[0],3600*24*7,60*60)
      #Month Compression 21 days 180'
      item.Compress(self.C[0],3600*24*18,3*60*60)
      #3Month Compression 40 days 6h
      item.Compress(self.C[0],3600*24*40,6*60*60)
      
    f=open(self.FileName,"w")
    f.write(json.dumps(self.Serialize()))    
    f.close()

  ##
  ## Serialize dataset to JSON
  ##
  def Serialize(self):
    ret={}
    ret["Generated"]=int(time.time())
    ret['GenerationTime']=(time.time()-self.start)
    ret['Compression']=self.C
    ret["Data"]=[]
    
    for item in self.DataSet:
      ret['Data'].append(item.Serialize())
    ret['GenerationTime']=(time.time()-self.start)
    return ret

  ##
  ## Deserialize JSon file to stats dataset
  ##
  def DeSerialize(self):
    f=open(self.FileName)
    try:
      ret=json.loads(f.read())
    except:
      self.InitData()
      return
    finally:
      f.close()

    CurModule = sys.modules[__name__]
    self.DataSet=[]    
    self.C=[0,0,0]
    if 'Compression' in ret:
      for index in ret['Compression']:
        self.C[index]=ret['Compression'][index]
    
    for index in ret['Data']:
      ClassName = index['TypeName']
      Obj=getattr(CurModule,ClassName)
      
      Stat = Obj()
      Stat.DeSerialize(index['Data'])
      self.DataSet.append(Stat)
        
###
###
### Base Stat instance class. Shoudl be inherited
###
###
class StatInstance:
  
  def __init__(self,data=0):
    if (data):
      self.__dict__=data    
    else:
      self.Data={}

  def GetNextStat(self):
    pass

  def CompressRow(self,Row,StartDate,Period,Interval):
    Index = 0
    EndDate=int(time.time())-Period
    EndDate-=EndDate%Period
    while Index < len(Row['Values']) and Row['Values'][Index]['date']<=StartDate:
      Index+=1
    if Index == len(Row):
      return
    if StartDate == 0:
      StartDate=Row['Values'][Index]['date'] - (Row['Values'][Index]['date']%Period)
    CurBound = StartDate + Interval
    
    CurSum = 0
    CurIndex = Index
    CurCount =0
    CurValue = 0
    while (Index<len(Row['Values']) and Row['Values'][Index]['date'] <= EndDate):
      if (Row['Values'][Index]['date']-Row['Values'][CurIndex]['date']<=Interval-1):
        CurSum+=Row['Values'][Index]['value']
        CurCount+=1
        Row['Values'][CurIndex]['value']=CurSum/CurCount
        if Index != CurIndex:
          del Row['Values'][Index]
        else:
          Row['Values'][CurIndex]['date']-=Row['Values'][CurIndex]['date']%Interval
          Index+=1
      else:
        CurSum=0
        CurCount=0
        #Index+=1
        CurIndex=Index
        CurBound+=Interval
        CurBound -= (CurBound%Interval)
        if (CurBound >=EndDate):
          return EndDate;
    return EndDate
    

  def Compress(self,StartDate,Period,Interval):
    for Row in self.Data:
      self.CompressRow(self.Data[Row],StartDate,Period,Interval)
    
  
  def SetStatValue(self,name,Dte,value,BulkLoad=False):
    if not name in self.Data:
        self.Data[name]={}
        self.Data[name]['Values']=[]
    if BulkLoad:
      NewData=None
    else:
      NewData = self.search(self.Data[name]['Values'],'date',Dte)
    if NewData==None:
      NewData={'date':Dte,'value':value}
      self.Data[name]['Values'].append(NewData)
    else:
      NewData['value']=value
    

  def Serialize(self):
    ret={}
    ret["TypeName"]=self.__class__.__name__
    ret["Data"]=[]
    for k in self.Data:
      objdata={"Name":k,"Values":self.Data[k]['Values']}      
      ret['Data'].append(objdata)
    return ret    

  def DeSerialize(self,Data):
    self.Data={}
    Rows = len(Data)
    for i in range(Rows):
      Name = Data[i]['Name']
      for Value in Data[i]['Values']:
        self.SetStatValue(Name,Value['date'],Value['value'],True)

  def search(self,list, key, value): 
    for item in list: 
      if item[key] == value: 
        return item
    return None
###
###
### MailQ Lentgh (should be 0) Stat instance class.
###
###
class Stat_MailQStat(StatInstance):
  def GetNextStat(self):
    MailPattern="\S+\s+\d+\s(\S+\s+){4}(\S+@\S+)"
    f=open(os.path.expanduser("~/tmp/s1"))
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    MailQLen = 0
    Dte=int(lines[0])
    for line in lines:
      m=re.match(MailPattern,line)
      if m!=None:
        MailQLen+=1
  
    self.SetStatValue("MailQ",Dte,MailQLen)

###
###
### Volume Stats  Stat instance class.
###
###    
class Stat_VolumeSpace(StatInstance):
  def GetNextStat(self):
    VolumeMatchPattern="([\w\/-]+)\s+(\d+(\.\d+)?[\w%]?)\s+(\d+(\.\d+)?\w?)\s+(\d+(\.\d+)?\w?)\s+(\d+(\.\d+)?)\%?\s([\w\/-]+)"
    f=open(os.path.expanduser("~/tmp/s2"))
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    Dte=int(lines[0])
    for line in lines:
      m=re.match(VolumeMatchPattern,line)
      if ( m != None):
        g= m.groups()
        self.SetStatValue(g[9],Dte,int(g[7]))

###
###
### MySQL Stats Stat instance class.
###
###

class Stat_MySQLStats(StatInstance):
  def GetNextStat(self):
    StatFields = ['Max_used_connections','Threads_connected','max_connections']
    f=open(os.path.expanduser("~/tmp/s3"))
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    Dte=int(lines[0])
    for line in lines:
      fields=line.split("\t")
      if ( fields[0] in StatFields):
        self.SetStatValue(fields[0],Dte,int(fields[1]))

###
###
### Engine Stats Stat instance class.
###
###
class Stat_EngineStats(StatInstance):
  def GetNextStat(self):
    f=open(os.path.expanduser("~/tmp/s4"))
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    Dte=int(lines[0])
    for line in lines:
      fields=line.split("\t")
      if ( len(fields)==4 and 'unix' not in line and int(fields[0]) > 1584000000):
        Epoch=int(fields[0])
        NbRaces=int(fields[1])
        NbBoats=int(fields[2])
        RunLength=float(fields[3])
        self.SetStatValue('RaceCount',Epoch,NbRaces)
        self.SetStatValue('BoatCount',Epoch,NbBoats)
        if NbBoats>0:
          self.SetStatValue('EngineBoatSpeed',Epoch,NbBoats/RunLength)
        else:
          self.SetStatValue('EngineBoatSpeed',Epoch,0)

###
###
### Npt Stats Stat instance class.
###
###
class Stat_NTPStats(StatInstance):
  def GetNextStat(self):    
    fname=os.path.expanduser("~/tmp/s5");
    if not os.path.isfile(fname):
      return
    NtpPattern="\*\S*\s+\S+\s+\d+\s+\S+\s+\d+\s+\S*\s+\S*\s+\S*\s+(\S+)"
    f=open(fname)
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    Dte=int(lines[0])
    for line in lines:
      m=re.match(NtpPattern,line)
      if ( m != None):
        g= m.groups()
        self.SetStatValue("NTP Offset (ms)",Dte,float(g[0]))
        return


if 'VLMCACHE' in os.environ:    
  cachepath=os.environ['VLMCACHE']
else:
  cachepath='.'
s=ServerStats(cachepath+"/SrvrStats.json")
s.UpdateData()


