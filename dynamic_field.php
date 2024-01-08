<?php
/*
Plugin Name: ACF Dynamic Field Shortcode
Description: Retrieve and display ACF custom field values using a shortcode for any post on any page.
Version: 1.0
Author: Igor Radovanov
*/

defined('ABSPATH') or die('No direct script access allowed');

/**
 * Registers the settings for the dynamic field.
 */
function dynamic_field_register_settings()
{
    add_option('dynamic_field_acf_param', '');
    register_setting('dynamic_field_options_group', 'dynamic_field_acf_param');
}

/**
 * Adds a menu item for Dynamic Field Options.
 */
function dynamic_field_add_menu_item()
{
    add_menu_page(
        'Dynamic Field Options',
        'Dynamic Field',
        'manage_options',
        'dynamic_field_options',
        'dynamic_field_options_page'
    );
}

/**
 * Renders the options page for dynamic field settings.
 */
function dynamic_field_options_page()
{
    ?>
    <div class="wrap">
        <h2>Dynamic Field Options</h2>
        <p>Enter the name of the URL parameter that will be used to retrieve the ACF field dynamically (ie. agent).</p>
        <i>Example: If the URL is http://example.com/?agent=123, the parameter used is agent. This should match your custom
            post type key.</i>
        <form method="post" action="options.php">
            <?php
            settings_fields('dynamic_field_options_group');
            do_settings_sections('dynamic_field_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">URL Parameter</th>
                    <td><input type="text" name="dynamic_field_acf_param"
                            value="<?php echo esc_attr(get_option('dynamic_field_acf_param')); ?>" /></td>
                </tr>
            </table>
            <?php
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_shortcode('dynamic_field', 'dynamic_field_shortcode');
add_action('admin_init', 'dynamic_field_register_settings');
add_action('admin_menu', 'dynamic_field_add_menu_item');

/**
 * Retrieves the value of a custom field using a shortcode.
 *
 * @param array $atts The shortcode attributes.
 * @return string The value of the custom field or an error message.
 */
function dynamic_field_shortcode($atts)
{
    $post_id = isset($_GET[get_option('dynamic_field_acf_param')]) ? intval($_GET[get_option('dynamic_field_acf_param')]) : false;

    $atts = shortcode_atts(
        array(
            'field' => '',
        ),
        $atts,
        'dynamic_field_shortcode'
    );

    $field = sanitize_text_field($atts['field']);

    if ($post_id !== false && $field !== '') {
        if (!function_exists('get_field')) {
            return 'Please install and activate the Advanced Custom Fields (ACF) plugin.';
        }

        if (get_field($field, $post_id) !== false) {
            return esc_html(get_field($field, $post_id));
        } else {
            return 'No data';
        }
    } else {
        return 'No valid post ID or field provided.';
    }
}
