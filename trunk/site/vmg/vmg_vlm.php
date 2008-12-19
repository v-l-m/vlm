<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>VMG pour VLM</title>
<?php 
  /* Convertion des degres décimaux en degres, minutes, secondes sur les deux coordonnées d'un point
     Returns an array :   coord["latdeg"=latdeg,
                                                                "latmin"=latmin,
                                                                "latsec"=latsec,
                                                                "lathem"=lathem,
                                                                "longdeg"=longdeg,
                                                                "longmin"=longmin,
                                                                "longsec"=longsec,
                                                                "longhem"=longhem]
  */
  function degminsec($latitude, $longitude)
  {
         $l=abs($latitude);
         $deg=floor($l);
         $reste=($l - $deg) * 60;
         $min=floor($reste);
         $reste=($reste - $min) * 60;
         $sec=round($reste);
         if ($sec == 60 ) { $min++; $sec=0; };
         if ($min == 60 ) { $deg++; $min=0; };
         if (  $latitude >= 0 )  {
                  $hem='n';
         } else {
                  $hem='s';
         }
         $coord["latdeg"]=$deg;
         $coord["latmin"]=$min;
         $coord["latsec"]=$sec;
         $coord["lathem"]=$hem;

         $l=abs($longitude);
         $deg=floor($l);
         $reste=($l - $deg ) * 60;
         $min=floor($reste);
         $reste=($reste - $min ) * 60;
         $sec=floor($reste);
         if ($sec == 60 ) { $min++; $sec=0; };
         if ($min == 60 ) { $deg++; $min=0; };
         if (  $longitude > 0 )  {
                  $hem='e';
         } else {
                  $hem='w';
         }
         $coord["longdeg"]=$deg;
         $coord["longmin"]=$min;
         $coord["longsec"]=$sec;
     $coord["longhem"]=$hem;
   
         return $coord;
  }
  
  // Convertion des degres, minutes, secondes en degres d?cimaux 
  function degdec($deg,$min,$sec,$hem)
  {
        if ($hem=="n" || $hem=="e") return $deg + ($min + $sec/60)/60;
        if ($hem=="s" || $hem=="w") return -($deg + ($min + $sec/60)/60);
  }
 
 
  /*if (!isset($_REQUEST['boat_lat'])==true)
  {
    $pos_boat["lat_deg"]=$_POST['boat_long_d'];
    echo "passe";
  }
  else
  {
    $pos_boat=giveDegMinSec($_REQUEST['boat_lat'],$_REQUEST['boat_long']);
  }
  */
  if (!isset($_REQUEST['boatlat'])) $_REQUEST['boatlat']=0;
  if (!isset($_REQUEST['boatlong'])) $_REQUEST['boatlong']=0;
  if (!isset($_REQUEST['wp1lat'])) $_REQUEST['wp1lat']=0;
  if (!isset($_REQUEST['wp1long'])) $_REQUEST['wp1long']=0;
  if (!isset($_REQUEST['wp2lat'])) $_REQUEST['wp2lat']=0;
  if (!isset($_REQUEST['wp2long'])) $_REQUEST['wp2long']=0;
  if (!isset($_REQUEST['wdd'])) $_REQUEST['wdd']=0;
  if (!isset($_REQUEST['wds'])) $_REQUEST['wds']=0;
  if (!isset($_REQUEST['boattype'])) $_REQUEST['boattype']="Imoca2008";

 
  $pos_boat=degminsec($_REQUEST['boatlat'],$_REQUEST['boatlong']);
  $pos_way1=degminsec($_REQUEST['wp1lat'],$_REQUEST['wp1long']);
  $pos_way2=degminsec($_REQUEST['wp2lat'],$_REQUEST['wp2long']);
  $wd=$_REQUEST['wdd'];
  $ws=$_REQUEST['wds'];
  
  //echo "\"".$pos_boat["long_deg"];
  //echo $pos_boat["lat_hem"];
  //$way1=array();
  //$way2=array();

        include_once("strings_vmg.inc");
        if(isset($_REQUEST['lang']))
        {
        $lang = ('fr' == $_REQUEST['lang']) ? 'fr' : 'en';
        }
        else
        {
                        $lang = ('fr' == substr($SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) ? 'en' : 'fr';
        }
?>

<script type="text/javascript">
<!--
<?php
$row = 0;

if ($_REQUEST['boattype'] == "")
{
    $path_polar = "http://virtual-loup-de-mer.org/Polaires/boat_Imoca2008.csv";
}
else
{
    $path_polar = "http://virtual-loup-de-mer.org/Polaires/boat_" + $_REQUEST['boattype'] + ".csv";
}
/*
switch ($_REQUEST['boattype'])
{
        case "C5v2":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_C5v2.csv", "r");
                break;

        case "C5":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_C5.csv", "r");
                break;

        case "hi5":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_hi5.csv", "r");
                break;

        case "figaro2":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_figaro2.csv", "r");
                break;
        case "imoca60":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_imoca60.csv", "r");
                break;

        case "maxicata":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_maxicata.csv", "r");
                break;
        case "A35":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_A35.csv", "r");
                break;

        case "Class40":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_Class40.csv", "r");
                break;

        case "Imoca2007":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_Imoca2007.csv", "r");
                break;
                
        case "Imoca2008":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_Imoca2008.csv", "r");
                break;
                
        case "VLM70":
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_VLM70.csv", "r");
                break;
                
        case "OceanExpress";
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_OceanExpress.csv", "r");
                break;

        case "Imoca";
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_Imoca.csv", "r");
                break;
        

        default:
                $handle = fopen("http://virtual-loup-de-mer.org/polaires/boat_Imoca2008.csv", "r");
                break;

}
*/


while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $num = count($data);
    echo "var p$row=new Array();\n";
    for ($c=0; $c < $num; $c++) {
                if ($data[$c]!=="")
                        {
                        if ($row==0 && $c==0)
                        {
                                echo " p$row"."[".$c."]=0;";
                        }
                        else
                        {
                                echo " p$row"."["."$c"."]=$data[$c];";
                        }
                }
                else
                {
                        echo " p$row"."[".$c."]=-1;";
                }
    }
        echo "\n";
        $row++;
}
echo "var pav=new Array();\n";
for ($c=0;$c<$row;$c++) {
        echo " pav[".$c."]=p".$c.";";
        }
echo "\n";
fclose($handle);
?>

var nb_speed=p0.length;
var nb_angle=pav.length;

// Remplissage des valeurs vide dans la polaire, interpolation sur la colonne
function remplivide(pol,nbspeed,nbangle)
{
        var aa=0;
        for (var s=1;s<nbspeed;s++)
        {
                for (var a=1;a<nbangle;a++)
                {
                        if (pol[a][s]==-1)
                        {
                                aa=1;
                                while ( pol[a+aa][s]==-1 && a+aa<nbangle)
                                {
                                        aa=aa+1;
                                        //document.write(" "+aa+" ");
                                }
                                pol[a][s]=((pol[a+aa][s]-pol[a-1][s])/(pol[a+aa][0]-pol[a-1][0]))*pol[a][0]+pol[a-1][s]-((pol[a+aa][s]-pol[a-1][s])/(pol[a+aa][0]-pol[a-1][0]))*pol[a-1][0];
                                
                        }
                        //document.write("<BR>"+"speed="+s+" ");
                        //document.write("angle="+a+" ");
                        //document.write("valeur="+pol[a][s]+"</BR>");
                }
        }
        return pol;
}

pav=remplivide(pav,nb_speed,nb_angle);




// fonction d'interpolation de la vitesse du bateau en fonction de la vitesse du vent sur la polaire du bateau
// boatspeed=f(windangle,windspeed,polaire[])
function interpol(windangle,windspeed,pol,nbspeed,nbangle)
{
        /* recherche des indices inferieur et superieur pour la vitesse*/
        var speed_ind_sup=2;
        while (speed_ind_sup<=nbspeed-2 && windspeed>pol[0][speed_ind_sup])
        {
                speed_ind_sup++;
        }
        var speed_ind_inf=speed_ind_sup-1;

        /* recherche des indices inferieur et superieur pour l'angle*/
        var angle_ind_sup=2;
        while (angle_ind_sup<=nbangle-2 && windangle>pol[angle_ind_sup][0])
        {
                angle_ind_sup++;
        }
        var angle_ind_inf=angle_ind_sup-1;

        /* 
        Interpolation lineaire pour trouver la valeur de la vitesse
        pour windspeed=speed et windangle=angle
        */

        var bse=0;
        var bsf=0;
        var boatspeed=0;
        bse=((pol[angle_ind_inf][speed_ind_sup]-pol[angle_ind_inf][speed_ind_inf])/(pol[0][speed_ind_sup]-pol[0][speed_ind_inf]))*(windspeed-pol[0][speed_ind_inf])+pol[angle_ind_inf][speed_ind_inf];
        bsf=((pol[angle_ind_sup][speed_ind_sup]-pol[angle_ind_sup][speed_ind_inf])/(pol[0][speed_ind_sup]-pol[0][speed_ind_inf]))*(windspeed-pol[0][speed_ind_inf])+pol[angle_ind_sup][speed_ind_inf];
        boatspeed=((bsf-bse)/(pol[angle_ind_sup][0]-pol[angle_ind_inf][0]))*(windangle-pol[angle_ind_inf][0])+bse;

        return boatspeed;
}




