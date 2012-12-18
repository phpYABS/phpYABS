<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Aggiungi libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$addbook=new PhpYabs_Book();
  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$addbook->IsValidISBN($_POST['ISBN'])) {
    include PATH_TEMPLATES.'/oldones/libri/tabadd.php';
  } else {
    $fields=Array("ISBN" => $_POST['ISBN'],"Titolo" => $_POST['Titolo'], "Autore" => $_POST['Autore'],
    "Editore" => $_POST['Editore'], "Prezzo" => $_POST['Prezzo']);

    $addbook->SetFields($fields);
    $addbook->SetValutazione($_POST['Valutazione']);

    if($addbook->SaveToDB())
      echo "<p>Libro inserito</p>";
    else
      echo "<p>Errore nell'inserimento del libro</p>";
  }
?>
</body>
</html>
