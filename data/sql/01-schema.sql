USE phpyabs;

CREATE TABLE `destinations` (
  `ISBN` char(9) NOT NULL,
  `destination` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ISBN`, `destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `purchases` (
  `book_id` int(10) unsigned NOT NULL auto_increment,
  `purchase_id` int(10) unsigned NOT NULL default '0',
  `ISBN` char(9) NOT NULL,
  PRIMARY KEY  (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hits` (
  `ISBN` char(9) NOT NULL,
  `hits` int(11) NOT NULL default 0,
  `found` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `books` (
  `ISBN` char(9) NOT NULL,
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
  `ISBN` char(9) NOT NULL,
  `rate` enum('zero','rotmed','rotsup','buono') NOT NULL default 'zero',
  PRIMARY KEY  (`ISBN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `buyback_rates`
  ADD CONSTRAINT `books_isbn_fk` FOREIGN KEY (`ISBN`) REFERENCES `books` (`ISBN`) ON DELETE CASCADE ON UPDATE CASCADE;