// Fonction pour transformer les degres sexagesimaux en degres decimaux
function conversion(hemisphere, degree, minute, seconde)
{
        if ((hemisphere == "n") || (hemisphere == "e"))
        {
                return ((degree + (minute / 60) + (seconde / 3600)) / 180 *Math.PI);
        }
        if (hemisphere == "s" || hemisphere == "w")
        {
                return (-(degree + (minute / 60) + (seconde / 3600)) / 180 *Math.PI);
        }
}

// Fonction pour calculer la longitude avec correction de l'antimeridien ( bateau )
function cor_long_bat(long_bat, long_wp)
{
        if ((long_bat < 0) && (long_wp > 0) && (long_bat - long_wp < Math.PI) && (long_bat - long_wp < -Math.PI))
        {
                return (2 * Math.PI + long_bat);
        }
        else
        {
                return (long_bat);
        }
}

// Fonction pour calculer la longitude avec correction de l'antimeridien ( way point )
function cor_long_wp(long_bat, long_wp)
{
        if ((long_bat > 0) && (long_wp < 0) && (long_bat - long_wp > Math.PI) && (long_bat - long_wp > -Math.PI))
        {
                return (2 * Math.PI + long_wp);
        }
        else
        {
                return (long_wp);
        }
}

// Fonction pour le calcul de la distance orthodromique en milles
function ortho(lat_bat, long_bat, lat_wp, long_wp)
{
        var x=0;
        x = Math.sin(lat_bat) * Math.sin(lat_wp) + (Math.cos(lat_bat) * Math.cos(lat_wp) * Math.cos(long_bat - long_wp));
        if ((lat_bat == 0) && (long_bat == 0) && (lat_wp == 0) && (long_wp == 0) || (lat_bat == lat_wp) && (long_bat == long_wp))
        {
                return (0);
        }
        else
        {
                return (10800 / Math.PI * Math.acos(x));
        }
}

// Fonction pour le calcul du cap orthodromique
function caportho(lat_bat, long_bat, lat_wp, long_wp)
{
        var x=0;
        var y=0;
        var z=0;
        var cap=0;
        if ((lat_bat == lat_wp) && (long_bat == long_wp))
        {
                return (0);
        }
        else
        {
                x = Math.PI / 2 - lat_wp
                y = Math.PI / 2 - lat_bat
                z = Math.acos(Math.sin(lat_bat) * Math.sin(lat_wp) + (Math.cos(lat_bat) * Math.cos(lat_wp) * Math.cos(long_bat - long_wp)))
                if (((Math.cos(x) - (Math.cos(y) * Math.cos(z))) / (Math.sin(y) * Math.sin(z)) > 1) || ((Math.cos(x) - (Math.cos(y) * Math.cos(z))) / (Math.sin(y) * Math.sin(z)) < -1))
                {
                        cap = 0;
                }
                else
                {
                        cap = Math.acos((Math.cos(x) - (Math.cos(y) * Math.cos(z))) / (Math.sin(y) * Math.sin(z))) * 180 / Math.PI;
                }
                if ((lat_bat < lat_wp) && (long_bat == long_wp) || (lat_bat == lat_wp) && (long_bat == long_wp) || (lat_bat == 0) && (long_bat == 0) && (lat_wp == 0) && (long_wp == 0))
                {
                        return (0);
                }
                else
                {
                        if ((lat_bat > lat_wp) && (long_bat == long_wp))
                        {
                                return (180);
                        }
                        else
                        {
                                if (long_bat < long_wp)
                                {
                                        return (cap);
                                }
                                else
                                {
                                        return (360 - cap);
                                }
                        }
                }
        }
}

// Fonction pour le calcul de la distance loxodromique en milles
function loxo(lat_bat, long_bat, lat_wp, long_wp)
{
        var dla=0;
        var dlom=0;
        var angle=0;
        dla = 60 * (lat_bat - lat_wp) * 180 / Math.PI;
        dlom = 60 * Math.cos((lat_bat + lat_wp) / 2) * (long_bat - long_wp) * 180 / Math.PI;
        if (dlom == 0)
        {
                angle = 0;
        }
        else
        {
                angle = Math.abs(Math.atan(dla / dlom) * 180 / Math.PI);
        }
        if (dlom == 0)
        {
                return (Math.abs(dla));
        }
        else
        {
                return (Math.abs(dlom / Math.cos(angle * Math.PI / 180)));
        }
}

// Fonction pour le calcul du cap loxodromique
function caploxo(lat_bat, long_bat, lat_wp, long_wp)
{
        var dla=0;
        var dlom=0;
        var angle=0;
        dla = 60 * (lat_bat - lat_wp) * 180 / Math.PI;
        dlom = 60 * Math.cos((lat_bat + lat_wp) / 2) * (long_bat - long_wp) * 180 / Math.PI;
        if (dlom == 0)
        {
                angle = 0;
        }
        else
        {
                angle = Math.abs(Math.atan(dla / dlom) * 180 / Math.PI);
        }
        if ((lat_bat < lat_wp) && (long_bat > long_wp))
        {
                return (270 + angle);
        }
        else
        {
                if ((lat_bat < lat_wp) && (long_bat < long_wp))
                {
                        return (90 - angle);
                }
                else
                {
                        if ((lat_bat > lat_wp) && (long_bat > long_wp) || (lat_bat == lat_wp) && (long_bat > long_wp))
                        {
                                return (270 - angle);
                        }
                        else
                        {
                                if ((lat_bat > lat_wp) && (long_bat < long_wp) || (lat_bat == lat_wp) && (long_bat < long_wp))
                                {
                                        return (90 + angle);
                                }
                                else
                                {
                                        if ((lat_bat < lat_wp) && (long_bat == long_wp) || (lat_bat == lat_wp) && (long_bat == long_wp))
                                        {
                                                return (0);
                                        }
                                        else
                                        {
                                                if ((lat_bat > lat_wp) && (long_bat == long_wp))
                                                {
                                                        return (180);
                                                }
                                        }
                                }
                        }
                }
        }
}

/* fonction pout verifier les entrees*/
function verif(entree)
{
        var seulement_ceci ="0123456789-+.";
        for (var i = 0; i < entree.length; i++)
        if (seulement_ceci.indexOf(entree.charAt(i))<0 ) return false;
        return true;
}
/* fonction pour verifier si un bouton est cochee*/
function test_radiobutton(radio)
{
        for (var i=0; i<radio.length;i++)
        {
                if (radio[i].checked)
                {
                        return (radio[i].value)
                }
        }
}

/* fonction correction angle pour eviter les caps negatif et superieur a 360*/
function correct_angle(angle)
{
        var angle_corrige=0;
        angle_corrige=angle;
        while(angle_corrige>=360 || angle_corrige<0)
        {
                if (angle_corrige>=360)
                {
                        angle_corrige=angle_corrige-360;
                }
                if (angle_corrige<0)
                {
                        angle_corrige=angle_corrige+360;
                }
        }
        return angle_corrige;
}

function mintohm(tpsmin)  //transforme un temps en minutes en heure+minutes
{
        var heure=0;
        var minute=0;
        
        heure=Math.floor(tpsmin/60);
        minute=Math.round((tpsmin/60-heure)*60);
        if (minute<10)
        {
                return " "+heure+"h"+"0"+minute+"'";
        }
        else
        {
                return " "+heure+"h"+minute+"'";
        }
}

function testdist(distance)
{
  if (distance<=0)
  {
    return 0.001;
  }
  else
  {
    return distance;
  }

}

