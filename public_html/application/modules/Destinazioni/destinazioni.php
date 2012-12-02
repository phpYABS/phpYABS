<?php
  $mysql=db_connect();
  
  $risultato=query("SELECT COUNT(*) FROM ".$prefix."_valutazioni");
  list($totlibri)=mysql_fetch_row($risultato);
  mysql_free_result($risultato);
  
  
 if($_GET['destinazione']!='_NEW') {
   if(!isset($_GET['destinazione']))
     $destinazione=$_COOKIE['destinazione'];
   else
     $destinazione=$_GET['destinazione'];
   if(!isset($_GET['start'])) {
     $get_start=$_COOKIE['start'];
   }
   else
     $get_start=$_GET['start'];
 }
 else {
   $get_start=0;
   $destinazione="";
 }
 
 setcookie('start',$get_start,time()+604800);
 setcookie('destinazione',$destinazione,time()+604800);
  
 switch($_GET['invia']) {
   case 'Avanti':
     $start=$get_start+50;
	 if($start>$totlibri)
	   $start=$totlibri-($totlibri%50);
	   break;
	 case 'Indietro':
	   $start=$get_start-50;
	   if($start<0)
		 $start=0;
	   break;
	 default:
	   if(strlen($get_start)>0)
	     $start=$get_start;
	   else
	     $start=0;
	   break;	  
	}
   if(!strlen($destinazione))
     $start=0;
   $pag=(int)($start/50)+1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Destinazione Libri</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 align="center">Aggiunta destinazioni</h1>
<p align="center">Pagina <?=$pag; ?></p>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="GET">
<?

 if(strlen($destinazione)>0) {
    $locked=" disabled";
	echo "<input type=\"hidden\" name=\"destinazione\" value=\"".strtoupper($destinazione)."\">";
 }
 else
   $locked="";
   
?>
<table width="100%" border="1" align="center">
<tr>
<td colspan="7">
<p align="center"><input type="text" name="destinazione" value="<? echo htmlentities(strtoupper($destinazione)); ?>" style="width: 400px"<?=$locked ?>></p>
</td>
</tr>
<?
    if(strlen($destinazione)>0) {
	  if (is_array($_GET['destina'])) {
      while(list($chiave,$valore)=each($_GET['destina']))
	    if($valore=="on") {
          $risultato=query("SELECT COUNT(*) FROM ".$prefix."_destinazioni ".
          "WHERE ISBN = '$chiave' AND destinazione = '$destinazione'");
          list($esiste)=mysql_fetch_row($risultato);
          mysql_free_result($risultato);
          if(!$esiste)
	        query("INSERT INTO ".$prefix."_destinazioni (ISBN, destinazione) ".
	        " VALUES ('$chiave', '$destinazione')");
          }
	    else
	      query("DELETE FROM ".$prefix."_destinazioni WHERE ISBN='$chiave' ".
		  "AND destinazione = '$destinazione'");
	}

    $risultato=query("SELECT ".$prefix."_libri.ISBN, Titolo, Autore, Editore FROM "
	.$prefix."_libri INNER JOIN ".$prefix."_valutazioni ON ".$prefix."_libri.ISBN = "
	.$prefix."_valutazioni.ISBN ORDER BY Editore, Autore, Titolo, ISBN LIMIT $start,50");
    while($risultati=mysql_fetch_row($risultato)) {
      $risultato1=query("SELECT COUNT(*) FROM ".$prefix."_destinazioni ". 
  	  "WHERE ISBN='{$risultati[0]}' AND destinazione ='$destinazione'");
  	  list($esiste)=mysql_fetch_row($risultato1);
	  if($esiste) {
	    $checkedSI="checked";
	    $checkedNO="";
	  }
	  else {
	    $checkedSI="";
	    $checkedNO="checked";
	  }
      ?>
	  <tr>
        <td>
          S&igrave;
            <input name="destina[<?=$risultati[0] ?>]" type="radio" value="on" <? echo $checkedSI; ?>>
          No
          <input name="destina[<?=$risultati[0] ?>]" type="radio" value="off" <? echo $checkedNO; ?>>
        </td>
	  <?
	  $risultati[0]=fullisbn($risultati[0]);
	  for($i=0; $i<count($risultati); $i++) {
	    if(strlen($risultati[$i])<1)
	      $risultati[$i]="&nbsp;";
	    else
	      $risultati[$i]=htmlentities($risultati[$i]);
	    echo "<td>$risultati[$i]</td>\n";
	  }
	  ?> </tr> <?
    }	
    mysql_free_result($risultato);
  }
?>
</table>

<input type="hidden" name="start" value="<?=$start ?>">
<table align="center" border="1">
<tr>
  <td><input name="invia" type="submit" value="Avanti"></td>
  <td><input name="invia" type="submit" value="Indietro"></td>
  <td><input name="invia" type="submit" value="Salva"></td>
  <td><input type="reset" value="Azzera"></td>
</tr>
</table>
</form>
<?
if(strlen($destinazione)) {
  $npag=(int)($totlibri/50);
  if($totlibri%50)
    $npag++;
  for($i=1; $i<=$npag; $i++) {
    echo "<a href=\"{$_SERVER['PHP_SELF']}?destinazione=$destinazione".
	"&start=".(($i-1)*50)."\">$i</a>\n";
  }
}
?>
<p align="center"><a href="<? echo $_SERVER['PHP_SELF']; ?>?destinazione=_NEW">Nuova destinazione</a></p>
</body>
</html>