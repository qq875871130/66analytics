<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class Heatmaps extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->website || !settings()->analytics->websites_heatmaps_is_enabled || ($this->website && $this->website->tracking_type == 'lightweight')) {
            redirect('websites');
        }

        /* Get basic overall data */

        /* Create Modal */
        $view = new \Altum\Views\View('heatmap/heatmap_create_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Update Modal */
        $view = new \Altum\Views\View('heatmap/heatmap_update_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Update Modal */
        $view = new \Altum\Views\View('heatmap/heatmap_retake_snapshots_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('heatmap/heatmap_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the paginator */
        $total_heatmaps = database()->query("SELECT COUNT(*) AS `total` FROM `websites_heatmaps` WHERE `website_id` = {$this->website->website_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_heatmaps, 25, $_GET['page'] ?? 1, url('heatmaps?page=%d')));

        /* Get the websites list for the user */
        $heatmaps = [];
        $heatmaps_result = database()->query("SELECT * FROM `websites_heatmaps` WHERE `website_id` = {$this->website->website_id} {$paginator->get_sql_limit()}");
        while($row = $heatmaps_result->fetch_object()) $heatmaps[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the View */
        $data = [
            'total_heatmaps' => $total_heatmaps,
            'heatmaps' => $heatmaps,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('heatmaps/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