/*fonction pricipale repond a l'appui sur "Valider"*/
function valider()
{
        var dist_way1_ortho=0;
        var course_way1_ortho=0;
        var dist_way2_ortho=0;
        var course_way2_ortho=0;
        var dist_way1_loxo=0;
        var course_way1_loxo=0;
        var dist_way2_loxo=0;
        var course_way2_loxo=0;
        var conv_boat_lat=0;
        var conv_boat_long=0;
        var conv_way1_lat=0;
        var conv_way1_long=0;
        var conv_way2_lat=0;
        var conv_way2_long=0;
        
        var pv=new Array();
        var winds=0;
        var windd=0;
        var way1_t=new Array();
        var way2_t=new Array();
        var way1_b=new Array();
        var way2_b=new Array();

        var cap1_t=new Array();
        var cap2_t=new Array();
        var cap1_b=new Array();
        var cap2_b=new Array();
        
        var vd1_t=new Array();
        var vd2_t=new Array();
        var vd1_b=new Array();
        var vd2_b=new Array();
        
        var t1_1=0;
        var t1_2=0;
        var t2_1=0;
        var t2_2=0;
        
        var t1_t=0;
        var t2_t=0;
        
        var vm1=0;
        var vm2=0;
        
        var vmo1=0;
        var vmo2=0;
        
        var avo1_t=0;
        var co1_t=0;
        var avo1_b=0;
        var co1_b=0;

        var avo2_t=0;
        var co2_t=0;
        var avo2_b=0;
        var co2_b=0;
        
        var vo1_t=0;
        var vo1_b=0;
        var vo2_t=0;
        var vo2_b=0;
        
        var vmgo1_t=0;
        var vmgo1_b=0;
        var vmgo2_t=0;
        var vmgo2_b=0;
        
        var vmg_way1_t=0;
        var vmg_way1_b=0;
        var vmg_way2_t=0;
        var vmg_way2_b=0;
        var angle_vmg_way1_t=0;
        var angle_vmg_way1_b=0;
        var angle_vmg_way2_t=0;
        var angle_vmg_way2_b=0;
        var cap_t=0;
        var cap_b=0;
        var txt_status_way1="";
        var txt_status_way2="";
        var cap1=0;
        var cap2=0;
        var cap1_calc=0;
        var cap2_calc=0;
        var dist1_calc=0;
        var dist2_calc=0;

        var to1_1=0;
        var to1_2=0;
        var tb1=0;
        var tt1=0;
        
        var to2_1=0;
        var to2_2=0;
        var tb2=0;
        var tt2=0;

window.document.vmg.status_way1.value="Calcul !!!";
window.document.vmg.status_way2.value="Calcul !!!";
        
        if (test_radiobutton(window.document.vmg.choix_1)=="w1")
        {
                /* calcul des distances et cap pour waypoint1*/
                /*Verifie que toutes les entrees sont correctes*/
                if (verif(window.document.vmg.boat_lat_d.value) && verif(window.document.vmg.boat_lat_m.value) && verif(window.document.vmg.boat_lat_s.value) &&
                        verif(window.document.vmg.boat_long_d.value) && verif(window.document.vmg.boat_long_m.value) && verif(window.document.vmg.boat_long_s.value) &&
                        verif(window.document.vmg.way1_lat_d.value) && verif(window.document.vmg.way1_lat_m.value) && verif(window.document.vmg.way1_lat_s.value) &&
                        verif(window.document.vmg.way1_long_d.value) && verif(window.document.vmg.way1_long_m.value) && verif(window.document.vmg.way1_long_s.value))
                {
                        /* concersion des angles*/
                        conv_boat_lat=conversion(test_radiobutton(window.document.vmg.boat_lat),eval(window.document.vmg.boat_lat_d.value),eval(window.document.vmg.boat_lat_m.value),eval(window.document.vmg.boat_lat_s.value));
                        conv_boat_long=conversion(test_radiobutton(window.document.vmg.boat_long),eval(window.document.vmg.boat_long_d.value),eval(window.document.vmg.boat_long_m.value),eval(window.document.vmg.boat_long_s.value));
                        conv_way1_lat=conversion(test_radiobutton(window.document.vmg.way1_lat),eval(window.document.vmg.way1_lat_d.value),eval(window.document.vmg.way1_lat_m.value),eval(window.document.vmg.way1_lat_s.value));
                        conv_way1_long=conversion(test_radiobutton(window.document.vmg.way1_long),eval(window.document.vmg.way1_long_d.value),eval(window.document.vmg.way1_long_m.value),eval(window.document.vmg.way1_long_s.value));
                        /* calcul des distances et cap ortho et loxo*/
                        dist_way1_ortho=ortho(conv_boat_lat,conv_boat_long,conv_way1_lat,conv_way1_long);
                        course_way1_ortho=caportho(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way1_long),conv_way1_lat,cor_long_wp(conv_boat_long,conv_way1_long));
                        dist_way1_loxo=loxo(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way1_long),conv_way1_lat,cor_long_wp(conv_boat_long,conv_way1_long));
                        course_way1_loxo=caploxo(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way1_long),conv_way1_lat,cor_long_wp(conv_boat_long,conv_way1_long));
                        /* affichage des valeurs dans les cases correspondantes*/
                        window.document.vmg.way1_ortho_dist.value =Math.round(dist_way1_ortho*1000)/1000;
                        window.document.vmg.way1_ortho_course.value =Math.round(course_way1_ortho*10)/10;
                        window.document.vmg.way1_loxo_dist.value =Math.round(dist_way1_loxo*1000)/1000;
                        window.document.vmg.way1_loxo_course.value =Math.round(course_way1_loxo*10)/10;
                }
                /* si les entrees sont incorrectes affiche des 0 dans les cases et Erreur dans info*/
                else
                {
                        window.document.vmg.way1_ortho_dist.value =0;
                        window.document.vmg.way1_ortho_course.value =0;
                        window.document.vmg.way1_loxo_dist.value =0;
                        window.document.vmg.way1_loxo_course.value =0;
                        window.document.vmg.status_way2.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        window.document.vmg.status_way1.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        return;
                }
        }
        if (test_radiobutton(window.document.vmg.choix_1)=="c1")
        {
                if (verif(window.document.vmg.cap1_course.value) && verif(window.document.vmg.cap1_distance.value))
                {
                        window.document.vmg.way1_ortho_dist.value =0;
                        window.document.vmg.way1_ortho_course.value =0;
                        window.document.vmg.way1_loxo_dist.value =0;
                        window.document.vmg.way1_loxo_course.value =0;
                        cap1=Math.round(correct_angle(eval(window.document.vmg.cap1_course.value)));
                        window.document.vmg.cap1_course.value =cap1;
                }
                else
                {
                        window.document.vmg.status_way2.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        window.document.vmg.status_way1.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        return;
                }
        }
        
        if (test_radiobutton(window.document.vmg.choix_2)=="w2")
        {
                /* idem pour waypoint2*/
                if (verif(window.document.vmg.boat_lat_d.value) && verif(window.document.vmg.boat_lat_m.value) && verif(window.document.vmg.boat_lat_s.value) &&
                        verif(window.document.vmg.boat_long_d.value) && verif(window.document.vmg.boat_long_m.value) && verif(window.document.vmg.boat_long_s.value) &&
                        verif(window.document.vmg.way2_lat_d.value) && verif(window.document.vmg.way2_lat_m.value) && verif(window.document.vmg.way2_lat_s.value) &&
                        verif(window.document.vmg.way2_long_d.value) && verif(window.document.vmg.way2_long_m.value) && verif(window.document.vmg.way2_long_s.value))
                {
                        conv_boat_lat=conversion(test_radiobutton(window.document.vmg.boat_lat),eval(window.document.vmg.boat_lat_d.value),eval(window.document.vmg.boat_lat_m.value),eval(window.document.vmg.boat_lat_s.value));
                        conv_boat_long=conversion(test_radiobutton(window.document.vmg.boat_long),eval(window.document.vmg.boat_long_d.value),eval(window.document.vmg.boat_long_m.value),eval(window.document.vmg.boat_long_s.value));
                        conv_way2_lat=conversion(test_radiobutton(window.document.vmg.way2_lat),eval(window.document.vmg.way2_lat_d.value),eval(window.document.vmg.way2_lat_m.value),eval(window.document.vmg.way2_lat_s.value));
                        conv_way2_long=conversion(test_radiobutton(window.document.vmg.way2_long),eval(window.document.vmg.way2_long_d.value),eval(window.document.vmg.way2_long_m.value),eval(window.document.vmg.way2_long_s.value));
                        dist_way2_ortho=ortho(conv_boat_lat,conv_boat_long,conv_way2_lat,conv_way2_long);
                        course_way2_ortho=caportho(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way2_long),conv_way2_lat,cor_long_wp(conv_boat_long,conv_way2_long));
                        dist_way2_loxo=loxo(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way2_long),conv_way2_lat,cor_long_wp(conv_boat_long,conv_way2_long));
                        course_way2_loxo=caploxo(conv_boat_lat,cor_long_bat(conv_boat_long,conv_way2_long),conv_way2_lat,cor_long_wp(conv_boat_long,conv_way2_long));
                        window.document.vmg.way2_ortho_dist.value =Math.round(dist_way2_ortho*1000)/1000;
                        window.document.vmg.way2_ortho_course.value =Math.round(course_way2_ortho*10)/10;
                        window.document.vmg.way2_loxo_dist.value =Math.round(dist_way2_loxo*1000)/1000;
                        window.document.vmg.way2_loxo_course.value =Math.round(course_way2_loxo*10)/10;
                }
                else
                {
                        window.document.vmg.way2_ortho_dist.value =0;
                        window.document.vmg.way2_ortho_course.value =0;
                        window.document.vmg.way2_loxo_dist.value =0;
                        window.document.vmg.way2_loxo_course.value =0;
                        window.document.vmg.status_way2.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        window.document.vmg.status_way1.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        return;
                }
        }
        if (test_radiobutton(window.document.vmg.choix_2)=="c2")
        {
                if (verif(window.document.vmg.cap2_course.value) && verif(window.document.vmg.cap2_distance.value))
                {
                        window.document.vmg.way2_ortho_dist.value =0;
                        window.document.vmg.way2_ortho_course.value =0;
                        window.document.vmg.way2_loxo_dist.value =0;
                        window.document.vmg.way2_loxo_course.value =0;
                        cap2=Math.round(correct_angle(eval(window.document.vmg.cap2_course.value)));
                        window.document.vmg.cap2_course.value =cap2;
                }
                else
                {
                        window.document.vmg.status_way2.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        window.document.vmg.status_way1.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                                "   <?php echo $strings[$lang]["wrong data"] ?> ???\n";
                        return;
                }
        }

        
        /* une decimale sur la vitesse*/
        winds= Math.round(eval(window.document.vmg.wind_speed.value)*10)/10;
        if (winds<=0)
        {
        window.document.vmg.status_way1.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                        "   <?php echo $strings[$lang]["the wind speed"] ?> <= 0 ???\n";
        window.document.vmg.status_way2.value ="<?php echo $strings[$lang]["error"] ?> !!!\n"+
                                                                                        "   <?php echo $strings[$lang]["the wind speed"] ?> <= 0 ???\n";
        return
        }
  
        window.document.vmg.wind_speed.value=winds;
        
        /* zero decimale sur la direction du vent*/
        windd= Math.round(correct_angle(eval(window.document.vmg.wind_direction.value)));
        window.document.vmg.wind_direction.value=windd;
        
        /*Calcul de la polaire pour la vitesse de vent donnee*/
        for (var i=0; i<=180;i++)
        {
                pv[i]=interpol(i,winds,pav,nb_speed,nb_angle); /*remplissage du tableau pv()*/
        }
        
        if (test_radiobutton(window.document.vmg.choix_1)=="w1")
        {
            cap1_calc= Math.round(course_way1_ortho);
            dist1_calc=dist_way1_ortho;
        }
        if (test_radiobutton(window.document.vmg.choix_1)=="c1")
        {
            cap1_calc=cap1;
            dist1_calc=eval(window.document.vmg.cap1_distance.value);
        }
        if (test_radiobutton(window.document.vmg.choix_2)=="w2")
        {
            cap2_calc=Math.round(course_way2_ortho);
            dist2_calc=dist_way2_ortho;
        }
        if (test_radiobutton(window.document.vmg.choix_2)=="c2")
        {
            cap2_calc=cap2;
            dist2_calc=eval(window.document.vmg.cap2_distance.value);
        }

        dist1_calc=testdist(dist1_calc);
        dist2_calc=testdist(dist2_calc);
        
        txt_status_way1="---<?php echo $strings[$lang]["vmg info"] ?>---\n";
        txt_status_way2="---<?php echo $strings[$lang]["vmg info"] ?>---\n";
        
        
        /* calcul de la vmg */
        for (var i=0; i<=180;i++)
        {
                /*calcul du cap pour les deux waypoint tribord & babord*/
                cap1_t[i]=correct_angle(Math.round(windd-i));
                cap1_b[i]=correct_angle(Math.round(windd+i));
                cap2_t[i]=correct_angle(Math.round(windd-i));
                cap2_b[i]=correct_angle(Math.round(windd+i));
                
                /*calcul de la vitesse de decalage pour les deux waypoint tribord & babord*/
                vd1_t[i]=pv[i]*Math.sin((cap1_calc-(windd-i))*Math.PI/180);
                vd1_b[i]=pv[i]*Math.sin((cap1_calc-(windd+i))*Math.PI/180);
                vd2_t[i]=pv[i]*Math.sin((cap2_calc-(windd-i))*Math.PI/180);
                vd2_b[i]=pv[i]*Math.sin((cap2_calc-(windd+i))*Math.PI/180);
                
                /*calcul de la vmg pour les deux waypoint tribord & babord*/
                way1_t[i]=pv[i]*Math.cos((cap1_calc-(windd-i))*Math.PI/180);
                way1_b[i]=pv[i]*Math.cos((cap1_calc-(windd+i))*Math.PI/180);
                way2_t[i]=pv[i]*Math.cos((cap2_calc-(windd-i))*Math.PI/180);
                way2_b[i]=pv[i]*Math.cos((cap2_calc-(windd+i))*Math.PI/180);
                
                /* remplissage de la variable que servira a l'affichage de toutes les valeurs dans les cases info*/
                txt_status_way1= txt_status_way1+
                                                "-"+i+String.fromCharCode(0x00B0)+" / "+
                                                cap1_t[i]+String.fromCharCode(0x00B0)+" / "+
                                                Math.round(pv[i]*100)/100+" / "+
                                                Math.round(way1_t[i]*100)/100+
                                                "\n"+
                                                "+"+i+String.fromCharCode(0x00B0)+" / "+
                                                cap1_b[i]+String.fromCharCode(0x00B0)+" / "+
                                                Math.round(pv[i]*100)/100+" / "+
                                                Math.round(way1_b[i]*100)/100+
                                                "\n\n";
                txt_status_way2 = txt_status_way2+
                                                "-"+i+String.fromCharCode(0x00B0)+" / "+
                                                cap2_t[i]+String.fromCharCode(0x00B0)+" / "+
                                                Math.round(pv[i]*100)/100+" / "+
                                                Math.round(way2_t[i]*100)/100+
                                                "\n"+
                                                "+"+i+String.fromCharCode(0x00B0)+" / "+
                                                cap2_b[i]+String.fromCharCode(0x00B0)+" / "+
                                                Math.round(pv[i]*100)/100+" / "+
                                                Math.round(way2_b[i]*100)/100+
                                                "\n\n";
                /* detection de la vmg maxi pour les deux waypoint tribord et babord*/
                if (way1_t[i]>vmg_way1_t)
                {
                        vmg_way1_t= way1_t[i];
                        angle_vmg_way1_t=i;
                }
                if (way1_b[i]>vmg_way1_b)
                {
                        vmg_way1_b= way1_b[i];
                        angle_vmg_way1_b=i;
                }
                if (way2_t[i]>vmg_way2_t)
                {
                        vmg_way2_t= way2_t[i];
                        angle_vmg_way2_t=i;
                }
                if (way2_b[i]>vmg_way2_b)
                {
                        vmg_way2_b= way2_b[i];
                        angle_vmg_way2_b=i;
                }
        }
        
        txt_status_way1=txt_status_way1+"\n";
        txt_status_way2=txt_status_way2+"\n";

        
        /* calcul du couple de valeur optimum pour waypoint_1 ou cap_1*/
        for (var i=0; i<=180;i++)
        {
                if (way1_t[i]>=0)
                {
                        for (var j=0; j<=180;j++)
                        {
                                if (way1_b[j]>=0)
                                {
                                        if (vd1_t[i]==0)
                                        {
                                                t1_2=0;
                                                t1_1=1/way1_t[i];
                                        }
                                        else
                                        {
                                                t1_2=1/(-way1_t[i]*vd1_b[j]/vd1_t[i]+way1_b[j]);
                                                t1_1=-t1_2*vd1_b[j]/vd1_t[i];
                                        }
                                        if (t1_1>=0 && t1_2>=0)
                                        {
                                                t1_t=t1_1+t1_2;
                                                vm1=1/t1_t;
                                                if (vm1>vmo1)
                                                {
                                                        vmo1=Math.round(vm1*100)/100;
                                                        avo1_t=-i;
                                                        co1_t=cap1_t[i];
                                                        vo1_t=pv[i];
                                                        vmgo1_t=way1_t[i];
                                                        avo1_b=j;
                                                        co1_b=cap1_b[j];
                                                        vo1_b=pv[j];
                                                        vmgo1_b=way1_b[j];                                                      
                                                        to1_1=Math.round(t1_1*100/t1_t);
                                                        to1_2=Math.round(100-to1_1);
                                                        tb1=Math.round(60*(dist1_calc/vmo1)*to1_1/100); // temps babord en minutes
                                                        tt1=Math.round(60*(dist1_calc/vmo1)*to1_2/100); // temps tribord en minutes
                                                }
                                        }
                                }
                        }
                }
        }

        /* calcul du couple de valeur optimum pour waypoint_2 ou cap_2*/
        for (var i=0; i<=180;i++)
        {
                if (way2_t[i]>=0)
                {
                        for (var j=0; j<=180;j++)
                        {
                                if (way2_b[j]>=0)
                                {
                                        if (vd2_t[i]==0)
                                        {
                                                t2_2=0;
                                                t2_1=1/way2_t[i];
                                        }
                                        else
                                        {
                                                t2_2=1/(-way2_t[i]*vd2_b[j]/vd2_t[i]+way2_b[j]);
                                                t2_1=-t2_2*vd2_b[j]/vd2_t[i];
                                        }
                                        if (t2_1>=0 && t2_2>=0)
                                        {
                                                t2_t=t2_1+t2_2;
                                                vm2=1/t2_t;
                                                if (vm2>vmo2)
                                                {
                                                        vmo2=Math.round(vm2*100)/100;
                                                        avo2_t=-i;
                                                        co2_t=cap2_t[i];
                                                        vo2_t=pv[i];
                                                        vmgo2_t=way2_t[i];
                                                        avo2_b=j;
                                                        co2_b=cap2_b[j];
                                                        vo2_b=pv[j];
                                                        vmgo2_b=way2_b[j];                                                      
                                                        to2_1=Math.round(t2_1*100/t2_t);
                                                        to2_2=Math.round(100-to2_1);
                                                        tb2=Math.round(60*(dist2_calc/vmo2)*to2_1/100); // temps babord en minutes
                                                        tt2=Math.round(60*(dist2_calc/vmo2)*to2_2/100); // temps tribord en minutes
                                                }
                                        }
                                }
                        }
                }
        }
        
        /* affichage des valeurs pour waypoint1*/
        window.document.vmg.way1_best_bwa_t.value = -angle_vmg_way1_t+String.fromCharCode(0x00B0);
        window.document.vmg.way1_best_bwa_b.value = angle_vmg_way1_b+String.fromCharCode(0x00B0);
        window.document.vmg.way1_best_course_t.value = correct_angle(Math.round(windd-angle_vmg_way1_t))+String.fromCharCode(0x00B0);
        window.document.vmg.way1_best_course_b.value = correct_angle(Math.round(windd+angle_vmg_way1_b))+String.fromCharCode(0x00B0);
        window.document.vmg.way1_best_speed_t.value = Math.round(pv[angle_vmg_way1_t]*100)/100;
        window.document.vmg.way1_best_speed_b.value = Math.round(pv[angle_vmg_way1_b]*100)/100;
        window.document.vmg.way1_best_vmg_t.value = Math.round(vmg_way1_t*100)/100;
        window.document.vmg.way1_best_vmg_b.value = Math.round(vmg_way1_b*100)/100;
        /* affichage des valeurs pour waypoint2*/
        window.document.vmg.way2_best_bwa_t.value = -angle_vmg_way2_t+String.fromCharCode(0x00B0);
        window.document.vmg.way2_best_bwa_b.value = angle_vmg_way2_b+String.fromCharCode(0x00B0);
        window.document.vmg.way2_best_course_t.value = correct_angle(Math.round(windd-angle_vmg_way2_t))+String.fromCharCode(0x00B0);
        window.document.vmg.way2_best_course_b.value = correct_angle(Math.round(windd+angle_vmg_way2_b))+String.fromCharCode(0x00B0);
        window.document.vmg.way2_best_speed_t.value = Math.round(pv[angle_vmg_way2_t]*100)/100;
        window.document.vmg.way2_best_speed_b.value = Math.round(pv[angle_vmg_way2_b]*100)/100;
        window.document.vmg.way2_best_vmg_t.value = Math.round(vmg_way2_t*100)/100;
        window.document.vmg.way2_best_vmg_b.value = Math.round(vmg_way2_b*100)/100;
        
        if (test_radiobutton(window.document.vmg.choix_1)=="c1")
        {
        }
        if (test_radiobutton(window.document.vmg.choix_1)=="w1")
        {
                if (correct_angle(Math.round(course_way1_ortho)-windd)>180)
                {
                        txt_status_way1 = "---<?php echo $strings[$lang]["direct road"] ?>---\n"+
                                                                "ETR: "+mintohm(Math.round(dist1_calc*60/pv[(360-correct_angle(Math.round(course_way1_ortho)-windd))]))+"\n"+
                                                                "\n"+
                                                                txt_status_way1;
                }
                if (correct_angle(Math.round(course_way1_ortho)-windd)>0 && correct_angle(Math.round(course_way1_ortho)-windd)<=180)
                {
                        txt_status_way1 = "---<?php echo $strings[$lang]["direct road"] ?>---\n"+
                                                                "ETR: "+mintohm(Math.round(dist1_calc*60/pv[(correct_angle(Math.round(course_way1_ortho)-windd))]))+"\n"+
                                                                "\n"+
                                                                txt_status_way1;
                }
        }
                txt_status_way1 =       "---<?php echo $strings[$lang]["optimum road"] ?>---\n"+
                                                        "a> "+
                                                        avo1_t+String.fromCharCode(0x00B0)+" / "+
                                                        co1_t+String.fromCharCode(0x00B0)+" / "+
                                                        Math.round(vo1_t*100)/100+" / "+
                                                        Math.round(vmgo1_t*100)/100+" / "+
                                                        to1_1+"%"+
                                                        "\n"+
                                                        "b> "+
                                                        avo1_b+String.fromCharCode(0x00B0)+" / "+
                                                        co1_b+String.fromCharCode(0x00B0)+" / "+
                                                        Math.round(vo1_b*100)/100+" / "+
                                                        Math.round(vmgo1_b*100)/100+" / "+
                                                        to1_2+"%"+
                                                        "\n"+
                                                        "<?php echo $strings[$lang]["average speed"] ?>: "+vmo1+"\n"+
                                                        "<?php echo $strings[$lang]["distance"] ?>: "+Math.round(dist1_calc*100)/100+" Nm\n"+
                                                        "<?php echo $strings[$lang]["time"] ?> a: "+ mintohm(tb1)+"\n"+
                                                        "<?php echo $strings[$lang]["time"] ?> b: "+ mintohm(tt1)+"\n"+
                                                        "<?php echo $strings[$lang]["total time"] ?> (ETR): "+mintohm(tb1+tt1)+"\n"+
                                                        "\n"+
                                                        txt_status_way1;
                
                window.document.vmg.status_way1.value = txt_status_way1;
        
        if (test_radiobutton(window.document.vmg.choix_2)=="c2")
        {
        }
        if (test_radiobutton(window.document.vmg.choix_2)=="w2")
        {
                if (correct_angle(Math.round(course_way2_ortho)-windd)>180)
                {
                        txt_status_way2 = "---<?php echo $strings[$lang]["direct road"] ?>---\n"+
                                                                "ETR: "+mintohm(Math.round(dist2_calc*60/pv[(360-correct_angle(Math.round(course_way2_ortho)-windd))]))+"\n"+
                                                                "\n"+
                                                                txt_status_way2;
                }
                if (correct_angle(Math.round(course_way2_ortho)-windd)>0 && correct_angle(Math.round(course_way2_ortho)-windd)<=180)
                {
                        txt_status_way2 = "---<?php echo $strings[$lang]["direct road"] ?>---\n"+
                                                                "ETR: "+mintohm(Math.round(dist2_calc*60/pv[(correct_angle(Math.round(course_way2_ortho)-windd))]))+"\n"+
                                                                "\n"+
                                                                txt_status_way2;
                }
        }
                txt_status_way2 =       "---<?php echo $strings[$lang]["optimum road"] ?>---\n"+
                                                        "a> "+
                                                        avo2_t+String.fromCharCode(0x00B0)+" / "+
                                                        co2_t+String.fromCharCode(0x00B0)+" / "+
                                                        Math.round(vo2_t*100)/100+" / "+
                                                        Math.round(vmgo2_t*100)/100+" / "+
                                                        to2_1+"%"+
                                                        "\n"+
                                                        "b> "+
                                                        avo2_b+String.fromCharCode(0x00B0)+" / "+
                                                        co2_b+String.fromCharCode(0x00B0)+" / "+
                                                        Math.round(vo2_b*100)/100+" / "+
                                                        Math.round(vmgo2_b*100)/100+" / "+
                                                        to2_2+"%"+
                                                        "\n"+
                                                        "<?php echo $strings[$lang]["average speed"] ?>: "+vmo2+"\n"+
                                                        "<?php echo $strings[$lang]["distance"] ?>: "+Math.round(dist2_calc*100)/100+" Nm\n"+
                                                        "<?php echo $strings[$lang]["time"] ?> a: "+ mintohm(tb2)+"\n"+
                                                        "<?php echo $strings[$lang]["time"] ?> b: "+ mintohm(tt2)+"\n"+
                                                        "<?php echo $strings[$lang]["total time"] ?> (ETR): "+mintohm(tb2+tt2)+"\n"+
                                                        "\n"+
                                                        txt_status_way2;
                
                window.document.vmg.status_way2.value = txt_status_way2;
        
}

