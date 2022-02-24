<?php

namespace Altum\Plugin;

class Affiliate {
    public static $plugin_id = 'affiliate';

    public static function install() {

        /* Run the installation process of the plugin */
        $queries = [
            "INSERT IGNORE INTO `settings` (`key`, `value`) VALUES ('affiliate', '');",

            "CREATE TABLE IF NOT EXISTS `affiliates_commissions` (
            `affiliate_commission_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
            `referred_user_id` int(11) DEFAULT NULL,
            `payment_id` int(11) unsigned DEFAULT NULL,
            `amount` float DEFAULT NULL,
            `currency` varchar(4) DEFAULT NULL,
            `is_withdrawn` tinyint(4) unsigned DEFAULT '0',
            `datetime` datetime DEFAULT NULL,
            PRIMARY KEY (`affiliate_commission_id`),
            UNIQUE KEY `affiliate_commission_id` (`affiliate_commission_id`),
            KEY `user_id` (`user_id`),
            KEY `referred_user_id` (`referred_user_id`),
            KEY `payment_id` (`payment_id`),
            CONSTRAINT `affiliates_commissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `affiliates_commissions_ibfk_2` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT `affiliates_commissions_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            "CREATE TABLE IF NOT EXISTS `affiliates_withdrawals` (
            `affiliate_withdrawal_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
            `amount` float DEFAULT NULL,
            `currency` varchar(4) DEFAULT NULL,
            `note` varchar(1024) DEFAULT NULL,
            `affiliate_commissions_ids` text,
            `is_paid` tinyint(4) unsigned DEFAULT NULL,
            `datetime` datetime DEFAULT NULL,
            PRIMARY KEY (`affiliate_withdrawal_id`),
            UNIQUE KEY `affiliate_withdrawal_id` (`affiliate_withdrawal_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach($queries as $query) {
            database()->query($query);
        }

        return self::save_status(1);

    }

    public static function uninstall() {

        /* Run the installation process of the plugin */
        $queries = [
            "DELETE FROM `settings` WHERE `key` = 'affiliate';",
            "DROP TABLE IF EXISTS `affiliates_commissions`;",
            "DROP TABLE IF EXISTS `affiliates_withdrawals`;",
        ];

        foreach($queries as $query) {
            database()->query($query);
        }

        return self::save_status(-1);

    }

    public static function activate() {
        return self::save_status(1);
    }

    public static function disable() {
        return self::save_status(0);
    }

    private static function save_status($new_status) {

        /* Enable the plugin from the config file */
        $new_config = clone \Altum\Plugin::get(self::$plugin_id);
        unset($new_config->path);
        $new_config->status = $new_status;

        /* Save the new config file */
        $config_saved = file_put_contents(\Altum\Plugin::get(self::$plugin_id)->path . 'config.json', json_encode($new_config));

        return (bool) $config_saved;

    }

}
