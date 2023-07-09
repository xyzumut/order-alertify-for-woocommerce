<div id="generalSettingsMainContainer">

    <div id="availableMailServicesContainer">
        <h3 id="availableMailServicesContainer-header"> <?php _e('Available Methods', '@@@') ?></h3>
        <ul>
            <li><label><input type="radio" name="mailOption_" class="availableMailRadio" id="outlookOption" value="useOutlook"><?php _e('Outlook Mail', '@@@')?></label></li>
            <li><label><input type="radio" name="mailOption_" class="availableMailRadio" id="yandexOption" value="useYandex"><?php _e('Yandex Mail', '@@@')?></label></li>
            <li><label><input type="radio" name="mailOption_" class="availableMailRadio" id="noMail" value="dontUseMail"><?php _e('None', '@@@')?></label></li>
        </ul>
        <h5 id="availableMailServicesContainer-footer"><?php _e('Choose one of the options above.', '@@@') ?></h5>
    </div>
    <div id="availableMailSettingsContainer">
        <div id="availableMailSettingsHeader"></div>

        <div id="inputsContainer">
            <div class="inputContainer">
                <label for="mailAddressInput" id="mailAddressInputLabel">Mail</label><span style="color:white;">:</span>
                <input type="mail" id="mailAddressInput">
            </div>

            <div class="inputContainer">
                <label for="mailPasswordInput" id="mailPasswordInputLabel">Password</label><span style="color:white;">:</span>
                <input type="password" id="mailPasswordInput">
            </div>
            
            <button class="btn-orderNotify" id="saveMailAccountButton"><?php _e('Save', '@@@'); ?></button>
        </div>

    </div>
</div>