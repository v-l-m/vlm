<?php
/*
** Page Admin, ré-entrante.
** Permet de demander et d'effectuer des tâches d'administration sur VLM.

- une page d'admin refaite (une URL en fait) (sécurité : plus de login/password dans l'URL)
   + * boat=xx&action=valide_wp&NWP=xx => valide un WP en mettant à jour le numéro duprochain WP pour un bateau donné.
   + * boat=xx&action=maj_position&LONG=xxx&LAT=xxx => positionne un bateau aux coordonnées indiquées.
   + * boat=xx&action=reset_pass&PWD=xxxxxxx => met à jour le mot de passe.
   + * boat=xx&action=reset_username&USN=xxxxxxx => met à jour le nom d'utilisateur
*/
session_start();
include_once("includes/strings.inc");
include_once("config.php");

// Les entêtes
echo "<html><head>";
echo "<title>Admin VLM</title>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style/base-style.css\" />";

///   CODE JAVASCRIPT

include_once("scripts/dms.js");


echo "</head></html>";

//all GET and POST variables
isset($_REQUEST['lang']) ? $lang=quote_smart($_REQUEST['lang']) : $lang=NavigatorLanguage();

// Les paramètres
$action=quote_smart($_REQUEST['action']);
$race=quote_smart($_REQUEST['race']);
$boat=quote_smart($_REQUEST['boat']);
$do=quote_smart($_REQUEST['do']);

// Si tentative de connexion en non admin, on ferme la fenêtre
if ( ! idusersIsAdmin (getLoginId()) ) {
     echo "<h4>You should not do that...your IP : " . $_SERVER["REMOTE_ADDR"] . "</h4>";
     exit;
}

$pseudo=getLoginName();

// On est encore là, un Admin est donc connecté.
echo "<H4>Admin connect&eacute; : " . $pseudo . "</h4>\n";
echo "<input type=button value=\"Fermer\" onClick=\"javascript:self.close();\">";

// Y a t'il une course choisie ?
// On n'a pas de course, on propose la liste
if ( $race == "" ) {
   $query = "SELECT idraces, racename  FROM races where started >=0 ";
   $query = $query . " order by idraces;";

   $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");

   $select_list="<option value=\"#\">--- CHOISIR ---</option>";
   while ( $row = mysql_fetch_assoc($result)) {
           $select_list = $select_list . "<option value=\"". 
                                      $_SERVER['PHP_SELF'] . 
              "?boat=".$boat.
              "&action=".$action . 
              "&race=".$row[idraces] . "\"";
           if ( $boat == $row[idraces] ) $select_list = $select_list . " selected ";
           $select_list = $select_list . ">". $row[idraces] . " - " . $row[racename] ."</option>\n";
     
   }
   echo "<h4>No de Course : " .$race;
   echo "<select name=\"race\" " ;
   echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
   echo $select_list . "</select>";
   echo "</h4>";
} else {
   // On a un numéro, est-ce qu'il existe ?
   if ( ! raceExists($race) ) {
     echo "<h4>You should not do that... your IP : " . $_SERVER["REMOTE_ADDR"] . "</h4>";
     exit;
   } else {
   // On a une course qui existe
      echo "<h4>Course : " . $race ;

      echo "<input type=button value=changer onClick=\"document.location='". $_SERVER['PHP_SELF'] .
                  "?action=".$action.
                  "&boat=".$boat.
                  "'\">";
      echo "</h4>";
   }
}




