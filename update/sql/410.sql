UPDATE `settings` SET `value` = '{\"version\":\"4.1.0\", \"code\":\"410\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table users add one_time_login_code varchar(32) null after twofa_secret;

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('webhooks', '{"user_new":"","user_delete":""}');


