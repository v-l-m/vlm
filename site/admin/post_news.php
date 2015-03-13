<?php
    $PAGETITLE = "Post some news to VLM Networks";
    include ("htmlstart.php");
    include_once ("functions.php");
    $newsmedias = explode(",", VLM_NOTIFY_LIST);
    $medias = get_cgi_var('medias', '');
    $news = get_cgi_var('news', '');
    $longnews = get_cgi_var('longnews', '');
    $url = get_cgi_var('url', '');
    $now = intval(time());

    if (get_cgi_var("action") == "postnews") {
        echo "<h3>Posting Message</h3>";
        echo "<hr>";
        foreach ($medias as $m) {
            print "Posting '$news' in '$m' at $now\n";
            insertNews($m, $news, $now, $longnews, $url);
        }
    } else {
?>
        <h3>Ecrivez votre message</h3>
        <form name="postnews" action="#" method="post">
            <input type="hidden" name="action" value="postnews" />
            <textarea name="news" cols="60" rows="3" maxlength="120"></textarea><br/>
            <textarea name="longnews" cols="60" rows="20" maxlength="5000"></textarea><br/>
            <input name="url" maxlength="250"/><br/>
<?php
    foreach ($newsmedias as $nm) {
        echo "<input type=\"checkbox\"  name=\"medias[]\" value=\"$nm\">$nm<br/>";
    }
?>
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>
