<form action="/modules.php" method="GET">
<table width="600" border="1" align="center">
  <tr>
    <th width="80">ISBN:</th>
    <td><?=$ISBN?>
    <input type="hidden" name="ISBN" value="<?=$ISBN?>">
    </td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td><input type="text" name="Titolo" value="<?=$Titolo?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td><input type="text" name="Autore" value="<?=$Autore?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td><input type="text" name="Editore" value="<?=$Editore?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td><input type="text" name="Prezzo" value="<?=$Prezzo?>" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><select name="Valutazione">
      <option value="NULL" <?=$selnull?>>Nessuna</option>
      <option value="zero" <?=$selzero?>>Macero</option>
      <option value="rotmed" <?=$selrotmed?>>Rottamazione Medie</option>
      <option value="rotsup" <?=$selrotsup?>>Rottamazione Superiori</option>
      <option value="buono" <?=$selbuono?>>Buono</option>
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
