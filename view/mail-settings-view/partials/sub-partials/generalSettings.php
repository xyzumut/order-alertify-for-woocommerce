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
                <label for="mailAddressInput" class="mailLabel">Mail</label><span style="color:white;">:</span>
                <input type="mail" class="mailSmtpInputs" id="mailAddressInput">
            </div>

            <div class="inputContainer">
                <label for="mailPasswordInput" class="mailLabel">Password</label><span style="color:white;">:</span>
                <input type="password" class="mailSmtpInputs" id="mailPasswordInput">
            </div>
            
            <div class="inputContainer">
                <label for="smtpHostInput" class="mailLabel"><?php _e('SMTP Server', '@@@');?></label><span style="color:white;">:</span>
                <input type="text" class="mailSmtpInputs" id="smtpHostInput">
            </div>

            <div class="inputContainer">
                <label for="smtpPortInput" class="mailLabel"><?php _e('SMTP Port', '@@@');?></label><span style="color:white;">:</span>
                <input type="number" class="mailSmtpInputs" id="smtpPortInput">
            </div>

            <div class="inputContainer">
                <span  class="mailLabel"><?php _e('Secure', '@@@');?><span style="color:white; margin-left:78px">:</span></span>
                <select name="mailHostSecure" id="mailHostSecureOptions">
                    <option value="SSL">SSL</option>
                    <option value="TLS">Tls</option>
                    <option value="STARTTLS">Starttls</option>
                    <option value="NONE">None</option>
                </select>
            </div>

            <button class="btn-orderNotify" id="saveMailAccountButton"><?php _e('Save', '@@@'); ?></button>
        </div>

    </div>
</div>