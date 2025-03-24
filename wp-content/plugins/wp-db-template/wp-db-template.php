<?php
/**
 * Plugin Name: WP DB Template
 * Description: A simple WordPress plugin to create and manage a database table.
 * Version: 1.1
 * Author: Onur Kalkan
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Activation hook: Create database table
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
    add_submenu_page(
        'wpdbt-admin', 
        'Manage Records', 
        'Manage Records', 
        'manage_options', 
        'wpdbt-manage', 
        'wpdbt_manage_page'
    );
}
add_action('admin_menu', 'wpdbt_add_menu');

// Admin page content
function wpdbt_admin_page() {
    echo '<div class="wrap"><h1>WP DB Template Admin Panel</h1>';
    echo '<p>Here you will be able to manage your database records.</p></div>';
}

// Manage records page content
function wpdbt_manage_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    
    // Handle form submission for adding records
    if (isset($_POST['add_record'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        
        if (!empty($name) && !empty($email)) {
            $wpdb->insert(
                $table_name,
                array('name' => $name, 'email' => $email),
                array('%s', '%s')
            );
            echo '<div class="updated"><p>Record added successfully!</p></div>';
        }
    }

    // Handle deletion of a record
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table_name, array('id' => $id), array('%d'));
        echo '<div class="updated"><p>Record deleted successfully!</p></div>';
    }

    // Display the form for adding a record
    ?>
    <div class="wrap">
        <h1>Manage Records</h1>
        
        <h2>Add New Record</h2>
        <form method="POST" action="">
            <table class="form-table">
                <tr>
                    <th><label for="name">Name</label></th>
                    <td><input type="text" name="name" id="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="email" name="email" id="email" class="regular-text" required></td>
                </tr>
            </table>
            <p><input type="submit" name="add_record" value="Add Record" class="button-primary"></p>
        </form>

        <h2>Existing Records</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $results = $wpdb->get_results("SELECT * FROM $table_name");
                foreach ($results as $row) {
                    echo "<tr>
                            <td>{$row->id}</td>
                            <td>{$row->name}</td>
                            <td>{$row->email}</td>
                            <td><a href='?page=wpdbt-manage&delete={$row->id}' class='button' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a></td>
                          </tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}

