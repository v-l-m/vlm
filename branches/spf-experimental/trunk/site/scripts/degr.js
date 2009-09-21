function dmsdec1() {
  var degres = 0;
  var minutes = 0;
  var seconds = 0;
  
  car = document.convdeg.txtdeg1.value; 
  car = car.replace(/-/g, ""); 
  document.convdeg.txtdeg1.value = car; 

  degres = parseInt(document.convdeg.txtdeg1.value * 1.0);
  minutes = parseInt(document.convdeg.txtmin1.value * 1.0);
  secondes = parseInt(document.convdeg.txtsec1.value * 1.0);

if (document.convdeg.txtdeg1.value >= 90) {document.convdeg.txtdeg1.value = ''; alert('le nombre doit être inférieur à 90');}
if (document.convdeg.txtmin1.value >= 60) {document.convdeg.txtmin1.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtsec1.value >= 60) {document.convdeg.txtsec1.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtdeg1.value=='') {degres='0'}
if (document.convdeg.txtmin1.value=='') {minutes='0'}
if (document.convdeg.txtsec1.value=='') {secondes='0'}
if (document.convdeg.or1.value=='N') {orientation='1'}
if (document.convdeg.or1.value=='S') {orientation='-1'}
  calcul = degres + (minutes * (1.0 / 60.0)) + (secondes * (1.0 / 3600.0));
  document.convdeg.txtdec1.value = calcul * orientation;
}

function decdms1() {
  var degres = 0;
  var degresTemp = 0.0;
  var minutes = 0;
  var minutesTemp = 0.0;
  var secondes = 0;
  var secondesTemp = 0.0;
  if (document.convdeg.txtdec1.value >= 90) {document.convdeg.txtdec1.value = ''; alert('le nombre doit être inférieur à 90');}
  if (document.convdeg.txtdec1.value <= -90) {document.convdeg.txtdec1.value = ''; alert('le nombre doit être inférieur à 90');}
  if (document.convdeg.txtdec1.value < 0) {degresTemp = parseFloat(document.convdeg.txtdec1.value * -1.0);
  document.convdeg.or1.value='S'; }
  else 
  {degresTemp = parseFloat(document.convdeg.txtdec1.value * 1.0); document.convdeg.or1.value='N';}
  degres     = Math.floor(degresTemp);
  minutesTemp = degresTemp - degres;
  minutesTemp = 60.0 * minutesTemp;
  minutes     = Math.floor(minutesTemp);
  secondesTemp = minutesTemp - minutes;
  secondesTemp = 60.0 * secondesTemp;
  secondes     = Math.round(secondesTemp);
  if (document.convdeg.txtdec1.value=='') {degres='0' ; minutes='0' ; secondes='0'}
  if (document.convdeg.txtdec1.value=='-') {degres='0' ; minutes='0' ; secondes='0'}
  
  document.convdeg.txtdeg1.value = degres; 
  document.convdeg.txtmin1.value = minutes ;
  document.convdeg.txtsec1.value = secondes ;
}

function dmsdec2() {
  var degres = 0;
  var minutes = 0;
  var seconds = 0;
  
  car = document.convdeg.txtdeg2.value; 
  car = car.replace(/-/g, ""); 
  document.convdeg.txtdeg2.value = car; 

  degres = parseInt(document.convdeg.txtdeg2.value * 1.0);
  minutes = parseInt(document.convdeg.txtmin2.value * 1.0);
  secondes = parseInt(document.convdeg.txtsec2.value * 1.0);
  
if (document.convdeg.txtdeg2.value >= 180) {document.convdeg.txtdeg2.value = ''; alert('le nombre doit être inférieur à 180');}
if (document.convdeg.txtmin2.value >= 60) {document.convdeg.txtmin2.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtsec2.value >= 60) {document.convdeg.txtsec2.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtdeg2.value=='') {degres='0'}
if (document.convdeg.txtmin2.value=='') {minutes='0'}
if (document.convdeg.txtsec2.value=='') {secondes='0'}
if (document.convdeg.or2.value=='E') {orientation='1'}
if (document.convdeg.or2.value=='W') {orientation='-1'}
  calcul = degres + (minutes * (1.0 / 60.0)) + (secondes * (1.0 / 3600.0));
  document.convdeg.txtdec2.value = calcul * orientation;
}

