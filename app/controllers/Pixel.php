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

class Pixel extends Controller {

    public function index() {
        $seconds_to_cache = settings()->analytics->pixel_cache;
        header('Content-Type: application/javascript');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds_to_cache) . ' GMT');
        header('Pragma: cache');
        header('Cache-Control: max-age=' . $seconds_to_cache);

        if(!isset($_SERVER['HTTP_REFERER'])) {
            die(json_encode(language()->pixel_track->error_message->no_referrer));
        }

        /* Check against bots */
        $CrawlerDetect = new \Jaybizzle\CrawlerDetect\CrawlerDetect();

        if($CrawlerDetect->isCrawler()) {
            die(json_encode(language()->pixel_track->error_message->excluded_bot));
        }

        /* Clean the pixel key */
        $pixel_key = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;

        /* Find the website for the host */
        $host = Database::clean_string(parse_url($_SERVER['HTTP_REFERER'])['host']);

        /* Remove www. from the host */
        $prefix = 'www.';

        if(mb_substr($host, 0, mb_strlen($prefix)) == $prefix) {
            $host = mb_substr($host, mb_strlen($prefix));
        }

        /* Get the details of the website from the database */
        $website = (new \Altum\Models\Website())->get_website_by_pixel_key($pixel_key);

        if(!$website) {
            die(json_encode(language()->pixel_track->error_message->no_website));
        }

        if(
            !$website->is_enabled
            || ($website->host != $host && $website->host != 'www.' . $host)
        ) {
            die();
        }

        /* Make sure to get the user data and confirm the user is ok */
        $user = (new \Altum\Models\User())->get_user_by_user_id($website->user_id);

        if(!$user) {
            die();
        }

        if($user->status != 1) {
            die();
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($user);

        /* Make sure that the user didn't exceed the current plan */
        if($user->plan_settings->sessions_events_limit != -1 && $website->current_month_sessions_events >= $user->plan_settings->sessions_events_limit) {
            die(json_encode(language()->pixel_track->error_message->plan_limit));
        }

        $pixel_track_events_children = (bool) $website->events_children_is_enabled && ($user->plan_settings->events_children_limit == -1 || $website->current_month_events_children < $user->plan_settings->events_children_limit);
        $pixel_track_sessions_replays = (bool) settings()->analytics->sessions_replays_is_enabled && $website->sessions_replays_is_enabled && ($user->plan_settings->sessions_replays_limit == -1 || $website->current_month_sessions_replays < $user->plan_settings->sessions_replays_limit);

        /* Get heatmaps if any and if the user has rights */
        $pixel_heatmaps = [];

        if($website->tracking_type == 'normal' && $user->plan_settings->websites_heatmaps_limit != 0) {
            $pixel_heatmaps = (new \Altum\Models\WebsitesHeatmaps())->get_website_heatmaps_by_website_id($website->website_id);

            foreach($pixel_heatmaps as $key => $pixel_heatmap) {
                /* Make sure the heatmap is active */
                if(!$pixel_heatmap->is_enabled) {
                    unset($pixel_heatmaps[$key]);
                    continue;
                }

                /* Generate the full url needed to match for the heatmap */
                $pixel_heatmap->url = $website->host . $website->path . $pixel_heatmap->path;
            }
        }

        /* Get available goals for the website */
        if($user->plan_settings->websites_goals_limit != 0) {
            $pixel_goals = (new \Altum\Models\WebsitesGoals())->get_website_goals_by_website_id($website->website_id);

            foreach($pixel_goals as $pixel_goal) {
                /* Generate the full url needed to match */
                $pixel_goal->url = $website->host . $website->path . $pixel_goal->path;
            }
        }

        /* Main View */
        switch($website->tracking_type) {
            case 'lightweight':
                $data = [
                    'pixel_key'                     => $pixel_key,
                    'pixel_goals'                   => $pixel_goals,
                ];

            break;

            case 'normal':
                $data = [
                    'pixel_key'                     => $pixel_key,
                    'pixel_heatmaps'                => $pixel_heatmaps,
                    'pixel_goals'                   => $pixel_goals,
                    'pixel_track_events_children'   => $pixel_track_events_children,
                    'pixel_track_sessions_replays'  => $pixel_track_sessions_replays
                ];

            break;
        }

        $view = new \Altum\Views\View('pixel/' . $website->tracking_type . '/pixel', (array) $this);

        echo $view->run($data);

    }
}
