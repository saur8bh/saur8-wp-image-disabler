<?php
/*
Plugin Name: WP Image Disabler by Saur8bh
Description: This plugin provides a settings page to disable specific image sizes from being generated.
Version: 1.0
Author: Saur8bh
*/

// Add the menu item and page
function wp_image_disabler_menu_page() {
    add_submenu_page('options-general.php', 'WP Image Disabler', 'WP Image Disabler', 'manage_options', 'wp-image-disabler', 'wp_image_disabler_page');
}
add_action('admin_menu', 'wp_image_disabler_menu_page');

// Settings page display
function wp_image_disabler_page() {
    echo '<div class="wrap">';
    echo '<h1>WP Image Disabler</h1>';

    // Get all image sizes
    global $_wp_additional_image_sizes;
    $image_sizes = get_intermediate_image_sizes();

    echo '<form method="post" action="options.php">';
    settings_fields('wp_image_disabler_settings');
    do_settings_sections('wp_image_disabler_settings');

    foreach ($image_sizes as $size) {
        $checked = get_option('disable_'.$size) ? 'checked' : '';
        echo '<input type="checkbox" id="disable_'.$size.'" name="disable_'.$size.'" '.$checked.' />';
        echo '<label for="disable_'.$size.'">'.$size.'</label><br>';
    }

    submit_button();

    // Display list of disabled image sizes
    echo '<h2>Disabled Image Sizes</h2>';
    foreach ($image_sizes as $size) {
        if (get_option('disable_'.$size)) {
            echo '<p>'.$size.'</p>';
        }
    }

    echo '</form></div>';
}

// Register settings
function wp_image_disabler_register_settings() {
    $image_sizes = get_intermediate_image_sizes();
    foreach ($image_sizes as $size) {
        register_setting('wp_image_disabler_settings', 'disable_'.$size);
    }
}
add_action('admin_init', 'wp_image_disabler_register_settings');

// Disable image sizes
function wp_image_disabler_adjust_sizes($sizes) {
    foreach ($sizes as $size => $dimensions) {
        if (get_option('disable_'.$size)) {
            unset($sizes[$size]);
        }
    }
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'wp_image_disabler_adjust_sizes');
