<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $jvlmredirect = get_cgi_var ("jvlm");

    // Redirect to JVLM interface to complete user creation.
    if ($jvlmredirect)
    {
      $url="/?".$_SERVER["QUERY_STRING"];
      ob_start();
      header('Location: '.$url);
      ob_end_flush();
      die();
    }

    if (isPlayerLoggedIn()) printErrorAndDie("You are already logged.", "Click here if you want to create a boat.", "create_boat.php");

    $actioncreate = get_cgi_var("createplayer");

    function printAccountSummary($emailid = "", $password = "", $playername = "") {
        echo "<ul>";
            echo "<li>".getLocalizedString("email")." : $emailid</li>";
            echo "<li>".getLocalizedString("password")." : $password</li>";
            echo "<li>".getLocalizedString("playername")." : $playername</li>";
        echo "</ul>";
    }

    function printFormRequest($emailid = "", $password = "", $playername = "") {
        echo "<div id=\"createplayerbox\">";
        echo "<h2>".getLocalizedString("chooseaccount")."</h2>";
?>
        <form action="#" method="post" accept-charset="utf-8" name="createplayer">
            <input size="25" maxlength="15" name="playername" value="<?php echo $playername; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("playername"); ?></span>
            <br />
            <hr />
            <input size="25" maxlength="64" name="emailid" value="<?php echo $emailid; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("email"); ?></span>
            <br />
            <input size="25" type="password" maxlength="15" name="password" value="<?php echo $password; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("password"); ?></span>
            <input type="hidden" name="createplayer" value="requested" />
            <br />
            <input type="submit" />
        </form> 
        <p><?php echo nl2br(getLocalizedString("createaccountrules")); ?></p>
        </div>
<?php
    }

    $emailid = get_cgi_var("emailid");
    $password = get_cgi_var("password");
    $playername = get_cgi_var("playername");
    
    if (!ALLOW_ACCOUNT_CREATION) {
            echo "<div id=\"createplayerbox-request\">";
            echo "<h1>".getLocalizedString("Account creation is disabled on this server, you should ask to the admins")."</h1>";
            echo "</div>";    
    } else if ($actioncreate == "requested") { //REQUESTED
        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        if (!$player->checkNonconformity()) {
            echo "<div id=\"createplayerbox-request\">";
            echo "<h2>".getLocalizedString("Here is your request for creating an account")."&nbsp;:</h2>";
            printAccountSummary($emailid, $password, $playername);
?>
            <form action="#" method="post" accept-charset="utf-8" name="createplayer">
                <input type="hidden" name="emailid" value="<?php echo $emailid; ?>"/>
                <input type="hidden" name="password" value="<?php echo $password; ?>"/>
                <input type="hidden" name="playername" value="<?php echo $playername; ?>"/>
                <input type="hidden" name="createplayer" value="confirmed"/>
                <input type="submit" value="<?php echo getLocalizedString("Confirm account request ?"); ?>" />
            </form> 
<?php
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Input invalid")."</h2>";
            echo "<div>".nl2br($player->error_string)."</div>";
            printFormRequest($emailid, $password, $playername);
        }
    } else if ($actioncreate == "confirmed") { //CONFIRMED
        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        $player->setPassword($password);
        $player->setSeed();
        if (!$player->checkNonconformity()) {
            $player->insert();
            if (!$player->error_status) {
               echo "<div id=\"createplayerbox\">";
               $player->mailValidationMessage();
               echo '<h2>'.getLocalizedString("An email has been sent. Click on the link to validate.").'</h2>';
               echo "</div>";
           } else {
               echo "<h2>".getLocalizedString("Input invalid")."</h2>";
               echo "<div>".nl2br($player->error_string)."</div>";
               printFormRequest($emailid, $password, $playername);
           }   
       }
    } else if ($actioncreate == "validate") { //VALIDATE
        echo "<div id=\"createplayerbox\">";
        $seed = get_cgi_var("seed");
        $jvlm = get_cgi_var("jvlm");
        $player = new playersPending($emailid, $seed);
        if (!$player->validate()) 
        {
          print getLocalizedString("Account validation error");
          print $player->error_string;
        }
        else
        {
          echo "<h2>".getLocalizedString("Your account is ready to be created")."</h2>";
          printAccountSummary($player->email, "****", $player->playername);
?>
          <form accept-charset="utf-8" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" name="createplayer">
            <input type="hidden" name="seed" value="<?php echo $seed; ?>" />
            <input type="hidden" name="emailid" value="<?php echo $emailid; ?>" />
            <input type="hidden" name="createplayer" value="create" />
            <input type="hidden" name="jvlm" value="<?php echo $jvlm ?>" />
            <input type="submit" value="<?php echo getLocalizedString("Confirm account creation ?"); ?>" />
          </form> 
<?php
        }
        echo "</div>";
    } 
    else if ($actioncreate == "create") 
    { //CREATE
      $seed = get_cgi_var("seed");
      $jvlm = get_cgi_var("jvlm");
      $player = new playersPending($emailid, $seed);
      echo "<div id=\"createplayerbox\">";
      if (!$player->create()) 
      {
        echo getLocalizedString("Account creation error");
        echo $player->error_string;
      }
      else 
      {
        echo "<h2>".getLocalizedString("Your account has been created")."</h2>";
        $player = new players(0, $player->email);
        if (! $jvlm)
        {
          printAccountSummary($player->email, "****", $player->playername);
        }
        
        if (!checkLoginExists($player->playername)
            && $idu = createBoat($player->playername, $password = generatePassword($player->playername), $player->email, $player->playername)) 
        {
          //Manual creation of users, forcing use of MASTER server
          //FIXME : factorise with create_boat.php
          $users = new users($idu, FALSE);
          $users->initFromId($idu, True);
          if (!$jvlm)
          {
            echo "<h2>".getLocalizedString("Your boat has been created")."</h2>";
            printBoatSummary($player->playername, $player->playername);
            echo "</div>";

            echo "<div id=\"attachboatbox\">";
          
            if ($users->setOwnerId($player->idplayers) && !$users->error_status) 
            {
              echo '<h2>'.getLocalizedString("Attachment successful").'.</h2>';
              echo '<p>'.getLocalizedString('You own this boat').'.</p>';
              echo '<p><b>'.getLocalizedString('Click here').'</b>&nbsp;:&nbsp;'.$users->htmlIdusers().'</p>';
            } 
            else
            {
              echo "<h2>".getLocalizedString("It was not possible to attach this boat. Please report this error.")."</h2>";
              if ($users->error_status) 
              {
                print nl2br($users->error_string);
              }
            }
            echo "</div>";
          }
          else
          {
            // Bind boat to player, and redirect
            $users->setOwnerId($player->idplayers);
            header("location:/jvlm");
            exit();
          }
        }
        else 
        {
          echo "<h2>".getLocalizedString('Please connect to create your first boat')."</h2>";    
          echo "</div>";
        }
      }
    }
    else
    {
      printFormRequest();
    }
    
    include_once("includes/footer.inc");
?>
