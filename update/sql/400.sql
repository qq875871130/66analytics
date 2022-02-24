UPDATE `settings` SET `value` = '{\"version\":\"4.0.0\", \"code\":\"400\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table users add api_key varchar(32) null after name;

-- SEPARATOR --

create index users_api_key_index on users (api_key);

-- SEPARATOR --

UPDATE users SET api_key = concat(
lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0),
lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0)
);

-- EXTENDED SEPARATOR --

alter table payments modify payment_id varchar(128) null;

