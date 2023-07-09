<div id="yandexHeader">
    <span id="yandexMessage">
        You Must Create an App Password for Yandex Mail
    </span>
    <!-- <img src="<?php echo(plugin_dir_url('OrderNotificationPlugin/admin/assets/images/warning.php').'warning.php')?>" id="yandexWarningIMG"> -->
</div>
<div id="yandexBody">
    <form action="" method="post" id="woocommerceOrderNotification-yandexForm">
        <div class="yandexFormItem">
            <label for="woocommerceOrderNotificationYandexAddress" class="woocommerceOrderNotificationYandexLabel">
                <div class="woocommerceYandexText">
                    <?php _e('Yandex Adress', '@@@') ?>
                </div>:
            </label>
            <input type="email" value="<?php echo($yandexAddress); ?>" name="woocommerceOrderNotificationYandexAddress" id="woocommerceOrderNotificationYandexAddress">
        </div>
        <div class="yandexFormItem">
            <label for="woocommerceOrderNotificationYandexPassword" class="woocommerceOrderNotificationYandexLabel">
                <div class="woocommerceYandexText">
                    <?php _e('Yandex APP Password', '@@@') ?>
                </div>:
            </label>
            <input type="password" value="<?php echo($yandexPassword); ?>" name="woocommerceOrderNotificationYandexPassword" id="woocommerceOrderNotificationYandexPassword">
        </div>
        <input type="hidden" name="operation_" value="yandexFormSubmit_">
        <button type="submit" id="yandexFormSubmit" class="formSubmitButton_">Save</button>
    </form>
</div>

