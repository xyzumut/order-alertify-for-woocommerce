<div id="availableMailServicesContainer">
    <h3 id="availableMailServicesContainer-header"> <?php _e('Available Methods', '@@@') ?></h3>
    <ul>
        <li><label><input type="radio" name="availableMail" id="isOutlookAvailable" value="useOutlook"><?php _e('Outlook Mail', '@@@')?></label></li>
        <li><label><input type="radio" name="availableMail" id="isYandexAvailable" value="useYandex"><?php _e('Yandex Mail', '@@@')?></label></li>
        <li><label><input type="radio" name="availableMail" id="isBrevoAvailable" value="useBrevo"><?php _e('Brevo', '@@@')?></label></li>
        <li><label><input type="radio" name="availableMail" id="noMail" value="dontUseMail"><?php _e('None', '@@@')?></label></li>
    </ul>
    <h5 id="availableMailServicesContainer-footer"><?php _e('If the option is disabled, enter the information of the relevant option.', '@@@') ?></h5>
</div>

<div id="woocommerceOrderNotification-MailTemplates">
    <div id="woocommerceOrderNotification-MailTemplates-leftColumn">

        <div id="woocommerceOrderNotification-MailTemplates-status-container">
            <!-- JavaScript Render -->
        </div>

        <!-- <form action="" method="post" id="woocommerceOrderNotification-MailTemplatesForm">
            <div id="woocommerceOrderNotification-MailTemplates-status-container">
            Editör burada render olmuyor
            </div>
        </form> -->

        <?php the_editor('<b>Selam</b>'); ?>

    </div>

    <div id="woocommerceOrderNotification-MailTemplates-rightColumn">
        <!-- JavaScript Render -->
    </div>
</div>