function decdms2() {
  var degres = 0;
  var degresTemp = 0.0;
  var minutes = 0;
  var minutesTemp = 0.0;
  var secondes = 0;
  var secondesTemp = 0.0;
  if (document.convdeg.txtdec2.value >= 180) {document.convdeg.txtdec2.value = ''; alert('le nombre doit être inférieur à 180');}
  if (document.convdeg.txtdec2.value <= -180) {document.convdeg.txtdec2.value = ''; alert('le nombre doit être inférieur à 180');}
  if (document.convdeg.txtdec2.value < 0) {degresTemp = parseFloat(document.convdeg.txtdec2.value * -1.0);
  document.convdeg.or2.value='W'; }
  else 
  {degresTemp = parseFloat(document.convdeg.txtdec2.value * 1.0); document.convdeg.or2.value='E';}
  degres     = Math.floor(degresTemp);
  minutesTemp = degresTemp - degres;
  minutesTemp = 60.0 * minutesTemp;
  minutes     = Math.floor(minutesTemp);
  secondesTemp = minutesTemp - minutes;
  secondesTemp = 60.0 * secondesTemp;
  secondes     = Math.round(secondesTemp);
  if (document.convdeg.txtdec2.value=='') {degres='0' ; minutes='0' ; secondes='0'}
  if (document.convdeg.txtdec2.value=='-') {degres='0' ; minutes='0' ; secondes='0'}
  
  document.convdeg.txtdeg2.value = degres; 
  document.convdeg.txtmin2.value = minutes ;
  document.convdeg.txtsec2.value = secondes ;
}

function dmsdec3() {
  var degres = 0;
  var minutes = 0;
  var seconds = 0;
  
  car = document.convdeg.txtdeg3.value; 
  car = car.replace(/-/g, ""); 
  document.convdeg.txtdeg3.value = car; 

  degres = parseInt(document.convdeg.txtdeg3.value * 1.0);
  minutes = parseInt(document.convdeg.txtmin3.value * 1.0);
  secondes = parseInt(document.convdeg.txtsec3.value * 1.0);

if (document.convdeg.txtdeg3.value >= 90) {document.convdeg.txtdeg3.value = ''; alert('le nombre doit être inférieur à 90');}
if (document.convdeg.txtmin3.value >= 60) {document.convdeg.txtmin3.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtsec3.value >= 60) {document.convdeg.txtsec3.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtdeg3.value=='') {degres='0'}
if (document.convdeg.txtmin3.value=='') {minutes='0'}
if (document.convdeg.txtsec3.value=='') {secondes='0'}
if (document.convdeg.or3.value=='N') {orientation='1'}
if (document.convdeg.or3.value=='S') {orientation='-1'}
  calcul = degres + (minutes * (1.0 / 60.0)) + (secondes * (1.0 / 3600.0));
  document.convdeg.txtdec3.value = calcul * orientation;
}

function decdms3() {
  var degres = 0;
  var degresTemp = 0.0;
  var minutes = 0;
  var minutesTemp = 0.0;
  var secondes = 0;
  var secondesTemp = 0.0;
  if (document.convdeg.txtdec3.value >= 90) {document.convdeg.txtdec3.value = ''; alert('le nombre doit être inférieur à 90');}
  if (document.convdeg.txtdec3.value <= -90) {document.convdeg.txtdec3.value = ''; alert('le nombre doit être inférieur à 90');}
  if (document.convdeg.txtdec3.value < 0) {degresTemp = parseFloat(document.convdeg.txtdec3.value * -1.0);
  document.convdeg.or3.value='S'; }
  else 
  {degresTemp = parseFloat(document.convdeg.txtdec3.value * 1.0); document.convdeg.or3.value='N';}
  degres     = Math.floor(degresTemp);
  minutesTemp = degresTemp - degres;
  minutesTemp = 60.0 * minutesTemp;
  minutes     = Math.floor(minutesTemp);
  secondesTemp = minutesTemp - minutes;
  secondesTemp = 60.0 * secondesTemp;
  secondes     = Math.round(secondesTemp);
  if (document.convdeg.txtdec3.value=='') {degres='0' ; minutes='0' ; secondes='0'}
  if (document.convdeg.txtdec3.value=='-') {degres='0' ; minutes='0' ; secondes='0'}
  
  document.convdeg.txtdeg3.value = degres; 
  document.convdeg.txtmin3.value = minutes ;
  document.convdeg.txtsec3.value = secondes ;
}

