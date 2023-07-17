<div id="orderAlertifyGeneralLayout">

    <div id="orderAlertifyTextContainer">
        <h1 id="orderAlertifyHeader">
            <?php _e('What is this plugin and how does it work?', 'orderAlertifyTextDomain');?>
        </h1>
        <p class="orderAlertfifyParagraph">
            <?php _e('This plugin detects the status changes of orders created on woocommerce and generates notifications about the options you have selected.', 'orderAlertifyTextDomain');?>
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('These notifications take place within the rules you set.', 'orderAlertifyTextDomain');?>
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('You set these rules on the options\' own pages.', 'orderAlertifyTextDomain');?>
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('These options are e-mail, sms and telegram options.', 'orderAlertifyTextDomain');?>
            
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('For Telegram notification, you must enter your bot\'s information on the "Telegram Settings" page and then message your bot on the "Telegram Settings" page and accept yourself.', 'orderAlertifyTextDomain');?>
            
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('For Mail and SMS, you must make your settings on the "Mail Settings" and "SMS Settings" page.', 'orderAlertifyTextDomain');?>
        </p>
        <p class="orderAlertfifyParagraph">
            <?php _e('After setting any option, you must set the rules and customize the content to be sent in the relevant rule.', 'orderAlertifyTextDomain');?>
        </p>
    </div>

    <div id="orderAlertifyOptionsContainer">

        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Mail Option', 'orderAlertifyTextDomain') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="mailToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the e-mail option.', 'orderAlertifyTextDomain') ?>
            </p>
        </div>


        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Sms Option', 'orderAlertifyTextDomain') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="smsToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the sms option.', 'orderAlertifyTextDomain') ?>
            </p>
        </div>


        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Telegram Option', 'orderAlertifyTextDomain') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="telegramToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the telegram option.', 'orderAlertifyTextDomain') ?>
            </p>
        </div>


    </div>
</div>

