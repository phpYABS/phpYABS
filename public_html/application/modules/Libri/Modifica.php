<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Modifica libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<script language="JavaScript" type="text/javascript">
document.form1.ISBN.focus()
</script>
<h1 align="center">Modifica Libro</h1>
<?
  if(!isset($_GET['ISBN'])) {?>
<div align="center"> 
  <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="get" name="form1">
    ISBN 
    <input type="text" name="ISBN">
    <input type="submit" name="Invia" value="Ok">
    <input type="hidden" name="Nome" value="<?=$_GET['Nome']?>">
    <input type="hidden" name="Azione" value="<?=$_GET['Azione']?>">
  </form>
</div>
<? }
  else {
    loadClass('PhpYabs_Book'); 
  	$modbook=new PhpYabs_Book();
	 
	 $fields=Array("ISBN" => $_GET['ISBN'],  "Titolo" => $_GET['Titolo'], "Autore" => $_GET['Autore'],
	 "Editore" => $_GET['Editore'], "Prezzo" => $_GET['Prezzo']);
	 
	 $modbook->SetFields($fields);
	 $modbook->SetValutazione($_GET['Valutazione']);
	 
	 if($modbook->SaveToDB())
	   echo "<p align=\"center\">Libro modificato!</p>";
	   
   	 $modbook->GetFromDB($_GET['ISBN']);
	 
     if(list($ISBN,$Titolo,$Autore,$Editore,$Prezzo)=$modbook->GetFields()) {
        $valutazione=$modbook->GetValutazione();
        switch($valutazione) {
          default:
            $selnull="selected";
            break;
          case 'zero':
            $selzero="selected";
            break;
          case 'rotmed':
            $selrotmed="selected";
            break;
          case 'rotsup':
            $selrotsup="selected";
            break;
          case 'buono':
            $selbuono="selected";
            break;
        }
	    
        include PATH_TEMPLATES.'/oldones/libri/tabmod.php';
	  }

      else { ?>
<p align="center">Libro non trovato</p>
<? 
    }
  }
?>
</body>
</html>
