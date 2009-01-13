<?php
//header("Content-type: text/html");
session_start();
include_once("strings.inc");
include_once("config.php");

//all GET and POST variables
if ( isset($_REQUEST['lang']) ) {
     $lang=quote_smart($_REQUEST['lang']);
} else {
     $lang=NavigatorLanguage();
}
$pseudo=quote_smart($_REQUEST['pseudo']);
$password=quote_smart($_REQUEST['password']);
$class=quote_smart($_REQUEST['class']);
$email=quote_smart($_REQUEST['email']);
$blocnote=quote_smart($_REQUEST['blocnote']);
if (eregi("^http", $blocnote) || eregi("://", $blocnote)) {
   $blocnote="BAD NOTEPAD (code inclusion is forbidden)";
}
$country=quote_smart($_REQUEST['country']);
$hidepos=quote_smart($_REQUEST['hidepos']);
$invisible=quote_smart($_REQUEST['invisible']);
$type =quote_smart($_REQUEST['type']);
$action=quote_smart($_REQUEST['action']);
$boatname=quote_smart($_REQUEST['boatname']);
$color=quote_smart($_REQUEST['color']);
$idusers=quote_smart($_REQUEST['idusers']);
$idraces=quote_smart($_REQUEST['idraces']);
$submittype=quote_smart($_REQUEST['submittype']);
$pilotmode=quote_smart($_REQUEST['pilotmode']);
$pilotparameter=quote_smart($_REQUEST['pilotparameter']);
$targetlat=htmlentities(quote_smart($_REQUEST['targetlat']));
$targetlong=htmlentities(quote_smart($_REQUEST['targetlong']));
if ( isset ( $_REQUEST['targetandhdg']) ) {
     $targetandhdg=htmlentities(quote_smart($_REQUEST['targetandhdg']));
} else {
     $targetandhdg=-1;
}
$boatheading=quote_smart($_REQUEST['boatheading']);
$adminpasswd =quote_smart($_REQUEST['adminpasswd']);

$msg="";
//need to be at start because modify header info
if ($type == "logout")
{
  logout();
  $msg = $strings[$lang]["loggedout"];
}

if ($type == "create")
{
  $pseudo = htmlentities($pseudo);
  $password = htmlentities($password);
  //check if login exists
  //$msg2 ="";
  $tmp="";
  if (checkLoginExists($pseudo))
  {
      $msg = $strings[$lang]["loginexist"];
  }
  else if (($pseudo == "") || ($password == ""))
  {
      $msg = $strings[$lang]["loginempty"];
  }
  else
  {

      $idu = createAccount($pseudo, $password, $email, $country);
      login($idu, $pseudo);
      $msg = $strings[$lang]["accountcreated"]." ".$pseudo." ".$password;
  }

}

//if password and pseudo try to connect
if ($type=="login" && $pseudo!="" && $password!="")
{
  $pseudo = htmlentities($pseudo);
  $password = htmlentities($password);
  if (  ($id = checkAccount($pseudo, $password)) != FALSE )
    {
      //create session info
      login($id, $pseudo);
      //$msg = $strings[$lang]["nowlogin"];
    }
  else
    {
      $msg = $strings[$lang]["authfailed"];
    }

}

if ($type == "savemywp")
{
  $users = new fullUsers(getLoginId());
  $users->updateTarget($targetlat, $targetlong,  $targetandhdg, false);
}

if ($type == "change")
{
  $users = new fullUsers(getLoginId());
  //  print_r($users);
  $users->users->boatname = $boatname;
  if ( $invisible == "on" ) {
       $users->users->color = "-" . $color ;
  } else {
       $users->users->color = $color;
  }

  $users->users->email = $email;
  //FIXME : no escaping ?
  $users->users->blocnote = $blocnote;
  $users->users->country = $country;
  // Hidepos : si on est caché, valeur positive (que le moteur décrémente)
  //           sinon, valeur négative.
  //           La valeur est le capital de vacations qu'on peut être caché
  $users->users->hidepos = abs($users->users->hidepos);
  if ( $hidepos != "on" ) {
       $users->users->hidepos = -1 * $users->users->hidepos  ;
  }

  $users->users->write();
//  setUserPref($users->users->idusers,"hidePos","on");
}

