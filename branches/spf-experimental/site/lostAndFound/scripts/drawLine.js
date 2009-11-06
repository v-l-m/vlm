//Fonction permettant de tracer une ligne en JS
// Cette fonction cr�er autant de bloc div flottant que n�cessaire et la place de mani�re r�guli�re le long du segment � tracer.
// Arguments:
// - x1,y2 coordonn�s du d�but du segment
// - x2,y2 coordonn�s de la fin du segment
// - color couleur du trac�
// - espacementPointill�, entier d�crivant en pixel l'espacement entres les points du trac�
// - divId, id d'un �lement HTML comme un div par exemple. Cet objet servira de container pour le code HTML des points.
// Je conseille d'utiliser un div flottant plac� en 0,0 de dimension 0,0 en position:absolute
function drawLine(x1,y1,x2,y2,color,espacementPointille,divId)
{
if(espacementPointille<1) { espacementPointille=1; }

//on calcule la longueur du segment
var lg=Math.sqrt((x1-x2)*(x1-x2)+(y1-y2)*(y1-y2));

//on determine maintenant le nombre de points necessaires
var nbPointCentraux=Math.ceil(lg/espacementPointille)-1;

//stepX, stepY (distance entre deux points de pointill�s);
var stepX=(x2-x1)/(nbPointCentraux+0);
var stepY=(y2-y1)/(nbPointCentraux+0);

//on recreer un point apres l'autre
var strNewPoints='';
for(var i=1 ; i<nbPointCentraux ; i++)
{
strNewPoints+='<div style="font-size:1px; width:1px; heigth:1px; background-color:'+color+'; position:absolute; top:'+Math.round(y1+i*stepY)+'px; left:'+Math.round(x1+i*stepX)+'px; ">&nbsp;</div>';
}

//pointe de depart
strNewPoints+='<div style="font-size:1px; width:3px; heigth:3px; background-color:'+color+'; position:absolute; top:'+(y1-1)+'px; left:'+(x1-1)+'px; ">&nbsp;</div>';
//point d'arrive
strNewPoints+='<div style="font-size:1px; width:3px; heigth:3px; background-color:'+color+'; position:absolute; top:'+(y2-1)+'px; left:'+(x2-1)+'px; ">&nbsp;</div>';


//on suprimme tous les points actuels et on mets les nouveaux div en place
//obj container des points
var myContainer=document.getElementById(divId);
myContainer.innerHTML=strNewPoints;
} 
