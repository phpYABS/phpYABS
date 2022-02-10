<form action="/modules.php" method="GET">
<table width="600" border="1" align="center">
  <tr>
    <th width="80">ISBN:</th>
    <td><?php echo $ISBN; ?>
    <input type="hidden" name="ISBN" value="<?php echo $ISBN; ?>">
    </td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td><input type="text" name="Titolo" value="<?php echo $Titolo; ?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td><input type="text" name="Autore" value="<?php echo $Autore; ?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td><input type="text" name="Editore" value="<?php echo $Editore; ?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td><input type="text" name="Prezzo" value="<?php echo $Prezzo; ?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><select name="Valutazione">
      <option value="NULL" <?php echo $selnull; ?>>Nessuna</option>
      <option value="zero" <?php echo $selzero; ?>>Macero</option>
      <option value="rotmed" <?php echo $selrotmed; ?>>Rottamazione Medie</option>
      <option value="rotsup" <?php echo $selrotsup; ?>>Rottamazione Superiori</option>
      <option value="buono" <?php echo $selbuono; ?>>Buono</option>
    </select></td>
  </tr>
  <tr>
    <td><input id="frm_book_edit_submit" type="submit" value="Modifica"/></td>
    <td><input type="Reset" value="Annulla"/></td>
  </tr>
</table>
  <input type="hidden" name="Nome" value="Libri">
  <input type="hidden" name="Azione" value="Modifica">
</form>
