<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\User;

class Cron extends Controller {

    private function update_cron_execution_datetimes($key) {
        /* Get non-cached values from the database */
        $settings_cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', 'value'));

        $new_settings_cron_array = [
            'key' => $settings_cron->key,
            'cron_datetime' => $settings_cron->cron_datetime ?? \Altum\Date::$date,
            'reset_date' => $settings_cron->reset_date ?? \Altum\Date::$date,
        ];

        $new_settings_cron_array[$key] = \Altum\Date::$date;

        /* Update database */
        db()->where('`key`', 'cron')->update('settings', ['value' => json_encode($new_settings_cron_array)]);
    }

    public function index() {

        /* Initiation */
        set_time_limit(0);

        /* Make sure the key is correct */
        if(!isset($_GET['key']) || (isset($_GET['key']) && $_GET['key'] != settings()->cron->key)) {
            die();
        }

        $this->users_deletion_reminder();

        $this->auto_delete_inactive_users();

        $this->websites_replays_cleanup();

        $this->events_children_cleanup();

        $this->email_reports();

        $this->users_plan_expiry_reminder();

        $this->update_cron_execution_datetimes('cron_datetime');

        /* Make sure the reset date month is different than the current one to avoid double resetting */
        $reset_date = (new \DateTime(settings()->cron->reset_date))->format('m');
        $current_date = (new \DateTime())->format('m');

        if($reset_date != $current_date) {
            $this->users_logs_cleanup();

            $this->websites_events_reset();

            $this->update_cron_execution_datetimes('reset_date');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('settings');
        }
    }

