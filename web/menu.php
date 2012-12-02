<?php
// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

/**
 * $Id: file-header.php 299 2009-11-21 17:09:54Z dvbellet $
 *
 * phpYABS - Web-based book management
 * Copyright (C) 2009 Davide Bellettini
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'application/includes/common.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<title>phpYabs - menu</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<base target="main">
</HEAD>
<BODY>
<h1>Menu</h1>
<p><a href="modules.php?Nome=Acquisti&Azione=Nuovo">Nuovo Acquisto</a></p>
<form action="modules.php" method="get" name="acquisto">
  Recupera acquisto:
  <input name="IdAcquisto" type="text" id="IdAcquisto" maxlength="10" style="width:90px">
  <input name="Nome" value="Acquisti" type="hidden">
  <input name="Azione" value="Acquisto" type="hidden">
  <input type="submit" value="Ok">
</form>
<?php if (edit) {?>
<p><a href="modules.php?Nome=Libri&Azione=Aggiungi">Aggiungi Libro</a></p>
<p><a href="modules.php?Nome=Libri&Azione=Modifica">Modifica Libro</a></p>
<p><a href="modules.php?Nome=Libri&Azione=Cancella">Cancella Libro</a></p>
<?php } ?>
<p><a href="modules.php?Nome=Acquisti&Azione=Elenco">Elenco Acquisti</a></p>
<p><a href="modules.php?Nome=Statistiche">Statistiche</a></p>
<p><a href="modules.php?Nome=Libri&Azione=Elenco" target="_blank">Elenco Libri</a></p>
</BODY>
</HTML>
