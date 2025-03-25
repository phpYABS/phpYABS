CREATE DATABASE phpyabs;
CREATE DATABASE phpyabs_test;
CREATE USER phpyabs@`%` IDENTIFIED BY 'yabbadabbadoo';
GRANT ALL ON phpyabs.* TO phpyabs@`%`;
GRANT ALL ON phpyabs_test.* TO phpyabs@`%`;
