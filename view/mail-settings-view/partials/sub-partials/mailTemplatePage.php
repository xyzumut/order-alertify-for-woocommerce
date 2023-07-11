
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

        <div id="infoBoxContainer">
            
        </div>
    </div>

</div>