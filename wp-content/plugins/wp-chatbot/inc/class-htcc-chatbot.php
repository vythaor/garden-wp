<?php 
/**
 * check condtions to display messenger or not
 * get app id
 * get page id
 * and add it to script, div
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'HTCC_Chatbot' ) ) :

class HTCC_Chatbot {

    // if sdk is not added then dont add - customer chat html content
    public $sdk_added = 'no';
    public $sdk_added_for_shortcode = 'no';
    public $sdk_load_time = '';

    public $api;

    public function __construct()
    {
        $this->api = new MobileMonkeyApi();
    }
   
    /**
     * load Customer Chat SDK at header
     *  if shortcode is used load sdk - even if hided based on other way.
     */
    public function chatbot() {

    // $htcc_options = get_option('htcc_options');
    $htcc_options = ht_cc()->variables->get_option;

    $fb_page_id = esc_attr( $htcc_options['fb_page_id'] );
    $fb_app_id = esc_attr( $htcc_options['fb_app_id'] );
    $log_events_value = esc_attr( $htcc_options['log_events'] );
    $fb_sdk_lang = esc_attr( $htcc_options['fb_sdk_lang'] );
    $fb_greeting_dialog_display = esc_attr( $htcc_options['greeting_dialog_display'] );
    $fb_greeting_dialog_delay = esc_attr( $htcc_options['greeting_dialog_delay'] );
    $fb_ref = esc_attr( $htcc_options['ref'] );
    $fb_color = esc_attr( $htcc_options['fb_color'] );
    $fb_greeting_login = esc_attr( $htcc_options['fb_greeting_login'] );
    $fb_greeting_logout = esc_attr( $htcc_options['fb_greeting_logout'] );
    $shortcode_name = esc_attr( $htcc_options['shortcode'] );

    $is_mobile = ht_cc()->device_type->is_mobile;
    
    /**
     * shortocode can add or have to work only on singular pages .. 
     * 
     * so check for shortocode in singular post .. 
     * if shortocode exists - load sdk - and dont load cc code ..
     * 
     * and for not singular post .. or is shortcode not exist .. 
     * then check for other conditions .. 
     * and load sdk, cc code .. 
     * 
     */

    //  check shortcode exists only on singular post .. if yes load sdk .. and dont load cc code
    if ( is_singular() ) {
        
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $shortcode_name ) ) {
            // If shortcode is added in this page - add sdk.

            // this will be useful at $this->customer_chat()
            $this->sdk_added_for_shortcode = 'yes';
        }
    }

    
    /**
     * if shortcode is not added .. ( in singular post ) - then check for other conditons .. 
     * if in a singular post - shortcode is added - then no need to check other conditons
     * as sdk is need for shortcode .. 
     */
     if ( 'yes' !== $this->sdk_added_for_shortcode  ) {
        // check for conditions that any thing is hiding 
        // - other then place where shortcode added in singular posts

        // in pro version .. hide base on device will be handle by js
        // not equal to true
        if ( 'true' !== HTCC_PRO ) {
            // Hide based on Devices - Mobile, Desktop
            if ( 'yes' == $is_mobile ) {
                if ( isset( $htcc_options['hideon_mobile'] ) ) {
                    return;
                }
            } else {
                if ( isset( $htcc_options['hideon_desktop'] ) ) {
                    return;
                }
            }
        }
        
        

        // single post
        if ( is_single() && isset( $htcc_options['hideon_posts'] ) ) {
            return;
        }

        // single page - but not on home page, front page
        if ( is_page() && isset( $htcc_options['hideon_page'] ) ) {
            if ( ( !is_home() ) && ( !is_front_page() ) ) {
                return;
            }
        }
    
        if ( is_home() && isset( $htcc_options['hideon_homepage'] ) ) {
            return;
        }
    
        if ( is_front_page() && isset( $htcc_options['hideon_frontpage'] ) ) {
            return;
        }
    
        if ( is_category() && isset( $htcc_options['hideon_category'] ) ) {
            return;
        }
    
        if ( is_archive() && isset( $htcc_options['hideon_archive'] ) ) {
            return;
        }
    
        if ( is_404() && isset( $htcc_options['hideon_404'] ) ) {
            return;
        } 
    
    
        $this_page_id = get_the_ID();
        $pages_list_tohide = $htcc_options['list_hideon_pages'];
        $pages_list_tohide_array = explode(',', $pages_list_tohide);
    
        if( ( is_single() || is_page() ) && in_array( $this_page_id, $pages_list_tohide_array ) ) {
            return;
        }
    
        // Hide styles on this catergorys - list
        $list_hideon_cat = $htcc_options['list_hideon_cat'];
    
        if( $list_hideon_cat ) {
            //  Get current post Categorys list and create an array for that..
            $current_categorys_array = array();
            $current_categorys = get_the_category();
            foreach ( $current_categorys as $category ) {
                $current_categorys_array[] = strtolower($category->name);
            }
            
            $list_hideon_cat_array = explode(',', $list_hideon_cat);
            
            foreach ( $list_hideon_cat_array as $category ) {
                $category_trim = trim($category);
                if ( in_array( strtolower($category_trim), $current_categorys_array ) ) {
                    return;
                }
            }
        }
    }
    
    
    
    $fb_sdk_src = "//connect.facebook.net/$fb_sdk_lang/sdk/xfbml.customerchat.js";
    // $fb_sdk_src = "https://connect.facebook.net/$fb_sdk_lang/sdk.js";

    
    // log events
    if ( 'yes' == $log_events_value ) {
        $log_events = true;
    } else {
        $log_events = false;
    }
    

    // update xfbml 
    $xfbml = true;
    if ( 'true' == HTCC_PRO ) {
        // if pro set to false - parse form js after conditons check .. .. 
        $xfbml = false;
    }

    // is_sdk_after_page_load
    // is_sdk_4_seconds
    $is_sdk_after_page_load = 'no';
    if ( isset( $htcc_options['is_sdk_after_page_load'] ) ) {
        $is_sdk_after_page_load = 'yes';
    }

    $sdk_load_time = '';
    $is_sdk_4_seconds = 'no';
    if ( isset( $htcc_options['is_sdk_4_seconds'] ) ) {
        $is_sdk_4_seconds = 'yes';
        $sdk_load_time = '4';
    }


    if ( isset( $htcc_options['sdk_load_time'] ) ) {
        $db_sdk_load_time = esc_attr( $htcc_options['sdk_load_time'] );
        
        // to avoud - if +2 is enabled and sdk_load_time value is empty. 
        if ( '0.1' < $db_sdk_load_time ) {
            $sdk_load_time = $db_sdk_load_time;
        }
    }

    if ( 'yes' == $is_sdk_after_page_load && '0.1' <= $sdk_load_time ) {
    ?>
        <!-- Add Messenger - wp-chatbot - HoliThemes - https://www.holithemes.com/wp-chatbot -->
        <script>
        window.onload = function() {
            setTimeout(function(){ 
                htcc_load_fb_sdk();
            }, <?php echo $sdk_load_time ?> * 1000 );
        }
        function htcc_load_fb_sdk() {
                window.fbAsyncInit = function() {
                FB.init({
                    appId            : '<?php echo $fb_app_id ?>',
                    autoLogAppEvents : <?php echo $log_events ? 'true' : 'false' ?>,
                    xfbml            : <?php echo $xfbml ? 'true' : 'false' ?>,
                    version          : 'v3.2'
                });
                };

                (function(d, s, id){
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = '<?php echo $fb_sdk_src ?>';
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
        }
        </script>
        <!-- / Add Messenger - wp-chatbot - HoliThemes -->    
    <?php
    } else {
    ?>
        <!-- Add Messenger - wp-chatbot - HoliThemes - https://www.holithemes.com/wp-chatbot -->
        <script>
            window.fbAsyncInit = function() {
            FB.init({
                appId            : '<?php echo $fb_app_id ?>',
                autoLogAppEvents : <?php echo $log_events ? 'true' : 'false' ?>,
                xfbml            : <?php echo $xfbml ? 'true' : 'false' ?>,
                version          : 'v3.2'
            });
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = '<?php echo $fb_sdk_src ?>';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    <!-- / Add Messenger - wp-chatbot - HoliThemes -->    
    <?php
    }
    
    // After sdk is added
    // for customer_chat(), var htcc_values
    $this->sdk_added = 'yes';
    $this->sdk_load_time = $sdk_load_time;
    
    }



    // cc code - customer chat code
    public function customer_chat() {
        // instead of adding the cc code in header added like this ..
        // can check more conditions as calling from footer .. woocommerce or so ...

        $htcc_options = ht_cc()->variables->get_option;

        $fb_page_id = esc_attr( $htcc_options['fb_page_id'] );
        $fb_greeting_dialog_display = esc_attr( $htcc_options['greeting_dialog_display'] );
        $fb_greeting_dialog_delay = esc_attr( $htcc_options['greeting_dialog_delay'] );
        $fb_ref = esc_attr( $htcc_options['ref'] );
        $fb_color = esc_attr( $htcc_options['fb_color'] );
        $fb_greeting_login = esc_attr( $htcc_options['fb_greeting_login'] );
        $fb_greeting_logout = esc_attr( $htcc_options['fb_greeting_logout'] );



        /**
         * update values for woocommerce selected pages
         * 
         * placeholders .. these are final values that place in cc code ..
         * localize this values if need to use in js - Actions ..
         * 
         */
        if ( 'true' == HTCC_PRO ) {
            include_once HTCC_PLUGIN_DIR . 'inc/pro/htcc-pro-woo.php';
            include_once HTCC_PLUGIN_DIR . 'inc/pro/htcc-pro-values.php';
        }

        // custom image - call this if sdk only.. 
        // call this before - checking sdk_added_for_shortcode
        if ( 'true' == HTCC_PRO ) {
            include_once HTCC_PLUGIN_DIR . 'inc/pro/htcc-pro-custom-image.php';
        }


        // make this check only after included - htcc-pro-values.php - using if statement

        // if sdk added for shortcode then this cc code not needed to add .. so return
        // cc code will added by shortocodes .. 
        if ( 'yes' == $this->sdk_added_for_shortcode ) {
            return;
        }
        // if sdk added then only load this
        if ( 'no' == $this->sdk_added ) {
            return;
        }

        


        if ( 'true' == HTCC_PRO ) {
            ?>

            <!-- Add Messenger - wp-chatbot pro - HoliThemes - https://www.holithemes.com/wp-chatbot -->    
            <div id="htcc-messenger" class="htcc-messenger">
                
            </div>
            <!-- / Add Messenger - wp-chatbot pro - HoliThemes -->  


            <?php
        } else {
            ?>

            <!-- Add Messenger - wp-chatbot - HoliThemes - https://www.holithemes.com/wp-chatbot -->    
            <div id="htcc-messenger" class="htcc-messenger">
                <div id="htcc-customerchat" class="fb-customerchat" 
                page_id="<?php echo $fb_page_id ?>"
                theme_color="<?php echo $fb_color ?>" 
                logged_in_greeting="<?php echo $fb_greeting_login ?>" 
                logged_out_greeting="<?php echo $fb_greeting_logout ?>" 
                ref="<?php echo $fb_ref ?>" 
                greeting_dialog_display = "<?php echo $fb_greeting_dialog_display ?>" 
                greeting_dialog_delay = "<?php echo $fb_greeting_dialog_delay ?>" 
                >
                </div>
            </div>
            <!-- / Add Messenger - wp-chatbot - HoliThemes -->   

            <?php
        }



    }




}



$chatbot = new HTCC_Chatbot();
add_action( 'wp_head', array( $chatbot, 'chatbot' ), 1 );
add_action( 'wp_footer', array( $chatbot, 'customer_chat' ) );


endif; // END class_exists check