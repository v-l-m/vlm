<?php
    $page_start_time=microtime(1);

    session_start();
    include_once("config.php");
    include_once("players.class.php");
    include_once("users.class.php");
    include_once("functions.php");
    
    header("Content-type: text/html; charset=utf-8");

    ob_start();
    //all GET and POST variables
    $pseudo = get_cgi_var('pseudo');
    $password = get_cgi_var('password');
    $class = get_cgi_var('class');
    $blocnote = get_cgi_var('blocnote');
    if (preg_match("/^http/i", $blocnote) || preg_match("/:\/\//i", $blocnote)) {
        $blocnote="BAD NOTEPAD (code inclusion (url, &lt;&gt; characters, ...) is forbidden)";
    }
    $theme = get_cgi_var('theme');
    $country = get_cgi_var('country');
    $hidepos = get_cgi_var('hidepos');
    //#658 $invisible = get_cgi_var('invisible');
    $type  = get_cgi_var('type');
    $action = get_cgi_var('action');
    $boatname = get_cgi_var('boatname');
    $color = get_cgi_var('color');
    $idusers = get_cgi_var('idusers');
    $idraces = get_cgi_var('idraces');
    $submittype = get_cgi_var('submittype');
    $pilotmode = get_cgi_var('pilotmode');
    $pilotparameter = get_cgi_var('pilotparameter');
    $targetlat=htmlentities(get_cgi_var('targetlat'));
    $targetlong=htmlentities(get_cgi_var('targetlong'));
    $targetandhdg=htmlentities(get_cgi_var('targetandhdg', -1));
    $boatheading = get_cgi_var('boatheading');

    $msg="";
    //need to be at start because modify header info
    if ($type == "logout")
    {
        logout();
        $msg = getLocalizedString("loggedout");
    }

    //if password and pseudo try to connect
    if ($type=="login" && $pseudo!="" && $password!="") {
        //FIXME : check this
        $pseudo = htmlentities($pseudo);
        $password = htmlentities($password);
        $player = new players(0, $pseudo);
        if (!$player->error_status && $player->checkPassword($password)) {
            //login player should be a method of playerobject ?
            loginPlayerDefaultBoat($player->idplayers);
        } else {
            $error_auth = True;
            $msg = getLocalizedString("authfailed")."<br /><a href=\"reset_password.php\">".getLocalizedString("Lost your password ?")."</a>";
        }
    }

    if ($type=="connectas" && isPlayerLoggedIn()) {
          //FIXME !
         $player = getLoggedPlayerObject();
         $newuid = $_POST['boatid'];
         $boatlist = $player->getManageableBoatIdList();
         if (in_array($newuid, $boatlist)) {
            $defaultuser = getUserObject($newuid);
            loginPlayer($defaultuser->idusers, $defaultuser->username, $player->idplayers, $player->playername);
         }
    }
    
    if ($type == "savemywp" && isPlayerLoggedIn())
    {
        $users = new fullUsers(getLoginId());
        $users->updateTarget($targetlat, $targetlong,  $targetandhdg);
    }

    if ($type == "change" && isPlayerLoggedIn()) {
        $users = new fullUsers(getLoginId());
        $users->users->boatname = $boatname;
        $users->users->color = $color;
        //FIXME : no escaping ?
        $users->users->blocnote = $blocnote;
        $users->users->country = $country;
        $users->users->theme = $theme;
        //on en profite pour nettoyer la session. le theme sera rechargé au premier appel de getTheme()
        unset($_SESSION['theme']);
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
    
    if ($type == "unsubscribe" && isPlayerLoggedIn() && getLoggedPlayerObject()->isOwner(getLoginId()))
    {
        $users = new fullUsers(getLoginId());
        $idraces_unsubscribe = quote_smart(get_cgi_var('idraces_unsubscribe'));
        if ($users->users->engaged == $idraces_unsubscribe) $users->setABD();
    }
    
    if ($type == "subscribe" && isPlayerLoggedIn())
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
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo getCurrentLang() ?>">

 <head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <meta name="verify-v1" content="m1CKSCjEAPxc1+D27ol3vqe87xq3L+LlTNK0pmV78Ns=" />
   <link rel="stylesheet" type="text/css" href="/style/<?php echo getTheme(); ?>/style.css" />
   <link rel="icon" type="image/png" href="/images/site/favicon.png" />
   <link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/races.rss2.php" />
   <link rel="alternate" type="text/ical" title="ICAL" href="/feed/races.ical.php" />

<?php
    if (VLM_XMPP_ON) {
?>
   <link rel="stylesheet" type="text/css" media="screen" href="/externals/conversejs/css/converse.min.css">
   <script src="/externals/conversejs/builds/converse.min.js"></script>
<?php
    }
?>
   <title>Virtual Loup-De-Mer </title>

<?php
    // finds current file name
    $CurrentFile = basename($_SERVER["PHP_SELF"]);

    //echo "current file =".$CurrentFile."SCrIPT name = ".$_SERVER["PHP_SELF"]."\n";
    //little trick : if update_angle page, display and then redirect to myboat
    if ($CurrentFile == "update_angle.php" ) {
        echo "  <meta http-equiv=\"refresh\" content=\"0; url=myboat.php\" />";
    } elseif ($CurrentFile == "myboat.php" && isPlayerLoggedIn() && is_null(getLoginId())) {
        echo "  <meta http-equiv=\"refresh\" content=\"0; url=palmares.php?type=player\" />";        
    } elseif ($CurrentFile == "myboat.php" ) {
        echo "  <meta http-equiv=\"refresh\" content=\"1200\" />";
    } //No refresh for other pages

?>

  <script type="text/javascript" src="/scripts/popup.js"></script>
  <script type="text/javascript" src="/scripts/dms.js"></script>
  <script type="text/javascript" src="/scripts/hashtable.js"></script>

  <script type="text/javascript" src="/externals/lightbox/js/prototype.js"></script>
  <script type="text/javascript" src="/externals/lightbox/js/scriptaculous.js?load=effects,builder"></script>
  <script type="text/javascript" src="/externals/lightbox/js/lightbox.js"></script>
  <link rel="stylesheet" href="/externals/lightbox/css/lightbox.css" type="text/css" media="screen" />
  <link rel="apple-touch-icon" sizes="57x57" href="/images/logos/logovlm-57x57.png"/>
  <link rel="apple-touch-icon" sizes="114x114" href="/images/logos/logovlm-114x114.png"/>

  <script type="text/javascript" src="/externals/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<!-- Google+ page -->
<link href="https://plus.google.com/u/0/b/110426006609687431139/" rel="publisher" />
 </head>

 <body>
   <!-- onLoad connot be in body so we create a false div not to  be displayed -->
   <!-- TODO put expert in cookies-->

   <div id="baneer">
       <div id="baneerleftbox"></div>
       <div id="baneercenterbox"></div>
       <div id="baneerrightbox">
       <?php echo getLocalizedString("sponsor"); ?>
       <a href="http://www.actilis.net/" target="_actilis"><img src="/images/site/actilis.png" alt="Actilis" /></a>
       </div>
   </div>
   <div class="OldSite">
    <H1 ><?php echo getLocalizedString("OldSiteDeprecationWarning"); ?></H1>
   </div>
   
   <div id="contenaire"> 

   <div id="leftcolumn">
<?php
    if ($msg != "" and !is_null($msg) ) {
?>
       <div id="msgbox" class="leftbox">      <?php echo $msg; ?></div>
<?php
    }
?>
<?php
    if (isPlayerLoggedIn() && !isLoggedIn()) {
        $player = getLoggedPlayerObject();
        $defb = $player->getDefaultBoat();
        if ($defb >0) {
            $defaultuser = getUserObject($defb);
            loginPlayer($defaultuser->idusers, $defaultuser->username, $player->idplayers, $player->playername);
        }   
    }

    if (!isLoggedIn() && !isPlayerLoggedIn()) //if not connected
    {
?>
        <div id="loginbox" class="leftbox">
            <form method="post" action="myboat.php">
                <?php echo getLocalizedString("email"); ?> : <input size="15" maxlength="64" name="pseudo" /><br />
                <?php echo getLocalizedString("password"); ?> : <input size="15" maxlength="15" type="password" name="password" /><br />
                <input type="hidden" name="type" value="login" />
                <p class="leftbutton">
                    <input type="submit" value="<?php echo getLocalizedString("login"); ?>" />
                </p>
            </form>
        </div> <!-- fin div loginbox-->
        <div id="pleasecreatebox" class="leftbox">
<?php
            printf (getLocalizedString("pleasecreate"), "<a href=\"create_player.php\">", "</a>");
?>
        </div> <!-- fin div pleasecreatebox-->
<?php
    } else if (isPlayerLoggedIn() && !isLoggedIn()) {    
?>
        <div id="pleasecreatebox" class="leftbox">
<?php
                $player = new players(getPlayerId());
                echo $player->htmlPlayername()."<br />\n";

            echo "<a href=\"create_boat.php\">".getLocalizedString("Please create a boat !")."</a>";
?>
        </div> <!-- fin div pleasecreatebox-->
        <div id="logoutbox" class="leftbox">
            <form method="post" action="index.php" onsubmit="sendLogout();">
                <input type="hidden" name="type" value="logout" />
                <p class="leftbutton">
                    <input type="submit" value="<?php echo getLocalizedString("logout"); ?>" />
                </p>
            </form>
        </div> <!-- fin div logoutbox-->

<?php
    } else { //if loggedin
?>
        <div id="loggedbox" class="leftbox">
<?php
            if (isPlayerLoggedIn()) {
                $player = new players(getPlayerId());
                echo $player->htmlPlayername()."<br />\n";
                //FIXME : factorise
                $boatlist = $player->getManageableBoatIdList();
                echo "<form method=\"post\" action=\"myboat.php\">";
                echo "<input type=\"hidden\" name=\"type\" value=\"connectas\" />";
                echo "<select name=\"boatid\" onchange=\"submit();\">";
                foreach ($boatlist as $idb) {
                    $u = getUserObject($idb);
                    echo "<option value=\"$idb\"";
                    if ($idb == getLoginId()) {
                        echo " selected";
                    }
                    echo ">";
                    if (!($u->engaged > 0)) echo '_';
                    echo $u->username;
                    echo "</option>";
                }
                echo "</select>";
                echo "</form>";
                    
            } else {

                //disp login name, infos
                echo "<a href=\"palmares.php?type=user&amp;idusers=" . getLoginId() . "\">" .
                     " (". getLoginId() . ") <u>" . getLoginName() . "</u></a>\n";
            }
?>
        </div> <!-- fin div loggedbox-->
        <div id="logoutbox" class="leftbox">
            <form method="post" action="index.php" onsubmit="sendLogout();">
                <input type="hidden" name="type" value="logout" />
                <p class="leftbutton">
                    <input type="submit" value="<?php echo getLocalizedString("logout"); ?>" />
                </p>
            </form>
        </div> <!-- fin div logoutbox-->
<?php
    }
?>
        <div id="pagesbox" class="leftbox">
            <?php if (isLoggedIn()) { ?>
                <a href="myboat.php"><?php echo getLocalizedString("control"); ?></a>
                <a href="mappref.php"><?php echo getLocalizedString("mymap"); ?></a>
                <a href="edit_boatprefs.php"><?php echo getLocalizedString("choose"); ?></a>
            <?php } ?>
            <?php if (isPlayerLoggedIn()) {
                echo "<a href=\"palmares.php?type=player&amp;idplayers=".getPlayerId()."\">".getLocalizedString("Profile")."</a>";
                }
            ?>
        </div> <!-- fin div pagesbox -->
        <div id="langbox" class="leftbox">
<?php
            $langs= getLocalizedString(null);
            foreach ($langs as $lg) {
                if ( isset($_SERVER['QUERY_STRING'])) {
                    $querystring = $_SERVER['QUERY_STRING']."&";
                }
                else
                {
                    $querystring = "";
                }
                $querystring .= "lang=$lg";
                echo "<a href=\"".$_SERVER['SCRIPT_NAME']."?$querystring\"><img src=\"/images/site/$lg.png\" title=\"".getLocalizedString('lang_version', $lg)."\" alt=\"".getLocalizedString('lang_version', $lg)."\" /></a>\n";
            }            
?>
        </div> <!-- fin div langbox-->
        <div id="menubox" class="leftbox">
            <a href="index.php"><?php echo getLocalizedString("home")?></a> 
<?php
                // Bouton "ADMIN" pour les administrateurs
                if ( isAdminLogged() ) {
                    echo "<a target=\"_admin\" href=\"/admin/\">Admin</a>";
                }
                //<a href="http://images.v-l-m.org/" target="_images">echo getLocalizedString("images"); </a> 
?>
            <a href="races.php"><?php echo getLocalizedString("races"); ?></a>
            <a href="calendar.php"><?php echo getLocalizedString("calendar"); ?></a>
            <?php if (isPlayerLoggedIn()) echo htmlRaceSpectatorLink(); ?>
        </div> <!-- fin div menubox-->
        <div id="rankingbox" class="leftbox">
            <a href="http://ranking.v-l-m.org/" target="_rankings"><?php echo getLocalizedString("ranking"); ?></a>
            <a href="/jvlm/?VLMIndex=Challenge1" target="_rankings"><?php echo getLocalizedString("VLMChallenge"); ?></a>
            <a href="/jvlm/?VLMIndex=1year1" target="_rankings"><?php echo getLocalizedString("VLMIndex"); ?></a>
<!--            <a href="<?php echo MOBILE_SERVER_URL; ?>" target="_mobile" title="<?php echo getLocalizedString("Interface for mobiles devices"); ?> ">
                <?php echo getLocalizedString("Mobiles"); ?>
            </a> 
-->
        </div> <!-- fin div rankingbox-->
        <div id="menucommunicationbox" class="leftbox">
            <a href="<?php echo FORUM_SERVER_URL; ?>" target="_forum"><?echo getLocalizedString("forum")?></a>
            <a href="#" title="<?echo getLocalizedString("tchat"); ?>" onClick="converse.rooms.open('<?echo VLM_NOTIFY_JABBER_MAIN; ?>');"><img src="/images/site/chaticon.png" /></a>
        </div> <!-- fin div menucommunicationbox -->
        <div id="docbox" class="leftbox">
            <a href="<?php echo DOC_SERVER_URL; ?>" target="_vlmwiki"><?php echo getLocalizedString("doc"); ?></a>
            <a href="<?echo DOC_SERVER_URL.'FAQ/'.getCurrentLang(); ?>" target="_vlmwiki"><?echo getLocalizedString("faq"); ?></a>
            <a href="<?echo DOC_SERVER_URL.'Chat/'.getCurrentLang(); ?>" target="_vlmwiki"><?echo getLocalizedString("tchat"); ?></a> 
            <a href="<?php echo TOOLS_SERVER_URL; ?>" target="_outils"><?php echo getLocalizedString("tools"); ?></a>            
            <a href="<?php echo DEV_SERVER_URL; ?>" target="_vlmdev"><?php echo getLocalizedString("dev"); ?></a>
            <a href="<?echo DOC_SERVER_URL.'About/'.getCurrentLang(); ?>" target="_vlmwiki"><?echo getLocalizedString("about"); ?></a>

        </div>
        <div id="weatherbox" class="leftbox">
<!--            <h3><?php echo getLocalizedString("weather"); ?></h3> -->
                <a href="<?php echo GRIB_SERVER_URL; ?>/" target="_meteodata"><?echo getLocalizedString("meteodata")?></a>
                <a href="<?php echo GRIB_TOOLS_URL; ?>" target="_meteotools"><?echo getLocalizedString("meteotools")?></a>
        </div>
        <div id="pubbox1" class="leftpubbox">
        </div>
        <div id="pubbox2" class="leftpubbox">
        </div>
        <div id="serverbox" class="leftbox">
            <?php
              echo "Srv = " . SERVER_NAME . ".";
              echo "<br />Version = ";  
              include("version.txt");
            ?>
        </div>
        <!-- Google+ medium icon -->
        <a href="https://plus.google.com/u/0/b/110426006609687431139/?prsrc=3" style="text-decoration:none;" target="_gplusvlm"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" alt="" style="border:0;width:32px;height:32px;" /></a>
        <!-- Facebook medium icon -->
        <a href="https://www.facebook.com/virtualloupmer" style="text-decoration:none;" target="_facebookvlm"><img src="/images/logos/facebook-32.png" alt="" style="border:0;width:32px;height:32px;" target="_fbvlm"/></a>
        <!-- Twitter medium icon -->
        <a href="https://twitter.com/#!/virtualloupmer" style="text-decoration:none;" target="_twittervlm"><img src="/images/logos/twitter-32.png" alt="" style="border:0;width:32px;height:32px;" /></a>


   </div>
<!--
end of firstcolumn
-->

   <div id="main">
<?php
if ( isset($error_auth) && $error_auth )
{
   include_once("includes/login_error.inc");
   include_once("includes/footer.inc");
   die();
}

if ( !isLoggedIn() && (in_array($_SERVER["PHP_SELF"], $restrictedPages) ) )
{
   echo "Access restricted\n<br />";
   include_once("includes/footer.inc");
   die();
}
?>
