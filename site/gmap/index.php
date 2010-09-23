<?php
/*
 * deux accès possible :
 * - par index.php?idraces=idraces
 *     renvoie une page html classique, avec une gmap et la lsite des joueurs pour une regate
 * - par index.php?idusers=idraces&type=ajax (utilisé par le script de index.php?idraces=idraces)
 *     renvoie un code javscript évaluer par index.php?idraces=idraces
 *
 * Installation :
 * En principe le code et autonome, il faut changer l'accès à la base de données, et la
 * cleft gmap dans le fichier form.html.
 *
 * Amélioration :
 * Il y a quelques améliorations à effectuer, il n'y a pas de retour utilisateur sur
 * les requêtes ajax envoyées.
 */

// A change le lien vers la base de donnée VLM
$localDBPages=array();
include_once "config.php";

mysql_connect(DBSLAVESERVER, DBSLAVEUSER, DBSLAVEPASSWORD) or die("Impossible de se connecter : " . mysql_error());
mysql_select_db(DBNAME);

$longGmap;
$latGmap;

// parse le parametre idusers
$idusers = quote_smart($_GET["idusers"]);
$idraces = quote_smart($_GET["idraces"]);
$type = quote_smart($_GET["type"]);
$riquette = quote_smart($_GET["riq"]);

if (!defined(VLMGMAPKEY)) {
    define("VLMGMAPKEY", "ABQIAAAAU9L35N6EdAtdkT4Cb2abDRR9fpxOiyHPEX_8YzC8CNXvq83W-hRDmTj4GD1F8DLKiaJ97BAfcB5i7w");
    }