//-->
</script>

</head>
<!-- couleur originale -->
<!--<body bgcolor="#7a96df">-->

<!-- couleur VLM -->
<body bgcolor="#90a0a0">


<div align="center">
<table cellpadding="0" cellspacing="0" width="100%">

  <tbody>

    <tr>

      <td width="33%">
      <p align="center"><a href="http://virtual-loup-de-mer.org" target="blank"><img src="/<? echo IMAGE_SITE_PATH ?>/banniere_vlm.jpg" border="0" height="55" width="320" /></a></p>

      </td>

      <td width="33%">
      <h1 style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 22pt;">V M G</span></font></h1>

      <h1 style= "line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 18pt;"> 
                <?php
                        switch($_REQUEST['boattype'])
                        {
                        case "C5v2":
                                echo "-C5v2-";
                                break;
                        
                        case "C5":
                                echo "-C5-";
                                break;
                        
                        case "hi5":
                                echo "-Hi5-";
                                break;
                        
                        case "figaro2":
                                echo "-Figaro2-";
                                break;
/*                      
                        case "imoca60":
                                echo "-IMOCA 60'-";
                                break;
                        
                        case "maxicata":
                                echo "-Maxicata-";
                                break;
*/                      
                        case "A35":
                                echo "-A35-";
                                break;
                        
                        case "Class40":
                                echo "-Class 40-";
                                break;
                        
                        case "Imoca2007":
                                echo "-IMOCA 2007-";
                                break;
                                
                        case "Imoca2008":
                                echo "-IMOCA 2008-";
                                break;
                                
                        case "VLM70":
                                echo "-VLM 70-";
                                break;
                                
                        case "OceanExpress":
                                echo "-OceanExpress-";
                                break;
                                
                        case "Imoca":
                                echo "-IMOCA-";
                                break;
                        
                        default:
                                echo "-IMOCA 2008-";
                                break;
                        
                        }
                ?>

