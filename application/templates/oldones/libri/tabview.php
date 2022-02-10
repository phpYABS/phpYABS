<p>
<table width="590" border="1" align="center">
  <tr>
    <th width="80">N�</th>
    <td><?php echo $numero ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">ISBN:</th>
    <td><?php echo $ISBN ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td><?php echo $Titolo ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td><?php echo $Autore ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td><?php echo $Editore ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td><?php echo $Prezzo ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><u><?php echo $Valutazione ?? ''; ?></u></td>
  </tr>
  <tr>
    <th width="80">Buono:</th>
    <td><?php echo $Buono ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Contanti:</th>
    <td><?php echo $Contanti ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Dest:</th>
    <td><?php echo $Dest ?? ''; ?></td>
  </tr>
  <tr>
    <th width="80">Cancella:</th>
    <td>Cancella
    <a href="modules.php?Nome=Libri&Azione=Modifica&ISBN=<?php echo $ISBN ?? ''; ?>">Modifica</a></td>
  </tr>
</table>
</p>
