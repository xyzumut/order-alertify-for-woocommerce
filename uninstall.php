<?php 
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;
    $tableName = $wpdb->prefix . "orderalertifylogs"; 
    $wpdb->query( "DROP TABLE IF EXISTS $tableName");

    $mailRuleTemp = get_option('mailRuleTemp', false);
    $smsRuleTemp = get_option('smsRuleTemp', false);
    $telegramRuleTemp = get_option('telegramRuleTemp', false);
    $telegramActiveUsersIndex = get_option('telegramActiveUsersIndex', false);
    
    if ($mailRuleTemp !== false) {
        $mailRuleTemp = json_decode($mailRuleTemp);
        $prefix = 'mailRule-';
        for ($i = 1; $i < $mailRuleTemp; $i++){
            delete_option($prefix.$i);
            delete_option($prefix.$i.'-mailContent');
            delete_option($prefix.$i.'-mailSubject');
            delete_option($prefix.$i.'-recipients');
        }
        delete_option('mailRuleTemp');
        delete_option('isMailEnable');
    }

    if ($smsRuleTemp !== false) {
        $smsRuleTemp = json_decode($smsRuleTemp);
        $prefix = 'smsRule-';
        for ($i = 1; $i < $smsRuleTemp; $i++){
            delete_option($prefix.$i);
            delete_option($prefix.$i.'-smsMessage');
            delete_option($prefix.$i.'-recipients');
        }
        delete_option('smsRuleTemp');
        delete_option('smsJwt');
        delete_option('smsSendMessageEndpoint');
        delete_option('smsLoginEndpoint');
        delete_option('smsBaseApiUrl');
        delete_option('smsLoginPassword');
        delete_option('smsLoginUsername');
        delete_option('isSmsEnable');
    }

    if ($telegramRuleTemp !== false) {
        $telegramRuleTemp = json_decode($telegramRuleTemp);
        $prefix = 'telegramRule-';
        for ($i = 1; $i < $telegramRuleTemp; $i++){
            delete_option($prefix.$i);
            delete_option($prefix.$i.'-telegramMessage');
        }
        delete_option('telegramToken');
        delete_option('telegramRuleTemp');
        delete_option('isTelegramEnable');
        
    }

    if ($telegramActiveUsersIndex !== false) {
        $telegramActiveUsersIndex = json_decode($telegramActiveUsersIndex);
        $prefix = 'telegramUser-';
        for ($i = 1; $i < $telegramActiveUsersIndex; $i++){
            delete_option($prefix.$i);
        }
        delete_option('telegramActiveUsersIndex');
    }

?>