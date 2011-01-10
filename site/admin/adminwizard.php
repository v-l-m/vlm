<?php
/*
** Page Admin, ré-entrante.
** Permet de demander et d'effectuer des tâches d'administration sur VLM.

- une page d'admin refaite (une URL en fait, appelable avec login/password de chacun de "nous", vérifiant qu'on est admin (champ en plus dans la table des utilisateurs), et des arguments : pseudo=login&password=password ...
   + * boat=xx&action=valide_wp&NWP=xx => valide un WP en mettant à jour le numéro duprochain WP pour un bateau donné.
   + * boat=xx&action=maj_position&LONG=xxx&LAT=xxx => positionne un bateau aux coordonnées indiquées.
   + * boat=xx&action=reset_username&USN=xxxxxxx => met à jour le nom d'utilisateur
*/
$PAGETITLE = "User Admin Wizard (Old admin interface)";
include("htmlstart.php"); // This will die if not logged or not admin.

// Les paramètres

$action = get_cgi_var('action');
$race = get_cgi_var('race');
$boat = get_cgi_var('boat');
$do = get_cgi_var('do');


// Y a t'il une course choisie ?
// On n'a pas de course, on propose la liste
if ( $race == "" ) {
   $query = "SELECT idraces, racename  FROM races where started >=0 ";
   $query = $query . " order by idraces;";

   $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n");

   $select_list="<option value=\"#\">--- CHOISIR ---</option>";
   while ( $row = mysql_fetch_assoc($result)) {
           $select_list = $select_list . "<option value=\"". 
                                      $_SERVER['PHP_SELF'] . 
              "?boat=".$boat.
              "&amp;action=".$action . 
              "&amp;race=".$row['idraces'] . "\"";
           if ( $boat == $row['idraces'] ) $select_list = $select_list . " selected ";
           $select_list = $select_list . ">". $row['idraces'] . " - " . $row['racename'] ."</option>\n";
     
   }
   echo "<h4>No de Course : " .$race . "</h4>";
   echo "<select name=\"race\" " ;
   echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
   echo $select_list . "</select>";
} else {
   // On a un numéro, est-ce qu'il existe ?
   if ( ! raceExists($race) ) {
     echo "<h4>You should not do that... your IP : " . getip() . "</h4>";
     exit;
   } else {
   // On a une course qui existe
      echo "<h4>Course : " . $race . "</h4>" ;

      echo "<input type=\"button\" value=\"changer\" onClick=\"document.location='". $_SERVER['PHP_SELF'] .
                  "?action=".$action.
                  "&amp;boat=".$boat.
                  "'\" />";
   }
}




