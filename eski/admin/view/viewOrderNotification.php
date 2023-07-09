<?php
    $partials = (__DIR__).'/partials/';
    $outlookAddress = get_option('woocommerceOrderNotificationOutlookAddress');
    $outlookPassword = get_option('woocommerceOrderNotificationOutlookPassword');
    $yandexAddress = get_option('woocommerceOrderNotificationYandexMailAddress');
    $yandexPassword = get_option('woocommerceOrderNotificationYandexAppPassword');
    $brevoToken = get_option('woocommerceOrderNotificationBrevoToken');
?>

<div id="orderNotificationLoadingModal">
    <div id="orderNotificationLoadingModalContainer">
        <div id="orderNotificationLoadingModalContainerHeader">
            Ayarlar Kaydediliyor . . .
        </div>
        <div id="orderNotificationLoadingModalContainerBody">
            <div v-if="loading" class="spinnerNotifiaction">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
    </div>
</div>
<div id="woocommerceOrderNotification-mainContainer">
    <div id="woocommerceOrderNotification-header">
        <?php include $partials.'woocommerceOrderNotificationHeader.php' ?>
    </div>
    <div id="woocommerceOrderNotification-body">
        
        <div id="woocommerceOrderNotification-generalSettingContainer" class="woocommerceOrderNotification-bodyContainer woocommerceOrderNotification-activeContainer">
            <?php include $partials.'woocommerceOrderNotificationBody/generalSettings.php' ?>
        </div>

        <div id="woocommerceOrderNotification-mailContainer" class="woocommerceOrderNotification-bodyContainer">
            <div id="woocommerceOrderNotification-mailContainer-LeftBar">
                <?php include $partials.'woocommerceOrderNotificationBody/mailContainer/leftBar.php' ?>
            </div>
            <div id="woocommerceOrderNotification-mailContainer-RightBar">
                <div id="woocommerceOrderNotification-mailContainer-RightBarMailSettingsContainer" class="woocommerceOrderNotification-mailContainer-RightBarContainer woocommerceOrderNotification-mailContainer-RightBarACtiveContainer">
                    <?php include $partials.'woocommerceOrderNotificationBody/mailContainer/mailContainerRightBar/mailSettingsContainer.php' ?>
                </div>
                <div id="woocommerceOrderNotification-mailContainer-RightBarOutlookContainer" class="woocommerceOrderNotification-mailContainer-RightBarContainer">
                    <?php include $partials.'woocommerceOrderNotificationBody/mailContainer/mailContainerRightBar/outlookContainer.php' ?>
                </div>
                <div id="woocommerceOrderNotification-mailContainer-RightBarYandexConteiner" class="woocommerceOrderNotification-mailContainer-RightBarContainer">
                    <?php include $partials.'woocommerceOrderNotificationBody/mailContainer/mailContainerRightBar/yandexContainer.php' ?>
                </div>
                <div id="woocommerceOrderNotification-mailContainer-RightBarBrevoContainer" class="woocommerceOrderNotification-mailContainer-RightBarContainer">
                    <?php include $partials.'woocommerceOrderNotificationBody/mailContainer/mailContainerRightBar/brevoContainer.php' ?>
                </div>
            </div>
        </div>
        
        <div id="woocommerceOrderNotification-telegramContainer" class="woocommerceOrderNotification-bodyContainer">
            
        </div>
        <div id="woocommerceOrderNotification-smsContainer" class="woocommerceOrderNotification-bodyContainer">
            
        </div>
    </div>
</div>