<?php

    include('config.php');

    header('Content-Type: application/rss+xml');

    $lang = getCurrentLang();

    require_once( 'FeedWriter/FeedWriter.php' );
    $rssobject = new FeedWriter(RSS2);
    $rssobject->setTitle($_SERVER['SERVER_NAME']." - Modules updates");
    $rssobject->setLink(sprintf("http://%s%s", $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']));

    //Image title and link must match with the 'title' and 'link' channel elements for RSS 2.0
    //$rssobject->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/images/logo.gif');
    //Use core setChannelElement() function for other optional channels
    $rssobject->setChannelElement('language', $lang."-".$lang); //FIXME : is this correct for language code ?

    $updateTime = 0;
    $query = "SELECT revid, moduleid, max(updated) as updated FROM modules_status GROUP BY revid, moduleid ORDER BY max(updated) DESC LIMIT 10";
    $res = wrapper_mysql_db_query_reader($query) or die("Query [".$this->query."] failed \n");
    while ($row = mysql_fetch_assoc($res) ) {
        //Create an empty FeedItem
        $newItem = $rssobject->createNewItem();
        
        //Add elements to the feed item
        //Use wrapper functions to add common feed elements
        $newItem->setTitle(sprintf("Update of %s (rev %s)", $row['moduleid'], $row['revid']));
        //The parameter is a timestamp for setDate() function
        $newItem->setDate($row['updated']);
        //$newItem->setEncloser('http://www.attrtest.com', '1283629', 'audio/mpeg');
        //Use core addElement() function for other supported optional elements
        $newItem->addElement('author', EMAIL_COMITE_VLM." (VLM)");
        //Attributes have to passed as array in 3rd parameter
        $newItem->addElement('guid', "".$row['revid'].$row['moduleid']);
        $newItem->setLink(sprintf("http://%s", $_SERVER['SERVER_NAME']));
        //Now add the feed item
        $rssobject->addItem($newItem);

        $updateTime = max($updateTime, $row['updated']);
    }

    $rssobject->setChannelElement('pubDate', date(DATE_RSS, strtotime($updateTime)));
    $rssobject->setChannelElement('description', "VLM Modules Status");
    $rssobject->genarateFeed();
    
?>
