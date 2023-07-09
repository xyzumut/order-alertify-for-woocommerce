
<div id="mailTempalesEditMainContainer">

    <div id="mailTemplateLeftColumn">
        
        <label for="mailTemplateSubject" id="mailTemplateSubjectLabel">
        <?php _e('Subject', '@@@'); ?>
        </label>
        <input type="text" id="mailTemplateSubject">

        <div id="editorMailTemplate">
        <?php the_editor(''); ?>
        </div>

    </div>  

    <div id="mailTemplateRightColumn">

        <div id="mailTemplateRightColumnHeader">
            <button class="btn-orderNotify" id="saveMailTemplateBtn"><?php _e('Save', '@@@'); ?></button>
        </div>

        <div id="mailRecipientsContainer">

            <div class="mailRecipientsItemDefault mailRecipientsContainerHeader"><?php _e('Recipients', '@@@'); ?></div>

            <div class="mailRecipientsItemDefault"><?php _e('Customer\'s mail', '@@@'); ?></div>

            <div id="mailRecipientsItems">

            <!-- İçerik JavaScriptten Gelir -->

            </div>

            <div id="recipeAddContainer">
                <span class="recipeAddPlus">+</span>
            </div>

            <div id="recipeInputContainer" class='dispnone'>
                <input type="email" id="recipeAddInput">
                <div id="recideAddPlusContainer">
                    <span class="recipeAddPlus">+</span>
                </div>
            </div>
        </div>

        <div id="infoBoxForMailTemplate">
            <div id="infoBoxForMailTemplateHeader">
                Short Codes For Mail Templates
            </div>
            <div id="infoBoxForMailTemplateBody">
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Customer Note', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {customer_note}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Order ID', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {order_id}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Customer ID', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {customer_id}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Order Key', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {order_key}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing First Name', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_first}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing Last Name', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_last}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing Address1', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_add1}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing Address2', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_add2}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing City', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_city}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing Email', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_mail}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Billing Phone', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {bil_phone}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping First Name', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_first}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping Last Name', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_last}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping Address1', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_add1}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping Address2', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_add2}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping City', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_city}</div>
                </div>
                <div class="infoBoxItem">
                    <div class="infoBoxItemLeft"><?php _e('Shipping Phone', '@@@') ?></div>
                    <div class="infoBoxItemRight">: {ship_phone}</div>
                </div>
            </div>
        </div>
    </div>

</div>