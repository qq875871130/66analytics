UPDATE `settings` SET `value` = '{\"version\":\"5.1.0\", \"code\":\"510\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

INSERT IGNORE INTO `settings` (`key`, `value`) VALUES ('opengraph', '');