// Y a t'il un bateau choisi ?
if ( $boat != "" ) {
   // On a un numéro, est-ce qu'il existe ?
   if ( !boatExists($boat) ) {
     echo "<h4>You should not do that... your IP : " . $_SERVER["REMOTE_ADDR"] . "</h4>";
     exit;
   } else {
   // On a un bateau qui existe, on est content, on instancie l'objet
      $usersObj = new fullUsers($boat);
      if ( $usersObj->users->engaged == $race ) {

           echo "<h4>Bateau : " . $boat . " (" . $usersObj->users->boatname . "), skipper=" . $usersObj->users->username . " (email=". $usersObj->users->email. ")"; 

           echo "<input type=button value=changer onClick=\"document.location='". $_SERVER['PHP_SELF'] .
                  "?action=".$action.
                  "&race=".$race.
                  "'\">";
           echo "</h4>";
       } else {
           // Le bateau qui avait été choisi n'est pas dans la course choisie...
           $boat="";
       }
   }
}
if ( $race != "" && $boat == "" ) {
   // On n'a pas de bateau, on propose la liste
   $queryusers = "SELECT idusers, username, boatname  FROM users ";
   if ( $race != "" ) {
        $queryusers = $queryusers . " where engaged=$race ";
   }
   $queryusers = $queryusers . " order by idusers;";

   $resultusers = mysql_db_query(DBNAME,$queryusers) or die("Query [$queryusers] failed \n");

   $select_list="<option value=\"#\">--- CHOISIR ---</option>";
   while ( $row = mysql_fetch_assoc($resultusers)) {
           $select_list = $select_list . "<option value=\"". 
                                      $_SERVER['PHP_SELF'] . 
              "?race=".$race.
              "&action=".$action . 
              "&boat=".$row[idusers] . "\"";
           if ( $boat == $row[idusers] ) $select_list = $select_list . " selected ";
           $select_list = $select_list . ">". $row[idusers] . " - skipper=" . $row[username] ."</option>\n";
     
   }
   echo "<h4>Bateau : " ; 
   echo "<select name=\"boat\" " ;
   echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
   echo $select_list . "</select>";
   echo "</h4>";
   // Si on n'a pas de bateau, on sort
   exit;

}

// On ne va pas plus loin si bateau ou course inconnu
if ( $race == "" || $boat == "" ) exit;

// Y a t'il une action fournie ?
$URL="\"document.location='". $_SERVER['PHP_SELF'] .
                                "?boat=".$boat .
                                "&race=".$race.
                                "&action=".$action.
                                "'\">";
// Réalisation de l'action si "do = yes"..
if ( $do == "yes" ) {
    echo "Mise a jour en cours...";
    switch ($action) {
        case "unlock_boat":
       $query = "update users " ;
             if ( quote_smart($_REQUEST['lock']) ) {
                  $query .= " set releasetime = " . ( time() + 3600 ); 
             } else {
                  $query .= " set releasetime =0 " ; 
             }

       $query .="     where idusers = " .  $boat  . 
          "     and engaged   = " .  $race  .
          "    ;";
             $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
       $action_tracking = "UNLOCK boat for user $boat in race $race";

       break;
        case "maj_nextwp":
             $nwp=quote_smart($_REQUEST['nwp']);
       $query = "update users set nextwaypoint= " .  $nwp . 
               "     where idusers = " .  $boat . 
          "     and engaged   = " .  $race .
          "    ;";
             $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
       $action_tracking = "UPDATE nextwaypoint ($nwp) for user $boat in race $race";

       break;
        case "maj_position":
             $longitude=quote_smart($_REQUEST['targetlong']);
             $latitude=quote_smart($_REQUEST['targetlat']);
       $query = "insert into positions (time, `long`, `lat`, idusers, race) " . 
                             "values   (" . 
                      time() . ", " .
                      $longitude*1000 . ", " .
                      $latitude*1000  . ", " .
                      $boat   . ", " .
                      $race      . 
           "                                            );";
             $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
       $action_tracking = "UPDATE coords (Long=$longitude,Lat=$latitude) for user $boat in race $race";

       break;
        case "reset_pass":
             $newpass=quote_smart($_REQUEST['newpass']);
       $query = "update users set password= '" .  $newpass . "'" .
               "     where idusers = " .  $boat . 
          "     and engaged   = " .  $race .
          "    ;";
             $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
       $action_tracking = "UPDATE password (newpass=*********) for user $boat in race $race";
             break;
        case "reset_username":
             $newusern=quote_smart($_REQUEST['newusern']);
       $query = "update users set username= '" .  $newusern . "'" .
               "     where idusers = " .  $boat . 
          "     and engaged   = " .  $race .
          "    ;";
             $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
       $action_tracking = "UPDATE username (username=$newusern) for user $boat in race $race";
             break;
  default:
    }
    echo "<B>OK<B><br>";
    echo "<input type=button value=\"Raffraichir la page\" onClick=$URL";


    //tracking...
    $query_tracking = "insert into admin_tasks (time, admin, action) " . 
                      " values (" . time() . ",'" . $pseudo . "','" . $action_tracking . "')";
    $result = mysql_db_query(DBNAME,$query_tracking) or die("Query [$query_tracking] failed \n");

    exit;
}


