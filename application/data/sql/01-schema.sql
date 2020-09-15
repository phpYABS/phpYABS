USE phpyabs;

CREATE TABLE `phpyabs_acquisti` (
  `IdLibro` int(10) unsigned NOT NULL auto_increment,
  `IdAcquisto` int(10) unsigned NOT NULL default '0',
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  PRIMARY KEY  (`IdLibro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phpyabs_hits` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `Hits` int(11) NOT NULL default '0',
  `Trovato` enum('si','no') NOT NULL default 'si',
  PRIMARY KEY  (`ISBN`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `phpyabs_libri` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `Titolo` varchar(40) NOT NULL default '',
  `Autore` varchar(20) default NULL,
  `Editore` varchar(25) default NULL,
  `Prezzo` decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`ISBN`),
  KEY `Titolo` (`Titolo`),
  KEY `Autore` (`Autore`),
  KEY `Editore` (`Editore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phpyabs_valutazioni` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `Valutazione` enum('zero','rotmed','rotsup','buono') NOT NULL default 'zero',
  PRIMARY KEY  (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `phpyabs_valutazioni`
  ADD CONSTRAINT `phpyabs_libri_isbn` FOREIGN KEY (`ISBN`) REFERENCES `phpyabs_libri` (`ISBN`) ON DELETE CASCADE ON UPDATE CASCADE;