if ($type == "unsubscribe")
{
  $users = new fullUsers(getLoginId());
  $users->setABD();
}

if ($type == "subscribe")
{
  $users = new fullUsers(getLoginId());
  // On vérifie que le joueur n'est pas déjà en course
  //print_r($users);
  if ( $users->users->engaged == 0 ) {
       // On vérifie aussi qu'il a accès à cette course :
       // $idraces doit être dans le tableau retourné par availableRaces($idusers) 
       $avRaces=availableRaces($users->users->idusers);

       //print_r($avRaces);
       if ( in_array($idraces, $avRaces) ) {
          $users->subscribeToRaces($idraces);
       } else {
          printf ("<h4>You should not do that.</h4>\n<h4>You can't subscribe to this race.</h4>");
       }
  } else {
      printf ("<h4>You should not do that.</h4>\n<h4>You are already engaged in a race. Please LogOff before and Log in an other boat ! Never go back with your browser, instead, close the session.</h4>");
      printf ("<h4>Ne faites pas &ccedil;a.</h4>\n<h4>Votre bateau est d&eacute;j&agrave; en course ! D&eacute;connectez ce bateau et connextez vous avec l'autre ! (JAMAIS DE RETOUR ARRIERE AVEC VOTRE NAVIGATEUR, MAIS FERMETURE DE SESSION)</h4>");
  }
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>">

 <head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <meta name="verify-v1" content="m1CKSCjEAPxc1+D27ol3vqe87xq3L+LlTNK0pmV78Ns=" />
   <link rel="stylesheet" type="text/css" href="style/new-style.css" />
   <link rel="icon" type="image/png" href="images/site/favicon.png" />
   <title>Virtual Loup-De-Mer </title>
    <?
    // finds current file name
    if(ereg("([^/?]+)(\?.*)?$",$_SERVER["PHP_SELF"] , $regs))
          $CurrentFile = $regs[1];
    else   // it should not be possible :-(
          $CurrentFile = '';

//echo "current file =".$CurrentFile."SCrIPT name = ".$_SERVER["PHP_SELF"]."\n";
//little trick : if update_angle page, display and then redirect to myboat
if ($CurrentFile == "update_angle.php" ) {
    echo "<meta http-equiv=\"refresh\" content=\"0; url=myboat.php?lang=$lang\" />";
} elseif ( $CurrentFile == "admin.php" )  {
           $do=quote_smart($_REQUEST['do']);
           if ( $do == 'yes' ) {
                echo "<meta http-equiv=\"refresh\" content=\"0\" />";
           }
} else {
    echo "<meta http-equiv=\"refresh\" content=\"1200\" />";
}

?>

    <script type="text/javascript" >
     function popup_small(URL,NOM) {
  window.open(URL, NOM, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=850,height=500');
      }
     function popUp(URL,NOM) {
     eval("page" + NOM + " = window.open(URL, NOM, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=800,height=595');");
      }
   </script>
  <script type="text/javascript" src="scripts/dms.js"></script>
  <script type="text/javascript" src="scripts/hashtable.js"></script>
  <script type="text/javascript" src="scripts/showdiv.js"></script>

 </head>

 <body>
   <!-- onLoad connot be in body so we create a false div not to  be displayed -->
   <!-- TODO put expert in cookies-->

   <div id="baneer" align="right">
     <b>
       <?php echo $strings[$lang]["sponsor"]; ?>
     </b>
     <a href="http://www.actilis.net/" target="_actilis"><img src="/images/site/actilis.png" alt="Actilis" /></a>
   </div>
   
   <div id="contenaire"> <!-- ligne rajouter, Debut div contenaire-->
   <div id="login">
<?php
echo "      ".$msg."\n";

if (!isLoggedIn()) //if not connected
{
   ?>
      <form method="post" action="myboat.php">
         <p align="left">
            User: <input size="10" maxlength="15" name="pseudo" /><br />
            Pass: <input size="10" maxlength="15" type="password" name="password" /><br />
            <input type="hidden" name="lang" value="<?echo $lang?>" />
            <input type="hidden" name="type" value="login" />
         </p>
         <p align="center">
     <input type="submit" value="<? echo $strings[$lang]["login"]?>" />
   </p>
      </form>
   <?
   printf ("   ".$strings[$lang]["pleasecreate"], "<a href=\"create.php?lang=$lang\">", "</a>");
}
else //if loggedin
{
  //disp login name, infos
  printf (  $strings[$lang]["youlogin"], getLoginName(), getLoginId() );
  $available_races=availableRaces(getLoginId());
  /*
  if ( in_array (20081109, $available_races) ) {
  printf ( "Vent Des Globes : <B>OK</B>" );
  }
  */
  
?>
  <p align="center">
  <a href="myboat.php?lang=<?echo $lang?>"><?echo $strings[$lang]["control"]?></a>
  <a href="mappref.php?lang=<?echo $lang?>"><?echo $strings[$lang]["mymap"]?></a>
  <a href="modify.php?lang=<?echo $lang?>"><?echo $strings[$lang]["choose"]?></a>
  </p>
  <form method="post" action="index.php">
    <p align="center">
      <input type="hidden" name="type" value="logout" />
      <input type="hidden" name="lang" value="<?echo $lang?>" />
      <input type="submit" value="<? echo $strings[$lang]["logout"]?>" />
    </p>
  </form>

<?
  // Bouton "ADMIN" pour les administrateurs
  if ( idusersIsAdmin (getLoginId()) ) {
      printf ("<p align=center>");
      printf (  "<input type=\"button\" value=\"Admin\" onClick=\"javascript:popUp('admin.php?pseudo=" . $pseudo . "&password=" . $password . "','Admin')\" />");
      printf ("</p>");
  }
}?>
  <hr />
  <a href="<? echo $SCRIPT_NAME ?>?lang=fr"><img src="images/site/fr.png" alt="version Francaise" width="30" height="20" /></a>
  <a href="<? echo $SCRIPT_NAME ?>?lang=en"><img src="images/site/en.png" alt="English version" width="30" height="20" /></a>
  <a href="<? echo $SCRIPT_NAME ?>?lang=it"><img src="images/site/it.png" alt="Italian version" width="30" height="20" /></a>
  <a href="<? echo $SCRIPT_NAME ?>?lang=pt"><img src="images/site/pt.png" alt="Portugese version" width="30" height="20" /></a>
  <br />
      <a href="index.php?lang=<?echo $lang?>"><?echo $strings[$lang]["home"]?></a> 
      <a href="http://images.virtual-loup-de-mer.org/" target="_images"><?echo $strings[$lang]["images"]?></a> 
      <a href="races.php?lang=<?echo $lang?>"><?echo $strings[$lang]["races"]?></a><br />
      <a href="http://ranking.virtual-loup-de-mer.org/" target="_rankings"><?echo $strings[$lang]["ranking"]?></a> 
      <a href="faq.php?lang=<?echo $lang?>"><?echo $strings[$lang]["faq"]?></a> 
      <a href="about.php?lang=<?echo $lang?>"><?echo $strings[$lang]["about"]?></a><br />
      <a href="http://www.virtual-winds.com/forum/index.php?showforum=276" target="_forum"><b><?echo $strings[$lang]["forum"]?></b></a>
      <?php 
          echo "<a href=\"javascript:popUp('" . CHAT_SERVER_URL . "/index.php?username=" . getLoginName() . "(" . getLoginId() . ")". "','chat')\">";
      ?><b>
      <?php 
          echo $strings[$lang]["tchat"];
          echo "</b></a> ";
          echo "<a href=\"javascript:popUp('http://www.webchat.epiknet.org/tchat.php?canal=vlm&amp;pseudo=" . getLoginName() . "-" . getLoginId() . "','newchat')\">";
      ?><b>
      <?php 
          echo "new".$strings[$lang]["tchat"];
          echo "</b></a> ";
          echo "<hr />";
          echo "<a href=\"http://wiki.virtual-loup-de-mer.org/\" target=\"_vlmwiki\"><img src=\"images/site/documentation.png\"  alt=\"". $strings[$lang]["doc"]."\" /></a>";
          echo "<a href=\"http://dev.virtual-loup-de-mer.org/vlm/\" target=\"_vlmdev\"><img src=\"images/site/vlmdev.png\"  alt=\"". $strings[$lang]["dev"]."\" /></a>";
 
    // echo "<h3>".$strings[$lang]["tools"]."</h3>\n"; 
    echo "<a href=\"http://www.virtual-winds.com/forum/index.php?showtopic=4737\" target=\"_tcv\">
                <img src=\"images/site/jpbateaux.png\" alt=\"" . $strings[$lang]["tcv"]. "\" /></a>
                <hr />";

    echo "<h3>".$strings[$lang]["weather"]."</h3>\n";

  ?>
  <a href="<?php echo GRIB_SERVER_URL; ?>/" target="_meteodata"><b><?echo $strings[$lang]["meteodata"]?></b></a><br />
  <a href="http://grib.us/" target="_ugrib"><img src="images/site/grib.us.gif" border="0" alt="Logo du site GRIB" /></a><hr />

        <br />
        ** <a href="http://www.passageweather.com/" target="_pw">Passage Weather</a> **<br />
  Meteo France
  <a href="http://www.meteofrance.com/FR/mer/popupCartePrevFrontsGF.jsp?LIEUID=ATLANTIQUE" target="_meteof">Fronts</a>
  <a href="http://www.meteofrance.com/FR/mer/popupCarteVentsGF.jsp?LIEUID=ATLANTIQUE" target="_meteof">Vents</a>

  <br />
  Wetterzentrale
  <a href="http://www.meteo-marine.com/meteo_marine/gfs.htm" target="_meteowz">GFS</a>,
  <a href="http://www.meteo-marine.com/meteo_marine/jma.htm" target="_meteowz">JMA</a>

  <br />
  Weather-online
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?03&amp;LANG=en&amp;WIND=g246" target="_wol">North Brazil</a>, 
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=euro&amp;MEER=atla" target="_wol">Europe</a>, 
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=euro&amp;MEER=eatl" target="_wol">Atlan.</a>, 
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=euro&amp;MEER=kana" target="_wol">Manche</a>,
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=euro&amp;MEER=mitt" target="_wol">Med.</a>
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=afri&amp;MEER=ioze" target="_wol">Indien</a>
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=aupa&amp;MEER=aust" target="_wol">Aust</a>
  <a href="http://www.weatheronline.co.uk/cgi-bin/windkarten?02&amp;LANG=en&amp;CONT=aupa&amp;MEER=neus" target="_wol">NZL.</a>

  <br />
  Aviation Weather
  <a href="http://aviationweather.gov/obs/sat/intl/ir.shtml" target="_wf">All Zones</a>
  <br />
  Wind Finder
  <a href="http://www.windfinder.com/forecasts/" target="_wf">All Zones</a>
  <br />
  MetVUW
  <a href="http://www.metvuw.com/ows/" target="_mv">MetVUW</a>
  <br />
  COTWeb
  <a href="http://www.cotweb.com/meteo.php4" target="_mv">COTWeb</a>
  <?
           echo "<hr />Srv = " . SERVER_NAME . ".";
           echo "<br />Version = ";  
           include("version.txt");
        ?>
      
   </div>

<!--   
<div id="menu"> </div> 
-->

   <div id="main">
<?php
if ( !isLoggedIn() && (in_array($_SERVER["PHP_SELF"], $restrictedPages) ) )
{
   echo "Access restricted\n<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
   include_once("includes/footer.inc");
   die();
}
?>
