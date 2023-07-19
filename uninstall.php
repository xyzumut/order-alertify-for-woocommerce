<?php 

register_uninstall_hook(__FILE__, 'deleteAction');

function deleteAction () {
    global $wpdb;
    $tableName = $wpdb->prefix . "orderalertifylogs"; 
    $wpdb->query( "DROP TABLE IF EXISTS $tableName" );
}

?>