<?php
/**
 * Central file for admin 
 * 
 * @package htcc
 * @subpackage Admin
 * @since 1.0.0
 * 
 * subpackage Admin loads only on wp-admin 
 */


if ( ! defined( 'ABSPATH' ) ) exit;

require_once(HTCC_PLUGIN_DIR . 'inc/MobileMonkeyApi.php');

require_once('class-htcc-lang.php');

require_once('class-htcc-admin.php');


require_once('class-htcc-enqueue.php');


$admin = new HTCC_Admin();
add_action('admin_menu',  array( $admin, 'htcc_options_page') );
add_action( 'admin_init', array( $admin, 'htcc_custom_settings' ) );



#premium
if ( 'true' == HTCC_PRO ) {
    include_once HTCC_PLUGIN_DIR . 'admin/pro/htcc-pro-update.php';
    include_once HTCC_PLUGIN_DIR . 'admin/pro/class-admin-htcc-pro.php';
}




/**
 * ht_cc_service_content  -  by default there is no option .. 
 * so when no option exists .. so it not equal to 'hide'
 * so in admin sidebar the service content will display . . 
 * if clicks on hide box .. 
 *      then an option update will happen ( create an option )
 * 
 */
add_action( 'wp_ajax_ht_cc_service_content', 'ht_cc_service_content_ajax' );

function ht_cc_service_content_ajax() {

    $service_content = get_option( 'ht_cc_service_content' );

    // wp_localize_script can use - but this may be easy, as only one value .. 
    echo $service_content;

    wp_die();
}



// action -  ht_cc_service_content_hide
// update the option ht_cc_service_content to hide
add_action( 'wp_ajax_ht_cc_service_content_hide', 'ht_cc_service_content_hide_ajax' );

function ht_cc_service_content_hide_ajax() {

    $service_content = get_option( 'ht_cc_service_content' );

    update_option( 'ht_cc_service_content', 'hide' );

    wp_die();
}