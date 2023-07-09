<form action="" method="post" id="woocommerceOrderNotification-BrevoForm">
    <div class="brevoFormItem">
        <label for="brevoToken" class="brevoLabel">
            <div class="brevoText">
                <?php _e('Your Brevo Token', '@@@') ?>
            </div>:
        </label>
        <input type="text" value="<?php echo($brevoToken);?>" name="brevoToken" id="brevoToken">
    </div>
    <input type="hidden"  name="operation_" value="brevoFormSubmit_">
    <button type="submit" id="brevoFormSubmit" class="formSubmitButton_">Save</button>
</form>


