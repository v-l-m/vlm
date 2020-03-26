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

    f=open(self.FileName,"w")
    f.write(json.dumps(self.Serialize()))    
    f.close()

  ##
  ## Serialize dataset to JSON
  ##
  def Serialize(self):
    ret={}
    ret["Generated"]=int(time.time())
    ret["Data"]=[]
    
    for item in self.DataSet:
      ret['Data'].append(item.Serialize())
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

  def SetStatValue(self,name,Dte,value):
    if not name in self.Data:
        self.Data[name]={}
        self.Data[name]['Values']=[]
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
    print(Rows)
    print(Data)
    for i in range(Rows):
      Name = Data[i]['Name']
      for Value in Data[i]['Values']:
        self.SetStatValue(Name,Value['date'],Value['value'])

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
    f=open(os.path.expanduser("~/tmp/s1"))
    ret=f.read()
    f.close()
    lines = ret.split("\n")
    MailQLen = 0
    Dte=int(lines[0])
    for line in lines:
        if "Deferred" in line:
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
### MySQL Stats (should be 0) Stat instance class.
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


if 'VLMCACHE' in os.environ:    
  cachepath=os.environ['VLMCACHE']
else:
  cachepath='.'
s=ServerStats(cachepath+"/SrvrStats.json")
s.UpdateData()


