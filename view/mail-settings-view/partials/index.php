<div id="oa_mainContainer">

    <div class="notification-box flex flex-col items-center justify-center fixed w-full z-50 p-3"> </div>

    <div id="orderNotificationLoadingModal">
        <div id="orderNotificationLoadingModalContainer">
            <div id="orderNotificationLoadingModalContainerHeader">
                <?php _e('Saving Settings . . .', 'orderAlertifyTextDomain'); ?>
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

    <div id="oa_header">
        <?php _e('Mail Settings Page', 'orderAlertifyTextDomain') ?> 
    </div>
    
    <div id="oa_body">

        <div id="oa_body_left">
            <!-- Burası oa_body_right'a göre kendi gelecek -->
        </div>

        <div id="oa_body_right">

            <div id="mailRuleSettingsContainer" buttonText="<?php _e('Mail Rule Settings', 'orderAlertifyTextDomain')?>" class="ou_body_right_item">
                <?php include (__DIR__.'/sub-partials/').'mailRuleSettings.php'; ?>
            </div>

            <div id="mailTemplateSettingsContainer" buttonText="<?php _e('Edit Rule Settings', 'orderAlertifyTextDomain')?>" class="ou_body_right_item privateMenuItem">
                <?php include (__DIR__.'/sub-partials/').'mailTemplatePage.php'; ?>
            </div>

        </div>

    </div>

</div>
