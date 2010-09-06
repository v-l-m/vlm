<?php
    require_once('functions.php');

    abstract class RacesIterator {
        var $query = "SELECT * FROM races";

        function RacesIterator() {
            $this->listing();
        }

        function listing() {
            $this->start();
            $res = wrapper_mysql_db_query_reader($this->query) or die("Query [".$this->query."] failed \n");
            while ($row = mysql_fetch_assoc($res) ) $this->onerow($row);
            $this->end();
        }

        abstract function onerow($row);
        abstract function start();
        abstract function end();
    }

    class IcalRacesIterator extends RacesIterator {
        var $query = "SELECT * FROM races
                      WHERE ( ( started = 0 AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) ) AND racetype = 0
                      ORDER BY started ASC, deptime ASC, closetime ASC ";
        var $icalobject;

        function start() {
            require_once( 'iCalcreator/iCalcreator.class.php' );
            $this->icalobject = new vcalendar();
            // create a new calendar instance
            $this->icalobject->setConfig( 'unique_id', 'virtual-loup-de-mer.org' ); // set Your unique id
            $this->icalobject->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
            $this->icalobject->setProperty( "x-wr-calname", "Agenda VLM" ); // required of some calendar software
            $this->icalobject->setProperty( "X-WR-CALDESC", "Agenda VLM" ); // required of some calendar software
            $this->icalobject->setProperty( "X-WR-TIMEZONE", "Europe/Paris" ); // required of some calendar software
        }

        function onerow($row) {
            $vevent = new vevent(); // create an event calendar component
            $vevent->setProperty( 'dtstart', array('timestamp' => $row['deptime']) );
            $vevent->setProperty( "organizer" , EMAIL_COMITE_VLM );
            $vevent->setProperty( 'dtend', array('timestamp' => $row['closetime']) );
            $vevent->setProperty( 'summary', html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8") );
            $vevent->setProperty( 'description', html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8")." ( ".substr($row['boattype'], 5 )." ) " );
            //FIXME: construction de l'url ???
            $vevent->setProperty( 'url', sprintf("http://%s/ics.php?idraces=%d", $_SERVER['SERVER_NAME'],  $row['idraces']));
            $this->icalobject->setComponent ( $vevent ); // add event to calendar
        }

        function end() {
            $this->icalobject->returnCalendar();
        }
    }


    class FullcalendarRacesIterator extends RacesIterator {
        var $query = "SELECT * FROM races
                      WHERE ( deptime > (UNIX_TIMESTAMP()-2592000 ) ) AND racetype = 0
                      ORDER BY started ASC, deptime ASC, closetime ASC ";
        var $jsonarray;

        function start() {
            $this->jsonarray = Array();
        }

        function onerow($row) {
            $jsonarray = Array();
            $jsonarray['start'] = $row['deptime'];
            $jsonarray['end'] = $row['closetime'];
            $jsonarray['title'] = html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8");
            $jsonarray['allDay'] = False;
            $jsonarray['url'] = sprintf("http://%s/ics.php?idraces=%d", $_SERVER['SERVER_NAME'],  $row['idraces']);
            $this->jsonarray[] = $jsonarray;
        }

        function end() {
            echo json_encode($this->jsonarray);
        }
    }


    class RssRacesIterator extends RacesIterator {
        var $query = "SELECT * FROM races
                      WHERE ( ( started = 0 AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) ) AND racetype = 0
                      ORDER BY started ASC, deptime ASC, closetime ASC ";
        var $rssobject;
        var $lang;
        var $updateTime = 0;

        function start() {
            $this->lang = getCurrentLang();

            require_once( 'FeedWriter/FeedWriter.php' );
            $this->rssobject = new FeedWriter(RSS2);
            $this->rssobject->setTitle("Virtual-loup-de-mer");
            $this->rssobject->setLink(sprintf("http://%s%s", $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']));
  
            //Image title and link must match with the 'title' and 'link' channel elements for RSS 2.0
            //$this->rssobject->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/images/logo.gif');
            //Use core setChannelElement() function for other optional channels
            $this->rssobject->setChannelElement('language', $this->lang."-".$this->lang); //FIXME : is this correct for language code ?
        }

        function onerow($row) {
            //Create an empty FeedItem
            $newItem = $this->rssobject->createNewItem();

            $linkics = sprintf("http://%s/ics.php?idraces=%d", $_SERVER['SERVER_NAME'],  $row['idraces']);
  
            //Add elements to the feed item
            //Use wrapper functions to add common feed elements
            $newItem->setTitle($row['racename']);
            $newItem->setLink($linkics);
            //The parameter is a timestamp for setDate() function
            $newItem->setDate($row['updated']);
            $ro = new Races(0, $row); //['idraces']);
            $newItem->setDescription($ro->htmlRaceDescription());
            //$newItem->setEncloser('http://www.attrtest.com', '1283629', 'audio/mpeg');
            //Use core addElement() function for other supported optional elements
            $newItem->addElement('author', EMAIL_COMITE_VLM);
            //Attributes have to passed as array in 3rd parameter
            $newItem->addElement('guid', $linkics, array('isPermaLink'=>'true'));
    
            //Now add the feed item
            $this->rssobject->addItem($newItem);

            $this->updateTime = max($this->updateTime, $row['updated']);
        }

        function end() {
            $this->rssobject->setChannelElement('pubDate', date(DATE_RSS, strtotime($this->updateTime)));
            $this->rssobject->setChannelElement('description', "VLM Races");
            $this->rssobject->genarateFeed();
        }
    }
?>
