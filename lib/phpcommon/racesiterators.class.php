<?php
  require_once('functions.php');
  require_once('races.class.php');

  abstract class RacesIterator 
  {
    var $query = "SELECT * FROM races";

    function __construct($iduser=-1) 
    {
      $this->IdUser = $iduser;
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
      var $icalobject;

      function __construct() {
          $this->query = "(SELECT deptime, closetime, racename, racename as description, boattype, idraces FROM `races` ".
                          "WHERE ( ( started = ". RACE_PENDING ." AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) ) ".
                          "AND !(racetype & ".RACE_TYPE_RECORD. ") ORDER BY started ASC, deptime ASC, closetime ASC ) ".
                          "UNION ( SELECT deptime, deptime+3600 as closetime, racename, comments as description, NULL as boattype, NULL as idraces ".
                          "FROM `racespreview` ".
                          "WHERE deptime > UNIX_TIMESTAMP() )";
          parent::__construct();
      }

      function start() {
          require_once( 'iCalcreator/iCalcreator.class.php' );
          date_default_timezone_set( 'UTC' );
          $this->icalobject = new vcalendar();
          
          // create a new calendar instance
          $this->icalobject->setConfig( 'unique_id', 'virtual-loup-de-mer.org' ); // set Your unique id
          $this->icalobject->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
          $this->icalobject->setProperty( "x-wr-calname", "Agenda VLM" ); // required of some calendar software
          $this->icalobject->setProperty( "X-WR-CALDESC", "Agenda VLM" ); // required of some calendar software
          $this->icalobject->setProperty( "X-WR-TIMEZONE", "UTC" ); // required of some calendar software
      }

      function onerow($row) {
          $vevent = new vevent(); // create an event calendar component
          $vevent->SetConfig('TZID','UTC') ;
          $vevent->setProperty("uid", $row['idraces']."@".$_SERVER['SERVER_NAME']);
          $vevent->setProperty( 'dtstart', array('timestamp' => $row['deptime']) );
          $vevent->setProperty( "organizer" , EMAIL_COMITE_VLM );
          $vevent->setProperty( 'dtend', array('timestamp' => $row['closetime']) );
          $vevent->setProperty( 'summary', html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8") );
          if (!is_null($row['boattype'])) {
              $vevent->setProperty( 'description', html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8")." ( ".substr($row['boattype'], 5 )." ) " );
          } else {
              $vevent->setProperty( 'description', html_entity_decode($row['description'], ENT_COMPAT, "UTF-8") );
          }
          //FIXME: construction de l'url ???
          if (!is_null($row['idraces'])) {
              $vevent->setProperty( 'url', sprintf("http://%s/ics.php?idraces=%d", $_SERVER['SERVER_NAME'],  $row['idraces']));
          }
          $this->icalobject->setComponent ( $vevent ); // add event to calendar
      }

      function end() {
          $this->icalobject->returnCalendar();
      }
  }


  class FullcalendarRacesIterator extends RacesIterator {
      var $jsonarray;

      function __construct() {
          $this->query = "(SELECT deptime, closetime, racename, racename as description, boattype, idraces FROM `races` ".
                          " WHERE ( deptime > (UNIX_TIMESTAMP()-2592000 ) ) AND !(racetype & ".RACE_TYPE_RECORD.") ".
                          " ORDER BY started ASC, deptime ASC, closetime ASC ) ".
                          "UNION ( SELECT deptime, NULL as closetime, racename, comments as description, NULL as boattype, NULL as idraces ".
                          "FROM `racespreview` ".
                          "WHERE deptime > UNIX_TIMESTAMP() )";

                          ;
          parent::__construct();
      }

      function start() {
          $this->jsonarray = Array();
      }

      function onerow($row) {
          $jsonarray = Array();
          $jsonarray['start'] = date(DATE_ISO8601,$row['deptime']);
          $jsonarray['end'] = date(DATE_ISO8601,$row['closetime']);
    $jsonarray['title'] = html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8");
    /* #700 candidate
    if (!is_null($row['racename']))
      $jsonarray['title'] = html_entity_decode($row['racename'], ENT_COMPAT, "UTF-8");
    else
      $jsonarray['title'] = "-no title found-";
    */
    $jsonarray['allDay'] = is_null($row['closetime']);
          if (!is_null($row['idraces'])) $jsonarray['url'] = sprintf("http://%s/ics.php?idraces=%d", $_SERVER['SERVER_NAME'],  $row['idraces']);
          $this->jsonarray[] = $jsonarray;
      }

      function end() {
          echo json_encode($this->jsonarray);
      }
  }


  class RssRacesIterator extends RacesIterator {
      var $rssobject;
      var $lang;
      var $updateTime = 0;

      function __construct() {
        $this->query = "SELECT * FROM races ".
                        "WHERE ( ( started = ".RACE_PENDING." AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) ) ".
                        "AND !(racetype & ".RACE_TYPE_RECORD.") ORDER BY started ASC, deptime ASC, closetime ASC ";
        parent::__construct();
      }

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
          $newItem->addElement('author', EMAIL_COMITE_VLM." (VLM)");
          //Attributes have to passed as array in 3rd parameter
          $newItem->addElement('guid', $linkics, array('isPermaLink'=>'true'));
  
          //Now add the feed item
          $this->rssobject->addItem($newItem);

          $this->updateTime = max($this->updateTime, $row['updated']);
      }

    function end() 
    {
      $this->rssobject->setChannelElement('pubDate', date(DATE_RSS, strtotime($this->updateTime)));
      $this->rssobject->setChannelElement('description', "VLM Races");
      $this->rssobject->genarateFeed();
    }
  }
?>