</span></font></h1>
      </td>

      <td width="33%">

      <p align="center"><a href=<?php echo "\""."vmg_vlm.php?lang=fr"."&boattype=".$_REQUEST['boattype']."&boatlat=".$_REQUEST['boatlat']."&boatlong=".$_REQUEST['boatlong']."&wdd=".$_REQUEST['wdd']."&wds=".$_REQUEST['wds']."&wp1lat=".$_REQUEST['wp1lat']."&wp1long=".$_REQUEST['wp1long']."&wp2lat=".$_REQUEST['wp2lat']."&wp2long=".$_REQUEST['wp2long']."\"" ?> ><img src="fr.gif" align="middle" border="0" height="16" width="24" alt="fr" /></A> <a href=<?php echo "\""."vmg_vlm.php?lang=en"."&boattype=".$_REQUEST['boattype']."&boatlat=".$_REQUEST['boatlat']."&boatlong=".$_REQUEST['boatlong']."&wdd=".$_REQUEST['wdd']."&wds=".$_REQUEST['wds']."&wp1lat=".$_REQUEST['wp1lat']."&wp1long=".$_REQUEST['wp1long']."&wp2lat=".$_REQUEST['wp2lat']."&wp2long=".$_REQUEST['wp2long']."\"" ?> ><img src="en.gif" alt="en" align="middle" border="0" height="16" width="24" /></a></p>

      </td>

    </tr>

  </tbody>
