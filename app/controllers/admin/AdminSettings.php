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
use Altum\Routing\Router;
use Altum\Uploads;

class AdminSettings extends Controller {

    public function index() {
        redirect('admin/settings/main');
    }

    private function process() {
        $method	= (isset(Router::$method) && file_exists(THEME_PATH . 'views/admin/settings/partials/' . Router::$method . '.php')) ? Router::$method : 'main';
        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Method View */
        $view = new \Altum\Views\View('admin/settings/partials/' . $method, (array) $this);
        $this->add_view_content('method', $view->run());

        /* Main View */
        $view = new \Altum\Views\View('admin/settings/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'method' => $method,
            'payment_processors' => $payment_processors,
        ]));
    }

    private function update_settings($key, $value) {
        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('`key`', $key)->update('settings', ['value' => $value]);

            $this->after_update_settings($key);
        }

        redirect('admin/settings/' . $key);
    }

    private function after_update_settings($key) {

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('settings');

        /* Set a nice success message */
        Alerts::add_success(language()->global->success_message->update2);

        /* Refresh the page */
        redirect('admin/settings/' . $key);

    }

    public function main() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'title' => $_POST['title'],
                'default_language' => $_POST['default_language'],
                'default_theme_style' => $_POST['default_theme_style'],
                'default_timezone' => $_POST['default_timezone'],
                'index_url' => $_POST['index_url'],
                'terms_and_conditions_url' => $_POST['terms_and_conditions_url'],
                'privacy_policy_url' => $_POST['privacy_policy_url'],
                'not_found_url' => $_POST['not_found_url'],
                'se_indexing' => (bool) $_POST['se_indexing'],
                'default_results_per_page' => (int) $_POST['default_results_per_page'],
                'default_order_type' => $_POST['default_order_type'],
            ]);

            db()->where('`key`', 'main')->update('settings', ['value' => $value]);

            /* Check for errors & process  potential uploads */
            $image = [
                'logo' => !empty($_FILES['logo']['name']) && !isset($_POST['logo_remove']),
                'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
                'opengraph' => !empty($_FILES['opengraph']['name']) && !isset($_POST['opengraph_remove']),
            ];

            foreach(['logo', 'favicon', 'opengraph'] as $image_key) {
                if($image[$image_key]) {
                    $file_name = $_FILES[$image_key]['name'];
                    $file_extension = explode('.', $file_name);
                    $file_extension = mb_strtolower(end($file_extension));
                    $file_temp = $_FILES[$image_key]['tmp_name'];

                    if($_FILES[$image_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                        Alerts::add_error(sprintf(language()->global->error_message->file_size_limit, get_max_upload()));
                    }

                    if($_FILES[$image_key]['error'] && $_FILES[$image_key]['error'] != UPLOAD_ERR_INI_SIZE) {
                        Alerts::add_error(language()->global->error_message->file_upload);
                    }

                    if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions($image_key))) {
                        Alerts::add_error(language()->global->error_message->invalid_file_type);
                    }

                    if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                        if(!is_writable(UPLOADS_PATH . $image_key . '/')) {
                            Alerts::add_error(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_PATH . $image_key . '/'));
                        }
                    }

                    if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                        /* Generate new name for image */
                        $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                        /* Offload uploading */
                        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                            try {
                                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                                /* Delete current image */
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/' . $image_key . '/' . settings()->{$image_key},
                                ]);

                                /* Upload image */
                                $result = $s3->putObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/' . $image_key . '/' . $image_new_name,
                                    'ContentType' => mime_content_type($file_temp),
                                    'SourceFile' => $file_temp,
                                    'ACL' => 'public-read'
                                ]);
                            } catch (\Exception $exception) {
                                Alerts::add_error($exception->getMessage());
                            }
                        }

                        /* Local uploading */
                        else {
                            /* Delete current image */
                            if(!empty(settings()->{$image_key}) && file_exists(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key})) {
                                unlink(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key});
                            }

                            /* Upload the original */
                            move_uploaded_file($file_temp, UPLOADS_PATH . $image_key . '/' . $image_new_name);
                        }

                        /* Database query */
                        db()->where('`key`', $image_key)->update('settings', ['value' => $image_new_name]);

                    }
                }

                /* Check for the removal of the already uploaded file */
                if(isset($_POST[$image_key . '_remove'])) {
                    /* Offload deleting */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_key . '/' . settings()->{$image_key},
                        ]);
                    }

                    /* Local deleting */
                    else {
                        /* Delete current file */
                        if(!empty(settings()->{$image_key}) && file_exists(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key})) {
                            unlink(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key});
                        }
                    }

                    /* Database query */
                    db()->where('`key`', $image_key)->update('settings', ['value' => '']);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $this->after_update_settings('main');
            }

            redirect('admin/settings/main');
        }
    }

    public function users() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));

            $value = json_encode([
                'email_confirmation' => (bool) $_POST['email_confirmation'],
                'register_is_enabled' => (bool) $_POST['register_is_enabled'],
                'auto_delete_inactive_users' => (int) $_POST['auto_delete_inactive_users'],
                'user_deletion_reminder' => (int) $_POST['user_deletion_reminder'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
            ]);

            $this->update_settings('users', $value);
        }
    }

    public function payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['type'] = in_array($_POST['type'], ['one_time', 'recurring', 'both']) ? filter_var($_POST['type'], FILTER_SANITIZE_STRING) : 'both';
            $_POST['codes_is_enabled'] = (bool)$_POST['codes_is_enabled'];
            $_POST['taxes_and_billing_is_enabled'] = (bool)$_POST['taxes_and_billing_is_enabled'];
            $_POST['invoice_is_enabled'] = (bool) $_POST['invoice_is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'type' => $_POST['type'],
                'currency' => $_POST['currency'],
                'codes_is_enabled' => $_POST['codes_is_enabled'],
                'taxes_and_billing_is_enabled' => $_POST['taxes_and_billing_is_enabled'],
                'invoice_is_enabled' => $_POST['invoice_is_enabled'],
            ]);

            $this->update_settings('payment', $value);
        }
    }

    public function paypal() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? filter_var($_POST['mode'], FILTER_SANITIZE_STRING) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'client_id' => $_POST['client_id'],
                'secret' => $_POST['secret'],
            ]);

            $this->update_settings('paypal', $value);
        }
    }

    public function stripe() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('stripe', $value);
        }
    }

    public function offline_payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'instructions' => $_POST['instructions'],
            ]);

            $this->update_settings('offline_payment', $value);
        }
    }

    public function coinbase() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('coinbase', $value);
        }
    }

    public function payu() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['secure', 'sandbox']) ? filter_var($_POST['mode'], FILTER_SANITIZE_STRING) : 'secure';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'merchant_pos_id' => $_POST['merchant_pos_id'],
                'signature_key' => $_POST['signature_key'],
                'oauth_client_id' => $_POST['oauth_client_id'],
                'oauth_client_secret' => $_POST['oauth_client_secret'],
            ]);

            $this->update_settings('payu', $value);
        }
    }

    public function paystack() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'public_key' => $_POST['public_key'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('paystack', $value);
        }
    }

    public function razorpay() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'key_id' => $_POST['key_id'],
                'key_secret' => $_POST['key_secret'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('razorpay', $value);
        }
    }

    public function mollie() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
            ]);

            $this->update_settings('mollie', $value);
        }
    }

    public function yookassa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'shop_id' => $_POST['shop_id'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('yookassa', $value);
        }
    }

    public function affiliate() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('affiliate')) {
                redirect('admin/settings/affiliate');
            }

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['commission_type'] = in_array($_POST['commission_type'], ['once', 'forever']) ? filter_var($_POST['commission_type'], FILTER_SANITIZE_STRING) : 'once';
            $_POST['minimum_withdrawal_amount'] = (float)$_POST['minimum_withdrawal_amount'];
            $_POST['commission_percentage'] = $_POST['commission_percentage'] < 1 || $_POST['commission_percentage'] > 99 ? 10 : (int)$_POST['commission_percentage'];
            $_POST['withdrawal_notes'] = trim(filter_var($_POST['withdrawal_notes'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'commission_type' => $_POST['commission_type'],
                'minimum_withdrawal_amount' => $_POST['minimum_withdrawal_amount'],
                'commission_percentage' => $_POST['commission_percentage'],
                'withdrawal_notes' => $_POST['withdrawal_notes'],
            ]);

            $this->update_settings('affiliate', $value);
        }
    }

    public function business() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['brand_name'] = filter_var($_POST['brand_name'], FILTER_SANITIZE_STRING);

            $value = json_encode([
                'brand_name' => $_POST['brand_name'],
                'invoice_nr_prefix' => $_POST['invoice_nr_prefix'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'county' => $_POST['county'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'tax_type' => $_POST['tax_type'],
                'tax_id' => $_POST['tax_id'],
                'custom_key_one' => $_POST['custom_key_one'],
                'custom_value_one' => $_POST['custom_value_one'],
                'custom_key_two' => $_POST['custom_key_two'],
                'custom_value_two' => $_POST['custom_value_two'],
            ]);

            $this->update_settings('business', $value);
        }
    }

    public function captcha() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['type'] = in_array($_POST['type'], ['basic', 'recaptcha', 'hcaptcha']) ? $_POST['type'] : 'basic';
            foreach(['login', 'register', 'lost_password', 'resend_activation'] as $key) {
                $_POST['' . $key . '_is_enabled'] = (bool) $_POST['' . $key . '_is_enabled'];
            }

            $value = json_encode([
                'type' => $_POST['type'],
                'recaptcha_public_key' => $_POST['recaptcha_public_key'],
                'recaptcha_private_key' => $_POST['recaptcha_private_key'],
                'hcaptcha_site_key' => $_POST['hcaptcha_site_key'],
                'hcaptcha_secret_key' => $_POST['hcaptcha_secret_key'],
                'login_is_enabled' => $_POST['login_is_enabled'],
                'register_is_enabled' => $_POST['register_is_enabled'],
                'lost_password_is_enabled' => $_POST['lost_password_is_enabled'],
                'resend_activation_is_enabled' => $_POST['resend_activation_is_enabled'],
                'contact_is_enabled' => $_POST['contact_is_enabled'],
            ]);

            $this->update_settings('captcha', $value);
        }
    }

    public function facebook() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'app_id' => $_POST['app_id'],
                'app_secret' => $_POST['app_secret'],
            ]);

            $this->update_settings('facebook', $value);
        }
    }

    public function google() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('google', $value);
        }
    }

    public function twitter() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'consumer_api_key' => $_POST['consumer_api_key'],
                'consumer_api_secret' => $_POST['consumer_api_secret'],
            ]);

            $this->update_settings('twitter', $value);
        }
    }

    public function ads() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
            ]);

            $this->update_settings('ads', $value);
        }
    }

    public function socials() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = [];
            foreach(require APP_PATH . 'includes/admin_socials.php' as $key => $social) {
                $value[$key] = $_POST[$key];
            }
            $value = json_encode($value);

            $this->update_settings('socials', $value);
        }
    }

    public function smtp() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['auth'] = (bool) isset($_POST['auth']);
            $_POST['username'] = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
            $_POST['password'] = $_POST['password'] ?? '';

            $value = json_encode([
                'from_name' => $_POST['from_name'],
                'from' => $_POST['from'],
                'host' => $_POST['host'],
                'encryption' => $_POST['encryption'],
                'port' => $_POST['port'],
                'auth' => $_POST['auth'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ]);

            $this->update_settings('smtp', $value);
        }
    }

    public function custom() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'head_js' => $_POST['head_js'],
                'head_css' => $_POST['head_css'],
            ]);

            $this->update_settings('custom', $value);
        }
    }

    public function announcements() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['guests_id'] = md5($_POST['content'] . time());
            $_POST['guests_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_text_color']) ? '#000' : $_POST['guests_text_color'];
            $_POST['guests_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_background_color']) ? '#fff' : $_POST['guests_background_color'];
            $_POST['users_id'] = md5($_POST['content'] . time());
            $_POST['users_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_text_color']) ? '#000' : $_POST['users_text_color'];
            $_POST['users_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_background_color']) ? '#fff' : $_POST['users_background_color'];

            $value = json_encode([
                'guests_id' => $_POST['guests_id'],
                'guests_content' => $_POST['guests_content'],
                'guests_text_color' => $_POST['guests_text_color'],
                'guests_background_color' => $_POST['guests_background_color'],
                'users_id' => $_POST['users_id'],
                'users_content' => $_POST['users_content'],
                'users_text_color' => $_POST['users_text_color'],
                'users_background_color' => $_POST['users_background_color'],
            ]);

            $this->update_settings('announcements', $value);
        }
    }

    public function email_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['emails'] = str_replace(' ', '', $_POST['emails']);
            $_POST['new_user'] = (bool) isset($_POST['new_user']);
            $_POST['new_payment'] = (bool) isset($_POST['new_payment']);
            $_POST['new_affiliate_withdrawal'] = (bool) isset($_POST['new_affiliate_withdrawal']);
            $_POST['contact'] = (bool) isset($_POST['contact']);

            $value = json_encode([
                'emails' => $_POST['emails'],
                'new_user' => $_POST['new_user'],
                'new_payment' => $_POST['new_payment'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
                'contact' => $_POST['contact'],
            ]);

            $this->update_settings('email_notifications', $value);
        }
    }

    public function webhooks() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['user_new'] = trim(filter_var($_POST['user_new'], FILTER_SANITIZE_STRING));
            $_POST['user_delete'] = trim(filter_var($_POST['user_delete'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'user_new' => $_POST['user_new'],
                'user_delete' => $_POST['user_delete'],
            ]);

            $this->update_settings('webhooks', $value);
        }
    }

    public function offload() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('offload')) {
                redirect('admin/settings/offload');
            }

            /* :) */
            $_POST['assets_url'] = trim(filter_var($_POST['assets_url'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'assets_url' => $_POST['assets_url'],
                'provider' => $_POST['provider'],
                'endpoint_url' => $_POST['endpoint_url'],
                'uploads_url' => $_POST['uploads_url'],
                'access_key' => $_POST['access_key'],
                'secret_access_key' => $_POST['secret_access_key'],
                'storage_name' => $_POST['storage_name'],
                'region' => $_POST['region'],
            ]);

            $this->update_settings('offload', $value);
        }
    }

    public function cron() {
        /* Get the latest cronjob details */
        settings()->cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', '`value`'));

        $this->process();
    }

    public function cache() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            \Altum\Cache::$adapter->clear();

            /* Set a nice success message */
            Alerts::add_success(language()->global->success_message->update2);

            /* Refresh the page */
            redirect('admin/settings/cache');
        }
    }

    public function license() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_license'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $altumcode_api = 'https://api2.altumcode.com/validate';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'type'              => 'license-update',
                'license_key'       => $_POST['new_license'],
                'installation_url'  => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
                'server_ip'         => $_SERVER['SERVER_ADDR'],
                'client_ip'         => get_ip()
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        $dump = explode('-- SEPARATOR --', $response->body->sql);

                        foreach ($dump as $query) {
                            database()->query($query);
                        }
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('license');
                }
            }

            redirect('admin/settings/license');
        }
    }

    public function analytics() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['sessions_replays_is_enabled'] = (bool) $_POST['sessions_replays_is_enabled'];
            $_POST['sessions_replays_minimum_duration'] = (int) $_POST['sessions_replays_minimum_duration'];
            $_POST['websites_heatmaps_is_enabled'] = (bool) $_POST['websites_heatmaps_is_enabled'];
            $_POST['pixel_cache'] = (int) $_POST['pixel_cache'];
            $_POST['pixel_exposed_identifier'] = trim(get_slug($_POST['pixel_exposed_identifier']));
            $_POST['email_reports_is_enabled'] = in_array($_POST['email_reports_is_enabled'], [0, 'weekly', 'monthly']) ? $_POST['email_reports_is_enabled'] : 0;

            $value = json_encode([
                'sessions_replays_is_enabled' => $_POST['sessions_replays_is_enabled'],
                'sessions_replays_minimum_duration' => $_POST['sessions_replays_minimum_duration'],
                'websites_heatmaps_is_enabled' => $_POST['websites_heatmaps_is_enabled'],
                'pixel_cache' => $_POST['pixel_cache'],
                'pixel_exposed_identifier' => $_POST['pixel_exposed_identifier'],
                'email_reports_is_enabled' => $_POST['email_reports_is_enabled'],
            ]);

            $this->update_settings('analytics', $value);
        }
    }

    public function send_test_email() {

        if(empty($_POST)) {
            redirect('admin/settings/smtp');
        }

        /* Check for any errors */
        $required_fields = ['email'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Alerts::add_field_error($field, language()->global->error_message->empty_field);
            }
        }

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        /* If there are no errors, continue */
        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $result = send_mail($_POST['email'], settings()->main->title . ' - Test Email', 'This is just a test email to confirm that the smtp email settings are properly working!', true);

            if($result->ErrorInfo == '') {
                Alerts::add_success(language()->admin_settings_send_test_email_modal->success_message);
            } else {
                Alerts::add_error(sprintf(language()->admin_settings_send_test_email_modal->error_message, $result->ErrorInfo));
                Alerts::add_info(implode('<br />', $result->errors));
            }

        }

        redirect('admin/settings/smtp');
    }

}
