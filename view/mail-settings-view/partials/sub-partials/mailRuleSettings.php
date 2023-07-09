    <div id="mailTemplatesMainContainer">

        <div id="mailTemplatesLeftContainer">
            <div id="newMailMainContainer">
                <div id="oldStatusContainer" class="mailBoxDrop">
                    <?php _e('Old Status', '@@@'); ?>
                </div>

                <div id="statusesMiddleContainer">
                    <span id="directionArrow">></span>
                    <button id="saveButtonDraggable" class="dispnone"><?php _e('Save', '@@@'); ?></button>
                </div>

                <div id="newStatusContainer" class="mailBoxDrop">
                    <?php _e('New Status', '@@@'); ?>
                </div>
            </div>
            <div id="definedRulesTemplates">
                <div id="definedRulesTemplatesHeader"><?php _e('Defined Rules', '@@@') ?></div>
                <div id="definedRulesTemplatesBody">
                    <!-- İçeriği JavaScriptten Gelir -->
                </div>
                
            </div>
        </div>

        <div id="mailTemplatesRightContainer">
            <div id="woocommerceStatuesContainer">
                <div draggable="true" class="woocommerceStatuesContainerItem" id="statusAll" status_slug="*"><?php _e( 'All', '@@@') ?></div>
                <!-- İçeriği JavaScriptten Gelir -->
            </div>
        </div>
    </div>