<div id="orderAlertifyGeneralLayout">

    <div id="orderAlertifyTextContainer">
        <h1 id="orderAlertifyHeader">
            What is this plugin and how does it work?
        </h1>
        <p class="orderAlertfifyParagraph">
            This plugin detects the status changes of orders created on woocommerce and generates notifications about the options you have selected.
        </p>
        <p class="orderAlertfifyParagraph">
            These notifications take place within the rules you set.
        </p>
        <p class="orderAlertfifyParagraph">
            You set these rules on the options' own pages.
        </p>
        <p class="orderAlertfifyParagraph">
            These options are e-mail, sms and telegram options.
        </p>
        <p class="orderAlertfifyParagraph">
            For Telegram notification, you must enter your bot's information on the "Telegram Settings" page and then message your bot on the "Telegram Settings" page 
            and accept yourself.
        </p>
        <p class="orderAlertfifyParagraph">
            For Mail and SMS, you must make your settings on the "Mail Settings" and "SMS Settings" page.
        </p>
        <p class="orderAlertfifyParagraph">
            After setting any option, you must set the rules and customize the content to be sent in the relevant rule.
        </p>
    </div>

    <div id="orderAlertifyOptionsContainer">

        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Mail Option', '@@@') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="mailToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the e-mail option.', '@@@') ?>
            </p>
        </div>


        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Sms Option', '@@@') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="smsToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the sms option.', '@@@') ?>
            </p>
        </div>


        <div class="orderAlertifyOptionBox">
            <h3 class="orderAlertifyOptionHeader">
                <?php _e('Telegram Option', '@@@') ?>
            </h3>

            <label class="orderAlertifySwitch">
                <input type="checkbox" id="telegramToggle">
                <span class="slider round"></span>
            </label>

            <p class="orderAlertifyOptionFooter">
                <?php _e('Click the button to enable the telegram option.', '@@@') ?>
            </p>
        </div>


    </div>
</div>

