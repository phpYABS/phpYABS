USE phpyabs;

CREATE TABLE `purchases` (
  `book_id` int(10) unsigned NOT NULL auto_increment,
  `purchase_id` int(10) unsigned NOT NULL default '0',
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  PRIMARY KEY  (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hits` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `hits` int(11) NOT NULL default '0',
  `found` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`ISBN`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `books` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `title` varchar(40) NOT NULL default '',
  `author` varchar(20) default NULL,
  `publisher` varchar(25) default NULL,
  `price` decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`ISBN`),
  KEY `title` (`title`),
  KEY `author` (`author`),
  KEY `publisher` (`publisher`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `buyback_rates` (
  `ISBN` int(9) unsigned zerofill NOT NULL default '000000000',
  `condition` enum('zero','rotmed','rotsup','buono') NOT NULL default 'zero',
  PRIMARY KEY  (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `buyback_rates`
  ADD CONSTRAINT `books_isbn_fk` FOREIGN KEY (`ISBN`) REFERENCES `books` (`ISBN`) ON DELETE CASCADE ON UPDATE CASCADE;