// Y a t'il un bateau choisi ?
if ( $boat != "" ) {
   // On a un numéro, est-ce qu'il existe ?
   if ( !boatExists($boat) ) {
     echo "<h4>You should not do that... your IP : " . getip() . "</h4>";
     exit;
   } else {
   // On a un bateau qui existe, on est content, on instancie l'objet
      require_once('users.class.php');
      $usersObj = new fullUsers($boat);
      if ( $usersObj->users->engaged == $race ) {

           echo "<h4>Bateau : " . $boat . " (" . $usersObj->users->boatname . "), skipper=" . $usersObj->users->username . " (email=". $usersObj->users->email. ")</h4>\n"; 

           echo "<input type=\"button\" value=\"changer\" onClick=\"document.location='". $_SERVER['PHP_SELF'] .
                  "?action=".$action.
                  "&amp;race=".$race.
                  "'\" />";
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

   $resultusers = wrapper_mysql_db_query_writer($queryusers) or die("Query [$queryusers] failed \n");

   $select_list="<option value=\"#\">--- CHOISIR ---</option>";
   while ( $row = mysql_fetch_assoc($resultusers)) {
           $select_list = $select_list . "<option value=\"". 
                                      $_SERVER['PHP_SELF'] . 
              "?race=".$race.
              "&amp;action=".$action . 
              "&amp;boat=".$row['idusers'] . "\"";
           if ( $boat == $row['idusers'] ) $select_list = $select_list . " selected ";
           $select_list = $select_list . ">". $row['idusers'] . " - skipper=" . $row['username'] ."</option>\n";
     
   }
   echo "<h4>Bateau : </h4>\n" ; 
   echo "<select name=\"boat\" " ;
   echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
   echo $select_list . "</select>";
   // Si on n'a pas de bateau, on sort
   exit;

}

// On ne va pas plus loin si bateau ou course inconnu
if ( $race == "" || $boat == "" ) exit;

// Y a t'il une action fournie ?
$URL="\"document.location='". $_SERVER['PHP_SELF'] .
                                "?boat=".$boat .
                                "&amp;race=".$race.
                                "&amp;action=".$action.
                                "'\" />";
// Réalisation de l'action si "do = yes"..
if ( $do == "yes" ) {
    echo "Mise a jour en cours...";
    switch ($action) {
        case "unlock_boat":
            $query = "UPDATE users " ;
            if ( get_cgi_var('lock') ) {
                $querysgo = "SELECT coastpenalty FROM races WHERE idraces = ".$race;
                $resgo = wrapper_mysql_db_query_writer($querysgo) or die("Query [$query] failed \n");
                $row = mysql_fetch_assoc($resgo);
                $coastpenalty = $row['coastpenalty'];
                if ( intval(get_cgi_var('coastpenalty')) != $coastpenalty ) {
                    $coastpenalty = intval(get_cgi_var('coastpenalty'));
                }
                $reltime = time() + $coastpenalty;
                $action_tracking = "LOCK boat for user $boat in race $race for ".$coastpenalty;
            } else {
                $reltime = 0;
                $action_tracking = "UNLOCK boat for user $boat in race $race";
            }
            $query .= " SET releasetime = " . ( $reltime ); 
            $query .= " WHERE idusers = " .  $boat  . 
                      " AND engaged   = " .  $race  .
                      " ;";
            $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n");
            break;
        case "maj_nextwp":
             $nwp = get_cgi_var('nwp');
       $query = "update users set nextwaypoint= " .  $nwp . 
               "     where idusers = " .  $boat . 
          "     and engaged   = " .  $race .
          "    ;";
             $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n");
//       $action_tracking = "UPDATE nextwaypoint ($nwp) for user $boat in race $race";
              $action_tracking = Array("operation" => "update", "tab" => "users", "col" => "nextwaypoint", "rowkey" => $boat, "newval" => $nwp);

       break;
        case "maj_position":
             $longitude = get_cgi_var('targetlong');
             $latitude  = get_cgi_var('targetlat');
       $query = "insert into positions (time, `long`, `lat`, idusers, race) " . 
                             "values   (" . 
                      time() . ", " .
                      $longitude*1000 . ", " .
                      $latitude*1000  . ", " .
                      $boat   . ", " .
                      $race      . 
           "                                            );";
             $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n");
            $action_tracking = "UPDATE coords (Lat=$latitude,Long=$longitude) for user $boat in race $race";

       break;
        case "reset_pass": 
            $newpass = get_cgi_var('newpass'); 
            $query = "update users set password= '" .  $newpass . "'" . 
                     "     where idusers = " .  $boat .  
                     "     and engaged   = " .  $race . 
                     "    ;"; 
            $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n"); 
            $action_tracking = Array("operation" => "update", "tab" => "users", "col" => "password", "rowkey" => $boat, "newval" => "********"); 
            break; 
        case "reset_username":
            $newusern = get_cgi_var('newusern');
            $query = "update users set username= '" .  addslashes($newusern) . "'" .
                     "     where idusers = " .  $boat . 
                     "     and engaged   = " .  $race .
                     "    ;";
            $result = wrapper_mysql_db_query_writer($query) or die("Query [$query] failed \n");
            $action_tracking = Array("operation" => "update", "tab" => "users", "col" => "username", "rowkey" => $boat, "newval" => $newusern);
            break;
        default:
    }
    echo "<b>OK<b><br />";
    echo "<input type=\"button\" value=\"Raffraichir la page\" onClick=$URL";


    //tracking...
    if (is_array($action_tracking) ) {
        insertAdminChangelog($action_tracking);
    } else {
        insertAdminChangelog(Array("operation" => $action_tracking) );
    }
    
    exit;
}


// Les actions valides laissent passer, si aucune action est choisie, on présente la liste et on s'arrête là
$URL="\"document.location='". $_SERVER['PHP_SELF'] .
                                "?boat=".$boat .
                                "&amp;race=".$race.
                                "&amp;action=".$action.
        "&amp;do=yes".
                                "'\" />";
echo "<h4>Action : " . $action ; 
echo "<form name=\"coordonnees\" action=\"". $_SERVER['PHP_SELF'] . "\"/>";
     echo "<input type=\"hidden\" name=\"action\" value=\"".$action.  "\"/>";
     echo "<input type=\"hidden\" name=\"boat\" value=\"".$boat .  "\"/>";
     echo "<input type=\"hidden\" name=\"race\" value=\"".$race. "\"/>";
     echo "<input type=\"hidden\" name=\"do\" value=\"yes\"/>";

switch ($action) {
    case "unlock_boat":
        $querysgo = "SELECT coastpenalty FROM races WHERE idraces = ".$race;
        $resgo = wrapper_mysql_db_query_writer($querysgo) or die("Query [$query] failed \n");
        $row = mysql_fetch_assoc($resgo);
        $coastpenalty = $row['coastpenalty'];
        echo "<hr />Bloquer le bateau (if checked) : <input type=\"checkbox\" name=\"lock\" ";
        if ( $usersObj->users->releasetime > time() ) {
            echo "checked=\"checked\" ";
        }
        echo " />&nbsp;pour&nbsp;<input type=\"text\" name=\"coastpenalty\" value=\"".intval($coastpenalty)."\" />&nbsp;secondes.<br />";
        break;
    case "maj_nextwp":
        echo "<hr />Prochaine marque : <input type=\"text\" name=\"nwp\" value=\"" . $usersObj->users->nwp . "\"/><br />";
        break;
    case "maj_position":
        echo "<hr />";
        echo "<table border=\"0\"><tr><td>&nbsp;</td><td>Millidegres</td><td>Deg/Min/Sec</td>
       </tr>";

        echo "<tr>";
        echo "<td align=\"right\">Lat : </td>";
        echo "<td><input type=\"text\" name=\"targetlat\" onKeyup=\"convertdmslat();\" value=\"" . $usersObj->lastPositions->lat/1000 . "\"/></td>";
        echo "<td><input type=\"button\"  class=\"blue\" name=\"latdms\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td align=\"right\">Lon : </td>";
        echo "<td><input type=\"text\" name=\"targetlong\" onKeyup=\"convertdmslong();\" value=\"" . $usersObj->lastPositions->long/1000 . "\"/></td>";
        echo "<td><input type=\"button\" class=\"blue\" name=\"longdms\"></td>";
        echo "</tr>";
        echo "</table>";
        break;
    case "reset_pass": 
        echo "<hr />Mot de passe : <input type=\"text\" name=\"newpass\" value=\"" . $usersObj->users->password . "\" /><br />"; 
        break; 
    case "reset_username":
        echo "<hr />Nouveau pseudo de bateau : <input type=\"text\" name=\"newusern\" value=\"" . $usersObj->users->username . "\"/><br />";
        break;
    default:
        // Choix de l'action à réaliser
        $actions=array("maj_nextwp","unlock_boat","maj_position","reset_pass","reset_username"); 
        $select_list="<option value=\"#\">--- CHOISIR ---</option>";
        foreach ($actions as $a) {
            $select_list = $select_list . "<option value=\"". 
                $_SERVER['PHP_SELF'] . 
                "?race=".$race.
                "&amp;boat=".$boat.
                "&amp;action=" . 
                $a . "\"";
            if ( $action == $a ) $select_list = $select_list . " selected=\"selected\" ";
            $select_list = $select_list . ">". $a ."</option>\n";
        }
        echo "<select name=\"action\" " ;
        echo "onChange=\"document.location=this.options[this.selectedIndex].value\">";
        echo $select_list . "</select>";
        echo "</h4>";
        // S'il n'y a pas d'action choisie, on s'arrête là.
        exit;
    }
  
    echo "<input type=\"button\" value=\"Changer d'action\" onClick=\"document.location='". $_SERVER['PHP_SELF'] .
              "?boat=".$boat .  "&amp;race=".$race. "'\"/>";
    echo "<input type=\"submit\" value=\"Juste fais le !\" />";
    echo "</form>";
    include("htmlend.php");
?>
