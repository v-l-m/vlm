<?
    require_once('base.class.php');

    abstract class VlmNotify extends baseClass {
        var $rate_limit = 2;
        var $media = null;
        var $messages = Array();
        
        function __construct() {
        }
        
        function fetch() {
            $now = intval(time());
            # idnews | media   | summary         | timetarget | published |
            $query = "SELECT * FROM `news` ";
            $query .= "WHERE `media` = '".$this->media."' ";
            $query .= "AND `published` = 0 ";
            $query .= "AND `timetarget` < $now ";
            $query .= "ORDER BY `timetarget` ASC ";
            $query .= "LIMIT ".$this->rate_limit;
            $res = $this->queryRead($query);
            if ($res) {
                while ($line = mysql_fetch_assoc($res)) {
                    $this->messages[] = $line;
                }
            }
            
        }

        function postone() {
            return True;
        }
        
        function post() {
            foreach ($this->messages as $k => $m) {
                $res = $this->postone($m);
                $this->messages[$k]['success'] = $res;
                $this->messages[$k]['published'] = intval(time());
            }
        }
                
        function close() {
            foreach ($this->messages as $k => $m) {
                if ($m['success']) {
                    $this->queryWrite("UPDATE `news` SET published = ".$m['published']." WHERE idnews = ".$m['idnews']);
                }
            }
        }
    }
    
    class VlmNotifyCurl extends VlmNotify {

        var $mh = null;
        var $handles = Array();
    
        function __construct() {
            parent::__construct();
            //create the multiple cURL handle
            $this->mh = curl_multi_init();
        }
        
        function postone($m) {
            $ch = $this->init_handle($m);
            $handles[] = $ch;
            curl_multi_add_handle($this->mh,$ch);
            return True; //#FIXME ?
        }

        function post() {
            parent::post();
            $active = null;
            //execute the handles
            do {
                $mrc = curl_multi_exec($this->mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($this->mh) != -1) {
                    do {
                        $mrc = curl_multi_exec($this->mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }
        }

        function close() {
            parent::close();
            //close the handles
            foreach ($this->handles as $ch) {
                curl_multi_remove_handle($this->mh, $ch);
            }
            curl_multi_close($this->mh);
            foreach ($this->handles as $ch) {
                curl_close($ch);
            }
        }
        
        function init_handle($m) {
            return null;
        }

        function init_curl_handle_from_url($url, $data, $coptdef = null) {          
            $ch = curl_init();
            // set URL and other appropriate options
            $copt = Array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 0,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $data
                );
            if (!is_null($coptdef)) {
                foreach ($coptdef as $k => $v) $copt[$k] = $v;
            }
            curl_setopt_array($ch, $copt);
            return $ch;
        }
    }

?>
