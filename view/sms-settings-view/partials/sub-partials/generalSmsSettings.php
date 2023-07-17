<div id="generalSmsSettingsLayout">
    <div id="mainSmsContainer">
        <div class="secondarySmsContainer">
            <div class="smsText">
                <label for="smsApiBaseUrlInput" class="smsLabel"><?php _e('Api BASE Url', 'orderAlertifyTextDomain'); ?></label>
            </div>
            <input type="text" class="smsInput" id="smsApiBaseUrlInput">
        </div>
        <div class="secondarySmsContainer">
            <div class="smsText">
                <label for="smsLoginEndpoint" class="smsLabel"><?php _e('Login Endpoint', 'orderAlertifyTextDomain'); ?></label>
            </div>
            <input type="text" class="smsInput" id="smsLoginEndpoint">
        </div>
        <div class="secondarySmsContainer">
            <div class="smsText">
                <label for="smsSendMessageEndpoint" class="smsLabel"><?php _e('Send Message Endpoint', 'orderAlertifyTextDomain'); ?></label>
            </div>
            <input type="text" class="smsInput" id="smsSendMessageEndpoint">
        </div>
        <div class="secondarySmsContainer">
            <div class="smsText">
                <label for="smsLoginInput" class="smsLabel"><?php _e('Login Username', 'orderAlertifyTextDomain'); ?></label>
            </div>
            <input type="text" class="smsInput" id="smsLoginInput">
        </div>
        <div class="secondarySmsContainer">
            <div class="smsText">
                <label for="smsLoginPasswordInput" class="smsLabel"><?php _e('Login Password', 'orderAlertifyTextDomain'); ?></label>
            </div>
            <input type="password" class="smsInput" id="smsLoginPasswordInput">
        </div>
        <div class="secondarySmsContainer buttonContainer">
            <button id="saveSmsSettingsButton"><?php _e('Save', 'orderAlertifyTextDomain'); ?></button>
        </div>
    </div>
</div>