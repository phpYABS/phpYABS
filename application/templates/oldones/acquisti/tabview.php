<p>
<table width="590" border="1" align="center">
  <tr>
    <th width="80">N</th>
    <td><?=$numero?></td>
  </tr>
  <tr>
    <th width="80">ISBN:</th>
    <td><?=$ISBN?></td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td><?=$Titolo?></td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td><?=$Autore?></td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td><?=$Editore?></td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td><?=$Prezzo?></td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><u><?=$Valutazione?></u></td>
  </tr>
  <tr>
    <th width="80">Buono:</th>
    <td><?=$Buono?></td>
  </tr>
  <tr>
    <th width="80">Contanti:</th>
    <td><?=$Contanti?></td>
  </tr>
  <tr>
    <th width="80">Dest:</th>
    <td><?=$dest?></td>
  </tr>
  <tr>
    <th width="80">Cancella:</th>
    <td><a href="modules.php?Nome=Acquisti&Azione=Acquisto&Cancella=<?=$IdLibro?>">Cancella</a>
    <a href="modules.php?Nome=Libri&Azione=Modifica&ISBN=<?=$sISBN?>">Modifica</a></td>
  </tr>
</table>
</p>
