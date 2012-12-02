<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Aggiungi libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
loadClass('PhpYabs_Book');
$addbook=new PhpYabs_Book();
  if (!$addbook->IsValidISBN($_GET['ISBN'])) {
    include PATH_TEMPLATES.'/oldones/libri/tabadd.php';
  } else {
    $fields=Array("ISBN" => $_GET['ISBN'],"Titolo" => $_GET['Titolo'], "Autore" => $_GET['Autore'],
    "Editore" => $_GET['Editore'], "Prezzo" => $_GET['Prezzo']);

    $addbook->SetFields($fields);
    $addbook->SetValutazione($_GET['Valutazione']);

    if($addbook->SaveToDB())
      echo "<p>Libro inserito</p>";
    else
      echo "<p>Errore nell'inserimento del libro</p>";
  }
?>
</body>
</html>