switch ($type) {

/*
 * Gestion des requetes ajax
 */
  case "ajax":
    header("content-type: text/javascript");
    // si idusers est un numerique
    if (is_numeric($idusers) and is_numeric($idraces) ) {
                        $sql = "SELECT `bobegin`, `boend` FROM `races`
                                                          WHERE `idraces` = $idraces ";
      $posbo = mysql_query($sql);
      $row = mysql_fetch_array($posbo, MYSQL_ASSOC);
                        $bobegin = $row['bobegin'];
                        $boend   = $row['boend'];
                        $now = time();

                        if ( $bobegin < $now && $now < $boend ) {
           $time_clause = " AND `time` < $bobegin ";
                        }

      switch ($riquette) {
        // Renvoie la position d'un joueurs
                                
        case "pos":
          // recupere les lat et long du user à sa dernière position
          $sql = "SELECT `long` , `lat` 
                  FROM `positions` 
                  WHERE `idusers` = $idusers 
                  AND `race` = $idraces ";
          $sql .= $time_clause;
                
          $sql .= "      ORDER BY `time` DESC LIMIT 1;";
                  //AND `time` < UNIX_TIMESTAMP()-60*60 
    
          $posplayer = mysql_query($sql);
          $row = mysql_fetch_array($posplayer, MYSQL_ASSOC);
          
          $longGmap = $row['long']/1000;
          $latGmap  = $row['lat']/1000;
    
          // recupere les infos du user
          $sql = "SELECT username, boatname 
              FROM `users` 
              WHERE `idusers` = $idusers 
              AND `engaged` = $idraces;";
          $infoplayer = mysql_query($sql);
          $row = mysql_fetch_array($infoplayer, MYSQL_ASSOC);
          $username = $row['username'];
          $boatname  = $row['boatname'];
          $varmarker = "marker";
          
          $strResult = "";
          $strResult .= "var $varmarker = new GMarker(new GLatLng($latGmap,$longGmap));\n";
          $strResult .= "GEvent.addListener($varmarker, 'click', function() {\n";
          $strResult .= "marker.openInfoWindowHtml('<b>$username</b>, sur ".addslashes($boatname)."');\n";
          $strResult .= "});\n";
          $strResult .= "map.addOverlay($varmarker);\n";
          // Affiche le code java script
          echo $strResult;
          break;
        // Renvoie la trajectoire d'un joueurs
        case "trj":
          // recupere les lat et long du user sur les dernières 24h
          $sql = "SELECT `long` , `lat` 
              FROM `positions` 
              WHERE `idusers` = $idusers 
              AND `race` = $idraces 
              AND `time` > UNIX_TIMESTAMP()-24*60*60 ";
           $sql .= $time_clause;
          $sql .= "  ORDER BY `time` DESC;";
              //AND `time` between UNIX_TIMESTAMP()-24*60*60 and UNIX_TIMESTAMP()-60*60
          
          $posplayer = mysql_query($sql);
          $numRow = mysql_num_rows($posplayer);
          $points = array();
          
          while ($row = mysql_fetch_array($posplayer, MYSQL_ASSOC)) {
            $long = $row['long']/1000;
            $lat = $row['lat']/1000;
            $point = "new GLatLng($lat,$long)";
            array_push($points, $point);
          }
          
          // formate le code JavaScript
          $strResult = "";
          $strResult .= "var polyline = new GPolyline([\n";
          
          for ($i=0;$i<$numRow;$i++) {
            $strResult .= $points[$i].",\n";
          }
          
          $strResult .= "], '#FF0000', 3);\n";
          $strResult .= "map.addOverlay(polyline);";
          
          echo $strResult;
          //print_r($points);
          break;
      }
    } else {
      echo "alert('une erreur est survenue')";
    }
    break;


/*
 * Affiche la liste de joueurs
 */
  case "race":
    header("content-type: text/html; charset=UTF-8");
    if (is_numeric($idraces)) {
      // recupere les idusers et les username pour une course
      $sql = "SELECT idusers, username, engaged
          FROM users
          WHERE engaged = $idraces
          ORDER BY username;";
      $listplayers = mysql_query($sql);
      $strListPlayes = "";
      // construit le tableau html
      while ($row = mysql_fetch_array($listplayers, MYSQL_ASSOC)) {
        $strListPlayers .= "<a href ='javascript:";
        $strListPlayers .= "getUserLine(".$row['idusers'].",".$row['engaged'].");";
        $strListPlayers .= "getUserMarker(".$row['idusers'].",".$row['engaged'].");";
        $strListPlayers .= "'>";
        $strListPlayers .= $row['username']." - ".$row['idusers'];
        $strListPlayers .= "</a>";
        $strListPlayers .= "<br>";
      }
      $html = file_get_contents("form.html");
      $html= str_replace('###LISTPLAYER###',$strListPlayers,$html);
      $html= str_replace('###VLMGMAPKEY###', VLMGMAPKEY, $html);
          
      echo $html;
    } else {
      echo 'Mauvais num&eacute;ro de course.';
    }
    break;
  default:
    header("content-type: text/html; charset=UTF-8");
      // recupere les courses en cours
      $sql = "SELECT idraces, racename, deptime
              FROM `races`
              WHERE started >= 0
              ORDER BY deptime DESC;";
      $listplayers = mysql_query($sql);
      $strListRaces = "";
      // construit le tableau html
      while ($row = mysql_fetch_array($listplayers, MYSQL_ASSOC)) {
        $strListRaces .= "<tr>";

        $daytime = getdate($row['deptime']);
        $year = $daytime['year'];
        $month = $daytime['mon'];
        $day = $daytime['mday'];
        $hours = $daytime['hours'];
        $mintues = $daytime['minutes'];
        $deptimeStr = "$day/$month/$year $hours:$mintues";

        $strListRaces .= "<td>";
        $strListRaces .= "$deptimeStr";
        $strListRaces .= "</td>";
        $strListRaces .= "<td>";
        $strListRaces .= "<a href ='index.php?type=race&idraces=".$row['idraces']."';>";
        $strListRaces .= $row['racename'];
        $strListRaces .= "</a>";
        $strListRaces .= "<td>";
        $strListRaces .= "</tr>";
      }
      $html = file_get_contents("formCourse.html");
      $html= str_replace('###LISTRACES###',$strListRaces,$html);
      echo $html;
    break;
}

?>
