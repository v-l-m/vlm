<?php

include_once("functions.php");
include_once("base.class.php");

$playersPrefsList = explode(',', PLAYER_PREF_ALLOWED); //Array("lang_ihm", "lang_communication", "contact_email", "contact_jabber");

$playersPrefsContactLinkPattern = Array(
    "contact_revatua" => "http://revatua.forumactif.com/u%scontact",
    "contact_fmv" => "http://forum-marinsvirtuels.forumactif.com/u%scontact",
    "contact_taverne" => "http://www.virtual-winds.com/forum/index.php?showuser=%s",
    "contact_twitter" => "https://twitter.com/#!/%s",
    "contact_identica" => "http://identi.ca/%s",
    );

//NB: par défaut, tout est privé.
define("VLM_ACL_BOATSIT", 1);
define("VLM_ACL_AUTH", 2);

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

    function getPrefValue($key) {
        $value = $this->playerclass->getPref($key);
        if (is_null($value)) return null;
        return $value['pref_value'];
    }
    
    function getPrefGroup($prefix) {
        return $this->playerclass->getPrefGroup($prefix);
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
        $plist = Array(VLM_ACL_BOATSIT => getLocalizedString('Boatsitter'), VLM_ACL_AUTH => getLocalizedString('VLM Players'));
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
        if (is_null($val) || $val == "") return "";
        switch($key) {
            case "contact_email":
            case "contact_jabber":
                $pattern = "/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i";
                if (preg_match($pattern  ,  $val) < 1) {
                    $this->set_error(getLocalizedString("pref_".$key)." : ".getLocalizedString("Your id doesn't seem to be valid"));
                    return null;
                }
                return $val;
            case "contact_taverne":
                return $this->checkDoublePattern($key, $val, "/^http:\/\/www\.virtual-winds\.com\/forum\/index.php\?showuser=(\d+)$/i", "/^(\d+)$/i");
            case "contact_fmv":
                return $this->checkDoublePattern($key, $val, "/^http:\/\/forum-marinsvirtuels\.forumactif\.com\/u(\d+)$/i", "/^(\d+)$/i");
            case "contact_revatua":
                return $this->checkDoublePattern($key, $val, "/^http:\/\/revatua\.forumactif\.com\/u(\d+)$/i", "/^(\d+)$/i");
            case "contact_twitter":
                return $this->checkDoublePattern($key, $val, "/^https:\/\/twitter.com\/#!\/([a-zA-Z0-9]+)$/i", "/^([a-zA-Z0-9]+)$/i");
            case "contact_identica":
                return $this->checkDoublePattern($key, $val, "/^http:\/\/identi.ca\/([a-zA-Z0-9]+)$/i", "/^([a-zA-Z0-9]+)$/i");
            case "lang_communication" :
                if (is_array($val)) $val = implode(',', $val);
            case "lang_ihm" :
            default :
                return $val;
        }
        return $val;
    }
    
    function checkDoublePattern($key, $val, $pattern, $pattern2) {
                if (is_null($val) || $val == "") return "";
                if (preg_match($pattern  ,  $val, $matches) > 0) {
                    $val = $matches[1];
                }
                if (preg_match($pattern2  ,  $val) <  1) {
                    $this->set_error(getLocalizedString("pref_".$key)." : ".getLocalizedString("Your id doesn't seem to be valid"));
                    return null;
                }
                return $val;
    }    
    
    function checkCgiPref($key) {
   
        $val = get_cgi_var("pref_$key");
        if (is_null($val = $this->checkPrefValue($key, $val))) {
             $this->set_error(getLocalizedString("Bad value for key")." : ".getLocalizedString("pref_$key"));
             return False;
        }
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
    
    function htmlPref($key) {
        global $playersPrefsContactLinkPattern;
        $val = $this->getPrefValue($key);
    
        switch($key) {
            case "contact_email":
            case "contact_jabber":
                return "<b>".getLocalizedString("pref_$key")."</b> : ".$val;
            case "contact_taverne":
            case "contact_fmv":
            case "contact_revatua":
            case "contact_twitter":
            case "contact_identica":
                return "<a href=\"".sprintf($playersPrefsContactLinkPattern[$key], $val)."\">".getLocalizedString("pref_$key")."</a>";
        
            default :
                  return $val;
        }
        return $val;
    }
}


?>
