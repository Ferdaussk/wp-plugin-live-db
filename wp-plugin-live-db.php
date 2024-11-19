<?php
/*
Plugin Name: WP Plugin Live DB
Description: Replace existing data in a live server database from the plugin dashboard.
Version: 1.2
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add a menu in the admin dashboard
add_action('admin_menu', 'wp_live_db_plugin_menu');
function wp_live_db_plugin_menu() {
    add_menu_page(
        'Live DB Replacer',
        'Live DB Replacer',
        'manage_options',
        'wp-live-db-replacer',
        'wp_live_db_replacer_dashboard',
        'dashicons-database',
        20
    );
}

// Dashboard Page
function wp_live_db_replacer_dashboard() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check for form submission
    if (isset($_POST['submit'])) {
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);
        $image_url = esc_url_raw($_POST['image_url']);
        $number = intval($_POST['number']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $response = wp_live_db_update_data($title, $description, $image_url, $number, $is_active);
        echo '<div class="notice notice-success"><p>' . esc_html($response) . '</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Replace Data in Live Database</h1>
        <form method="POST">
            <table class="form-table">
                <tr>
                    <th><label for="title">Title:</label></th>
                    <td><input type="text" id="title" name="title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="description">Description:</label></th>
                    <td><textarea id="description" name="description" class="large-text" rows="4" required></textarea></td>
                </tr>
                <tr>
                    <th><label for="image_url">Image URL:</label></th>
                    <td><input type="url" id="image_url" name="image_url" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="number">Number:</label></th>
                    <td><input type="number" id="number" name="number" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="is_active">Is Active:</label></th>
                    <td><input type="checkbox" id="is_active" name="is_active"></td>
                </tr>
            </table>
            <?php submit_button('Replace Data'); ?>
        </form>
    </div>
    <?php
}

// Update or Replace Data in Live Server Database
function wp_live_db_update_data($title, $description, $image_url, $number, $is_active) {
    $remote_db_host = 'localhost';
    $remote_db_name = 'live_datas_insert';
    $remote_db_user = 'root';
    $remote_db_pass = '';
    $remote_db_table = 'live_table_name';

    // Connect to the live database
    $remote_db = new mysqli($remote_db_host, $remote_db_user, $remote_db_pass, $remote_db_name);

    // Check connection
    if ($remote_db->connect_error) {
        return 'Connection failed: ' . $remote_db->connect_error;
    }

    // Prepare and execute the update query
    $stmt = $remote_db->prepare("UPDATE $remote_db_table SET title = ?, description = ?, image_url = ?, number = ?, is_active = ? WHERE id = 1");
    $stmt->bind_param('sssii', $title, $description, $image_url, $number, $is_active);

    if ($stmt->execute()) {
        $stmt->close();
        $remote_db->close();
        return 'Data replaced successfully!';
    } else {
        $error = $stmt->error;
        $stmt->close();
        $remote_db->close();
        return 'Error replacing data: ' . $error;
    }
}



    // $remote_db_host = 'localhost';
    // $remote_db_name = 'live_datas_insert';
    // $remote_db_user = 'root';
    // $remote_db_pass = '';
    // $remote_db_table = 'live_table_name';

