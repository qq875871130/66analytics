<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Models\Model;
use Altum\Models\User;
use Altum\Response;
use MaxMind\Db\Reader;

class PixelTrack extends Controller {
    public $website;
    public $website_user;

    public function index() {

        if(!isset($_SERVER['HTTP_REFERER'])) {
            die(json_encode(language()->pixel_track->error_message->no_referrer));
        }

        /* Check against bots */
        $CrawlerDetect = new \Jaybizzle\CrawlerDetect\CrawlerDetect();

        if($CrawlerDetect->isCrawler()) {
            die(json_encode(language()->pixel_track->error_message->excluded_bot));
        }

        /* Get the Payload of the Post */
        $payload = @file_get_contents('php://input');
        $post = json_decode($payload);

        if(!$post) {
            die(json_encode(language()->pixel_track->error_message->no_post));
        }

        /* Clean the pixel key */
        $pixel_key = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;
        $date = \Altum\Date::$date;

        /* Allowed types of requests to this endpoint */
        $allowed_types = [
            /* Sessions events */
            'initiate_visitor',
            'landing_page',
            'pageview',

            /* Events children */
            'click',
            'scroll',
            'form',
            'resize',

            /* Sessions replays */
            'replays',

            /* Heatmaps */
            'heatmap_snapshot',

            /* Goal conversions */
            'goal_conversion'
        ];

        if(!isset($post->type) || isset($post->type) && !in_array($post->type, $allowed_types)) {
            die(json_encode(language()->pixel_track->error_message->type_not_allowed));
        }

        /* Parsed referrer */
        $parsed_referrer = parse_url($_SERVER['HTTP_REFERER']);

        /* Find the website for the domain */
        $host = Database::clean_string($parsed_referrer['host']);

        /* Remove www. from the host */
        $prefix = 'www.';

        if(mb_substr($host, 0, mb_strlen($prefix)) == $prefix) {
            $host = mb_substr($host, mb_strlen($prefix));
        }

        /* Get the details of the campaign from the database */
        $website = $this->website = (new \Altum\Models\Website())->get_website_by_pixel_key($pixel_key);

        /* Make sure the campaign has access */
        if(!$website) {
            die(json_encode(language()->pixel_track->error_message->no_website));
        }

        if(
            !$website->is_enabled
            || ($website->host != $host && $website->host != 'www.' . $host)
        ) {
            die('1');
        }

        /* Check excluded IPs */
        $excluded_ips = explode(',', $this->website->excluded_ips);

        /* Do not track if its an excluded ip */
        if(in_array(get_ip(), $excluded_ips)) {
            die(json_encode(language()->pixel_track->error_message->excluded_ip));
        }

        /* Make sure to get the user data and confirm the user is ok */
        $user = $this->website_user = (new \Altum\Models\User())->get_user_by_user_id($website->user_id);

        if(!$user) {
            die('2');
        }

        if($user->status != 1) {
            die('3');
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($user);

        /* Check against available limits */
        if(
            ($this->website_user->plan_settings->sessions_events_limit != -1 && $this->website->current_month_sessions_events >= $this->website_user->plan_settings->sessions_events_limit) ||

            (
                $this->website_user->plan_settings->events_children_limit != -1 &&
                $this->website->current_month_events_children >= $this->website_user->plan_settings->events_children_limit &&
                in_array($post->type, ['click', 'scroll', 'form','resize']) &&
                !isset($post->heatmap_id)
            ) ||

            (
                $this->website_user->plan_settings->sessions_replays_limit != -1 &&
                $this->website->current_month_sessions_replays >= $this->website_user->plan_settings->sessions_replays_limit &&
                in_array($post->type, ['replays'])
            ) ||

            (
                $this->website_user->plan_settings->websites_heatmaps_limit == 0 &&
                in_array($post->type, ['click', 'scroll']) &&
                isset($post->heatmap_id)
            ) ||

            (
                $this->website_user->plan_settings->websites_goals_limit == 0 &&
                in_array($post->type, ['goal_conversion'])
            )
        ) {
            die(json_encode(language()->pixel_track->error_message->plan_limit));
        }

        /* Lightweight */
        if($website->tracking_type == 'lightweight') {
            /* Processing depending on the type of request */
            switch($post->type) {
                case 'landing_page':
                case 'pageview':

                    /* Process referrer */
                    $referrer = parse_url($post->data->referrer);

                    /* Check if the referrer comes from the same location */
                    if(
                        isset($referrer['host'])
                        && $referrer['host'] == $this->website->host
                        && (
                            isset($referrer['path']) && mb_substr($referrer['path'], 0, mb_strlen($this->website->path)) == $this->website->path
                        )
                    ) {
                        $referrer = [
                            'host' => null,
                            'path' => null
                        ];
                    }

                    if(isset($referrer['host']) && !isset($referrer['path'])) {
                        $referrer['path'] = '/';
                    }

                    /* Detect the location */
                    try {
                        $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
                    } catch(\Exception $exception) {
                        /* :) */
                    }

                    $location = [
                        'city_name' => isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null,
                        'country_code' => isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null,
                    ];

                    /* Detect extra details about the user */
                    $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

                    /* Detect extra details about the user */
                    $os = [
                        'name' => $whichbrowser->os->name ?? null
                    ];

                    $browser = [
                        'name' => $whichbrowser->browser->name ?? null,
                        'language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null
                    ];

                    $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                    $screen_resolution = (int) $post->data->resolution->width . 'x' . (int) $post->data->resolution->height;

                    $event = [
                        'path'              => $this->website->path ? preg_replace('/^' . preg_quote($this->website->path, '/') . '/', '', $post->data->path) : $post->data->path ?? '',
                        'referrer_host'     => $referrer['host'] ?? null,
                        'referrer_path'     => $referrer['path'] ?? null,
                        'utm_source'        => $post->data->utm->source ?? null,
                        'utm_medium'        => $post->data->utm->medium ?? null,
                        'utm_campaign'      => $post->data->utm->campaign ?? null,
                    ];

                    /* Insert the event */
                    db()->insert('lightweight_events', [
                        'website_id' => $this->website->website_id,
                        'type' => $post->type,
                        'path' => $event['path'],
                        'referrer_host' => $event['referrer_host'],
                        'referrer_path' => $event['referrer_path'],
                        'utm_source' => $event['utm_source'],
                        'utm_medium' => $event['utm_medium'],
                        'utm_campaign' => $event['utm_campaign'],
                        'country_code' => $location['country_code'],
                        'city_name' => $location['city_name'],
                        'os_name' => $os['name'],
                        'browser_name' => $browser['name'],
                        'browser_language' => $browser['language'],
                        'screen_resolution' => $screen_resolution,
                        'device_type' => $device_type,
                        'date' => $date,
                    ]);

                    break;

                /* Handling goal conversions */
                case 'goal_conversion':

                    /* Some data to use */
                    $goal_key = Database::clean_string($post->goal_key);

                    /* Get the goal if any */
                    $website_goal = database()->query("SELECT `goal_id`, `type`, `path` FROM `websites_goals` WHERE `website_id` = {$this->website->website_id} AND `key` = '{$goal_key}'")->fetch_object() ?? null;

                    if(!$website_goal) {
                        die('4');
                    }

                    /* Check if the goal is valid */
                    if($website_goal->type == 'pageview') {
                        $referrer_explode = explode($host, $post->url);

                        if(!isset($referrer_explode[1]) || (isset($referrer_explode[1]) && $referrer_explode[1] != $this->website->path . $website_goal->path)) {
                            die('5');
                        }
                    }

                    /* Prepare to insert the goal conversion */
                    db()->insert('goals_conversions', [
                        'goal_id' => $website_goal->goal_id,
                        'website_id' => $this->website->website_id,
                        'date' => $date
                    ]);

                    break;
            }

            /* Update the website usage */
            db()->where('website_id', $this->website->website_id)->update('websites', ['current_month_sessions_events' => db()->inc()]);

        }

        if($website->tracking_type == 'normal') {
            /* Processing depending on the type of request */
            switch($post->type) {

                /* Initiate the visitor event */
                case 'initiate_visitor':

                    /* Check for custom parameters */
                    $dirty_custom_parameters = $post->data->custom_parameters ?? null;
                    $custom_parameters = [];

                    if($dirty_custom_parameters) {

                        $i = 1;
                        foreach ((array)$dirty_custom_parameters as $key => $value) {
                            $key = Database::clean_string($key);
                            $value = Database::clean_string($value);

                            if($i++ >= 5) {
                                break;
                            } else {
                                $custom_parameters[$key] = $value;
                            }
                        }
                    }

                    $custom_parameters = json_encode($custom_parameters);

                    /* Detect the location */
                    try {
                        $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
                    } catch(\Exception $exception) {
                        /* :) */
                    }

                    $location = [
                        'city_name' => isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null,
                        'country_code' => isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null,
                        'country_name' => isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['names']['en'] : null,
                    ];

                    /* Detect extra details about the user */
                    $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

                    /* Detect extra details about the user */
                    $os = [
                        'name' => $whichbrowser->os->name ?? null,
                        'version' => $whichbrowser->os->version->value ?? null
                    ];

                    $browser = [
                        'name' => $whichbrowser->browser->name ?? null,
                        'version' => $whichbrowser->browser->version->value ?? null,
                        'language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null
                    ];

                    $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                    $screen_resolution = (int)$post->data->resolution->width . 'x' . (int)$post->data->resolution->height;

                    /* Insert or update the visitor */
                    $stmt = database()->prepare("
                        INSERT INTO 
                            `websites_visitors` (`website_id`, `visitor_uuid`, `custom_parameters`, `country_code`, `city_name`, `os_name`, `os_version`, `browser_name`, `browser_version`, `browser_language`, `screen_resolution`, `device_type`, `date`, `last_date`) 
                        VALUES 
                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            `custom_parameters` = VALUES (custom_parameters),
                            `country_code` = VALUES (country_code),
                            `city_name` = VALUES (city_name),
                            `os_name` = VALUES (os_name),
                            `os_version` = VALUES (os_version),
                            `browser_name` = VALUES (browser_name),
                            `browser_version` = VALUES (browser_version),
                            `browser_language` = VALUES (browser_language),
                            `screen_resolution` = VALUES (screen_resolution),
                            `device_type` = VALUES (device_type),
                            `last_date` = VALUES (last_date)
                    ");
                    $stmt->bind_param(
                        'ssssssssssssss',
                        $this->website->website_id,
                        $post->visitor_uuid,
                        $custom_parameters,
                        $location['country_code'],
                        $location['city_name'],
                        $os['name'],
                        $os['version'],
                        $browser['name'],
                        $browser['version'],
                        $browser['language'],
                        $screen_resolution,
                        $device_type,
                        $date,
                        $date
                    );
                    $stmt->execute();
                    $stmt->close();

                    break;

                /* Landing page event */
                case 'landing_page':

                    $post->data = json_encode($post->data);

                    /* Make sure to check if the visitor exists */
                    $visitor = db()->where('visitor_uuid', $post->visitor_uuid)->where('website_id', $this->website->website_id)->getOne('websites_visitors', ['visitor_id']);

                    if(!$visitor) {
                        Response::json('', 'error', ['refresh' => 'visitor']);
                    }

                    /* Insert the session */
                    $session_id = db()->insert('visitors_sessions', [
                        'session_uuid' => $post->visitor_session_uuid,
                        'visitor_id' => $visitor->visitor_id,
                        'website_id' => $this->website->website_id,
                        'date' => $date,
                    ]);

                    /* If session is false then it was a double request, end it */
                    if(!$session_id) {
                        die('6');
                    }

                    /* Insert the event */
                    $event_id = $this->insert_session_event(
                        $post->visitor_session_event_uuid,
                        $session_id,
                        $visitor->visitor_id,
                        $this->website->website_id,
                        $post->type,
                        $post->data,
                        $date
                    );

                    /* Update the last action of the visitor */
                    db()->where('visitor_id', $visitor->visitor_id)->update('websites_visitors', ['last_date' => $date, 'total_sessions' => db()->inc(), 'last_event_id' => $event_id]);

                    break;

                /* Pageview event */
                case 'pageview':

                    $post->data = json_encode($post->data);

                    /* Make sure to check if the visitor exists */
                    $visitor = db()->where('visitor_uuid', $post->visitor_uuid)->where('website_id', $this->website->website_id)->getOne('websites_visitors', ['visitor_id']);

                    if(!$visitor) {
                        Response::json('', 'error', ['refresh' => 'visitor']);
                    }

                    /* Make sure to check if the session exists */
                    $session = db()->where('session_uuid', $post->visitor_session_uuid)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('visitors_sessions', ['session_id', 'total_events']);

                    if(!$session) {
                        Response::json('', 'error', ['refresh' => 'session']);
                    }

                    /* Insert the event */
                    $event_id = $this->insert_session_event(
                        $post->visitor_session_event_uuid,
                        $session->session_id,
                        $visitor->visitor_id,
                        $this->website->website_id,
                        $post->type,
                        $post->data,
                        $date
                    );

                    /* Check if we should update the landing page event to set it as not bounced */
                    if($session->total_events == 1) {
                        db()->where('session_id', $session->session_id)->where('type', 'landing_page')->update('sessions_events', ['has_bounced' => 0]);
                    }

                    /* Update session */
                    db()->where('session_id', $session->session_id)->update('visitors_sessions', ['total_events' => db()->inc()]);

                    /* Update visitor */
                    db()->where('visitor_id', $visitor->visitor_id)->update('websites_visitors', ['last_date' => $date, 'last_event_id' => $event_id]);

                    break;

                /* Events Children */
                case 'click':
                case 'scroll':
                case 'form':
                case 'resize':

                    $post->data = json_encode($post->data);

                    /* Make sure to check if the visitor exists */
                    $visitor = db()->where('visitor_uuid', $post->visitor_uuid)->where('website_id', $this->website->website_id)->getOne('websites_visitors', ['visitor_id']);

                    if(!$visitor) {
                        Response::json('', 'error', ['refresh' => 'visitor']);
                    }

                    /* Make sure to check if the session exists */
                    $session = db()->where('session_uuid', $post->visitor_session_uuid)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('visitors_sessions', ['session_id']);

                    if(!$session) {
                        Response::json('', 'error', ['refresh' => 'session']);
                    }

                    /* Make sure to check if the main event exists */
                    $event = db()->where('event_uuid', $post->visitor_session_event_uuid)->where('session_id', $session->session_id)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('sessions_events', ['event_id']);

                    if(!$event) {
                        die('7');
                    }

                    $expiration_date = (new \DateTime($date))->modify('+' . $this->website_user->plan_settings->events_children_retention . ' days')->format('Y-m-d');
                    $snapshot_id = null;

                    /* Check if the event is sent for a heatmap */
                    if(isset($post->heatmap_id) && $post->heatmap_id && $this->website_user->plan_settings->websites_heatmaps_limit != 0) {

                        /* Make sure the heatmap exists and matches the data */
                        $heatmap_id = (int) Database::clean_string($post->heatmap_id);
                        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                        $snapshot_id_type = 'snapshot_id_' . $device_type;

                        /* Get heatmaps if any */
                        $website_heatmap = database()->query("SELECT `heatmap_id`, `path`, `{$snapshot_id_type}` FROM `websites_heatmaps` WHERE `website_id` = {$this->website->website_id} AND `heatmap_id` = {$heatmap_id} AND `{$snapshot_id_type}` IS NOT NULL AND `is_enabled` = 1")->fetch_object() ?? null;

                        if(!$website_heatmap) {
                            die('8');
                        }

                        /* Check the referrer against the set heatmap path */
                        $referrer_explode = explode($host, $post->url);

                        if(!isset($referrer_explode[1]) || (isset($referrer_explode[1]) && $referrer_explode[1] != $this->website->path . $website_heatmap->path)) {
                            die('n');
                        }

                        $snapshot_id = $website_heatmap->{$snapshot_id_type};

                        $expiration_date = null;
                    }

                    /* Insert the event */
                    $this->insert_session_event_child(
                        $event->event_id,
                        $session->session_id,
                        $visitor->visitor_id,
                        $snapshot_id,
                        $this->website->website_id,
                        $post->type,
                        $post->data,
                        (int)$post->count,
                        $date,
                        $expiration_date
                    );

                    break;

                /* Replay events */
                case 'replays':

                    /* Make sure to check if the visitor exists */
                    $visitor = db()->where('visitor_uuid', $post->visitor_uuid)->where('website_id', $this->website->website_id)->getOne('websites_visitors', ['visitor_id']);

                    if(!$visitor) {
                        die('9');
                    }

                    /* Make sure to check if the session exists */
                    $session = db()->where('session_uuid', $post->visitor_session_uuid)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('visitors_sessions', ['session_id']);

                    if(!$session) {
                        Response::json('', 'error', ['refresh' => 'session']);
                    }

                    /* Check if the replay exists and get the data */
                    $replay = db()->where('session_id', $session->session_id)->getOne('sessions_replays');

                    /* Check if the time limit was crossed */
                    if($replay && (new \DateTime($replay->last_date))->diff((new \DateTime($replay->date)))->i >= $this->website_user->plan_settings->sessions_replays_time_limit) {
                        die('10');
                    }

                    /* Expiration date for the replay */
                    $expiration_date = (new \DateTime($date))->modify('+' . $this->website_user->plan_settings->sessions_replays_retention . ' days')->format('Y-m-d');

                    /* New events to save */
                    $events = count($post->data);

                    /* Insert or update */
                    $stmt = database()->prepare("
                        INSERT INTO
                            `sessions_replays` (`session_id`, `visitor_id`, `website_id`, `events`, `date`, `last_date`, `expiration_date`) 
                        VALUES
                            (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            `events` = `events` + VALUES (events),
                            `last_date` = VALUES (last_date),
                            `expiration_date` = VALUES (expiration_date)
                    ");
                    $stmt->bind_param(
                        'sssssss',
                        $session->session_id,
                        $visitor->visitor_id,
                        $this->website->website_id,
                        $events,
                        $date,
                        $date,
                        $expiration_date
                    );
                    $stmt->execute();
                    $affected_rows = $stmt->affected_rows;
                    $stmt->close();

                    /* If its a new session replay, insert the usage */
                    if($affected_rows == 1) {
                        db()->where('website_id', $this->website->website_id)->update('websites', ['current_month_sessions_replays' => db()->inc()]);
                    }

                    /* Try to get already existing session replay data, if any */
                    $cache_instance = \Altum\Cache::$store_adapter->getItem('session_replay_' . $session->session_id);

                    $session_replay_data = $cache_instance->get();

                    if(is_null($session_replay_data)) {
                        $session_replay_data = [];
                    }

                    /* Gzencode the big data */
                    foreach($post->data as $key => $value) {
                        $post->data[$key]->data = gzencode(json_encode($post->data[$key]->data), 4);

                        $session_replay_data[] = $post->data[$key];
                    }

                    /* Prepare the expiration seconds data */
                    $expiration_seconds = (new \DateTime($date))->modify('+' . $this->website_user->plan_settings->sessions_replays_retention . ' days')->getTimestamp() - (new \DateTime())->getTimestamp();

                    $cache_instance->set($session_replay_data)->expiresAfter($expiration_seconds)->addTag('session_replay_user_' . $this->website->user_id)->addTag('session_replay_website_' . $this->website->website_id);

                    \Altum\Cache::$store_adapter->save($cache_instance);

                    break;

                /* The initial snapshot of the heatmap */
                case 'heatmap_snapshot':

                    /* Some data to use */
                    $heatmap_id = (int) Database::clean_string($post->heatmap_id);
                    $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                    $snapshot_id_type = 'snapshot_id_' . $device_type;

                    /* Get heatmaps if any */
                    $website_heatmap = database()->query("SELECT `heatmap_id`, `path` FROM `websites_heatmaps` WHERE `website_id` = {$this->website->website_id} AND `heatmap_id` = {$heatmap_id} AND `{$snapshot_id_type}` IS NULL AND `is_enabled` = 1")->fetch_object() ?? null;

                    if(!$website_heatmap) {
                        die('11');
                    }

                    /* Check the referrer against the set heatmap path */
                    $referrer_explode = explode($host, $post->url);

                    if(!isset($referrer_explode[1]) || (isset($referrer_explode[1]) && $referrer_explode[1] != $this->website->path . $website_heatmap->path)) {
                        die('12');
                    }

                    /* Gzencode the data for storage in the database */
                    $data = gzencode(json_encode($post->data), 4);

                    /* Prepare to insert the snapshot */
                    $stmt = database()->prepare("
                        INSERT INTO
                            `heatmaps_snapshots` (`heatmap_id`, `website_id`, `type`, `data`, `date`) 
                        VALUES
                            (?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        'sssss',
                        $heatmap_id,
                        $this->website->website_id,
                        $device_type,
                        $data,
                        $date
                    );
                    $stmt->execute();
                    $snapshot_id = $stmt->insert_id;
                    $stmt->close();

                    database()->query("UPDATE `websites_heatmaps` SET `{$snapshot_id_type}` = {$snapshot_id} WHERE `heatmap_id` = {$website_heatmap->heatmap_id}");

                    break;

                /* Handling goal conversions */
                case 'goal_conversion':

                    /* Make sure to check if the visitor exists */
                    $visitor = db()->where('visitor_uuid', $post->visitor_uuid)->where('website_id', $this->website->website_id)->getOne('websites_visitors', ['visitor_id']);

                    if(!$visitor) {
                        Response::json('', 'error', ['refresh' => 'visitor']);
                    }

                    /* Make sure to check if the session exists */
                    $session = db()->where('session_uuid', $post->visitor_session_uuid)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('visitors_sessions', ['session_id']);

                    if(!$session) {
                        Response::json('', 'error', ['refresh' => 'session']);
                    }

                    /* Make sure to check if the main event exists */
                    $event = db()->where('event_uuid', $post->visitor_session_event_uuid)->where('session_id', $session->session_id)->where('visitor_id', $visitor->visitor_id)->where('website_id', $this->website->website_id)->getOne('sessions_events', ['event_id']);

                    if(!$event) {
                        die('13');
                    }

                    /* Some data to use */
                    $goal_key = Database::clean_string($post->goal_key);

                    /* Get the goal if any */
                    $website_goal = database()->query("SELECT `goal_id`, `type`, `path` FROM `websites_goals` WHERE `website_id` = {$this->website->website_id} AND `key` = '{$goal_key}'")->fetch_object() ?? null;

                    if(!$website_goal) {
                        die('14');
                    }

                    /* Check if the goal is valid */
                    if($website_goal->type == 'pageview') {
                        /* Check the referrer against the set goal path */
                        $referrer_explode = explode($host, $post->url);

                        if(!isset($referrer_explode[1]) || (isset($referrer_explode[1]) && $referrer_explode[1] != $this->website->path . $website_goal->path)) {
                            die('15');
                        }
                    }

                    /* Make sure the goal for this user didnt already convert */
                    $conversion = Database::get(['conversion_id'], 'goals_conversions', ['visitor_id' => $visitor->visitor_id, 'website_id' => $this->website->website_id, 'goal_id' => $website_goal->goal_id]);

                    if($conversion) {
                        die('16');
                    }

                    /* Prepare to insert the goal conversion */
                    db()->insert('goals_conversions', [
                        'event_id' => $event->event_id,
                        'session_id' => $session->session_id,
                        'visitor_id' => $visitor->visitor_id,
                        'website_id' => $this->website->website_id,
                        'goal_id' => $website_goal->goal_id,
                        'date' => $date,
                    ]);

                    break;
            }
        }
    }

    private function insert_session_event($event_uuid, $session_id, $visitor_id, $website_id, $type, $data, $date) {

        /* Parse data */
        $data = json_decode($data);

        /* Process the page path */
        $data->path = $this->website->path ? preg_replace('/^' . preg_quote($this->website->path, '/') . '/', '', $data->path) : $data->path;

        /* Process referrer */
        $referrer = parse_url($data->referrer);

        /* Check if the referrer comes from the same location */
        if(
            isset($referrer['host'])
            && $referrer['host'] == $this->website->host
            && (
                isset($referrer['path']) && mb_substr($referrer['path'], 0, mb_strlen($this->website->path)) == $this->website->path
            )
        ) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        if(isset($referrer['host']) && !isset($referrer['path'])) {
            $referrer['path'] = '/';
        }

        $session_data = [
            'path'              => $data->path ?? '',
            'title'             => $data->title ?? '',
            'referrer_host'     => $referrer['host'] ?? null,
            'referrer_path'     => $referrer['path'] ?? null,
            'utm_source'        => $data->utm->source ?? null,
            'utm_medium'        => $data->utm->medium ?? null,
            'utm_campaign'      => $data->utm->campaign ?? null,
            'utm_term'          => $data->utm->term ?? null,
            'utm_content'       => $data->utm->content ?? null,
            'viewport_width'    => $data->viewport->width ?? 0,
            'viewport_height'   => $data->viewport->height ?? 0,
            'has_bounced'       => $type == 'landing_page' ? 1 : null
        ];

        /* Insert the event */
        $event_id = db()->insert('sessions_events', [
            'event_uuid' => $event_uuid,
            'session_id' => $session_id,
            'visitor_id' => $visitor_id,
            'website_id' => $website_id,
            'type' => $type,
            'path' => $session_data['path'],
            'title' => $session_data['title'],
            'referrer_host' => $session_data['referrer_host'],
            'referrer_path' => $session_data['referrer_path'],
            'utm_source' => $session_data['utm_source'],
            'utm_medium' => $session_data['utm_medium'],
            'utm_campaign' => $session_data['utm_campaign'],
            'utm_term' => $session_data['utm_term'],
            'utm_content' => $session_data['utm_content'],
            'viewport_width' => $session_data['viewport_width'],
            'viewport_height' => $session_data['viewport_height'],
            'has_bounced' => $session_data['has_bounced'],
            'date' => $date,
        ]);

        /* Update the website usage */
        db()->where('website_id', $website_id)->update('websites', ['current_month_sessions_events' => db()->inc()]);

        return $event_id;
    }

    private function insert_session_event_child($event_id, $session_id, $visitor_id, $snapshot_id, $website_id, $type, $data, $count, $date, $expiration_date) {

        /* Insert the event */
        $stmt = database()->prepare("
            INSERT INTO
                `events_children` (`event_id`, `session_id`, `visitor_id`, `snapshot_id`, `website_id`, `type`, `data`, `count`, `date`, `expiration_date`) 
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'ssssssssss',
            $event_id,
            $session_id,
            $visitor_id,
            $snapshot_id,
            $website_id,
            $type,
            $data,
            $count,
            $date,
            $expiration_date
        );
        $stmt->execute();
        $stmt->close();

        /* Update the website usage */
        db()->where('website_id', $website_id)->update('websites', ['current_month_events_children' => db()->inc()]);

    }

}
