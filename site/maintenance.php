<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
 
 <head> 
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
   <title>Virtual-loup-de-mer - Maintenance page - Work in progress</title>
   <link rel="stylesheet" type="text/css" href="style/default/style.css" /> 
</head>
  <body>
    <div class="container">
    <img  style="
        width: 80%;
        
    " src="/jvlm/images/Maintenance.png">
      <div style="
    position: relative;
    width: 766px;
    margin-left: 441px;
    margin-top: -310px;
    background-color: #ffffff29;">
        <h1>Site Maintenance In Progress</h1>
        <p>We are currently updating VLM. It will be available again shortly. ...</p>
        <hr />
        <h1>Maintenance du site en cours</h1>
        <p>Nous sommes actuellement en train de mettre &agrave; jour VLM. Il sera de nouveau disponible dans quelques instants...</p>
        <hr />
        <p><?php 
        if(file_exists('file.php'))
        {
          ?>
          <h2>Message:</h2>
          <?php
          include("maintenance.txt");
        }
        ?></p>
      </div>
    </div>
  </body>
</html>
