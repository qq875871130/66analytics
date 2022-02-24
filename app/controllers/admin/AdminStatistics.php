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

class AdminStatistics extends Controller {
    public $type;
    public $datetime;

    public function index() {

        $this->type = isset($this->params[0]) && method_exists($this, $this->params[0]) ? Database::clean_string($this->params[0]) : 'growth';

        $this->datetime = \Altum\Date::get_start_end_dates_new();

        /* Process only data that is needed for that specific page */
        $type_data = $this->{$this->type}();

        /* Main View */
        $data = [
            'type' => $this->type,
            'datetime' => $this->datetime
        ];
        $data = array_merge($data, $type_data);

        $view = new \Altum\Views\View('admin/statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    protected function growth() {

        $total = ['users' => 0, 'users_logs' => 0, 'redeemed_codes' => 0];

        /* Users */
        $users_chart = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $users_chart[$row->formatted_date] = [
                'users' => $row->total
            ];

            $total['users'] += $row->total;
        }

        $users_chart = get_chart_data($users_chart);

        /* Users logs */
        $users_logs_chart = [];
        $result = database()->query("
            SELECT
                 COUNT(DISTINCT `user_id`) AS `total`,
                 DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `users_logs`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $users_logs_chart[$row->formatted_date] = [
                'users_logs' => $row->total
            ];

            $total['users_logs'] += $row->total;
        }

        $users_logs_chart = get_chart_data($users_logs_chart);

        /* Redeemed codes */
        if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
            $redeemed_codes_chart = [];
            $result = database()->query("
                SELECT
                     COUNT(*) AS `total`,
                     DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`
                FROM
                     `redeemed_codes`
                WHERE
                    `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
                GROUP BY
                    `formatted_date`
                ORDER BY
                    `formatted_date`
            ");
            while($row = $result->fetch_object()) {

                $row->formatted_date = $this->datetime['process']($row->formatted_date);

                $redeemed_codes_chart[$row->formatted_date] = [
                    'redeemed_codes' => $row->total
                ];

                $total['redeemed_codes'] += $row->total;
            }

            $redeemed_codes_chart = get_chart_data($redeemed_codes_chart);
        }

        return [
            'total' => $total,
            'users_chart' => $users_chart,
            'users_logs_chart' => $users_logs_chart,
            'redeemed_codes_chart' => $redeemed_codes_chart ?? null
        ];
    }

    protected function payments() {

        $total = ['total_amount' => 0, 'total_payments' => 0];

        $payments_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_payments`, DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`total_amount`), 2) AS `total_amount` FROM `payments` WHERE `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $payments_chart[$row->formatted_date] = [
                'total_amount' => $row->total_amount,
                'total_payments' => $row->total_payments
            ];

            $total['total_amount'] += $row->total_amount;
            $total['total_payments'] += $row->total_payments;
        }

        $payments_chart = get_chart_data($payments_chart);

