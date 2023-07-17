
<div id="smsMessageSettingsLayout">

    <div id="smsMessageLeftBar">

       <div id="leftBarContent">
        <div id="smsMessageLeftBarHeader">
                <div id="smsMessageLeftBarHeaderText">
                    <?php _e('Your Sms Message', '@@@');?>
                </div>

                <button id="smsMessageSaveButton">
                    <?php _e('Save', '@@@');?>
                </button>
            </div>

            <textarea id="smsMessageTextArea" cols="30" rows="10"></textarea>
       </div>

    </div>

    <div id="smsMessageRightBar">
        <div id="smsRecipientsContainer">

            <div class="smsRecipientsItemDefault smsRecipientsContainerHeader"><?php _e('Recipients', 'orderAlertifyTextDomain'); ?></div>

            <div class="smsRecipientsItemDefault"><?php _e('Customer\'s Phone', 'orderAlertifyTextDomain'); ?></div>

            <div id="smsRecipientsItems">

            <!-- İçerik JavaScriptten Gelir -->

            </div>

            <div id="recipeAddContainer">
                <span class="recipeAddPlus">+</span>
            </div>

            <div id="recipeInputContainer" class='dispnone'>
                <input type="text" id="recipeAddInput">
                <div id="recideAddPlusContainer">
                    <span class="recipeAddPlus">+</span>
                </div>
            </div>
        </div>

        <div id="infoBoxContainer"></div>
    </div>



</div>