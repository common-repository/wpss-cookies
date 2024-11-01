<?php
/**
 * WPSS Cookie Consent
 *
 * @package           wpss_cookies
 * @author            Angelo Rocha
 * @copyleft          2020
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WPSS Cookies
 * Plugin URI:        https://github.com/wpsuperstars/wpss_cookies
 * Description:       A simple way to add a cookie consent message in your WordPress
 * Version:           1.3.3
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            Angelo Rocha
 * Author URI:        https://angelorocha.com.br
 * Text Domain:       wpss
 * License:           GNU General Public License v3 or later
 * License URI:       /LICENSE
 */

final class WPSSCookiesAdmin{

    public function __construct(){
        add_action('admin_menu', array($this, 'wpss_plugin_menu_page'));
        add_action('admin_init', array($this, 'wpss_register_plugin_settings'));
        add_action('admin_enqueue_scripts', array($this, 'wpss_plugin_admin_scripts'));
        add_action('plugins_loaded', array($this, 'wpss_load_plugin_textdomain'));
    }

    /**
     * Plugin admin menu page
     */
    public function wpss_plugin_menu_page(){
        add_menu_page(
            __('Cookies Consent', 'wpss-cookies'),
            __('Cookies Consent', 'wpss-cookies'),
            'administrator',
            'wpss-cookies-consent',
            array($this, 'wpss_plugin_menu_content'),
            WPSS_COOKIES_PLUGIN_URL . "assets/images/plugin-ico.png",
            75
        );
    }

    /**
     * Plugin admin page frontend
     */
    public function wpss_plugin_menu_content(){
        $plugin_title    = __('Cookie Consent Option', 'wpss-cookies');
        $message_label   = __('Edit Message', 'wpss-cookies');
        $editor_settings = array('media_buttons' => false, 'quicktags' => false, 'textarea_rows' => get_option('default_post_edit_rows', 5));
        echo "<div class='wpss-container'>";
        echo "<div class='wpss-save-msg'>";
        settings_errors();
        echo "</div>";
        echo "<h1>$plugin_title</h1>";
        self::wpss_admin_social_links();
        echo "<form method='post' action='options.php'>";
        settings_fields('wpss_cookie_options');
        do_settings_sections('wpss_cookie_options');
        self::wpss_show_message_field();
        self::wpss_message_position_field();
        self::wpss_message_style_field();
        self::wpss_message_button_field();
        self::wpss_cookie_name();
        self::wpss_cookie_life_time();
        echo "<label for='wpss_cookie_message'>$message_label</label>";
        wp_editor(get_option('wpss_cookie_message'), 'wpss_cookie_message', $editor_settings);

        submit_button(__('Update Settings', 'wpss-cookies'));

        echo "</form>";
        echo "</div>";
    }

