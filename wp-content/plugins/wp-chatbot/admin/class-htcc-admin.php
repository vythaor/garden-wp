<?php
/**
 * Creates top level menu
 * and options page
 *
 * @package htcc
 * @subpackage admin
 * @since 1.0.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HTCC_Admin' ) ) :

class HTCC_Admin {

		private $api;
		private $logedin;
		public function __construct()
		{
			$this->api = new MobileMonkeyApi();
		}

    private function getApi()
    {
        return $this->api;
    }

    /**
     * Adds top level menu -> WP CSS Shapes
     *
     * @uses action hook - admin_menu
     *
     * @since 1.0.0
     * @return void
     */
    public function htcc_options_page()
    {
        add_menu_page(
            'WP-Chatbot Setting page',
            'WP-Chatbot',
            'manage_options',
            'wp-chatbot',
            array($this, 'settings_page'),
            'dashicons-format-chat'
        );
        if ($this->getApi()->connectMobileMonkey()) {
            add_submenu_page('wp-chatbot', 'Contacts', 'Contacts', 8, 'wp-chatbot-contact', [
                $this, 'page_contact'
            ]);
        }


    }

    /**
     * Options page Content -
     *   get settings form from a template settings_page.php
     *
     * Call back from - $this->htcc_options_page, add_menu_page
     *
     * @since 1.0.0
     * @return void
     */
    public function settings_page()
    {

        if (!current_user_can('manage_options')) {
            return;
        }

        // get options page form
        require_once('settings_page.php');
    }

    function page_contact()
    {
        include('contact_page.php');
        $table = new MobileMonkey_Contacts_List_Table();
        $table->prepare_items();
        $table->display();
    }

    /**
     * Options page - Regsiter, add section and add setting fields
     *
     * @uses action hook - admin_init
     *
     * @since 1.0.0
     * @return void
     */
    public function htcc_custom_settings()
    {

    $options = get_option('htcc_options');
    $fb_page_id = $options['fb_page_id'];
    $welcome_message = $options['fb_welcome_message'];

    register_setting( 'htcc_settings_group', 'htcc_options' , array( $this, 'htcc_options_sanitize' ) );

    add_settings_section( 'htcc_settings', '', array( $this, 'htcc_settings_section_cb' ), 'htcc_options_settings' );

    if ($fb_page_id && !empty($welcome_message)) {
        add_settings_field('htcc_fb_welcome_message', __('Welcome Message', 'wp-chatbot'), array($this, 'htcc_fb_welcome_message_cb'), 'htcc_options_settings');
    }
    add_settings_field( 'htcc_fb_color', __( 'Color' , 'wp-chatbot' ), array( $this, 'htcc_fb_color_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_fb_greeting_login', __( 'Logged in Greeting' , 'wp-chatbot' ), array( $this, 'htcc_fb_greeting_login_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_fb_greeting_logout', __( 'Logged out Greeting' , 'wp-chatbot' ), array( $this, 'htcc_fb_greeting_logout_cb' ), 'htcc_options_settings', 'htcc_settings' );

    add_settings_field( 'htcc_fb_greeting_dialog_display', __( 'Greeting Dialog Display' , 'wp-chatbot' ), array( $this, 'htcc_fb_greeting_dialog_display_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_fb_greeting_dialog_delay', __( 'Greeting Dialog Delay' , 'wp-chatbot' ), array( $this, 'htcc_fb_greeting_dialog_delay_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_fb_sdk_lang', __( 'Messenger language' , 'wp-chatbot' ), array( $this, 'htcc_fb_sdk_lang_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_fb_ref', __( 'Ref' , 'wp-chatbot' ), array( $this, 'htcc_fb_ref_cb' ), 'htcc_options_settings', 'htcc_settings' );

    add_settings_field( 'htcc_show_hide', __( 'Hide Based on post type' , 'wp-chatbot' ), array( $this, 'htcc_show_hide_post_types_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_list_id_tohide', __( 'Post, Page Id\'s to Hide' , 'wp-chatbot' ), array( $this, 'htcc_list_id_tohide_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_list_cat_tohide', __( 'Categorys to Hide' , 'wp-chatbot' ), array( $this, 'htcc_list_cat_tohide_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_devices_show_hide', __( 'Hide Based on Devices' , 'wp-chatbot' ), array( $this, 'htcc_show_hide_devices_cb' ), 'htcc_options_settings', 'htcc_settings' );
    add_settings_field( 'htcc_shortcode', __( 'Shortcode name' , 'wp-chatbot' ), array( $this, 'htcc_custom_shortcode_cb' ), 'htcc_options_settings', 'htcc_settings' );

    // add_settings_field( 'htcc_fb_is_minimized', __( 'Minimized' , 'wp-chatbot' ), array( $this, 'htcc_fb_is_minimized_cb' ), 'htcc_options_settings', 'htcc_settings' );

    add_settings_field( 'htcc_page_load', __( 'Call SDK after Page Load' , 'wp-chatbot' ), array( $this, 'htcc_page_load_cb' ), 'htcc_options_settings', 'htcc_settings' );

    }

    // section heading
    function htcc_settings_section_cb()
    {
		$activepage = get_option('mobilemonkey_active_page_id');
		if (!$activepage) {
             $this->logedin = false;
        }else{
             $this->logedin = true;
         } ?>
        <h2 class="acc-title <?php if (!$this->logedin){ echo "open"; } else {echo "close";} ?>"><div class="circle">1</div> Connect to your Facebook account <i class="fa fa-angle-down step_fa"></i></h2>
        <div class="acc-content" style="display: <?php if (!$this->logedin) echo "block"; else {echo "none";}?>">
            <div class="accordionItemHeading">

                <div class="row">
                    <div class="input-field col s12">
                        <?php $this->htcc_fb_connection_button_cb(); ?>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="acc-title <?php if (!$this->logedin){ echo "close"; } else {echo "open";} ?>"><div class="circle">2</div> Set up your ChatBot <i class="fa fa-angle-down step_fa"></i></h2>
        <div class="acc-content" style="display: <?php if (!$this->logedin) echo "none"; else {echo "block";}?>">
            <div class="accordionItemHeading">
                <div class="row">
                    <div class="input-field col s12">
                        <?php $this->htcc_fb_welcome_message_cb() ?>
                    </div>
                </div>
            </div>
        </div>
         <h2 class="acc-title <?php if (!$this->logedin){ echo "close"; } else {echo "open";} ?>"><div class="circle">3</div> Customize <i class="fa fa-angle-down step_fa"></i></h2>
        <div class="acc-content" style="display: <?php if (!$this->logedin) echo "none"; else {echo "block";}?>">
        <?php
    }

    /**
     * Facebook connection button area view
     */

    public function htcc_fb_connection_button_cb() {

        $options = get_option('htcc_options');
        $api = $this->getApi();

        $api->logoutMobilemonkey();
        $token = $api->connectMobileMonkey();

        if ($token) {

            $reset = FALSE;
            if ($api->connectPage() || $api->disconnectPage()) {
                $reset = TRUE;

            }

            $pages = $api->getPages($reset);
            $activePage = $api->getActivePage($reset);

            if ($activePage) {
                $current_welcome_message = $api->getWelcomeMessage($activePage['remote_id']);
                if (!empty($options['fb_welcome_message']) && $options['fb_welcome_message'] !== $current_welcome_message) {
                    $api->updateWelcomeMessage($options['fb_welcome_message'], $activePage['remote_id']);
                }
                $fb_connected_area_active_page_settings = [
                    'connected_page' => $activePage,
                    'current_facebook_page_block' => '',
                    'logout_path' => add_query_arg([
                        'page' => HTCC_PLUGIN_MAIN_MENU,
                        'logout' => true,
                    ], admin_url('admin.php')),
                ];
                HT_CC::view('ht-cc-admin-fb-button-connected', $fb_connected_area_active_page_settings);

            } else {

                $fb_connected_area_pages_settings = [
                    'pages' => $pages,
                    'logout_path' => add_query_arg([
                        'page' => HTCC_PLUGIN_MAIN_MENU,
                        'logout' => true,
                    ], admin_url('admin.php')),
                ];
                HT_CC::view('ht-cc-admin-fb-button-select-page', $fb_connected_area_pages_settings);
            }

        } else {

            HT_CC::view('ht-cc-admin-fb-button-not-connected', [
                'options' => $options,
                'path' => $this->getApi()->connectLink(),
            ]);
        }

    }


    // color - next new version added ..
    public function htcc_fb_color_cb_old() {

        $htcc_fb_color = get_option('htcc_options');
        ?>

        <div class="row">
            <div class="input-field col s12">

                <!-- <input name="htcc_options[fb_color]" data-default-color="#26a69a" value="<?php echo esc_attr( $htcc_fb_color['fb_color'] ) ?>" type="text" class="htcc-color-wp" style="height: 1.375rem;" > -->

                <input id="htcc-color-wp" class="htcc-color-wp" name="htcc_options[fb_color]" value="<?php echo esc_attr( $htcc_fb_color['fb_color'] ) ?>" type="text" style="height: 1.375rem;" >
                <p class="description"><?php _e( 'messenger theme color, leave empty for default color - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/messenger-theme-color/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }

    // color
    public function htcc_fb_color_cb() {

        $htcc_fb_color = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <!-- <input name="htcc_options[fb_color]" value="<?php echo esc_attr( $htcc_fb_color['fb_color'] ) ?>" type="color" class="htcc-color-wp" style="width: 5rem; height: 1.5rem;" > -->
                <input name="htcc_options[fb_color]" value="<?php echo esc_attr( $htcc_fb_color['fb_color'] ) ?>" type="text" class="htcc-color-wp" style="height: 1.375rem;" >
                <p class="description"><?php _e( 'messenger theme color, leave empty for default color - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/messenger-theme-color/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
                <!-- <p class="description"><?php _e( 'please open settings page in the browser that supports "type color", we are planning to make a better way to choose the color ' , 'wp-chatbot' ) ?></p> -->
            </div>
        </div>
        <?php
    }

    // Welcome message
    public function htcc_fb_welcome_message_cb() {
        $htcc_fb_welcome_message = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input type="text" name="htcc_options[fb_welcome_message]" id="fb_welcome_message" value="<?php echo esc_attr($htcc_fb_welcome_message['fb_welcome_message']) ?>">
                <label for="fb_greeting_login"><?php _e('Welcome message', 'ht-click') ?></label>
                <p class="description"><?php _e('Welcome message - wp-chatbot will greet your chat users with this message ', 'wp-chatbot') ?><!--<a target="_blank" href="#"><?php //_e('more info', 'wp-chatbot') ?></a> --></p>
            </div>
        </div>
    <?php
    }

    // Greeting for logged in user
    public function htcc_fb_greeting_login_cb() {

        $htcc_fb_greeting_login = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input type="text" name="htcc_options[fb_greeting_login]" id="fb_greeting_login" value="<?php echo esc_attr( $htcc_fb_greeting_login['fb_greeting_login'] ) ?>">
                <label for="fb_greeting_login"><?php _e( 'Logged in Greetings' , 'ht-click' ) ?></label>
                <p class="description"><?php _e( 'Greetings text - If Facebook logged in the current browser, leave empty for default message - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/change-facebook-messenger-greetings-text/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
                <!-- <p class="description"><?php _e( 'Grettings can add in any language, this can be different to the messenger language' , 'wp-chatbot' ) ?></p> -->
                <!-- <p class="description"><?php _e( 'If this Greetings fields are blank, default Greetings will load based on Messenger Language' , 'wp-chatbot' ) ?></p> -->
            </div>
        </div>
        <?php
    }

    // Greeting for logged out user
    public function htcc_fb_greeting_logout_cb() {

        $htcc_fb_greeting_logout = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input type="text" name="htcc_options[fb_greeting_logout]" id="fb_greeting_logout" value="<?php echo esc_attr( $htcc_fb_greeting_logout['fb_greeting_logout'] ) ?>">
                <label for="fb_greeting_logout"><?php _e( 'Logged out Greetings' , 'ht-click' ) ?></label>
                <p class="description"><?php _e( 'Greetings text - If Facebook logged out in the current browser, leave empty for default message - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/change-facebook-messenger-greetings-text/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }

    // sdk lang. / messenger lang
    public function htcc_fb_sdk_lang_cb() {

        $sdk_lang = get_option('htcc_options');
        $lang = esc_attr( $sdk_lang['fb_sdk_lang'] );
        ?>
        <div class="row">
            <div class="input-field col s12">
                <select name="htcc_options[fb_sdk_lang]">
                <?php
                $fb_lang = HTCC_Lang::$fb_lang;

                foreach ( $fb_lang as $key => $value ) {
                ?>
                <option value="<?php echo $key ?>" <?php echo $lang == $key ? 'SELECTED' : ''; ?> ><?php echo $value ?></option>
                <?php
                }

                ?>
                </select>
                <label for=""><?php _e( 'Language' , 'ht-click' ) ?></label>
                <p class="description"><?php _e( 'Language displays in chat window, not user input - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/messenger-language/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
                <p class="description"><?php _e( 'Facebook SDK is not supporting all languages.., please don\'t consider it, as an error ' , 'wp-chatbot' ) ?> </p>
                <!-- <p class="description"><?php _e( 'If desired Language is not added - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://www.messenger.com/t/1541811499235090/"><?php _e( 'please message us' , 'wp-chatbot' ) ?></a> </p> -->
        </div>
        </div>
        <?php
    }


    // greeting_dialog_display - since v2.2
    public function htcc_fb_greeting_dialog_display_cb() {
        $greeting_dialog_display = get_option('htcc_options');
        $min_value = esc_attr( $greeting_dialog_display['greeting_dialog_display'] );
        ?>
        <div class="row">
            <div class="input-field col s12">
                <select name="htcc_options[greeting_dialog_display]" class="select-1">
                <option value="" <?php echo $min_value == "" ? 'SELECTED' : ''; ?> >Default</option>
                <option value="show" <?php echo $min_value == "show" ? 'SELECTED' : ''; ?> >Show</option>
                <option value="fade" <?php echo $min_value == "fade" ? 'SELECTED' : ''; ?> >Fade</option>
                <option value="hide" <?php echo $min_value == "hide" ? 'SELECTED' : ''; ?> >Hide</option>
                </select>
                <label for=""><?php _e( 'Greeting Dialog Display' , 'ht-click' ) ?></label>
                <p class="description"><?php _e( 'Greetings Dialog Display  - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/greeting-dialog-display/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
                <p class="description"><?php _e( 'Show - The greeting dialog will always be shown when the plugin loads.' , 'wp-chatbot' ) ?></p>
                <p class="description"><?php _e( 'Fade - The greeting dialog of the plugin will be shown, then fade away and stay hidden afterwards.' , 'wp-chatbot' ) ?></p>
                <p class="description"><?php _e( 'Hide - The greeting dialog of the plugin will always be hidden until a user clicks on the plugin.' , 'wp-chatbot' ) ?></p>
            </div>
        </div>
        <?php
    }

    // greeting_dialog_delay - since v2.2
    public function htcc_fb_greeting_dialog_delay_cb() {
        $greeting_dialog_delay = get_option('htcc_options');
        $delay_time = esc_attr( $greeting_dialog_delay['greeting_dialog_delay'] );
        ?>
        <div class="row">
            <div class="input-field col s12">
            <input type="number" min="0" name="htcc_options[greeting_dialog_delay]" id="greeting_dialog_delay" value="<?php echo $delay_time ?>">
            <label for="greeting_dialog_delay"><?php _e( 'Greeting Dialog Delay' , 'ht-click' ) ?></label>
            <p class="description"><?php _e( 'Sets the number of seconds of delay before the greeting dialog is shown after the plugin is loaded - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/greeting-dialog-delay/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }


    // ref
    public function htcc_fb_ref_cb() {

        $reference = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input type="text" name="htcc_options[ref]" id="ref" value="<?php echo esc_attr( $reference['ref'] ) ?>">
                <label for="ref"><?php _e( 'REF Attribute' , 'ht-click' ) ?></label>
                <p class="description"><?php _e( 'Useful to create Entry Point to your messenger chatbot - ' , 'wp-chatbot' ) ?><a target="_blank" href="https://mobilemonkey.com/wp-chatbot/messenger-ref/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }


    // minimized  - deprecated - since v2.2
    // removed since 3.2
    public function htcc_fb_is_minimized_cb() {
        $minimized = get_option('htcc_options');
        $min_value = esc_attr( $minimized['minimized'] );
        ?>
        <div class="row">
            <div class="input-field col s12">
        <div>
            <select name="htcc_options[minimized]" class="select-1">
            <option value="" <?php echo $min_value == "" ? 'SELECTED' : ''; ?> >Default</option>
            <option value="false" <?php echo $min_value == "false" ? 'SELECTED' : ''; ?> >False</option>
            <option value="true" <?php echo $min_value == "true" ? 'SELECTED' : ''; ?> >True</option>
            </select>  This attribute is now deprecated - <a target="_blank" href="https://mobilemonkey.com/wp-chatbot/minimize-messenger/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a>
        </div>
        <p class="description"><?php _e( 'Instead, use greeting_dialog_display, greeting_dialog_delay for customization' , 'wp-chatbot' ) ?> </p>
            </div>
        </div>
        <?php
    }



    // checkboxes - Hide based on Type of posts ..
    public function htcc_show_hide_post_types_cb() {
        $htcc_checkbox = get_option('htcc_options');

        // Single Posts
        if ( isset( $htcc_checkbox['hideon_posts'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_posts]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_posts'], 1 ); ?> id="filled-in-box1" />
                    <span><?php _e( 'Hide on - Posts' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_posts]" type="checkbox" value="1" id="filled-in-box1" />
                    <span><?php _e( 'Hide on - Posts' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }


        // Page
        if ( isset( $htcc_checkbox['hideon_page'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_page]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_page'], 1 ); ?> id="filled-in-box2" />
                    <span><?php _e( 'Hide on - Pages' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_page]" type="checkbox" value="1" id="filled-in-box2" />
                    <span><?php _e( 'Hide on - Pages' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }


        // Home Page
        if ( isset( $htcc_checkbox['hideon_homepage'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_homepage]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_homepage'], 1 ); ?> id="filled-in-box3" />
                    <span><?php _e( 'Hide on - Home Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_homepage]" type="checkbox" value="1" id="filled-in-box3" />
                    <span><?php _e( 'Hide on - Home Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }


        /* Front Page
         A front page is also a home page, but home page is not a front page
         if front page unchecked - it works on both homepage and fornt page
         but if home page is unchecked - it works only on home page, not on front page */
         if ( isset( $htcc_checkbox['hideon_frontpage'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_frontpage]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_frontpage'], 1 ); ?> id="filled-in-box4" />
                    <span><?php _e( 'Hide on - Front Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_frontpage]" type="checkbox" value="1" id="filled-in-box4" />
                    <span><?php _e( 'Hide on - Front Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }



        // Category
        if ( isset( $htcc_checkbox['hideon_category'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_category]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_category'], 1 ); ?> id="filled-in-box5" />
                    <span><?php _e( 'Hide on - Category' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_category]" type="checkbox" value="1" id="filled-in-box5" />
                    <span><?php _e( 'Hide on - Category' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }



        // Archive
        if ( isset( $htcc_checkbox['hideon_archive'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_archive]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_archive'], 1 ); ?> id="filled-in-box6" />
                    <span><?php _e( 'Hide on - Archive' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_archive]" type="checkbox" value="1" id="filled-in-box6" />
                    <span><?php _e( 'Hide on - Archive' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }



        // 404 Page
        if ( isset( $htcc_checkbox['hideon_404'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_404]" type="checkbox" value="1" <?php checked( $htcc_checkbox['hideon_404'], 1 ); ?> id="filled-in-box7" />
                    <span><?php _e( 'Hide on - 404 Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_404]" type="checkbox" value="1" id="filled-in-box7" />
                    <span><?php _e( 'Hide on - 404 Page' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }
        ?>
        <p class="description"> <?php _e( 'check for not to load Messenger - based on type of the page - ' , 'wp-chatbot' ) ?> <a target="_blank" href="https://mobilemonkey.com/wp-chatbot/show-hide-messenger-based-on-type-of-the-page/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>


        <?php
    }


    // ID 's list to hide styles
    function htcc_list_id_tohide_cb() {
        $htcc_list_id_tohide = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input name="htcc_options[list_hideon_pages]" value="<?php echo esc_attr( $htcc_list_id_tohide['list_hideon_pages'] ) ?>" id="list_hideon_pages htcc_list_id_tohide" type="text">
                <label for="list_hideon_pages"><?php _e( 'Post, Page Ids to Hide' , 'ht-click' ) ?></label>
                <p class="description"> <?php _e( 'Add Post, Page, Media - ID\'s to hide,' , 'wp-chatbot' ) ?> <br> <?php _e( 'can add multiple IDs separate with comma ( , )' , 'wp-chatbot' ) ?> - <a target="_blank" href="https://mobilemonkey.com/wp-chatbot/hide-messenger-based-on-post-id/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }

    //  Categorys list - to hide
    function htcc_list_cat_tohide_cb() {
        $htcc_list_cat_tohide = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input name="htcc_options[list_hideon_cat]" value="<?php echo esc_attr( $htcc_list_cat_tohide['list_hideon_cat'] ) ?>" id="list_hideon_cat htcc_list_cat_tohide" type="text" >
                <label for="list_hideon_cat"><?php _e( 'Categorys to Hide' , 'ht-click' ) ?></label>
                <p class="description"> <?php _e( 'Category name\'s to hide,' , 'wp-chatbot' ) ?> <br> <?php _e( 'can add multiple Categories separate with comma ( , )' , 'wp-chatbot' ) ?> - <a target="_blank" href="https://mobilemonkey.com/wp-chatbot/hide-messenger-based-on-category/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
            </div>
        </div>
        <?php
    }




    // checkboxes - based on Type of device ..
    public function htcc_show_hide_devices_cb() {
        $htcc_devices = get_option('htcc_options');

        // Hide on Mobile Devices
        if ( isset( $htcc_devices['hideon_mobile'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_mobile]" type="checkbox" value="1" <?php checked( $htcc_devices['hideon_mobile'], 1 ); ?> id="hideon_mobile" />
                    <span><?php _e( 'Hide on - Mobile Devices' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_mobile]" type="checkbox" value="1" id="hideon_mobile" />
                    <span><?php _e( 'Hide on - Mobile Devices' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }


        // Hide on Desktop Devices
        if ( isset( $htcc_devices['hideon_desktop'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_desktop]" type="checkbox" value="1" <?php checked( $htcc_devices['hideon_desktop'], 1 ); ?> id="hideon_desktop" />
                    <span><?php _e( 'Hide on - Desktops' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[hideon_desktop]" type="checkbox" value="1" id="hideon_desktop" />
                    <span><?php _e( 'Hide on - Desktops' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }
    }


    //  Custom shortcode
    function htcc_custom_shortcode_cb() {
        $htcc_shortcode = get_option('htcc_options');
        ?>
        <div class="row">
            <div class="input-field col s12">
                <input name="htcc_options[shortcode]" value="<?php echo esc_attr( $htcc_shortcode['shortcode'] ) ?>" id="shortcode" type="text" class="validate input-margin">
                <label for="shortcode"><?php _e( 'Shortcode name' , 'ht-click' ) ?></label>
                <?php
                // $shorcode_list = '';
                // foreach ($GLOBALS['shortcode_tags'] AS $key => $value) {
                //    $shorcode_list .= $key . ', ';
                //  }
                ?>
                <p class="description"> <?php printf( __( 'Default value is \'%1$s\', can customize shortcode name' , 'wp-chatbot' ), 'chatbot' ) ?> - <a target="_blank" href="https://mobilemonkey.com/wp-chatbot/change-shortcode-name/"><?php _e( 'more info' , 'wp-chatbot' ) ?></a> </p>
                <p class="description"> <?php _e( 'please don\'t add an already existing shortcode name' , 'wp-chatbot' ) ?>
                <!-- <p class="description"> <?php _e( 'please dont add this already existing shorcode names' , 'wp-chatbot' ) ?> - <?php echo $shorcode_list ?> </p> -->
            </div>
        </div>
        <?php
    }

    // page load
    public function htcc_page_load_cb() {
        $options = get_option('htcc_options');

        ?>
        <p class="description"><a href="https://mobilemonkey.com/wp-chatbot/call-sdk-after-page-loads/" target="_blank">Call SDK after page load</a></p>
        <p class="description">This feature is in beta stage ( please review these settings at least in the next two versions )</p>
        <p class="description">Don't enable this features If some other plugins or some other source also calls Facebook SDK.</p>
        <br>

        <?php
        // load sdk after page loaded
        if ( isset( $options['is_sdk_after_page_load'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[is_sdk_after_page_load]" type="checkbox" value="1" <?php checked( $options['is_sdk_after_page_load'], 1 ); ?> id="is_sdk_after_page_load" />
                    <span><?php _e( 'Call SDK after page loads' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[is_sdk_after_page_load]" type="checkbox" value="1" id="is_sdk_after_page_load" />
                    <span><?php _e( 'Call SDK after page loads' , 'wp-chatbot' ) ?></span>
                </label>
            </p>
            <?php
        }
        ?>
        <p class="description">Improves user experience. After webpage loaded, call SDK and display messenger</p>
        <br>

        <!-- <div class="time-delay" style="margin-left: 25px;"> -->

        <?php

        // +4 seconds - load sdk after page loaded
        if ( isset( $options['is_sdk_4_seconds'] ) ) {
            ?>
            <p>
                <label>
                    <input name="htcc_options[is_sdk_4_seconds]" type="checkbox" value="1" <?php checked( $options['is_sdk_4_seconds'], 1 ); ?> id="is_sdk_4_seconds" />
                    <span><?php _e('+4 seconds after page loaded' , 'wp-chatbot') ?></span>
                </label>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label>
                    <input name="htcc_options[is_sdk_4_seconds]" type="checkbox" value="1" id="is_sdk_4_seconds" />
                    <span><?php _e('+4 seconds after page loaded' , 'wp-chatbot') ?></span>
                </label>
            </p>
            <?php
        }
        ?>
        <!-- <p class="description">After webpage loaded and after 4 seconds, call SDK and display Messenger</p> -->
        <p class="description">Adds benefit at page speed metrics</p>
        </div>
        <?php
    }

    /**
     * Sanitize each setting field as needed
     *
     * @since 1.0.0
     * @param array $input Contains all settings fields as array keys
     */
    public function htcc_options_sanitize( $input ) {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'not allowed to modify - please contact admin ' );
        }

        $new_input = array();

        foreach ($input as $key => $value) {
            if( isset( $input[$key] ) ) {
                $new_input[$key] = sanitize_text_field( $input[$key] );
            }
        }


        return $new_input;
    }

}

endif; // END class_exists check
