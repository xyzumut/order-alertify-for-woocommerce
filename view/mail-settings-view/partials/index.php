<div id="oa_mainContainer">

    <div class="notification-box flex flex-col items-center justify-center fixed w-full z-50 p-3"> </div>

    <div id="oa_header">
        <?php _e('Mail Settings Page', '@@@') ?> 
    </div>
    
    <div id="oa_body">

        <div id="oa_body_left">
            <div id="mailGeneralSettingsButton" class="mailSettingsButton">
                <?php _e('General Mail Settings', '@@@') ?>
            </div>
            <div id="mailRulesSettingsButton" class="mailSettingsButton">
                <?php _e('Mail Rules', '@@@') ?>
            </div>
            <div id="mailTempateSettingsButton" class="mailSettingsButton">
                <?php _e('Mail Edit', '@@@') ?>
            </div>
        </div>

        <div id="oa_body_right">

            <div id="mailGeneralSettingsContainer" class="ou_body_right_item">
                <?php include (__DIR__.'/sub-partials/').'generalSettings.php'; ?>
            </div>

            <div id="mailRuleSettingsContainer" class="ou_body_right_item">
                <?php include (__DIR__.'/sub-partials/').'mailRuleSettings.php'; ?>
            </div>

            <div id="mailTemplateSettingsContainer" class="ou_body_right_item">
                <?php include (__DIR__.'/sub-partials/').'mailTemplatePage.php'; ?>
            </div>

        </div>

    </div>

</div>
<div id="orderNotificationLoadingModal">
    <div id="orderNotificationLoadingModalContainer">
        <div id="orderNotificationLoadingModalContainerHeader">
            <?php _e('Saving Settings . . .'); ?>
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