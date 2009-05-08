<?php

include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

echo "usage : http://virtual-loup-de-mer.org/gi.php?chaine=xxxx&navigateur=sonnom\n";

printf ("========= Sans quotesmart ============\n");
$chaine=$_REQUEST['chaine'];
printf ("La chaine, non traitée : %s\n", $chaine);

$chaine=htmlentities($_REQUEST['chaine']);
printf ("La chaine, après quotesmart et htmlentities simple : %s\n", $chaine);

$chaine=htmlentities($_REQUEST['chaine'], ENT_COMPAT, "UTF-8");
printf ("La chaine, après quotesmart et htmlentities Compat/UTF8 : %s\n", $chaine);

$chaine=base64_decode($_REQUEST['chaine'], ENT_COMPAT, "UTF-8");
printf ("La chaine, après base64_decode(quotemart) : %s\n", $chaine);

$chaine=rawurldecode($_REQUEST['chaine'], ENT_COMPAT, "UTF-8");
printf ("La chaine, après rawurldecode(quotemart) : %s\n", $chaine);


printf ("========= Avec quotesmart ============\n");
$chaine=quote_smart($_REQUEST['chaine']);
printf ("La chaine, après quotesmart : %s\n", $chaine);

$chaine=htmlentities(quote_smart($_REQUEST['chaine']));
printf ("La chaine, après quotesmart et htmlentities simple : %s\n", $chaine);

$chaine=htmlentities(quote_smart($_REQUEST['chaine']), ENT_COMPAT, "UTF-8");
printf ("La chaine, après quotesmart et htmlentities Compat/UTF8 : %s\n", $chaine);

$chaine=base64_decode(quote_smart($_REQUEST['chaine']), ENT_COMPAT, "UTF-8");
printf ("La chaine, après base64_decode(quotemart) : %s\n", $chaine);

$chaine=rawurldecode(quote_smart($_REQUEST['chaine']), ENT_COMPAT, "UTF-8");
printf ("La chaine, après rawurldecode(quotemart) : %s\n", $chaine);

$chaine=urldecode(quote_smart($_REQUEST['chaine']), ENT_COMPAT, "UTF-8");
printf ("La chaine, après urldecode(quotemart) : %s\n", $chaine);





$navigateur=quote_smart($_REQUEST['navigateur']);
printf ("Navigateur, pour valider qu'on voit bien 2 arguments, et pour que François regarde dans ses logs ce qu'il a reçu comme requête : %s\n", $navigateur);


?>
  
