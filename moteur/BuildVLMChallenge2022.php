<?php
///// Based on BuildVLMIndex.php

include_once("vlmc.php");
include_once("functions.php");

function BuildIndex($query, $FileNameBase)
{
  
  $host = DBSLAVESERVER;
  $db   = 'vlm';
  $user = DBSLAVEUSER;
  $pass = DBSLAVEPASSWORD;
  $charset = 'utf8mb4';

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => true,
  ];
  try 
  {
    $pdo = new PDO($dsn, $user, $pass, $options);
  } 
  catch (\PDOException $e) 
  {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }

  if ($pdo)
  {
    //var_dump($query);
    $stmt = $pdo->prepare($query);
    //$stmt->bindParam(1, $return_value, PDO::PARAM_STR, 4000); 
    // call the stored procedure
    $stmt->execute();
    
    //print "procedure returned $return_value\n";
    //$result1= $stmt->fetchall();
    $i = 1;
    do
    {
      try
      {
        $rowset = $stmt->fetchall();
        if ($rowset) 
        {
          $fp = fopen("$FileNameBase$i.json", 'w');
          fwrite($fp, json_encode($rowset));
          fclose($fp);
        }
      }
      catch(PDOException $e) {
        //var_dump($e);             
      }
      $i++;
    }while ($stmt->nextRowset()) ;
    
  

   /* // you have to read the result set here 
    if ($result1)
    {
      var_dump($result1);
    }
    else
    {      
      print("no result1\n");
    } */
  }
  //$stmt->close_cursor();
  $pdo=null;
};

////////////////////////////////////////CHECK IF SOMEONE END RACE
echo "\n1- === Build VLMChallenge2022 \n";
//$QueryIndex365Days = "call SP_BUILD_VLM_INDEX(UNIX_TIMESTAMP()-365*3600*24,UNIX_TIMESTAMP(),0,1,36,52);";
//BuildIndex($QueryIndex365Days, getenv("VLMCACHE")."/rankings/VLMIndex_1year");

$ChallengeStart=strtotime('2022/01/01 00:00:00 UTC');
$ChallengeEnd=strtotime('2022/12/31 23:59:59 UTC');
$QueryIndex365Days = "call SP_BUILD_VLM_INDEX($ChallengeStart,$ChallengeEnd,0,1,1,1);"; //UNIX_TIMESTAMP()-2*365*3600*24,0,1);";
BuildIndex($QueryIndex365Days, getenv("VLMCACHE")."/rankings/VLMIndex_Challenge2022");

?>