    /**
     * Register plugin settings
     */
    public function wpss_register_plugin_settings(){
        register_setting('wpss_cookie_options', 'wpss_show_cookie_message', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_message_position', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_message_style', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_button_text', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_cookie_name', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_cookie_life', self::wpss_sanitize_fields());
        register_setting('wpss_cookie_options', 'wpss_cookie_message', self::wpss_sanitize_fields(
            'string',
            'wp_kses_post'
        ));
    }

    /**
     * Plugin social links
     */
    public function wpss_admin_social_links(){
        echo "<div class='wpss-social-links'>";
        $stay_in_touch = __('Stay in touch', 'wpss-cookies');
        $facebook      = "https://www.facebook.com/angelorochawp/";
        $instagram     = "https://www.instagram.com/angelorocha.wp/";
        $linkedin      = "https://br.linkedin.com/in/angelorocha";
        $github        = "https://github.com/angelorocha/";
        echo "<ul><li><strong>$stay_in_touch: </strong></li>";
        echo "<li class='facebook'><a href='$facebook' title='Facebook' target='_blank'>Facebook</a></li>";
        echo "<li class='instagram'><a href='$instagram' title='Instagram' target='_blank'>Instagram</a></li>";
        echo "<li class='linkedin'><a href='$linkedin' title='Linkedin' target='_blank'>Linkedin</a></li>";
        echo "<li class='github'><a href='$github' title='Github' target='_blank'>Github</a></li></li>";
        echo "</div>";
    }

    /**
     * Sanitize plugin fields
     * @param string $type
     * @param string $sanitize_cb
     * @param null $default
     * @return array
     */
    public function wpss_sanitize_fields($type = 'string', $sanitize_cb = 'sanitize_text_field', $default = null){
        return array(
            'type'              => $type,
            'sanitize_callback' => $sanitize_cb,
            'default'           => $default,
        );
    }

    /**
     * Enable cookie message field
     */
    public function wpss_show_message_field(){
        $enable_label = __('Enable Cookie Consent', 'wpss-cookies');
        $op_on        = __('Yes', 'wpss-cookies');
        $op_off       = __('No', 'wpss-cookies');
        echo "<div class='wpss-input-group'>";
        echo "<h3>$enable_label</h3>";
        echo "<label for='wpss_show_cookie_message_yes'>";
        echo "<input type='radio' name='wpss_show_cookie_message' id='wpss_show_cookie_message_yes' value='1'" . self::wpss_radio_checked('1', 'wpss_show_cookie_message') . ">$op_on</label>";
        echo "<label for='wpss_show_cookie_message_no'>";
        echo "<input type='radio' name='wpss_show_cookie_message' id='wpss_show_cookie_message_no' value='0'" . self::wpss_radio_checked('0', 'wpss_show_cookie_message') . ">$op_off</label>";
        echo "</div>";
    }

    /**
     * Message position field
     */
    public function wpss_message_position_field(){
        $position_label = __('Message Position', 'wpss-cookies');
        $op_top         = __('Top', 'wpss-cookies');
        $op_bottom      = __('Bottom', 'wpss-cookies');
        echo "<div class='wpss-input-group'>";
        echo "<h3>$position_label</h3>";
        echo "<label for='wpss_message_position_top'>";
        echo "<input type='radio' name='wpss_message_position' id='wpss_message_position_top' value='top'" . self::wpss_radio_checked('top', 'wpss_message_position') . ">$op_top</label>";
        echo "<label for='wpss_message_position_bottom'>";
        echo "<input type='radio' name='wpss_message_position' id='wpss_message_position_bottom' value='bottom'" . self::wpss_radio_checked('bottom', 'wpss_message_position') . ">$op_bottom</label>";
        echo "</div>";
    }

    /**
     * Message style field
     */
    public function wpss_message_style_field(){
        $style_label = __('Message Style', 'wpss-cookies');
        $ocean_op    = WPSS_COOKIES_PLUGIN_URL . "assets/images/ocean_op.png";
        $light_op    = WPSS_COOKIES_PLUGIN_URL . "assets/images/light_op.png";
        $forest_op   = WPSS_COOKIES_PLUGIN_URL . "assets/images/forest_op.png";
        $solar_op    = WPSS_COOKIES_PLUGIN_URL . "assets/images/solar_op.png";
        $aqua_op     = WPSS_COOKIES_PLUGIN_URL . "assets/images/aqua_op.png";
        $midnight_op = WPSS_COOKIES_PLUGIN_URL . "assets/images/midnight_op.png";
        echo "<div class='wpss-input-group wpss-align-center'>";
        echo "<h3>$style_label</h3>";
        echo "<div class='wpss-radio-image-inline'>";
        echo "<label for='wpss_message_style_ocean'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_ocean' value='wpss_ocean'" . self::wpss_radio_checked('wpss_ocean', 'wpss_message_style') . "> Ocean<img src='$ocean_op'></label>";
        echo "<label for='wpss_message_style_light'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_light' value='wpss_light'" . self::wpss_radio_checked('wpss_light', 'wpss_message_style') . "> Light<img src='$light_op'></label>";
        echo "<label for='wpss_message_style_forest'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_forest' value='wpss_forest'" . self::wpss_radio_checked('wpss_forest', 'wpss_message_style') . "> Forest<img src='$forest_op'></label>";
        echo "<label for='wpss_message_style_solar'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_solar' value='wpss_solar'" . self::wpss_radio_checked('wpss_solar', 'wpss_message_style') . "> Solar<img src='$solar_op'></label>";
        echo "<label for='wpss_message_style_aqua'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_aqua' value='wpss_aqua'" . self::wpss_radio_checked('wpss_aqua', 'wpss_message_style') . "> Aqua<img src='$aqua_op'></label>";
        echo "<label for='wpss_message_style_midnight'>";
        echo "<input type='radio' name='wpss_message_style' id='wpss_message_style_midnight' value='wpss_midnight'" . self::wpss_radio_checked('wpss_midnight', 'wpss_message_style') . "> Midnight<img src='$midnight_op'></label>";
        echo "</div>";
        echo "</div>";
    }

    /**
     * Button text field
     */
    public function wpss_message_button_field(){
        $button_label = __('Button Label', 'wpss-cookies');
        $button_text  = get_option('wpss_button_text');
        echo "<div class='wpss-input-group'>";
        echo "<label for='wpss_button_text'><h3>$button_label</h3></label>";
        echo "<input type='text' name='wpss_button_text' id='wpss_button_text' value='$button_text'>";
        echo "</div>";
    }

    /**
     * Button text field
     */
    public function wpss_cookie_name(){
        $label       = __('Cookie Name', 'wpss-cookies');
        $cookie_name = get_option('wpss_cookie_name');
        if(!$cookie_name):
            $cookie_name = "wpss_cookie_accepted";
        endif;
        echo "<div class='wpss-input-group'>";
        echo "<label for='wpss_cookie_name'><h3>$label</h3></label>";
        echo "<input type='text' name='wpss_cookie_name' id='wpss_cookie_name' value='$cookie_name'>";
        echo "</div>";
    }

    public function wpss_cookie_life_time(){
        $cookie_time = __('Cookie Lifetime', 'wpss-cookies');
        $week        = __('One Week', 'wpss-cookies');  // 604800 seconds
        $month       = __('One Month', 'wpss-cookies'); // 2629746 seconds
        $year        = __('One Year', 'wpss-cookies');  // 31556926 seconds
        echo "<div class='wpss-input-group'>";
        echo "<h3>$cookie_time</h3>";
        echo "<div class='wpss-radio-image-inline'>";
        echo "<label for='wpss_cookie_life_year'>";
        echo "<input type='radio' name='wpss_cookie_life' id='wpss_cookie_life_year' value='31556926'" . self::wpss_radio_checked('31556926', 'wpss_cookie_life') . "> $year</label>";
        echo "<label for='wpss_cookie_life_month'>";
        echo "<input type='radio' name='wpss_cookie_life' id='wpss_cookie_life_month' value='2629746'" . self::wpss_radio_checked('2629746', 'wpss_cookie_life') . "> $month</label>";
        echo "<label for='wpss_cookie_life_week'>";
        echo "<input type='radio' name='wpss_cookie_life' id='wpss_cookie_life_week' value='604800'" . self::wpss_radio_checked('604800', 'wpss_cookie_life') . "> $week</label>";
        echo "</div>";
        echo "</div>";
    }

    /***
     * Check current input radio value
     * @param $val
     * @param $option
     * @return string
     */
    public function wpss_radio_checked($val, $option){
        $checked = '';
        if($val === get_option($option)):
            $checked = ' checked';
        endif;

        return $checked;
    }

    /**
     * Plugin activate hook
     */
    public static function wpss_on_plugin_activate(){
        $accept  = __('Accept', 'wpss-cookies');
        $message = __('We use cookies to provide our services and for analytics and marketing. To find out more about our use of cookies, please see our Privacy Policy. By continuing to browse our website, you agree to our use of cookies.', 'wpss-cookies');
        add_option('wpss_show_cookie_message', '0');
        add_option('wpss_message_position', 'bottom');
        add_option('wpss_message_style', 'wpss_ocean');
        add_option('wpss_button_text', $accept);
        add_option('wpss_cookie_name', 'wpss_cookie_accepted');
        add_option('wpss_cookie_life', '31556926');
        add_option('wpss_cookie_message', $message);
    }

    /**
     * Plugin deactivate hook
     */
    public static function wpss_on_plugin_deactivate(){
        delete_option('wpss_show_cookie_message');
        delete_option('wpss_message_position');
        delete_option('wpss_message_style');
        delete_option('wpss_button_text');
        delete_option('wpss_cookie_name');
        delete_option('wpss_cookie_life');
        delete_option('wpss_cookie_message');
    }

    /**
     * Plugin internationalization
     */
    public function wpss_load_plugin_textdomain(){
        load_plugin_textdomain('wpss-cookies', false, WPSS_COOKIES_PLUGIN_DIR . '/lang/');
    }

    /**
     * Get plugin admin scripts
     */
    public function wpss_plugin_admin_scripts(){
        if(isset($_GET['page']) && $_GET['page'] === 'wpss-cookies-consent'):
            wp_enqueue_style('wpss-cookie-admin', WPSS_COOKIES_PLUGIN_URL . 'assets/css/wpss-cookie-admin.css', '', WPSS_COOKIES_PLUGIN_VERSION, 'all');
            wp_enqueue_script('wpss-cookie-admin', WPSS_COOKIES_PLUGIN_URL . 'assets/js/wpss-cookie-admin.js', '', WPSS_COOKIES_PLUGIN_VERSION, 'all');
        endif;
    }
}