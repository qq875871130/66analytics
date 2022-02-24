<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;

class AdminUserUpdate extends Controller {

    public function index() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if user exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        /* Get current plan proper details */
        $user->plan = (new Plan())->get_plan_by_id($user->plan_id);

        /* Check if its a custom plan */
        if($user->plan->plan_id == 'custom') {
            $user->plan->settings = json_decode($user->plan_settings);
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['status'] = (int) $_POST['status'];
            $_POST['type'] = (int) $_POST['type'];
            $_POST['plan_trial_done'] = (int) $_POST['plan_trial_done'];

            switch($_POST['plan_id']) {
                case 'free':

                    $plan_settings = json_encode(settings()->plan_free->settings);

                    break;

                case 'custom':

                    $plan_settings = json_encode([
                        'no_ads'                     => (bool) isset($_POST['no_ads']),
                        'email_reports_is_enabled'   => (bool) isset($_POST['email_reports_is_enabled']),
                        'teams_is_enabled'           => (bool) isset($_POST['teams_is_enabled']),
                        'websites_limit'             => (int) $_POST['websites_limit'],
                        'sessions_events_limit'      => (int) $_POST['sessions_events_limit'],
                        'events_children_limit'      => (int) $_POST['events_children_limit'],
                        'events_children_retention'  => $_POST['events_children_retention'] > 0 ? (int) $_POST['events_children_retention'] : 30,
                        'sessions_replays_limit'     => (int) $_POST['sessions_replays_limit'],
                        'sessions_replays_retention' => $_POST['sessions_replays_retention'] > 0 ? (int) $_POST['sessions_replays_retention'] : 30,
                        'sessions_replays_time_limit' => $_POST['sessions_replays_time_limit'] >= 1 ? (int) $_POST['sessions_replays_time_limit'] : 10,
                        'websites_heatmaps_limit'     => (int) $_POST['websites_heatmaps_limit'],
                        'websites_goals_limit'        => (int) $_POST['websites_goals_limit'],
                        'api_is_enabled' => (bool) isset($_POST['api_is_enabled']),
                        'affiliate_is_enabled' => (bool) isset($_POST['affiliate_is_enabled']),
                    ]);

                    break;

                default:

                    $_POST['plan_id'] = (int) $_POST['plan_id'];

                    /* Make sure this plan exists */
                    if(!$plan_settings = db()->where('plan_id', $_POST['plan_id'])->getValue('plans', 'settings')) {
                        redirect('admin/user-update/' . $user->user_id);
                    }

                    break;
            }

            $_POST['plan_expiration_date'] = (new \DateTime($_POST['plan_expiration_date']))->format('Y-m-d H:i:s');

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name', 'email'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }
            if(mb_strlen($_POST['name']) < 3 || mb_strlen($_POST['name']) > 64) {
                Alerts::add_field_error('name', language()->admin_users->error_message->name_length);
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                Alerts::add_field_error('email', language()->admin_users->error_message->invalid_email);
            }
            if(db()->where('email', $_POST['email'])->has('users') && $_POST['email'] !== $user->email) {
                Alerts::add_field_error('email', language()->admin_users->error_message->email_exists);
            }

            if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                if(mb_strlen($_POST['new_password']) < 6 || mb_strlen($_POST['new_password']) > 64) {
                    Alerts::add_field_error('new_password', language()->global->error_message->password_length);
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    Alerts::add_field_error('repeat_password', language()->global->error_message->passwords_not_matching);
                }
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the basic user settings */
                db()->where('user_id', $user->user_id)->update('users', [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'status' => $_POST['status'],
                    'type' => $_POST['type'],
                    'plan_id' => $_POST['plan_id'],
                    'plan_expiration_date' => $_POST['plan_expiration_date'],
                    'plan_expiry_reminder' => $user->plan_expiration_date != $_POST['plan_expiration_date'] ? 0 : 1,
                    'plan_settings' => $plan_settings,
                    'plan_trial_done' => $_POST['plan_trial_done']
                ]);

                /* Update the password if set */
                if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    /* Database query */
                    db()->where('user_id', $user->user_id)->update('users', ['password' => $new_password]);
                }

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->update1, '<strong>' . filter_var($_POST['name'], FILTER_SANITIZE_STRING) . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

                redirect('admin/user-update/' . $user->user_id);
            }

        }

        /* Get all the plans available */
        $plans = db()->where('status', 0, '<>')->get('plans');

        /* Main View */
        $data = [
            'user' => $user,
            'plans' => $plans,
        ];

        $view = new \Altum\Views\View('admin/user-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