function dmsdec4() {
  var degres = 0;
  var minutes = 0;
  var seconds = 0;
  
  car = document.convdeg.txtdeg4.value; 
  car = car.replace(/-/g, ""); 
  document.convdeg.txtdeg4.value = car; 

  degres = parseInt(document.convdeg.txtdeg4.value * 1.0);
  minutes = parseInt(document.convdeg.txtmin4.value * 1.0);
  secondes = parseInt(document.convdeg.txtsec4.value * 1.0);

if (document.convdeg.txtdeg4.value >= 180) {document.convdeg.txtdeg4.value = ''; alert('le nombre doit être inférieur à 180');}
if (document.convdeg.txtmin4.value >= 60) {document.convdeg.txtmin4.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtsec4.value >= 60) {document.convdeg.txtsec4.value = ''; alert('le nombre doit être inférieur à 60');}
if (document.convdeg.txtdeg4.value=='') {degres='0'}
if (document.convdeg.txtmin4.value=='') {minutes='0'}
if (document.convdeg.txtsec4.value=='') {secondes='0'}
if (document.convdeg.or4.value=='E') {orientation='1'}
if (document.convdeg.or4.value=='W') {orientation='-1'}
  calcul = degres + (minutes * (1.0 / 60.0)) + (secondes * (1.0 / 3600.0));
  document.convdeg.txtdec4.value = calcul * orientation;
}
function decdms4() {
  var degres = 0;
  var degresTemp = 0.0;
  var minutes = 0;
  var minutesTemp = 0.0;
  var secondes = 0;
  var secondesTemp = 0.0;
  if (document.convdeg.txtdec4.value >= 180) {document.convdeg.txtdec4.value = ''; alert('le nombre doit être inférieur à 180');}
  if (document.convdeg.txtdec4.value <= -180) {document.convdeg.txtdec4.value = ''; alert('le nombre doit être inférieur à 180');}
  if (document.convdeg.txtdec4.value < 0) {degresTemp = parseFloat(document.convdeg.txtdec4.value * -1.0);
  document.convdeg.or4.value='W'; }
  else 
  {degresTemp = parseFloat(document.convdeg.txtdec4.value * 1.0); document.convdeg.or4.value='E';}
  degres     = Math.floor(degresTemp);
  minutesTemp = degresTemp - degres;
  minutesTemp = 60.0 * minutesTemp;
  minutes     = Math.floor(minutesTemp);
  secondesTemp = minutesTemp - minutes;
  secondesTemp = 60.0 * secondesTemp;
  secondes     = Math.round(secondesTemp);
  if (document.convdeg.txtdec4.value=='') {degres='0' ; minutes='0' ; secondes='0'}
  if (document.convdeg.txtdec4.value=='-') {degres='0' ; minutes='0' ; secondes='0'}
  
  document.convdeg.txtdeg4.value = degres; 
  document.convdeg.txtmin4.value = minutes ;
  document.convdeg.txtsec4.value = secondes ;
}

function round(number,X) {
  X = (!X ? 3 : X);
  return Math.round(number*Math.pow(10,X))/Math.pow(10,X);
}

function caldis() {
a=eval(document.convdeg.txtdec1.value);
b=eval(document.convdeg.txtdec2.value); 
c=eval(document.convdeg.txtdec3.value); 
d=eval(document.convdeg.txtdec4.value); 

if (document.convdeg.txtdec1.value=='') {a='0'}
if (document.convdeg.txtdec2.value=='') {b='0'}
if (document.convdeg.txtdec3.value=='') {c='0'}
if (document.convdeg.txtdec4.value=='') {d='0'}

e=(3.1415926538*a/180); 
f=(3.1415926538*b/180); 
g=(3.1415926538*c/180);
h=(3.1415926538*d/180);
i=(Math.cos(e)*Math.cos(g)*Math.cos(f)*Math.cos(h)+Math.cos(e)*Math.sin(f)*Math.cos(g)*Math.sin(h)+Math.sin(e)*Math.sin(g)); 
j=(Math.acos(i));
k=round(6371*j);
l=round(k/1.852)
document.convdeg.km.value = k;
document.convdeg.mn.value = l;
}