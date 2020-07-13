class QTree
{
  static get QTREE_MAX_DEPTH()
  {
    return 5;
  }
  constructor(Depth, Bob, Address=null)
  {
    this.GetCellXYFromIdx = function(CellIdx)
    {
      let C = Math.pow(2, QTree.QTREE_MAX_DEPTH-1);
      let x = C;
      let y = C;

      for (let index in CellIdx)
      {
        let C2 = Math.floor(C / 2);
          
        
        switch (CellIdx[index])
        {
          case "0":
            x -= C;
            y -= C;
            break;
          case "1":
            x += C2;
            y -= C;
            break;
          case "2":
            x -= C;
            y += C2;
            break;
          case "3":
            x += C2;
            y += C2;
            break;
        }

        C=C2;
        if (!C)
        {
          break;
        }
      }
      return [Math.floor(x), Math.floor(y)];
    };

    if (!Bob)
    {
      throw "Null Bob not allowed";
    }

    if (Depth > QTree.QTREE_MAX_DEPTH)
    {
      throw "Max Depth Error";
    }

    this.Bob = Bob;
    this.SubTrees = [];
    this.Points = [];

    if (Bob.MaxLon < Bob.MinLon)
    {
      let Tmp=Bob.MaxLon;
      Bob.MaxLon = Bob.MinLon;
      Bob.MinLon = Tmp;
    }
    
    if (Bob.MaxLat < Bob.MinLat)
    {
      let Tmp=Bob.MaxLat;
      Bob.MaxLat = Bob.MinLat;
      Bob.MinLat = Tmp;
    }
    

    let Cx = (Bob.MaxLon + Bob.MinLon) / 2;
    let Cy = (Bob.MaxLat + Bob.MinLat) / 2;

    this.Center = [Cx, Cy];
    this.Span = [(Bob.MaxLon - Bob.MinLon) / 2, (Bob.MaxLat - Bob.MinLat) / 2];

    if (Depth)
    {
      if (typeof Address === "undefined" || Address === null)
      {
        Address="";
      }

      for (let x = 0; x < 2; x++)
      {
        for (let y = 0; y < 2; y++)
        {
          let Bob = {};

          Bob.MinLon = this.Bob.MinLon + x * this.Span[0] / 2;
          Bob.MaxLon = this.Bob.MinLon + (x + 1) * this.Span[0] / 2;
          Bob.MinLat = this.Bob.MinLat + y * this.Span[1] / 2;
          Bob.MaxLat = this.Bob.MinLat + (y + 1) * this.Span[1] / 2;
          this.SubTrees[2 * y + x] = new QTree(Depth - 1, Bob, Address + (2*y+x));
        }
      }
    }
    else
    {
      this.XY=this.GetCellXYFromIdx(Address+"0");
    }
    
    this.AddPoint = function(P)
    {
      if (this.SubTrees[0])
      {
        let x = P.Position.Lon.Value > this.Center[0] ? 1 : 0;
        let y = P.Position.Lat.Value < this.Center[1] ? 1 : 0;
        return ("" + (2 * y + x) + "") + this.SubTrees[2 * y + x].AddPoint(P);
      }
      else
      {
        this.Points.push(P);
        return 0;
      }
    };

    this.GetCellKeyFromPoint = function(P)
    {
      if (this.SubTrees[0])
      {
        let x = P[0] > this.Center[0] ? 1 : 0;
        let y = P[1] < this.Center[1] ? 1 : 0;
        return ("" + (2 * y + x) + "") + this.SubTrees[2 * y + x].GetCellKeyFromPoint(P);
      }
      else
      {
        return 0;
      }
    };

    this.GetClosestCellFromList = function(Cell, CellList)
    {
      let PCel = this.GetCellXYFromIdx(Cell);
      let CurDist = 9999999;
      let RetList = [];
      for (let index in CellList)
      {
        let CellP = this.GetCellXYFromIdx(index);
        let Dist = Math.max(Math.abs(CellP[0] - PCel[0]), Math.abs(CellP[1] - PCel[1]));
        if (Dist < CurDist)
        {
          CurDist = Dist;
          RetList = [index];
        }
        else if (Dist === CurDist)
        {
          RetList.push(index);
        }

      }
      return RetList;
    };

    this.GetPointListInCell = function(Idx)
    {
      let Dir = parseInt(Idx[0], 10);
      let Idx2 = Idx.substring(1);

      if (this.SubTrees[0])
      {
        return this.SubTrees[Dir].GetPointListInCell(Idx2);
      }
      else
      {
        return this.Points;
      }
    };
  }

}