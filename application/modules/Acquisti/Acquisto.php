<?php
//se c'è una richiesta di nuovo acquisto, elimino il precedente
if ($_GET['Azione']=='Nuovo') {
    unset($_SESSION['IdAcquisto']);
    unset($_SESSION['totalec']);
    unset($_SESSION['totaleb']);
}

$acquisto=new PhpYabs_Acquisto();
if (isset($_GET['IdAcquisto'])) {
    if (!$acquisto->SetID($_GET['IdAcquisto'])) {
        $errmsg="L'acquisto ".$_GET['IdAcquisto']." non esiste!";
    }
} else {
    $acquisto->setId($_SESSION['IdAcquisto']);
}

$IdAcquisto=$_SESSION['IdAcquisto']=$acquisto->GetID();

$trovato=true;

if (isset($_POST['newISBN'])) {
    $trovato=($acquisto->addBook($_POST['newISBN']));
} elseif (isset($_GET['Cancella'])) {
    $acquisto->delBook($_GET['Cancella']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML PUBLIC 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Valutazione dei libri in acquisto</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="document.libro.newISBN.focus()">
<font face="Arial, Helvetica, sans-serif">
<h1 align="center">Valutazione dei libri in acquisto</h1>
<h2 align="center">Acquisto N° <?php echo $IdAcquisto; ?></h2>
<?php if($errmsg) echo "<p align=\"center\"><font color=\"RED\">$errmsg</font></p>" ?>
<?php
  $acquisto->PrintAcquisto();
  if(!$trovato)
    echo "<script language=\"Javascript\">alert('Libro non trovato!');</script>";
  $bill=$acquisto->GetBill();
?>
<p align="center"><?php echo $acquisto->NumBook(); ?> Libri acquistati<br>Totale contanti: <?php echo $bill['totalec'] ?> &euro;
&nbsp;&nbsp;&nbsp;&nbsp;Totale buono: <?php echo $bill['totaleb']; ?> &euro;
&nbsp;&nbsp;&nbsp;&nbsp;Totale rottamazione: <?php echo $bill['totaler']; ?> &euro;</p>
<div align="center">
    <input type="hidden" name="Nome" value="<?=$_GET['Nome']?>">
    <input type="hidden" name="Azione" value="<?=$_GET['Azione']?>">
    <form action="<?php echo $_SERVER['PHP_SELF']."?Nome=".$_GET['Nome'].
    "&Azione=Acquisto" ?>" method="post" name="libro">
    ISBN o EAN
    <input name="newISBN" type="text" maxlength="13">
  <input type="submit" value="Ok">
</form>

</div>
</font>
</body>
</html>
