<?php
/**
* Plugin Name: OnCustomer Livechat
* Description: Kết nối ứng dụng với ứng dụng OnCustomer Livechat để bắt đầu chăm sóc khách hàng của bạn trên website.
* Version: 1.0
* Author: OnCustomer
* Author URI: https://oncustomer.asia/
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
**/

define('OC_LIVECHAT_URL', plugin_dir_url( __FILE__ ));


function oc_add_livechat_settings_page(){
    add_menu_page(
        'OnCustomer Settings',
        'OnCustomer',
        'manage_options',
        'oncustomer',
        'oc_render_livechat_options_page',
        'dashicons-format-status'
    );
}

function oc_render_livechat_options_page(){
    if (!current_user_can('manage_options'))
    {
        wp_die('You do not have sufficient permissions to access Livechat settings');
    }

    $option = get_option('livechat_token');

    $targetUri = urlencode(get_site_url().'/wp-admin/admin.php?page=oncustomer');

    $url = 'https://livechat.oncustomer.asia/integration?uri='.$targetUri.'&origin=wordpress';
    
    wp_enqueue_style('livechat.css', OC_LIVECHAT_URL.'css/livechat.css');

    if($option == ''){
        $html = '
            <div class="livechat-wrapper">
                <div class="livechat-content"><img class="livechat-logo" src="'.OC_LIVECHAT_URL.'images/logo.png" /></div>
                <div class="livechat-content">
                    <span>
                        Kết nối ứng dụng với ứng dụng OnCustomer Livechat<br/>
                        để bắt đầu chăm sóc khách hàng của bạn trên website.<br/>
                        Nếu bạn chưa có tài khoản? Bấm vào đây để <a class="livechat-link" href="https://livechat.oncustomer.asia/register">tạo tài khoản</a>.
                    </span>
                </div>
                <div class="livechat-content">
                    <a href="'.$url.'" class="livechat-button-green">Tích hợp ngay</a>
                </div>
            </div>
        ';
        echo $html;

    } else {
        $html = '
            <div class="livechat-wrapper">
                <div class="livechat-content"><img class="livechat-logo" src="'.OC_LIVECHAT_URL.'images/logo.png" /></div>
                <div class="livechat-content">
                    Kết nối ứng dụng với ứng dụng OnCustomer Livechat<br/>
                    để bắt đầu chăm sóc khách hàng của bạn trên website.
                </div>
                <div class="livechat-content">
                    <img class="livechat-icon" src="'.OC_LIVECHAT_URL.'images/check.png" />
                    Bạn đã kết nối thành công với OnCustomer Livechat.
                </div>
                <div class="livechat-content">
                    <a href="'.$url.'" class="livechat-button-outline">Kết nối lại</a>
                    <a href="https://livechat.oncustomer.asia" target="_blank" class="livechat-button-outline">Dashboard</a>
                </div>
            </div>
        ';
        echo $html;
    }
    
}

function oc_add_livechat_snippet(){
    $option = get_option('livechat_token');
    if($option != ''){
        $script = '
        <script>
            (function(d, s, id, t) {
                if (d.getElementById(id)) return;
                var js, fjs = d.getElementsByTagName(s)[0];
                js = d.createElement(s);
                js.id = id;
                js.src = "https://widget-beta.oncustomer.asia/js/index.js?token=" + t;
                fjs.parentNode.insertBefore(js, fjs);}
            (document, "script", "oc-chat-widget-bootstrap", "'.$option.'"));
        </script>
        ';
        echo $script;
    }
    
}

function oc_livechat_settings(){
    $option = get_option('livechat_token');

    if(isset($_GET['livechat_token'])){
        $livechat_token = $_GET['livechat_token'];

        if(preg_match ('/^[0-9a-zA-Z]{1,}$/', $livechat_token) && current_user_can('manage_options')){
            if($option == ''){
                add_option('livechat_token', $livechat_token);
            } else if($option != ''){
                update_option('livechat_token', $livechat_token);   
            }
        }
        wp_safe_redirect(get_site_url().'/wp-admin/admin.php?page=oncustomer');
    }
}

add_action('admin_menu', 'oc_add_livechat_settings_page');
add_action('wp_footer', 'oc_add_livechat_snippet');
add_action('admin_init', 'oc_livechat_settings');

?>