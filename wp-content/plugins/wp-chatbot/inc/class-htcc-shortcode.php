<?php
/**
* shortcodes 
* base shorcode name is [chat]
* for list of attribute support check  -> shortcode_atts ( $a )
*
* @package ccw
* @since 1.0
*/    

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HTCC_Shortcode' ) ) :
    
class HTCC_Shortcode {

    
    function shortcode($atts = [], $content = null, $shortcode = '') {

        // let the script add - when shortcode added 
        // ~ any how fb won't load the sdk second time .. 

        // $htcc_options = get_option('htcc_options');
        $htcc_options = ht_cc()->variables->get_option;
        
        $is_mobile = ht_cc()->device_type->is_mobile;

    
        $fb_app_id = esc_attr( $htcc_options['fb_app_id'] );
        $fb_page_id = esc_attr( $htcc_options['fb_page_id'] );
        $fb_sdk_lang = esc_attr( $htcc_options['fb_sdk_lang'] );
        $fb_ref = esc_attr( $htcc_options['ref'] );
        $fb_color = esc_attr( $htcc_options['fb_color'] );
        $fb_greeting_login = esc_attr( $htcc_options['fb_greeting_login'] );
        $fb_greeting_logout = esc_attr( $htcc_options['fb_greeting_logout'] );

        $fb_greeting_dialog_display = esc_attr( $htcc_options['greeting_dialog_display'] );
        $fb_greeting_dialog_delay = esc_attr( $htcc_options['greeting_dialog_delay'] );


        $a = shortcode_atts(
            array(
                'page_id' => $fb_page_id,
                
                'color' => $fb_color,
                'logged_in_greetings' => $fb_greeting_login,
                'logged_out_greetings' => $fb_greeting_logout,
                
                'greeting_dialog_display' => $fb_greeting_dialog_display,
                'greeting_dialog_delay' => $fb_greeting_dialog_delay,
                
                'ref' => $fb_ref,

                'hide_mobile' => '',
                'hide_desktop' => '',
                
                'app_id' => $fb_app_id, // Deprecated

            ), $atts, $shortcode );


        $app_id = $a["app_id"];
        $page_id = $a["page_id"];
        $fb_color = $a["color"];
        $fb_greeting_login = $a["logged_in_greetings"];
        $fb_greeting_logout = $a["logged_out_greetings"];

        $greeting_dialog_display = $a["greeting_dialog_display"];
        $greeting_dialog_delay = $a["greeting_dialog_delay"];

        $ref = $a["ref"];


        // hide based on device 
        $hide_mobile = $a["hide_mobile"];
        $hide_desktop = $a["hide_desktop"];
        // if set to true then hide. - here shortcode wont use main options
        if ( 'yes' == $is_mobile ) {
            if ( "true" == $hide_mobile ) {
                return;
            }
        } else {
            if ( "true" == $hide_desktop ) {
                return;
            }
        }



        // js.src = '//connect.facebook.net/$fb_sdk_lang/sdk/xfbml.customerchat.js';
        // js.src = 'https://connect.facebook.net/$fb_sdk_lang/sdk.js';

        $o = '';
    //     $o .= "<script>
    //     window.fbAsyncInit = function() {
    //       FB.init({
    //         appId            : '$app_id',
    //         autoLogAppEvents : true,
    //         xfbml            : true,
    //         version          : 'v3.2'
    //       });
    //     };
      
    //     (function(d, s, id){
    //        var js, fjs = d.getElementsByTagName(s)[0];
    //        if (d.getElementById(id)) {return;}
    //        js = d.createElement(s); js.id = id;
    //        js.src = '//connect.facebook.net/$fb_sdk_lang/sdk/xfbml.customerchat.js';
    //        fjs.parentNode.insertBefore(js, fjs);
    //      }(document, 'script', 'facebook-jssdk'));
    //   </script>";


        $o .= '';
        $o .= '<div id="htcc-messenger-shortcode" class="htcc-messenger htcc-shortcode">
        <div id="htcc-customerchat" class="fb-customerchat"
        page_id="'.$page_id.'" 
        theme_color="' .$fb_color. '" 
        logged_in_greeting="' .$fb_greeting_login. '" 
        logged_out_greeting="' .$fb_greeting_logout. '" 
        ref="'.$ref.'"
        greeting_dialog_display="'.$greeting_dialog_display.'" 
        greeting_dialog_delay="'.$greeting_dialog_delay.'" 
        >
        </div>
      </div>';
        $o .= '';
        

        return $o;
    }


    //  Register shortcode
    function htcc_shortcodes_init() {

        $htcc_options = get_option('htcc_options');
        
        $shortcode_name = esc_attr( $htcc_options['shortcode'] );
        
        // add_shortcode('chatbot', array( $this, 'shortcode' ));
        add_shortcode( $shortcode_name, array( $this, 'shortcode' ) );
    }


}

$shortcode = new HTCC_Shortcode();

add_action('init', array( $shortcode, 'htcc_shortcodes_init' ) );

endif; // END class_exists check