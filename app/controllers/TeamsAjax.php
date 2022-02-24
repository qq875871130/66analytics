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
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;

class TeamsAjax extends Controller {

    public function index() {

        Authentication::guard();

        /* Make sure its not a request from a team member */
        if($this->team || !$this->user->plan_settings->teams_is_enabled) {
            die();
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(!empty($_POST) && (Csrf::check() || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die();
    }

    private function create() {
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $websites_ids = [];

        /* Check for possible errors */
        if(empty($_POST['name']) || !isset($_POST['websites_ids'])) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        foreach($_POST['websites_ids'] as $website_id) {
            if(array_key_exists($website_id, $this->websites)) {
                $websites_ids[] = (int) $website_id;
            }
        }

        if(!count($websites_ids)) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        if(empty($errors)) {
            $websites_ids = json_encode($websites_ids);

            /* Insert to database */
            $stmt = database()->prepare("INSERT INTO `teams` (`user_id`, `name`, `websites_ids`, `date`) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $this->user->user_id, $_POST['name'], $websites_ids, Date::$date);
            $stmt->execute();
            $team_id = $stmt->insert_id;
            $stmt->close();

            /* Set a nice success message */
            Response::json(sprintf(language()->global->success_message->create1, '<strong>' . filter_var($_POST['name']) . '</strong>'), 'success', ['team_id' => $team_id]);
        }
    }

    private function update() {
        $_POST['team_id'] = (int) $_POST['team_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $websites_ids = [];

        /* Check for possible errors */
        if(empty($_POST['name']) || !isset($_POST['websites_ids'])) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        foreach($_POST['websites_ids'] as $website_id) {
            if(array_key_exists($website_id, $this->websites)) {
                $websites_ids[] = (int) $website_id;
            }
        }

        if(!count($websites_ids)) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        if(empty($errors)) {
            $websites_ids = json_encode($websites_ids);

            /* Insert to database */
            $stmt = database()->prepare("UPDATE`teams` SET `name` = ?, `websites_ids` = ? WHERE `user_id` = ? AND `team_id` = ?");
            $stmt->bind_param('ssss', $_POST['name'], $websites_ids, $this->user->user_id, $_POST['team_id']);
            $stmt->execute();
            $team_id = $stmt->insert_id;
            $stmt->close();

            /* Set a nice success message */
            Response::json(sprintf(language()->global->success_message->update1, '<strong>' . filter_var($_POST['name']) . '</strong>'), 'success', ['team_id' => $team_id]);
        }

    }

    private function delete() {
        $_POST['team_id'] = (int) $_POST['team_id'];

        if(!$team = db()->where('team_id', $_POST['team_id'])->where('user_id', $this->user->user_id)->getOne('teams', ['team_id', 'name'])) {
            die();
        }

        /* Delete from database */
        db()->where('team_id', $team->team_id)->delete('teams');

        /* Set a nice success message */
        Response::json(sprintf(language()->global->success_message->delete1, '<strong>' . $team->name . '</strong>'));
    }

}
