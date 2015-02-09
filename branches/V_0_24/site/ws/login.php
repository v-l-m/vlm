<?php
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    function checkLogin() {
        session_start();
        if (isPlayerLoggedIn() && isLoggedIn() ) {
            //we know the player from a previous login session
            return True;
        } else if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) ) {
            //Trying to do http auth, registering session
            if (!is_null($player = checkPlayerLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) ) {
                $idu = get_cgi_var('select_idu');
                if (is_null($idu) || !in_array($idu, $player->getManageableBoatIdList())) {
                     //select_idu is not correct, selecting default
                     $idu = $player->getDefaultBoat();
                }
                    
                if ($idu > 0) {
                    $user = getUserObject($idu);
                    loginPlayer($user->idusers, $user->username, $player->idplayers, $player->playername);
                } else {
                    loginPlayer(0, "noboat", $player->idplayers, $player->playername);
                }
                return True;
            } else {
                return False;
            }
        } else if (isset($_POST['VLM_AUTH_USER']) && isset($_POST['VLM_AUTH_PW']) ) {
            if (!is_null($player = checkPlayerLogin($_POST['VLM_AUTH_USER'], $_POST['VLM_AUTH_PW'])) ) {
                ask_for_auth("");
                return True;
            } else {
                return False;
            }
        }
        return False;
    }

    echo json_encode(Array('success' => checkLogin()));

?>

