<?php 

    register_uninstall_hook(__FILE__, function(){
        global $wpdb;
        $tableName = $wpdb->prefix . "orderalertifylogs"; 
        $wpdb->query( "DROP TABLE IF EXISTS $tableName" );
    });

?>