    private function users_deletion_reminder() {
        if(!settings()->users->auto_delete_inactive_users) {
            return;
        }

        /* Determine when to send the email reminder */
        $days_until_deletion = settings()->users->user_deletion_reminder;
        $days = settings()->users->auto_delete_inactive_users - $days_until_deletion;
        $past_date = (new \DateTime())->modify('-' . $days . ' days')->format('Y-m-d H:i:s');

        /* Get the users that need to be reminded */
        $result = database()->query("
            SELECT `user_id`, `name`, `email`, `language` FROM `users` WHERE `plan_id` = 'free' AND `last_activity` < '{$past_date}' AND `user_deletion_reminder` = 0 AND `type` = 0 LIMIT 25
        ");

        /* Go through each result */
        while($user = $result->fetch_object()) {

            /* Get the language for the user */
            $language = language($user->language);

            /* Prepare the email */
            $email_template = get_email_template(
                [
                    '{{DAYS_UNTIL_DELETION}}' => $days_until_deletion,
                ],
                $language->global->emails->user_deletion_reminder->subject,
                [
                    '{{DAYS_UNTIL_DELETION}}' => $days_until_deletion,
                    '{{LOGIN_LINK}}' => url('login'),
                    '{{NAME}}' => $user->name,
                ],
                $language->global->emails->user_deletion_reminder->body
            );

            if(settings()->users->user_deletion_reminder) {
                send_mail($user->email, $email_template->subject, $email_template->body);
            }

            /* Update user */
            db()->where('user_id', $user->user_id)->update('users', ['user_deletion_reminder' => 1]);

            if(DEBUG) {
                if(settings()->users->user_deletion_reminder) echo sprintf('User deletion reminder email sent for user_id %s', $user->user_id);
            }
        }

    }

    private function auto_delete_inactive_users() {
        if(!settings()->users->auto_delete_inactive_users) {
            return;
        }

        /* Determine what users to delete */
        $days = settings()->users->auto_delete_inactive_users;
        $past_date = (new \DateTime())->modify('-' . $days . ' days')->format('Y-m-d H:i:s');

        /* Get the users that need to be reminded */
        $result = database()->query("
            SELECT `user_id`, `name`, `email`, `language` FROM `users` WHERE `plan_id` = 'free' AND `last_activity` < '{$past_date}' AND `user_deletion_reminder` = 1 AND `type` = 0 LIMIT 25
        ");

        /* Go through each result */
        while($user = $result->fetch_object()) {

            /* Get the language for the user */
            $language = language($user->language);

            /* Prepare the email */
            $email_template = get_email_template(
                [],
                $language->global->emails->auto_delete_inactive_users->subject,
                [
                    '{{INACTIVITY_DAYS}}' => settings()->users->auto_delete_inactive_users,
                    '{{REGISTER_LINK}}' => url('register'),
                    '{{NAME}}' => $user->name,
                ],
                $language->global->emails->auto_delete_inactive_users->body
            );

            send_mail($user->email, $email_template->subject, $email_template->body);

            /* Delete user */
            (new User())->delete($user->user_id);

            if(DEBUG) {
                echo sprintf('User deletion for inactivity user_id %s', $user->user_id);
            }
        }

    }

    private function users_logs_cleanup() {
        /* Delete old users logs */
        $ninety_days_ago_datetime = (new \DateTime())->modify('-90 days')->format('Y-m-d H:i:s');
        db()->where('datetime', $ninety_days_ago_datetime, '<')->delete('users_logs');
    }

    private function websites_events_reset() {
        db()->update('users', [
            'current_month_sessions_events' => 0,
            'current_month_events_children' => 0,
            'current_month_sessions_replays' => 0,
        ]);
    }

    private function events_children_cleanup() {
        $date = \Altum\Date::$date;
        db()->where('expiration_date', $date, '<')->delete('events_children');
    }

    private function websites_replays_cleanup() {
        $date = \Altum\Date::$date;

        /* Delete all the sessions replays which do not meet the minimum amount of seconds or are expired */
        $sessions_replays_minimum_duration = settings()->analytics->sessions_replays_minimum_duration;
        $result = database()->query("SELECT `session_id`, TIMESTAMPDIFF(SECOND, `date`, `last_date`) AS `seconds` FROM `sessions_replays` WHERE TIMESTAMPDIFF(SECOND, `date`, `last_date`) < {$sessions_replays_minimum_duration} OR `expiration_date` < '{$date}' LIMIT 25;");

        while($row = $result->fetch_object()) {
            db()->where('session_id', $row->session_id)->delete('sessions_replays');

            /* Clear cache */
            \Altum\Cache::$store_adapter->deleteItem('session_replay_' . $row->session_id);
        }
    }

    private function users_plan_expiry_reminder() {

        /* Determine when to send the email reminder */
        $days = 5;
        $future_date = (new \DateTime())->modify('+' . $days . ' days')->format('Y-m-d H:i:s');

        /* Get potential monitors from users that have almost all the conditions to get an email report right now */
        $result = database()->query("
            SELECT
                `user_id`,
                `name`,
                `email`,
                `plan_id`,
                `plan_expiration_date`,
                `language`
            FROM 
                `users`
            WHERE 
                `status` = 1
                AND `plan_id` <> 'free'
                AND `plan_expiry_reminder` = '0'
                AND (`payment_subscription_id` IS NULL OR `payment_subscription_id` = '')
				AND '{$future_date}' > `plan_expiration_date`
            LIMIT 25
        ");

        /* Go through each result */
        while($user = $result->fetch_object()) {

            /* Determine the exact days until expiration */
            $days_until_expiration = (new \DateTime($user->plan_expiration_date))->diff((new \DateTime()))->days;

            /* Get the language for the user */
            $language = language($user->language);

            /* Prepare the email */
            $email_template = get_email_template(
                [
                    '{{DAYS_UNTIL_EXPIRATION}}' => $days_until_expiration,
                ],
                $language->global->emails->user_plan_expiry_reminder->subject,
                [
                    '{{DAYS_UNTIL_EXPIRATION}}' => $days_until_expiration,
                    '{{USER_PLAN_RENEW_LINK}}' => url('pay/' . $user->plan_id),
                    '{{NAME}}' => $user->name,
                    '{{PLAN_NAME}}' => (new \Altum\Models\Plan())->get_plan_by_id($user->plan_id)->name,
                ],
                $language->global->emails->user_plan_expiry_reminder->body
            );

            send_mail($user->email, $email_template->subject, $email_template->body);

            /* Update user */
            db()->where('user_id', $user->user_id)->update('users', ['plan_expiry_reminder' => 1]);

            if(DEBUG) {
                echo sprintf('Email sent for user_id %s', $user->user_id);
            }
        }

    }

    private function email_reports() {

        /* Only run this part if the email reports are enabled */
        if(!settings()->analytics->email_reports_is_enabled) {
            return;
        }

        $date = \Altum\Date::$date;

        /* Determine the frequency of email reports */
        $days_interval = 7;

        switch(settings()->analytics->email_reports_is_enabled) {
            case 'weekly':
                $days_interval = 7;

                break;

            case 'monthly':
                $days_interval = 30;

                break;
        }

        /* Get potential websites from users that have almost all the conditions to get an email report right now */
        $result = database()->query("
            SELECT
                `websites`.`website_id`,
                `websites`.`name`,
                `websites`.`host`,
                `websites`.`path`,
                `websites`.`email_reports_last_date`,
                `websites`.`tracking_type`,
                `users`.`user_id`,
                `users`.`email`,
                `users`.`plan_settings`,
                `users`.`language`
            FROM 
                `websites`
            LEFT JOIN 
                `users` ON `websites`.`user_id` = `users`.`user_id` 
            WHERE 
                `users`.`status` = 1
                AND `websites`.`is_enabled` = 1 
                AND `websites`.`email_reports_is_enabled` = 1
				AND DATE_ADD(`websites`.`email_reports_last_date`, INTERVAL {$days_interval} DAY) <= '{$date}'
            LIMIT 25
        ");

        /* Go through each result */
        while($row = $result->fetch_object()) {
            $row->plan_settings = json_decode($row->plan_settings);

            /* Make sure the plan still lets the user get email reports */
            if(!$row->plan_settings->email_reports_is_enabled) {
                database()->query("UPDATE `websites` SET `email_reports_is_enabled` = 0 WHERE `website_id` = {$row->website_id}");

                continue;
            }

            /* Prepare */
            $previous_start_date = (new \DateTime())->modify('-' . $days_interval * 2 . ' days')->format('Y-m-d H:i:s');
            $start_date = (new \DateTime())->modify('-' . $days_interval . ' days')->format('Y-m-d H:i:s');

            /* Start getting information about the website to generate the statistics */
            switch($row->tracking_type) {
                case 'lightweight':
                    $basic_analytics = database()->query("
                        SELECT 
                            COUNT(*) AS `pageviews`, 
                            SUM(CASE WHEN `type` = 'landing_page' THEN 1 ELSE 0 END) AS `visitors`
                        FROM 
                            `lightweight_events`
                        WHERE 
                            `website_id` = {$row->website_id} 
                            AND (`date` BETWEEN '{$start_date}' AND '{$date}')
                    ")->fetch_object() ?? null;

                    $previous_basic_analytics = database()->query("
                        SELECT 
                            COUNT(*) AS `pageviews`, 
                            SUM(CASE WHEN `type` = 'landing_page' THEN 1 ELSE 0 END) AS `visitors`
                        FROM 
                            `lightweight_events`
                        WHERE 
                            `website_id` = {$row->website_id} 
                            AND (`date` BETWEEN '{$previous_start_date}' AND '{$start_date}')
                    ")->fetch_object() ?? null;
                break;

                case 'normal':
                    $basic_analytics = database()->query("
                        SELECT 
                            COUNT(*) AS `pageviews`, 
                            COUNT(DISTINCT `sessions_events`.`session_id`) AS `sessions`, 
                            COUNT(DISTINCT `sessions_events`.`visitor_id`) AS `visitors`
                        FROM 
                            `sessions_events`
                        LEFT JOIN
                            `websites_visitors` ON `sessions_events`.`visitor_id` = `websites_visitors`.`visitor_id`
                        WHERE 
                            `sessions_events`.`website_id` = {$row->website_id} 
                            AND (`sessions_events`.`date` BETWEEN '{$start_date}' AND '{$date}')
                    ")->fetch_object() ?? null;

                    $previous_basic_analytics = database()->query("
                        SELECT 
                            COUNT(*) AS `pageviews`, 
                            COUNT(DISTINCT `sessions_events`.`session_id`) AS `sessions`, 
                            COUNT(DISTINCT `sessions_events`.`visitor_id`) AS `visitors`
                        FROM 
                            `sessions_events`
                        LEFT JOIN
                            `websites_visitors` ON `sessions_events`.`visitor_id` = `websites_visitors`.`visitor_id`
                        WHERE 
                            `sessions_events`.`website_id` = {$row->website_id} 
                            AND (`sessions_events`.`date` BETWEEN '{$previous_start_date}' AND '{$start_date}')
                    ")->fetch_object() ?? null;
                break;
            }

            /* Get the language for the user */
            $language = language($row->language);

            /* Prepare the email title */
            $email_title = sprintf(
                $language->cron->email_reports->title,
                $row->name,
                \Altum\Date::get($start_date, 2),
                \Altum\Date::get('', 2)
            );

            /* Prepare the View for the email content */
            $data = [
                'row'                       => $row,
                'basic_analytics'           => $basic_analytics,
                'previous_basic_analytics'  => $previous_basic_analytics,
                'language'                  => $language
            ];

            $email_content = (new \Altum\Views\View('partials/cron/email_reports', (array) $this))->run($data);

            /* Send the email */
            send_mail($row->email, $email_title, $email_content);

            /* Update the website */
            db()->where('website_id', $row->website_id)->update('websites', ['email_reports_last_date' => $date]);

            /* Insert email log */
            db()->insert('email_reports', [
                'user_id' => $row->user_id,
                'website_id' => $row->website_id,
                'date' => $date,
            ]);

            if(DEBUG) {
                echo sprintf('Email sent for user_id %s and website_id %s', $row->user_id, $row->website_id);
            }
        }

    }

}
