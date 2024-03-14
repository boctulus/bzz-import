<?php

use boctulus\SW\core\libs\DB;

global $wpdb;

$table_name = $wpdb->prefix . "link2product_metadata";
$charset_collate = $wpdb->get_charset_collate();

DB::statement("DROP TABLE IF EXISTS `$table_name`;");

if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned AUTO_INCREMENT,
            link_id VARCHAR(100) NOT NULL UNIQUE, 
            metadata JSON,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
    ) ENGINE=InnoDB $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $ok = dbDelta($sql);
}