        return [
            'total' => $total,
            'payments_chart' => $payments_chart
        ];

    }

    protected function affiliates_commissions() {

        $total = ['amount' => 0, 'total_affiliates_commissions' => 0];

        $affiliates_commissions_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_affiliates_commissions`, DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `amount` FROM `affiliates_commissions` WHERE `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $affiliates_commissions_chart[$row->formatted_date] = [
                'amount' => $row->amount,
                'total_affiliates_commissions' => $row->total_affiliates_commissions
            ];

            $total['amount'] += $row->amount;
            $total['total_affiliates_commissions'] += $row->total_affiliates_commissions;
        }

        $affiliates_commissions_chart = get_chart_data($affiliates_commissions_chart);

        return [
            'total' => $total,
            'affiliates_commissions_chart' => $affiliates_commissions_chart
        ];

    }

    protected function affiliates_withdrawals() {

        $total = ['amount' => 0, 'total_affiliates_withdrawals' => 0];

        $affiliates_withdrawals_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_affiliates_withdrawals`, DATE_FORMAT(`datetime`, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `amount` FROM `affiliates_withdrawals` WHERE `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $affiliates_withdrawals_chart[$row->formatted_date] = [
                'amount' => $row->amount,
                'total_affiliates_withdrawals' => $row->total_affiliates_withdrawals
            ];

            $total['amount'] += $row->amount;
            $total['total_affiliates_withdrawals'] += $row->total_affiliates_withdrawals;
        }

        $affiliates_withdrawals_chart = get_chart_data($affiliates_withdrawals_chart);

        return [
            'total' => $total,
            'affiliates_withdrawals_chart' => $affiliates_withdrawals_chart
        ];

    }

    protected function websites() {

        $total = ['websites' => 0];

        /* Monitors */
        $websites_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `websites`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $websites_chart[$row->formatted_date] = [
                'websites' => $row->total
            ];

            $total['websites'] += $row->total;
        }

        $websites_chart = get_chart_data($websites_chart);

        return [
            'total' => $total,
            'websites_chart' => $websites_chart,
        ];

    }

    protected function lightweight_events() {

        $total = ['lightweight_events' => 0];

        /* Monitors */
        $lightweight_events_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `lightweight_events`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $lightweight_events_chart[$row->formatted_date] = [
                'lightweight_events' => $row->total
            ];

            $total['lightweight_events'] += $row->total;
        }

        $lightweight_events_chart = get_chart_data($lightweight_events_chart);

        return [
            'total' => $total,
            'lightweight_events_chart' => $lightweight_events_chart,
        ];

    }

    protected function sessions_events() {

        $total = ['sessions_events' => 0];

        /* Monitors */
        $sessions_events_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `sessions_events`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $sessions_events_chart[$row->formatted_date] = [
                'sessions_events' => $row->total
            ];

            $total['sessions_events'] += $row->total;
        }

        $sessions_events_chart = get_chart_data($sessions_events_chart);

        return [
            'total' => $total,
            'sessions_events_chart' => $sessions_events_chart,
        ];

    }

    protected function events_children() {

        $total = ['click' => 0, 'form' => 0, 'scroll' => 0, 'resize' => 0];

        /* Track conversions */
        $events_children_chart = [];
        $result = database()->query("    
            SELECT
                 `type`,
                 COUNT(`id`) AS `total`,
                 DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `events_children`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`,
                `type`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {

            /* Handle if the date key is not already set */
            if(!array_key_exists($row->formatted_date, $events_children_chart)) {
                $events_children_chart[$row->formatted_date] = [
                    'click' => 0,
                    'form' => 0,
                    'scroll' => 0,
                    'resize' => 0,
                ];
            }

            $events_children_chart[$row->formatted_date][$row->type] = $row->total;

            $total[$row->type] += $row->total;
        }

        $events_children_chart = get_chart_data($events_children_chart);


        return [
            'total' => $total,
            'events_children_chart' => $events_children_chart
        ];
    }

    protected function sessions_replays() {

        $total = ['sessions_replays' => 0];

        /* Monitors */
        $sessions_replays_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `sessions_replays`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $sessions_replays_chart[$row->formatted_date] = [
                'sessions_replays' => $row->total
            ];

            $total['sessions_replays'] += $row->total;
        }

        $sessions_replays_chart = get_chart_data($sessions_replays_chart);

        return [
            'total' => $total,
            'sessions_replays_chart' => $sessions_replays_chart,
        ];

    }

    protected function websites_heatmaps() {

        $total = ['websites_heatmaps' => 0];

        /* Monitors */
        $websites_heatmaps_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `websites_heatmaps`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $websites_heatmaps_chart[$row->formatted_date] = [
                'websites_heatmaps' => $row->total
            ];

            $total['websites_heatmaps'] += $row->total;
        }

        $websites_heatmaps_chart = get_chart_data($websites_heatmaps_chart);

        return [
            'total' => $total,
            'websites_heatmaps_chart' => $websites_heatmaps_chart,
        ];

    }

    protected function websites_goals() {

        $total = ['websites_goals' => 0];

        /* Monitors */
        $websites_goals_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `websites_goals`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $websites_goals_chart[$row->formatted_date] = [
                'websites_goals' => $row->total
            ];

            $total['websites_goals'] += $row->total;
        }

        $websites_goals_chart = get_chart_data($websites_goals_chart);

        return [
            'total' => $total,
            'websites_goals_chart' => $websites_goals_chart,
        ];

    }

    protected function goals_conversions() {

        $total = ['goals_conversions' => 0];

        /* Monitors */
        $goals_conversions_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `goals_conversions`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $goals_conversions_chart[$row->formatted_date] = [
                'goals_conversions' => $row->total
            ];

            $total['goals_conversions'] += $row->total;
        }

        $goals_conversions_chart = get_chart_data($goals_conversions_chart);

        return [
            'total' => $total,
            'goals_conversions_chart' => $goals_conversions_chart,
        ];

    }

    protected function teams() {

        $total = ['teams' => 0];

        /* Monitors */
        $teams_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `teams`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $teams_chart[$row->formatted_date] = [
                'teams' => $row->total
            ];

            $total['teams'] += $row->total;
        }

        $teams_chart = get_chart_data($teams_chart);

        return [
            'total' => $total,
            'teams_chart' => $teams_chart,
        ];

    }

    protected function email_reports() {

        $total = ['email_reports' => 0];

        $email_reports_chart = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT(`date`, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `email_reports`
            WHERE
                `date` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $email_reports_chart[$row->formatted_date] = [
                'email_reports' => $row->total
            ];

            $total['email_reports'] += $row->total;
        }

        $email_reports_chart = get_chart_data($email_reports_chart);

        return [
            'total' => $total,
            'email_reports_chart' => $email_reports_chart
        ];
    }

}
