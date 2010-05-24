<?php
    include_once("config.php");
    include_once("wslib.php");

    function usage() {
      return "Usage: FIXME ";
    }

    function reply($answer) {
        $fmt = "json";

        switch ($fmt) {
            //retourne du json par défaut, mais peut être qu'on pourra supporter autre chose plus tard
            case "json":
            default:
                header('Content-type: application/json; charset=UTF-8');
                echo json_encode($answer);
        }
        exit();
    }

    function reply_with_error($code, $answer, $error_string = null) {
        $answer['success'] = False;
        $answer['error'] = get_error($code);
        if (!is_null($error_string)) $answer['error']['custom_error_string'] = $error_string;
        reply($answer);
    }
    
    function reply_with_success($answer) {
        $answer['success'] = True;
        reply($answer);
    }

    function reply_with_error_if_not_exists($key, $request, $code, $answer) {
        if (!isset($request[$key])) reply_with_error($code, $answer);
    }

    function check_pilototo_tasktime($request, $answer) {
        reply_with_error_if_not_exists('tasktime', $request, 'PILOTOTO01', $answer);
        $tasktime = $request['tasktime'];
        if (!is_int($tasktime)) reply_with_error('PILOTOTO02', $answer);
        return $tasktime;
    }

    function check_pilototo_taskid($request, $answer) {
        reply_with_error_if_not_exists('taskid', $request, 'PILOTOTO03', $answer);
        $taskid = $request['taskid'];
        if (!is_int($taskid)) reply_with_error('PILOTOTO04', $answer);
        return $taskid;
    }    

    function check_pim($request, $answer) {
        reply_with_error_if_not_exists('pim', $request, 'PIM01', $answer);
        $pim = $request['pim'];
        if (!is_int($pim)) reply_with_error('PIM02', $answer);
        return $pim;
    }

    function check_pip_with_float($request, $answer) {
        reply_with_error_if_not_exists('pip', $request, 'PIP01', $answer);
        $pip = $request['pip'];
        if (!is_numeric($pip)) reply_with_error('PIP02', $answer);
        return $pip;
    }
    
    function check_pip_with_wp($request, $answer) {
        //existence et pip/wp en tant qu'array
        reply_with_error_if_not_exists('pip', $request, 'WP01', $answer);
        $pip = $request['pip'];
        if (!is_array($pip)) reply_with_error('WP02', $answer);
        //existence des paramètres
        reply_with_error_if_not_exists('targetlat', $pip, 'WP03', $answer);
        reply_with_error_if_not_exists('targetlong', $pip, 'WP04', $answer);
        if (!isset($pip['targetandhdg'])) {
            $pip['targetandhdg'] = -1.;
        }
        if (is_numeric($pip['targetlat']) && is_numeric($pip['targetlong']) && (is_numeric($pip['targetandhdg']))) {
            return $pip;
        } else {
            reply_with_error('WP05', $answer);
        }
    }

    function target_array2string($target) {
        if (isset($target['targetandhdg'])) {
            return sprintf("%f,%f@%f", $target['targetlat'], $target['targetlong'], $target['targetandhdg']);
        } else {
            return sprintf("%f,%f@%f", $target['targetlat'], $target['targetlong']);
        }
    }

    // now start the real work
    login_if_not(usage());

    $answer = Array();

    //surface test
    $input = get_cgi_var('parms', null);
    if (is_null($input)) reply_with_error('PARM01', $answer);
    $request = json_decode($input, true);
    if (is_null($request) || !is_array($request)) reply_with_error("PARM02", $answer);

    //ask for debug
    if (isset($request['debug']) && $request['debug']) $answer['request'] = $request;

    //auth check
    reply_with_error_if_not_exists('idu', $request, "AUTH01", $answer);
    if ($_SESSION['idu'] != $request['idu']) reply_with_error("AUTH02", $answer);

    //OK, on peut instancier l'utilisateur
    $fullusers = new fullUsers(getLoginId());
    
    switch($request['action']) {
        case "boat" :
            $pim = check_pim($request, $answer);

            switch ($pim) {
                case PILOTMODE_HEADING:
                case PILOTMODE_WINDANGLE:
                    $pip = check_pip_with_float($request, $answer);
                    //OK, on a un pip et un pim
                    $fullusers->writeNewheading($pim, $pip, $pip);
                    break;
                case PILOTMODE_ORTHODROMIC:
                case PILOTMODE_BESTVMG:
                case PILOTMODE_VBVMG:
                    if (isset($request['pip'])) {
                        $pip = check_pip_with_wp($request, $answer);
                        //OK, on a un pip, et il est valide
                        $fullusers->updateTarget($pip['targetlat'], $pip['targetlong'], $pip['targetandhdg']);
                    }
                    $fullusers->writeNewheading($pim);
                    break;
                default :
                    reply_with_error('PIM03', $answer);
            }                
            break;
        case "target" :
            $pip = check_pip_with_wp($request, $answer);
            //OK, on a un pip, et il est valide
            $fullusers->updateTarget($pip['targetlat'], $pip['targetlong'], $pip['targetandhdg']);
            break;
        case "pilototo_add" :
            #FIXME on devrait pouvoir factoriser le switch() avec l'action boat
            
            $tasktime = check_pilototo_tasktime($request, $answer);
            $pim = check_pim($request, $answer);

            switch ($pim) {
                case PILOTMODE_HEADING:
                case PILOTMODE_WINDANGLE:
                    $pip = check_pip_with_float($request, $answer);
                    break;
                case PILOTMODE_ORTHODROMIC:
                case PILOTMODE_BESTVMG:
                case PILOTMODE_VBVMG:
                    $pip = check_pip_with_wp($request, $answer);
                    $pip = target_array2string($pip);
                    break;
                default :
                    reply_with_error('PIM03', $answer);
            }
            $fullusers->users->pilototoAdd($tasktime, $pim, $pip);
            break;
        case "pilototo_update" :
            #FIXME on devrait pouvoir factoriser le switch() avec l'action boat
            
            $taskid = check_pilototo_taskid($request, $answer);
            $tasktime = check_pilototo_tasktime($request, $answer);
            $pim = check_pim($request, $answer);

            switch ($pim) {
                case PILOTMODE_HEADING:
                case PILOTMODE_WINDANGLE:
                    $pip = check_pip_with_float($request, $answer);
                    //OK, on a un pip et un pim
                    break;
                case PILOTMODE_ORTHODROMIC:
                case PILOTMODE_BESTVMG:
                case PILOTMODE_VBVMG:
                    $pip = check_pip_with_wp($request, $answer);
                    //OK, on a un pip, et il est valide
                    $pip = target_array2string($pip);
                    break;
                default :
                    reply_with_error('PIM03', $answer);
            }
            $fullusers->users->pilototoAdd($taskid, $tasktime, $pim, $pip);
            break;  
        case "pilototo_delete" :
            $taskid = check_pilototo_taskid($request, $answer);
            $fullusers->users->pilototoDelete($taskid);
            break;          
        default :
            reply_with_error('PARM03', $answer);
    }
    if ($fullusers->users->error_status) {
        reply_with_error("CORE01", $answer, $fullusers->users->error_string);
    } else {
        reply_with_success($answer);
    }

?>
