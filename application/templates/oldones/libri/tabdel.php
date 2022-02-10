<p>
<table width="590" border="1" align="center">
  <tr>
    <th width="80">ISBN:</th>
    <td>
      <?php echo $ISBN; ?>
    </td>
  </tr>
  <tr>
    <th width="80">Titolo:</th>
    <td>
      <?php echo $Titolo; ?>
    </td>
  </tr>
  <tr>
    <th width="80">Autore:</th>
    <td>
      <?php echo $Autore; ?>
    </td>
  </tr>
  <tr>
    <th width="80">Editore:</th>
    <td>
      <?php echo $Editore; ?>
    </td>
  </tr>
  <tr>
    <th width="80">Prezzo:</th>
    <td>
      <?php echo $Prezzo; ?>
    </td>
  </tr>
  <tr>
    <th width="80">Valutazione:</th>
    <td><u><?php echo $Valutazione; ?></u></td>
  </tr>
</table>
</p>
<div align="center"><a href="<?php echo $_SERVER['PHP_SELF'] . '?Nome=' . $_GET['Nome'] . '&Azione=' . $_GET['Azione'] . '&ISBN=' .
$ISBN . '&cancella=on'; ?>">Cancella Libro</a>
