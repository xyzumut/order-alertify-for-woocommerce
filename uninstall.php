<?php 
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;
    $tableName = $wpdb->prefix . "orderalertifylogs"; 
    $wpdb->query( "DROP TABLE IF EXISTS $tableName");
?>