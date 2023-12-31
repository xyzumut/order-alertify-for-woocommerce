


<div id="telegramMainSettingsLayout">

    <div id="telegramTokenContainer">
        <div id="telegramTokenLabelContainer">
            <label for="telegramTokenInput" id="telegramTokenLabel">Telegram Token : </label>
        </div>
        <input type="text" id="telegramTokenInput">
        <button id="saveTelegramTokenButton"><?php _e('Save') ?></button>
    </div>

    <div class="info">
        <?php _e('Below are active telegram recipients.', 'orderAlertifyTextDomain'); ?>
    </div>

    <div id="activeTelegramUsersContainer">

        <div id="activeTelegramUsersHeader">
            <div id="activeTelegramUsersHeaderText">
                <?php _e('Active Telegram Users', 'orderAlertifyTextDomain'); ?>
            </div>
            <div id="activeTelegramUsersHeaderLine"></div>
        </div>

        <div id="activeTelegramUsersBody">

            <div id="activeTelegramUsersBodyHeader">
                <div id="activeTelegramUsersBodyHeaderRow">
                    <div class="telegramBodyHeaderCol telegramBodyCol"><?php _e('Name Surname', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyHeaderCol telegramBodyCol"><?php _e('Telegram Username', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyHeaderCol telegramBodyCol"><?php _e('Chat ID', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyHeaderCol telegramBodyCol"></div>
                </div>
            </div>

            <div id="activeTelegramUsersBodyRows">

                <div class="activeTelegramUsersBodyRow">
                    <div class="telegramBodyCol"><?php _e('Not Yet', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyCol"><?php _e('Not Yet', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyCol"><?php _e('Not Yet', 'orderAlertifyTextDomain'); ?></div>
                    <div class="telegramBodyCol"></div>
                </div>

            </div>
        </div>
    </div>

    <div class="info">
        <?php _e('In order to define a new user for your Telegram Bot, you must first start a chat with your Telegram Bot.', 'orderAlertifyTextDomain'); ?>
    </div>

    <div class="info">
        <?php _e('When you do this, you can accept yourself from the panel below.', 'orderAlertifyTextDomain'); ?>
    </div>

    <div id="telegramPendingRequestsContainer">

        <div id="pendingRequestsHeader">
            <div id="pendingRequestsHeaderText">
                <?php _e('Pending Telegram Requests', 'orderAlertifyTextDomain'); ?>
            </div>
            <div id="pendingRequestsHeaderLine"></div>
        </div>

        <div id="pendingRequestBody">

            <div class="pendingRequestRow">
                <div class="pendingRequestCol telegramPendingNameSurname"><?php _e('Not Yet', 'orderAlertifyTextDomain') ?></div>
                <div class="pendingRequestCol telegramPendingUsername"><?php _e('Not Yet', 'orderAlertifyTextDomain') ?></div>
                <div class="pendingRequestCol telegramPendingChatId"><?php _e('Not Yet', 'orderAlertifyTextDomain') ?></div>
                <div class="pendingRequestCol telegramPendingButtons"></div>
            </div>

        </div>
    </div>
</div>