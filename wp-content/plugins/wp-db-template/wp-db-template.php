<?php
/**
 * Plugin Name: WP DB Template
 * Description: A simple WordPress plugin to create and manage a database table.
 * Version: 1.0
 * Author: Onur Kalkan
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Activation hook: Create DB table
function wpdbt_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'wpdbt_create_table');

// Admin menu
function wpdbt_add_menu() {
    add_menu_page(
        'WP DB Template',
        'WP DB Template',
        'manage_options',
        'wpdbt-admin',
        'wpdbt_admin_page',
        'dashicons-database'
    );
}
add_action('admin_menu', 'wpdbt_add_menu');

// Admin page content
function wpdbt_admin_page() {
    echo '<div class="wrap"><h1>WP DB Template Admin Panel</h1>';
    echo '<p>Here you will be able to manage your database records.</p></div>';
}

// Shortcode to display data
function wpdbt_display_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    
    $output = '<table border="1"><tr><th>ID</th><th>Name</th><th>Email</th></tr>';
    foreach ($results as $row) {
        $output .= "<tr><td>{$row->id}</td><td>{$row->name}</td><td>{$row->email}</td></tr>";
    }
    $output .= '</table>';
    
    return $output;
}
add_shortcode('wp_db_display', 'wpdbt_display_data');
