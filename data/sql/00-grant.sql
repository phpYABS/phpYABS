CREATE DATABASE phpyabs;
CREATE USER phpyabs@`%` IDENTIFIED BY 'yabbadabbadoo';
GRANT ALL ON phpyabs.* TO phpyabs@`%`;
