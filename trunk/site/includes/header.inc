<?php

    $page_start_time=microtime(1);

    session_start();
    include_once("config.php");
    
    header("Content-type: text/html; charset=utf-8");

    ob_start();
    //all GET and POST variables
    $lang = getCurrentLang();
    $pseudo=quote_smart($_REQUEST['pseudo']);
    $password=quote_smart($_REQUEST['password']);
    $class=quote_smart($_REQUEST['class']);
    $email=quote_smart($_REQUEST['email']);
    $blocnote=quote_smart($_REQUEST['blocnote']);
    if (eregi("^http", $blocnote) || eregi("://", $blocnote)) {
        $blocnote="BAD NOTEPAD (code inclusion (url, &lt;&gt; characters, ...) is forbidden)";
    }
    $theme=quote_smart($_REQUEST['theme']);
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
        $msg = getLocalizedString("loggedout");
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
            $msg = getLocalizedString("loginexist");
        } else if (($pseudo == "") || ($password == "")) {
            $msg = getLocalizedString("loginempty");
        } else {
            $idu = createAccount($pseudo, $password, $email, $country);
            login($idu, $pseudo);
            $msg = getLocalizedString("accountcreated")." ".$pseudo." ".$password;
        }
    }

    //if password and pseudo try to connect
    if ($type=="login" && $pseudo!="" && $password!="") {
        //FIXME : check this
        $pseudo = htmlentities($pseudo);
        $password = htmlentities($password);
        if (  ($id = checkAccount($pseudo, $password)) != FALSE ) {
            //create session info
            login($id, $pseudo);
            //$msg = getLocalizedString("nowlogin");
        } else {
            include_once("players.class.php");
            $player = new players(0, $pseudo);
            if (!$player->error_status && $player->checkPassword($password)) {
                loginPlayer(1, "testfm", $player->idplayers, $player->playername);
            } else {
                $msg = getLocalizedString("authfailed");
            }
        }
    }

    if ($type == "savemywp")
    {
        $users = new fullUsers(getLoginId());
        $users->updateTarget($targetlat, $targetlong,  $targetandhdg);
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
    
    if ($type == "unsubscribe")
    {
        $users = new fullUsers(getLoginId());
        $idraces_unsubscribe = quote_smart($_REQUEST['idraces_unsubscribe']);
        if ($users->users->engaged == $idraces_unsubscribe) $users->setABD();
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
   <link rel="stylesheet" type="text/css" href="style/<?php echo getTheme(); ?>/style.css" />
   <link rel="icon" type="image/png" href="images/site/favicon.png" />
   <link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/races.rss2.php" />
   <link rel="alternate" type="text/ical" title="ICAL" href="/feed/races.ical.php" />
   <title>Virtual Loup-De-Mer </title>

<?php
    // finds current file name
    if(ereg("([^/?]+)(\?.*)?$",$_SERVER["PHP_SELF"] , $regs)) {
          $CurrentFile = $regs[1];
    } else {   // it should not be possible :-(
          $CurrentFile = '';
    }

    //echo "current file =".$CurrentFile."SCrIPT name = ".$_SERVER["PHP_SELF"]."\n";
    //little trick : if update_angle page, display and then redirect to myboat
    if ($CurrentFile == "update_angle.php" ) {
        echo "  <meta http-equiv=\"refresh\" content=\"0; url=myboat.php?lang=$lang\" />";
    } elseif ( $CurrentFile == "admin.php" )  {
        $do=quote_smart($_REQUEST['do']);
        if ( $do == 'yes' ) {
            echo "  <meta http-equiv=\"refresh\" content=\"0\" />";
        }
    } elseif ($CurrentFile == "myboat.php" ) {
        echo "  <meta http-equiv=\"refresh\" content=\"1200\" />";
    } //No refresh for other pages

?>

  <script type="text/javascript" src="scripts/popup.js"></script>
  <script type="text/javascript" src="scripts/dms.js"></script>
  <script type="text/javascript" src="scripts/hashtable.js"></script>

  <script type="text/javascript" src="externals/lightbox/js/prototype.js"></script>
  <script type="text/javascript" src="externals/lightbox/js/scriptaculous.js?load=effects,builder"></script>
  <script type="text/javascript" src="externals/lightbox/js/lightbox.js"></script>
  <link rel="stylesheet" href="externals/lightbox/css/lightbox.css" type="text/css" media="screen" />

  <script type="text/javascript" src="externals/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>

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

    if (!isLoggedIn()) //if not connected
    {
?>
        <div id="loginbox" class="leftbox">
            <form method="post" action="myboat.php">
                User: <input size="10" maxlength="64" name="pseudo" /><br />
                Pass: <input size="10" maxlength="15" type="password" name="password" /><br />
                <input type="hidden" name="lang" value="<?php echo $lang; ?>" />
                <input type="hidden" name="type" value="login" />
                <p class="leftbutton">
                    <input type="submit" value="<?php echo getLocalizedString("login"); ?>" />
                </p>
            </form>
        </div> <!-- fin div loginbox-->
        <div id="pleasecreatebox" class="leftbox">
<?php
            printf (getLocalizedString("pleasecreate"), "<a href=\"create_player.php?lang=$lang\">", "</a>");
?>
        </div> <!-- fin div pleasecreatebox-->
<?php
    } else { //if loggedin
?>
        <div id="loggedbox" class="leftbox">
<?php
            if (isPlayerLoggedIn()) {
                echo getPlayername()."<br />\n";
            }

            //disp login name, infos
            echo "<a href=\"palmares.php?lang=".$lang."&amp;type=user&amp;idusers=" . getLoginId() . "\">" .
                 " (". getLoginId() . ") <u>" . getLoginName() . "</u></a>\n";

?>
        </div> <!-- fin div loggedbox-->
        <div id="logoutbox" class="leftbox">
            <form method="post" action="index.php">
                <input type="hidden" name="type" value="logout" />
                <input type="hidden" name="lang" value="<?php echo $lang; ?>" />
                <p class="leftbutton">
                    <input type="submit" value="<?php echo getLocalizedString("logout"); ?>" />
                </p>
            </form>
        </div> <!-- fin div logoutbox-->
<?php
    }
?>
        <div id="pagesbox" class="leftbox">
            <a href="myboat.php?lang=<?echo $lang?>"><?php echo getLocalizedString("control"); ?></a>
            <a href="mappref.php?lang=<?echo $lang?>"><?php echo getLocalizedString("mymap"); ?></a>
            <a href="modify.php?lang=<?echo $lang?>"><?php echo getLocalizedString("choose"); ?></a>
            <a href="palmares.php?type=user&lang=<?echo $lang?>"><?php echo getLocalizedString("Profile"); ?></a>
        </div> <!-- fin div pagesbox -->
        <div id="langbox" class="leftbox">
<?php
            $langs= getLocalizedString(null);
            foreach ($langs as $lg) {
                echo "<a href=\"".$SCRIPT_NAME."?lang=$lg\"><img src=\"images/site/$lg.png\" title=\"".getLocalizedString('lang_version', $lg)."\" alt=\"".getLocalizedString('lang_version', $lg)."\" /></a>\n";
            }            
?>
        </div> <!-- fin div langbox-->
        <div id="menubox" class="leftbox">
            <a href="index.php?lang=<?echo $lang?>"><?echo getLocalizedString("home")?></a> 
<?php
                // Bouton "ADMIN" pour les administrateurs
                if ( idusersIsAdmin (getLoginId()) ) {
                    echo "<a target=\"_admin\" href=\"/admin/\">Admin</a>";
                }
                //<a href="http://images.virtual-loup-de-mer.org/" target="_images">echo getLocalizedString("images"); </a> 
?>
            <a href="races.php?lang=<?echo $lang?>"><?echo getLocalizedString("races"); ?></a>
            <a href="calendar.php?lang=<?echo $lang?>"><?echo getLocalizedString("calendar"); ?></a>
            <a href="http://ranking.virtual-loup-de-mer.org/" target="_rankings"><?php echo getLocalizedString("ranking"); ?></a> 
            <a href="<?php echo MOBILE_SERVER_URL; ?>" target="_mobile" title="<?php echo getLocalizedString("Interface for mobiles devices"); ?> ">
                <?php echo getLocalizedString("Mobiles"); ?>
            </a> 
        </div> <!-- fin div menubox-->
        <div id="menucommunicationbox" class="leftbox">
            <a href="http://www.virtual-winds.com/forum/index.php?showforum=276" target="_forum"><?echo getLocalizedString("forum")?></a>
            <?php
                $ircpseudo = preg_replace("([^a-zA-Z0-9-\[\]\\`^{}])", "-", getLoginName(). "-" . getLoginId());
                $ircpseudo = preg_replace("(-+)", "-", $ircpseudo);
                echo "<a href=\"http://widget.mibbit.com/?settings=6039fdaa49195f3d8c578c16d1b3ae63&amp;server=mibbit.epiknet.org&amp;channel=%23vlm&amp;noServerTab=false&amp;autoConnect=true&amp;customprompt=Welcome%20To%20VLM%20Chat&amp;charset=iso-8859-15&amp;nick=" . $ircpseudo . "\" target=\"_blank\" >";
                echo getLocalizedString("tchat");
            ?>
            </a>
            <?php 
                echo "(<a href=\"javascript:popUp('" . CHAT_SERVER_URL . "/index.php?username=" . getLoginName() . "(" . getLoginId() . ")". "','chat')\">";
                echo "old".getLocalizedString("tchat");
                ?>
            </a>)
        </div> <!-- fin div menucommunicationbox -->
        <div id="docbox" class="leftbox">
            <a href="<?php echo DOC_SERVER_URL; ?>" target="_vlmwiki"><?php echo getLocalizedString("doc"); ?></a>
            <a href="<?echo DOC_SERVER_URL.'FAQ/'.$lang; ?>" target="_vlmwiki"><?echo getLocalizedString("faq"); ?></a> 
            <a href="<?php echo TOOLS_SERVER_URL; ?>" target="_outils"><?php echo getLocalizedString("tools"); ?></a>            
            <a href="<?php echo DEV_SERVER_URL; ?>" target="_vlmdev"><?php echo getLocalizedString("dev"); ?></a>
            <a href="<?echo DOC_SERVER_URL.'About/'.$lang; ?>" target="_vlmwiki"><?echo getLocalizedString("about"); ?></a>

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
   </div>
<!--
end of firstcolumn
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