// Les actions valides laissent passer, si aucune action est choisie, on présente la liste et on s'arrête là
$URL="\"document.location='". $_SERVER['PHP_SELF'] .
                                "?boat=".$boat .
                                "&race=".$race.
                                "&action=".$action.
        "&do=yes".
                                "'\">";
echo "<h4>Action : " . $action ; 
echo "<form name=coordonnees action=". $_SERVER['PHP_SELF'] . ">";
     echo "<input type=hidden name=action value=".$action.  ">";
     echo "<input type=hidden name=boat value=".$boat .  ">";
     echo "<input type=hidden name=race value=".$race. ">";
     echo "<input type=hidden name=do value=yes>";

switch ($action) {
        case "unlock_boat":
       echo "<hr>Bloquer le bateau : <input type=checkbox name=lock ";
             if ( $usersObj->users->releasetime > time() ) {
      echo "checked ";
             }
             echo "><br>";

       break;
        case "maj_nextwp":
       echo "<hr>Prochaine marque : <input type=text name=nwp value=" . $usersObj->users->nwp . "><br>";

       break;
        case "maj_position":
       echo "<hr>";
       echo "<table border=0>
             <tr>
           <td>&nbsp;</td><td>Millidegres</td><td>Deg/Min/Sec</td>
       </tr>";

       echo "<tr>
            <td align=right>Lon : </td>";
       echo "<td><input type=text name=targetlong onKeyup=\"convertdmslong();\" value=" . $usersObj->lastPositions->long/1000 . "></td>";
//       echo "<td><input type=text name=longdms disabled value=" . $usersObj->lastPositions->long . "></td>";
       echo "<td><input type=button  class=\"blue\" name=\"longdms\"></td>";
       echo "</tr>";
       echo "<tr>
            <td align=right>Lat : </td>";
       echo "<td><input type=text name=targetlat onKeyup=\"convertdmslat();\" value=" . $usersObj->lastPositions->lat/1000 . "></td>";
//       echo "<td><input type=text name=latdms disabled value=" . $usersObj->lastPositions->long . "></td>";
       echo "<td><input type=button  class=\"blue\" name=\"latdms\"></td>";
       echo "</tr>";
       echo "</table>";

       break;
        case "reset_pass":
       echo "<hr>Mot de passe : <input type=text name=newpass value=" . $usersObj->users->password . "><br>";

             break;
        case "reset_username":
       echo "<hr>Nouveau nom d'utilisateur : <input type=text name=newusern value=" . $usersObj->users->username . "><br>";

             break;
  default:
    // Choix de l'action à réaliser
                $actions=array("maj_nextwp","unlock_boat","maj_position","reset_pass","reset_username");
                $select_list="<option value=\"#\">--- CHOISIR ---</option>";
                foreach ($actions as $a) {
                    $select_list = $select_list . "<option value=\"". 
                                      $_SERVER['PHP_SELF'] . 
              "?race=".$race.
              "&boat=".$boat.
              "&action=" . 
              $a . "\"";
                    if ( $action == $a ) $select_list = $select_list . " selected ";
                    $select_list = $select_list . ">". $a ."</option>\n";
                }
    echo "<select name=\"action\" " ;
    echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
    echo $select_list . "</select>";
    echo "</h4>";
    // S'il n'y a pas d'action choisie, on s'arrête là.
    exit;
}
  
    echo "<input type=button value=\"Changer d'action\" onClick=\"document.location='". $_SERVER['PHP_SELF'] .
              "?boat=".$boat .  "&race=".$race. "'\">";
  echo "<input type=submit value=\"Juste fais le !\" >";
echo "</form>";
echo "</h4>";