</table>

</div>

<form name="vmg" id="vmg" action="vmg_vlm.php" method="post">

  <table style="width: 90%;" border="0" cellpadding="5">

    <tbody>

      <tr>
                                                <td align="center" valign="middle" width="320">
                                                        <table width="216" border="1" cellspacing="2" cellpadding="3">
                                                                <tbody>
                                                                        <tr height="20">
                                                                                <td colspan="2" align="center" height="20" valign="middle" width="200" bgcolor="#70a1a1"><font color="black" face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["boat"] ?></b></span></font></td>
                                                                        </tr>
                                                                        <tr height="36">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="130" height="36"><font face="verdana"><span style="font-size: 9pt;"><b><input name="boat_lat_d" value=<?php echo "\"".$pos_boat["latdeg"]."\"" ?> size="3" maxlength="3" tabindex="1" type="text" />&deg; <input name="boat_lat_m" value=<?php echo "\"".$pos_boat["latmin"]."\"" ?> size="2" maxlength="2" tabindex="2" type="text" />' <input name="boat_lat_s" value=<?php echo "\"".$pos_boat["latsec"]."\"" ?> size="2" maxlength="2" tabindex="3" type="text" />" </b></span></font></td>
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="36">
                                                                                        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 9pt;"><b><input name="boat_lat" value="n" <?php if ($pos_boat["lathem"]=="n") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="4" type="radio" />N</b></span></font></p>
                                                                                        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 9pt;"><b><input name="boat_lat" value="s" <?php if ($pos_boat["lathem"]=="s") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="5" type="radio" />S</b></span></font></p>
                                                                                </td>
                                                                        </tr>
                                                                        <tr height="26">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="130" height="26"><font face="verdana"><span style="font-size: 9pt;"><b><input name="boat_long_d" value=<?php echo "\"".$pos_boat["longdeg"]."\"" ?> size="3" maxlength="3" tabindex="6" type="text" />&deg;
              <input name="boat_long_m" value=<?php echo "\"".$pos_boat["longmin"]."\"" ?> size="2" maxlength="2" tabindex="7" type="text" />'
              <input name="boat_long_s" value=<?php echo "\"".$pos_boat["longsec"]."\"" ?> size="2" maxlength="2" tabindex="8" type="text" />"
              </b></span></font></td>
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="26"><font face="verdana"><span style="font-size: 9pt;"><b><input name="boat_long" value="w" <?php if ($pos_boat["longhem"]=="w") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?>tabindex="9" type="radio" />W<input name="boat_long" value="e" <?php if ($pos_boat["longhem"]=="e") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="10" type="radio" />E</b></span></font></td>
                                                                        </tr>
                                                                </tbody>
                                                        </table>
                                                </td>
                                                <td align="center" valign="middle" width="320">
                                                        <table border="0" cellspacing="2" cellpadding="0">
                                                                <tr>
                                                                        <td><input type="radio" name="choix_1" value="w1" checked="checked" tabindex="13" /></td>
                                                                        <td width="226">
                                                                                <table width="100" align="left" border="1" cellpadding="3" cellspacing="2">
                                                                                        <tbody>
                                                                                                <tr height="20">
                                                                                                        <td colspan="2" align="center" height="20" valign="middle" width="210" bgcolor="#70a1a1">
                                                                                                                <div align="center">
                                                                                                                        <font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["waypoint"] ?> 1</b></span></font></div>
                                                                                                        </td>
                                                                                                </tr>
                                                                                                <tr height="36">
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="130" height="36"><font face="verdana"><span style="font-size: 9pt;"><b><input name="way1_lat_d" value=<?php echo "\"".$pos_way1["latdeg"]."\"" ?> size="3" maxlength="3" tabindex="16" type="text" />&deg; <input name="way1_lat_m" value=<?php echo "\"".$pos_way1["latmin"]."\"" ?> size="2" maxlength="2" tabindex="17" type="text" />' <input name="way1_lat_s" value=<?php echo "\"".$pos_way1["latsec"]."\"" ?> size="2" maxlength="2" tabindex="18" type="text" />&quot;</b></span></font></td>
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="70" height="36">
                                                                                                                <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font face="verdana"><span style="font-size: 9pt;"><b><input name="way1_lat" value="n" <?php if ($pos_way1["lathem"]=="n") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="19" type="radio" />N</b></span></font></p>
                                                                                                                <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font face="verdana"><span style="font-size: 9pt;"><b><input name="way1_lat" value="s" <?php if ($pos_way1["lathem"]=="s") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="20" type="radio" />S</b></span></font></p>
                                                                                                        </td>
                                                                                                </tr>
                                                                                                <tr height="26">
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="130" height="26"><font face="verdana"><span style="font-size: 9pt;"><b><input name="way1_long_d" value=<?php echo "\"".$pos_way1["longdeg"]."\"" ?> size="3" maxlength="3" tabindex="21" type="text" />&deg; <input name="way1_long_m" value=<?php echo "\"".$pos_way1["longmin"]."\"" ?> size="2" maxlength="2" tabindex="22" type="text" />' <input name="way1_long_s" value=<?php echo "\"".$pos_way1["longsec"]."\"" ?> size="2" maxlength="2" tabindex="23" type="text" />&quot;</b></span></font></td>
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="70" height="26"><font face="verdana"><span style="font-size: 9pt;"><b><input name="way1_long" value="w" <?php if ($pos_way1["longhem"]=="w") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="24" type="radio" />W<input name="way1_long" value="e" <?php if ($pos_way1["longhem"]=="e") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="25" type="radio" />E</b></span></font></td>
                                                                                                </tr>
                                                                                        </tbody>
                                                                                </table>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td><input type="radio" name="choix_1" value="c1" tabindex="14" /></td>
                                                                        <td width="226">
                                                                                <table width="100%" border="1" cellspacing="2" cellpadding="3" align="left">
                                                                                        <tr height="20">
                                                                                                <td width="173" height="20" bgcolor="#70a1a1">
                                                                                                        <div align="center">
                                                                                                                <font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["course"] ?> 1</b></span></font></div>
                                                                                                </td>
                                                                                        </tr>
                                                                                        <tr height="26">
                                                                                                <td nowrap="nowrap" width="173" height="26">
                                                                                                        <div align="center">
                                                                                                                <input name="cap1_course" value="0" size="5" maxlength="5" tabindex="15" type="text" />&deg&nbsp&nbsp&nbsp&nbsp<input name="cap1_distance" value="100" size="7" maxlength="7" tabindex="15" type="text" /><font face="verdana"><span style="font-size: 9pt;">nm</span></font></div>
                                                                                                </td>
                                                                                        </tr>
                                                                                </table>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                </td>
                                                <td align="center" valign="middle" width="320">
                                                        <table border="0" cellspacing="2" cellpadding="0">
                                                                <tr>
                                                                        <td><input type="radio" name="choix_2" value="w2" checked="checked" tabindex="26" /></td>
                                                                        <td width="226">
                                                                                <table width="100%" align="left" border="1" cellpadding="3" cellspacing="2">
                                                                                        <tbody>
                                                                                                <tr height="20">
                                                                                                        <td colspan="2" align="center" height="20" valign="middle" width="210" bgcolor="#70a1a1">
                                                                                                                <div align="center">
                                                                                                                        <font color="black" face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["waypoint"] ?> 2</b></span></font></div>
                                                                                                        </td>
                                                                                                </tr>
                                                                                                <tr height="36">
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="130" height="36"><font color="black" face="verdana"><span style="font-size: 9pt;"><b><input name="way2_lat_d" value=<?php echo "\"".$pos_way2["latdeg"]."\"" ?> size="3" maxlength="3" tabindex="29" type="text" />&deg; <input name="way2_lat_m" value=<?php echo "\"".$pos_way2["latmin"]."\"" ?> size="2" maxlength="2" tabindex="30" type="text" />' <input name="way2_lat_s" value=<?php echo "\"".$pos_way2["latsec"]."\"" ?> size="2" maxlength="2" tabindex="31" type="text" />&quot;</b></span></font></td>
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="70" height="36">
                                                                                                                <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font color="black" face="verdana"><span style="font-size: 9pt;"><b><input name="way2_lat" value="n" <?php if ($pos_way2["lathem"]=="n") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="32" type="radio" />N</b></span></font></p>
                                                                                                                <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font color="black" face="verdana"><span style="font-size: 9pt;"><b><input name="way2_lat" value="s" <?php if ($pos_way2["lathem"]=="s") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="33" type="radio" />S</b></span></font></p>
                                                                                                        </td>
                                                                                                </tr>
                                                                                                <tr height="26">
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="130" height="26"><font color="#9fafc1" face="verdana"><span style="font-size: 9pt;"><b><input name="way2_long_d" value=<?php echo "\"".$pos_way2["longdeg"]."\"" ?> size="3" maxlength="3" tabindex="34" type="text" /></b></span></font><span style="font-size: 9pt;"><b><font color="black" face="verdana">&deg;</font><font face="verdana"> <input name="way2_long_m" value=<?php echo "\"".$pos_way2["longmin"]."\"" ?> size="2" maxlength="2" tabindex="35" type="text" /></font><font color="black" face="verdana">'</font><font face="verdana"> <input name="way2_long_s" value=<?php echo "\"".$pos_way2["longsec"]."\"" ?> size="2" maxlength="2" tabindex="36" type="text" /></font></b><font color="black" face="verdana"><b>&quot;</b></font></span></td>
                                                                                                        <td align="center" nowrap="nowrap" valign="middle" width="70" height="26"><font color="black" face="verdana"><span style="font-size: 9pt;"><b><input name="way2_long" value="w" <?php if ($pos_way2["longhem"]=="w") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="37" type="radio" />W<input name="way2_long" value="e" <?php if ($pos_way2["longhem"]=="e") {echo "checked="."\""."checked"."\"";} else {echo " ";} ?> tabindex="38" type="radio" />E</b></span></font></td>
                                                                                                </tr>
                                                                                        </tbody>
                                                                                </table>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td><input type="radio" name="choix_2" value="c2" tabindex="27" /></td>
                                                                        <td width="226">
                                                                                <table width="100%" border="1" cellspacing="2" cellpadding="3" align="left">
                                                                                        <tr height="20">
                                                                                                <td width="165" height="20" bgcolor="#70a1a1">
                                                                                                        <div align="center">
                                                                                                                <font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["course"] ?> 2</b></span></font></div>
                                                                                                </td>
                                                                                        </tr>
                                                                                        <tr height="26">
                                                                                                <td nowrap="nowrap" width="165" height="26">
                                                                                                        <div align="center">
                                                                                                                <input name="cap2_course" value="0" size="5" maxlength="5" tabindex="28" type="text" />&deg&nbsp&nbsp&nbsp&nbsp<input name="cap2_distance" value="100" size="7" maxlength="7" tabindex="28" type="text" /><font face="verdana"><span style="font-size: 9pt;">nm</span></font></div>
                                                                                                </td>
                                                                                        </tr>
                                                                                </table>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                </td>
                                        </tr>

      <tr>

        <td align="center" valign="middle" width="320">
                                                        <table border="1" cellpadding="3" cellspacing="2">
                                                                <tbody>
                                                                        <tr align="center" valign="middle" height="26">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="160" height="26"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["orthodromic distance"] ?></span></font></td>
                                                                        </tr>
                                                                        <tr align="center" valign="middle" height="26">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="160" height="26"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["orthodromic course"] ?></span></font></td>
                                                                        </tr>
                                                                        <tr align="center" valign="middle" height="26">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="160" height="26"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["loxodromic distance"] ?></span></font></td>
                                                                        </tr>
                                                                        <tr align="center" valign="middle" height="26">
                                                                                <td align="center" nowrap="nowrap" valign="middle" width="160" height="26"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["loxodromic course"] ?></span></font></td>
                                                                        </tr>
                                                                </tbody>
                                                        </table>
                                                </td>

        <td align="center" valign="middle" width="320">
                        <table border="1" cellpadding="3" cellspacing="2">
                                <tbody>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way1_ortho_dist" value="0" readonly="readonly" size="15" maxlength="15" type="text" />nm</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way1_ortho_course" value="0" readonly="readonly" size="15" maxlength="15" type="text" />&deg;</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way1_loxo_dist" value="0" readonly="readonly" size="15" maxlength="15" type="text" />nm</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way1_loxo_course" value="0" readonly="readonly" size="15" maxlength="5" type="text" />&deg;</span></font></div>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
        </td>

        <td align="center" valign="middle" width="320">
                        <table border="1" cellpadding="3" cellspacing="2">
                                <tbody>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way2_ortho_dist" value="0" readonly="readonly" size="15" maxlength="15" type="text" />nm</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way2_ortho_course" value="0" readonly="readonly" size="15" maxlength="15" type="text" />&deg;</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way2_loxo_dist" value="0" readonly="readonly" size="15" maxlength="15" type="text" />nm</span></font></div>
                                                </td>
                                        </tr>
                                        <tr align="center" valign="middle" height="26">
                                                <td align="left" nowrap="nowrap" valign="middle" width="160" height="26">
                                                        <div align="left">
                                                                <font face="verdana"><span style="font-size: 9pt;">&nbsp&nbsp&nbsp<input name="way2_loxo_course" value="0" readonly="readonly" size="15" maxlength="5" type="text" />&deg;</span></font></div>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </td>

      </tr>

      <tr>

        <td align="center" valign="middle" width="320">
                                                        <div align="center">
                                                                <table width="64" border="0" cellspacing="6" cellpadding="0">
                                                                        <tr>
                                                                                <td>
                                                                                        <table border="1" cellpadding="3">
                                                                                                <tbody>
                                                                                                        <tr height="20">
                                                                                                                <td colspan="2" align="center" width="134" valign="middle" height="20" bgcolor="#70a1a1"><font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["wind"] ?></b></span></font></td>
                                                                                                        </tr>
                                                                                                        <tr height="20">
                                                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="20"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["speed"] ?></span></font></td>
                                                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="20"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["direction"] ?></span></font></td>
                                                                                                        </tr>
                                                                                                        <tr height="26">
                                                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="26"><input name="wind_speed" value=<?php echo "\"".$ws."\"" ?> size="5" maxlength="5" tabindex="11" type="text" /></td>
                                                                                                                <td align="center" nowrap="nowrap" valign="middle" width="60" height="26"><font face="verdana"><span style="font-size: 9pt;"><input name="wind_direction" value=<?php echo "\"".$wd."\"" ?> size="5" maxlength="5" tabindex="12" type="text" />&deg;</span></font></td>
                                                                                                        </tr>
                                                                                                </tbody>
                                                                                        </table>
                                                                                </td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td>
                                                                                        <div align="center">
                                                                                                <input name="validate_button" value="<?php echo $strings[$lang]["validate"] ?>" onclick="valider()" tabindex="39" type="button" /></div>
                                                                                </td>
                                                                        </tr>
                                                                </table>
                                                        </div>
                                                </td>

        <td align="center" valign="middle" width="320">
        <table border="1" cellpadding="3" cellspacing="2">

          <tbody>

            <tr align="center" height="20" valign="middle">

              <td colspan="4" align="center" width="318" height="20" valign="middle" bgcolor="#70a1a1"><font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["best course / speed"] ?></b></span></font></td>

            </tr>

            <tr align="center" height="30" valign="middle">

              <td align="center" width="78" height="30" valign="middle"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["boat/wind angle"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["course"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["speed"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["vmg"] ?></span></font></td>

            </tr>

            <tr align="center" height="26" valign="middle">

              <td align="center" bgcolor="lime" width="78" height="26" valign="middle"><font color="lime" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_bwa_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font color="lime" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_course_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font color="lime" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_speed_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font color="lime" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_vmg_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

            </tr>

            <tr align="center" height="26" valign="middle">

              <td align="center" bgcolor="red" width="78" height="26" valign="middle"><font color="black" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_bwa_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font color="black" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_course_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font color="black" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_speed_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font color="black" face="verdana"><span style="font-size: 9pt;"><input name="way1_best_vmg_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

            </tr>

          </tbody>

        </table>

        </td>

        <td align="center" valign="middle" width="320">
        <table border="1" cellpadding="3" cellspacing="2">

          <tbody>

            <tr align="center" height="20" valign="middle">

              <td colspan="4" align="center" width="318" height="20" valign="middle" bgcolor="#70a1a1"><font face="verdana"><span style="font-size: 9pt;"><b><?php echo $strings[$lang]["best course / speed"] ?></b></span></font></td>

            </tr>

            <tr align="center" height="30" valign="middle">

              <td align="center" width="78" height="30" valign="middle"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["boat/wind angle"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["course"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["speed"] ?></span></font></td>

              <td align="center" height="30" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["vmg"] ?></span></font></td>

            </tr>

            <tr align="center" height="26" valign="middle">

              <td align="center" bgcolor="lime" width="78" height="26" valign="middle"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_bwa_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_course_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_speed_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="lime" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_vmg_t" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

            </tr>

            <tr align="center" height="26" valign="middle">

              <td align="center" bgcolor="red" width="78" height="26" valign="middle"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_bwa_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_course_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_speed_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

              <td align="center" bgcolor="red" height="26" valign="middle" width="70"><font face="verdana"><span style="font-size: 9pt;"><input name="way2_best_vmg_b" value="0" readonly="readonly" size="5" maxlength="5" type="text" /></span></font></td>

            </tr>

          </tbody>
        </table>

        </td>

      </tr>

      <tr>

        <td style="width: 250px; text-align: center; vertical-align: middle;" width="320">
                                                        <table border="0" cellspacing="2" cellpadding="0" align="center">
                                                                <tr>
                  <?php
                    switch ($_REQUEST['boattype'])
                    {
		    case "C5v2":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_C5v2\" target=\"blank\"><img style=\"width: 233px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_C5/C5.png\" border=\"0\" /></a></td>";
		    break;
                    
		    case "C5":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_C5\" target=\"blank\"><img style=\"width: 233px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_C5/C5.png\" border=\"0\" /></a></td>";
		    break;
                    
		    case "hi5":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_hi5\" target=\"blank\"><img style=\"width: 233px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Hi5/boat2.jpg\" border=\"0\" /></a></td>";
		    break;
                    
		    case "figaro2":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_figaro2\" target=\"blank\"><img style=\"width: 243px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Figaro2/Figaro2_Beneteau.jpg\" border=\"0\" /></a></td>";
		    break;
		    /*                                                      
									    case "imoca60":
									    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_imoca60\" target=\"blank\"><img style=\"width: 243px; height: 175px;\" alt=\"\" src=\"imoca60.jpg\" border=\"0\" /></a></td>";
									    break;
                                                                            
									    case "maxicata":
									    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_maxicata\" target=\"blank\"><img style=\"width: 243px; height: 175px;\" alt=\"\" src=\"maxicata.jpg\" border=\"0\" /></a></td>";
									    break;
		    */                                                      
		    case "A35":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_A35\" target=\"blank\"><img style=\"width: 259px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_A35/A35_couleur.jpg\" border=\"0\" /></a></td>";
                                                                                        break;
                                                                                                                                        
		    case "Class40":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Class40\" target=\"blank\"><img style=\"width: 233px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Class40/Class40.jpg\" border=\"0\" /></a></td>";
		    break;
                    
		    case "Imoca2007":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Imoca2007\" target=\"blank\"><img style=\"width: 258px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Imoca2007/Imoca2007.png\" border=\"0\" /></a></td>";
		    break;
		    
		    case "Imoca2008":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Imoca2007\" target=\"blank\"><img style=\"width: 258px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Imoca2008/Imoca2008.png\" border=\"0\" /></a></td>";
		    break;
		    
		    case "VLM70":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Imoca2007\" target=\"blank\"><img style=\"width: 258px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_VLM70/VLM70.png\" border=\"0\" /></a></td>";
		    break;
		    
		    case "OceanExpress":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_OceanExpress\" target=\"blank\"><img style=\"width: 254px; height: 167px;\" alt=\"\" src=\"/images/Boats/Boat_OceanExpress/OceanExpress.jpg\" border=\"0\" /></a></td>";
		    break;
		    
		    case "Imoca":
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Imoca2007\" target=\"blank\"><img style=\"width: 258px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Imoca2007/Imoca2007.png\" border=\"0\" /></a></td>";
		    break;
		    
                    
		    default:
		    echo "<td><a href=\"http://virtual-loup-de-mer.org/speedchart.php?boattype=boat_Imoca2008\" target=\"blank\"><img style=\"width: 258px; height: 175px;\" alt=\"\" src=\"/images/Boats/Boat_Imoca2008/Imoca2008.png\" border=\"0\" /></a></td>";
		    break;
                    
                                                                                }       
                                                                                
                                                                        ?>
                                                                </tr>
                                                                <tr>
                                                                        <!--<td>
                                    
                                                                                <input type="submit" name="boattype" value="C5v2"/>
                                                                                <input type="submit" name="boattype" value="C5"/>
                                                                                <input type="submit" name="boattype" value="hi5"/>
                                                                                <input type="submit" name="boattype" value="figaro2"/>
                                                                                <input type="submit" name="boattype" value="imoca60"/>
                                                                                <input type="submit" name="boattype" value="maxicata"/>
                                                                                <input type="submit" name="boattype" value="A35"/>
                                                                                <input type="submit" name="boattype" value="Class40"/>
                                                                                <input type="submit" name="boattype" value="Imoca2007"/>
                                                                                <input type="submit" name="boattype" value="Imoca2008"/>
                                                                        
                                    </td>-->
                                                                </tr>
                                                        </table>
                                                </td>

        <td align="center" valign="middle" width="320"><font face="verdana"><span style="font-size: 9pt;"><textarea name="status_way1" rows="11" cols="38" readonly="readonly"></textarea></span></font></td>

        <td align="center" valign="middle" width="320"><font face="verdana"><span style="font-size: 9pt;"><textarea name="status_way2" rows="11" cols="38" readonly="readonly"></textarea></span></font></td>

      </tr>

      <tr>

        <td width="320">
        <!--
        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 9pt;"> &copy; </span></font><font face="verdana"><span style="font-size: 9pt;">Original
: Excel Worksheet</span></font></p>

        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 9pt;">by John-Pet</span></font></p>

        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;" align="center"><font face="verdana"><span style="font-size: 9pt;"> &copy; </span></font><font face="verdana"><span style="font-size: 9pt;">Adaptation
PHP by </span></font><span style="font-size: 9pt;"><font color="black" face="verdana">StephPEN</font></span></p>
                -->
        </td>

        <td width="320">

        <div align="center">
        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font face="verdana"><span style="font-size: 9pt;"><?php echo $strings[$lang]["help"] ?>
: </span></font><span style="font-size: 9pt;"><a href="http://www.virtual-winds.com/forum/index.php?showtopic=4948" target="_blank"><font color="blue" face="verdana"><?php echo $strings[$lang]["taverne"] ?></font></a></span></p>

        <p style="line-height: 100%; margin-top: 0pt; margin-bottom: 0pt;"><font face="verdana"><span style="font-size: 9pt;">@
Virtual-Winds.com</span></font></p>

        </div>

        </td>

        <td width="320">
        <p align="center"></p>

        </td>

      </tr>

    </tbody>
  </table>

</form>


</body>
</html>
