<p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<table width="600" border="1" align="center">
  <tr>
    <th width="80">ISBN:</th>
    <td><input name="ISBN" type="text" id="ISBN">
</td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td><input name="Titolo" type="text" id="Titolo" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td><input name="Autore" type="text" id="Autore" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td><input name="Editore" type="text" id="Editore" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td><input name="Prezzo" type="text" id="Prezzo" style="width:300px"></td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><select name="Valutazione" style="width:300px">
      <option value="NULL" selected>Nessuna</option>
      <option value="zero">Macero</option>
      <option value="rotmed">Rottamazione Medie</option>
      <option value="rotsup">Rottamazione Superiori</option>
      <option value="buono">Buono</option>
    </select></td>
  </tr>
  <tr>
    <td><input type="submit" value="Aggiungi"/></td>
    <td><input type="reset" value="Annulla"></td>
  </tr>
</table>
<input type="hidden" name="Nome" value="Libri">
<input type="hidden" name="Azione" value="Aggiungi">
</form>
</p>
