<?php

include_once("functions.php");
include_once("base.class.php");

$playersPrefsList = Array("lang_ihm", "lang_communication", "contact_email", "contact_jabber");

//NB: par défaut, tout est privé.
define("VLM_ACL_BOATSIT", 1);
define("VLM_ACL_PUBLIC", 2);

function playersPrefsGroups() {
    global $playersPrefsList;
    
    $pg = Array();
    foreach ($playersPrefsList as $key) {
        $path = explode('_', $key);
        if (!isset($pg[$path[0]])) $pg[$path[0]] = Array();
        $pg[$path[0]][] = $key;
    }
    return $pg;
}

class playersPrefs extends baseClass {
    var $idplayers,
        $playerclass;

    function playersPrefs($idplayers) {
        $po = getPlayerObject($idplayers);
        if (!is_null($po)) {
            $this->playerclass = $po;
            $this->idplayers = $idplayers;
        }
    }

    
}

class playersPrefsHtml extends playersPrefs {
    
    function getForm($key) {
        global $playersPrefsList;
        if (!in_array($key, $playersPrefsList)) return False;
        
        switch($key) {
            case "lang_ihm" :
                $langs = Array();
                foreach (getLocalizedString(null) as $l) {
                    $langs[$l] = getLocalizedString('lang_version', $l);
                }
                return $this->baseDropDown($key, $langs);
            case "lang_communication" :
                $langfile = file_get_contents('./includes/ISO-639-2_utf-8.txt', FILE_USE_INCLUDE_PATH);
                $langlines = explode("\n", $langfile);
                $langlist = Array();
                foreach($langlines as $line) {
                    $lexp = explode('|', $line);
                    if ($lexp[2] != '') $langlist[$lexp[0]] = $lexp[3];
                }
                return $this->baseDropdownMultiple($key, $langlist);
            default :
                return $this->baseInput($key);
        }
    }
    
    function getPrefValue($key) {
        $value = $this->playerclass->getPref($key);
        if (is_null($value)) return null;
        return $value['pref_value'];
    }    
    function baseInput($key) {
        $value = $this->getPrefValue($key);
        $str = "<input size=32 name=\"pref_$key\" class=\"inputpref\" id=\"".$this->getId($key)."\" value=\"$value\" />";
        return $str;
    }

    function baseDropDown($key, $list) {
        $value = $this->playerclass->getPref($key);
        $str = "<select name=\"pref_$key\" class=\"selectpref\" id=\"".$this->getId($key)."\">";
        foreach ($list as $k =>$v) {
            $str .= "<option value=\"$k\"";
            if ($k == $value['pref_value']) $str .= " selected";
            $str .= ">$v</option>";
        }
        $str .= "</select>";
        return $str;
    }

    function baseDropDownMultiple($key, $list) {
        $value = $this->playerclass->getPref($key);
        $values = explode(',', $value['pref_value']);
        $str = "<select name=\"pref_$key"."[]\" multiple class=\"selectpref\" id=\"".$this->getId($key)."\">";
        foreach ($list as $k =>$v) {
            $str .= "<option value=\"$k\"";
            if (in_array($k,$values)) $str .= " selected";
            $str .= ">$v</option>";
        }
        $str .= "</select>";
        return $str;
    }

    function permissions($key) {
        $value = $this->playerclass->getPref($key);
        $plist = Array(VLM_ACL_BOATSIT => getLocalizedString('Boatsitter'), VLM_ACL_PUBLIC => getLocalizedString('Public'));
        $str = "<select size=\"3\"name=\"perm_$key"."[]\" multiple class=\"selectperm\" id=\"perm-".$this->getId($key)."\">";
        foreach ($plist as $k =>$v) {
            $str .= "<option value=\"$k\"";
            if ($value['permissions'] & $k) $str .= " selected";
            $str .= ">$v</option>";
        }
        $str .= "</select>";
        return $str;
    }          
        
    function getId($key) {
        return "inputpref-$key";
    }
    
    function checkPrefValue($key, $val) {
        switch($key) {
            case "lang_communication" :
                if (is_array($val)) $val = implode(',', $val);
            default :
                return $val;
        }
    }
    
    function checkCgiPref($key) {
   
        $val = get_cgi_var("pref_$key");
        if (is_null($val = $this->checkPrefValue($key, $val))) return False;
        if ($val == "") $val = null;
        
        $permint = 0;
        $perm = get_cgi_var("perm_$key", array());
        if (!is_array($perm)) return False;
        foreach($perm as $pv) {
            $permint += $pv;
        }
        //FIXME
        $permint &= 3;
        
        $old = $this->playerclass->getPref($key);
        if (is_null($old)) {
            if (!is_null($val)) {
                $this->playerclass->setPref($key, $val, $permint);
            }
        } else {
            if ($old['pref_value'] != $val || $old['permissions'] != $permint) {
                $this->playerclass->setPref($key, $val, $permint);
            }
        }
        return !($this->playerclass->error_status || $this->error_status);
    }
        
}


?>
