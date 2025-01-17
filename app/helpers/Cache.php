<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

/* Simple wrapper for phpFastCache */

class Cache {
    public static $adapter;
    public static $store_adapter;

    public static function initialize($force_enable = false) {

        $driver = $force_enable ? 'Files' : (CACHE ? 'Files' : 'Devnull');

        /* Cache adapter for phpFastCache */
        if($driver == 'Files') {
            $config = new \Phpfastcache\Drivers\Files\Config([
                'securityKey' => '66analytics',
                'path' => UPLOADS_PATH . 'cache',
                'preventCacheSlams' => true,
                'cacheSlamsTimeout' => 20,
                'secureFileManipulation' => true
            ]);
        } else {
            $config = new \Phpfastcache\Config\Config([
                'path' => UPLOADS_PATH . 'cache',
            ]);
        }

        self::$adapter = \Phpfastcache\CacheManager::getInstance($driver, $config);

    }

    public static function store_initialize() {

        $driver = 'Files';

        /* Cache adapter for phpFastCache */
        $config = new \Phpfastcache\Drivers\Files\Config([
            'securityKey' => '66analytics',
            'path' => UPLOADS_PATH . 'store'
        ]);

        self::$store_adapter = \Phpfastcache\CacheManager::getInstance($driver, $config);

    }

}
