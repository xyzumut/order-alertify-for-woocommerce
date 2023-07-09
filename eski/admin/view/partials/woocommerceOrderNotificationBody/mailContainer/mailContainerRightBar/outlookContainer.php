<form action="" method="post" id="woocommerceOrderNotification-outlookForm">
    <div class="outlookFormItem">
        <label for="woocommerceOrderNotificationOutlookAddress" class="woocommerceOrderNotificationOutlookLabel">
            <div class="woocommerceOutlookText">
                <?php _e('Outlook Adress', '@@@') ?>
            </div>:
        </label>
        <input type="email" value="<?php echo($outlookAddress); ?>" name="woocommerceOrderNotificationOutlookAddress" id="woocommerceOrderNotificationOutlookAddress">
    </div>
    <div class="outlookFormItem">
        <label for="woocommerceOrderNotificationOutlookPassword" class="woocommerceOrderNotificationOutlookLabel">
            <div class="woocommerceOutlookText">
                <?php _e('Outlook Password', '@@@') ?>
            </div>:
        </label>
        <input type="password" value="<?php echo($outlookPassword); ?>" name="woocommerceOrderNotificationOutlookPassword" id="woocommerceOrderNotificationOutlookPassword">
    </div>
    <input type="hidden" name="operation_" value="outlookFormSubmit_">
    <button type="submit" id="outlookFormSubmit" class="formSubmitButton_">Save</button>
</form>