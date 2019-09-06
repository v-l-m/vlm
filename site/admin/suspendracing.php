<?php
    $PAGETITLE = "Suspend Racing";
    include ("htmlstart.php");
    include_once ("functions.php");

    $RestartEpoch = get_cgi_var("Restart");
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) 
    {
      echo "<h3>Ready to Suspend all racing boats until $RestartEpoch.</h3>";
      $CurTime = time();

      if (0 && $RestartEpoch <= $CurTime + 900 )
      {
        echo "<H2> Invalid target Epoch </H2>";
      }
      else
      {
        echo "<H3> Listing updates </H3>";
        $qry="select race, idusers, max(time) from positions group by race, idusers;";
        insertAdminChangelog(Array("operation" => "suspending all racing boats until $RestartEpoch"));
        $result = wrapper_mysql_db_query_reader("rollback transaction;");        
        $result = wrapper_mysql_db_query_reader("begin transaction;");
        $result = wrapper_mysql_db_query_reader($qry);
        while ( $boat =mysqli_fetch_array($result, MYSQLI_NUM)) 
        {          
          $updqry= "update positions set time = $RestartEpoch where race = $boat[0] and idusers= $boat[1] and time = $boat[2];";
          echo "$updqry<BR> from $boat[2]<BR>";
          wrapper_mysql_db_query_writer($updqry);        
        }

        $result = wrapper_mysql_db_query_reader("rollback transaction;");
        
      }
    }
    else
    {
      echo " action ".get_cgi_var("action") ."<BR>";
      echo " confirm ".get_cgi_var("confirm") ."<BR>";      
    }
?>
<H1> Race Suspension, Set Restart Date </H1>
  <form action="#" method="post">
    <input type="hidden" name="action" value="go" />
    <input type="hidden" name="confirm" value="on" />
    <input type="number" name="Restart">
    <input type="submit" value="Suspend Races" />
  </form>
<?php
    include ("htmlend.php");
?>