window.addEventListener('load', async  () => {

    const recipentInit = () => { document.querySelectorAll('.mailRecipientsItem').forEach(element => element.addEventListener('click', () => {element.remove()})) }

    const dispNoneClassName = 'dispnone';

    const generalMailSettingsMailInput = document.getElementById('mailAddressInput');
    const generalMailSettingsPasswordInput = document.getElementById('mailPasswordInput');

    const initGeneralMailSettings = async () => {
        const generalSettingsMainContainer = document.getElementById('generalSettingsMainContainer');
        const tempHTML = generalSettingsMainContainer.innerHTML;

        const modalData = modalOpen('Veriler Getiriliyor . . .');

        const formData = new FormData();
        formData.append('_operation', 'generalMailSettingsInit');


        const request = await fetch(orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
            method:'POST',
            body:formData
        });

        const response = await request.json();        

        // backendden gelecek
        const oldSelectedOption = response.data.selectedMailOption;
        const email = response.data.mail;
        const password = response.data.password;
        // backendden gelecek

        modalClose(modalData)

        if(response.status === true){
            sendNotification('success', response.message);
        }
        else{
            sendNotification('error', response.message);
        }

        const availableMailRadios = document.querySelectorAll('.availableMailRadio');
        let selectedOption = oldSelectedOption;
        availableMailRadios.forEach( radioBtn => { 
            radioBtn.addEventListener('click', (e) => {
                selectedOption = radioBtn.value;
            })
            if (radioBtn.value === selectedOption) {
                console.log([radioBtn.value, selectedOption, oldSelectedOption])
                radioBtn.checked = true;
                console.log(radioBtn.value)
                console.log(radioBtn)
            }
        });

        generalMailSettingsMailInput.value = email;
        generalMailSettingsPasswordInput.value = password;

        const saveButton = document.getElementById('saveMailAccountButton');

        saveButton.addEventListener('click', async () => {
            const enableMailOpiton = selectedOption;
            const orderAlertifyMail = generalMailSettingsMailInput.value;
            const orderAlertifyPassword = generalMailSettingsPasswordInput.value;
            
            const modalData = modalOpen();

            const formData = new FormData();
            formData.append('_operation', 'generalMailSettingsUpdate');
            formData.append('enableMailOption', enableMailOpiton);
            formData.append('orderAlertifyMail', orderAlertifyMail);
            formData.append('orderAlertifyPassword', orderAlertifyPassword);


            const request = await fetch(orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData
            });

            const response = await request.json();  
            
            modalClose(modalData);

            if(response.status === true){
                sendNotification('success', response.message);
            }
            else{
                sendNotification('error', response.message);
            }

        })
    }

    initGeneralMailSettings();
    const droppableMainContainer = document.getElementById('newRuleMainContainer');
    const dropSaveButton = document.getElementById('saveButtonDraggable');
    const statusesContainer = document.getElementById('woocommerceStatuesContainer');
    const statuesDropZones = document.querySelectorAll('.mailBoxDrop'); 
    const directionArrow = document.getElementById('directionArrow');
    const activeDraggableClassName = 'draggableActive';
    const activeDroppableClassName = 'droppableActive';
    const droppableOkeyClassName = 'droppableOkey';
    const slugAttributeKey = 'status_slug';
    // const droppableMainContainerBaseborderColor = droppableMainContainer.style.borderColor;
    const recipeAddContainer = document.getElementById('recipeAddContainer');
    const recipeInputContainer = document.getElementById('recipeInputContainer');
    const mailRecipientsItems = document.getElementById('mailRecipientsItems');
    const recipeAddInput = document.getElementById('recipeAddInput');
    const recideAddPlusContainer = document.getElementById('recideAddPlusContainer');
    const infoBoxItemRight = document.querySelectorAll('.infoBoxItemRight');

    infoBoxItemRight.forEach( item => item.addEventListener('click', async () => {
        const value = item.innerText.replace(': ', '');
        await navigator.clipboard.writeText(value);
        sendNotification('info', value +' '+ 'Copy to Clipboard');
    }))

    const oaHeader = document.getElementById('oa_header');
    const oaHeaderBasePath = document.getElementById('oa_header').innerText;

    const mailGeneralSettingsButton = document.getElementById('mailGeneralSettingsButton');
    const mailRulesSettingsButton = document.getElementById('mailRulesSettingsButton');
    const activeButtonClassName = 'mailSettingsButton-active';

    const mailGeneralSettingsContainer = document.getElementById('mailGeneralSettingsContainer');
    const mailRuleSettingsContainer = document.getElementById('mailRuleSettingsContainer');
    const activeContainerClassName = 'ou_body_right_item-active';

    const mailTemplatePage = document.querySelectorAll('.ou_body_right_item')[document.querySelectorAll('.ou_body_right_item').length-1]
    const mailTemplateButton = document.getElementById('mailTempateSettingsButton');

    mailGeneralSettingsButton.addEventListener('click', () => { handleMenuSwitch(mailGeneralSettingsButton, mailGeneralSettingsContainer); });
    mailRulesSettingsButton.addEventListener('click', () => { handleMenuSwitch(mailRulesSettingsButton, mailRuleSettingsContainer); });
    const handleMenuSwitch = async (newActiveButon, newActiveContainer, menuSlug=null) => {

        const newPath = newActiveButon.innerText;

        const oldActiveButon = document.getElementsByClassName(activeButtonClassName)[0];
        const oldActiveContainer = document.getElementsByClassName(activeContainerClassName)[0];

        oldActiveButon.classList.remove(activeButtonClassName);
        oldActiveContainer.classList.remove(activeContainerClassName);
        newActiveButon.classList.add(activeButtonClassName);
        newActiveContainer.classList.add(activeContainerClassName);

        oaHeader.innerText = oaHeaderBasePath + ' > ' + (menuSlug || newPath)
    }   
    const menuInit = () => {
        const firstButton = document.querySelectorAll('.mailSettingsButton')[0];
        const firstContainer = document.querySelectorAll('.ou_body_right_item')[0];
        const firstPath = firstButton.innerText;
        firstButton.classList.add(activeButtonClassName);
        firstContainer.classList.add(activeContainerClassName);
        oaHeader.innerText = oaHeaderBasePath + ' > ' + firstPath
    }
    menuInit();
    recipeAddContainer.addEventListener('click', () => {
        recipeAddContainer.classList.add(dispNoneClassName);
        recipeInputContainer.classList.remove(dispNoneClassName);
    })
    recideAddPlusContainer.addEventListener('click', () => {

        if (recipeAddInput.value.length < 5) {
            sendNotification('warning', 'Lütfen Değer Giriniz');
            return;
        }

        const newItem = '<div class="mailRecipientsItem" >'+recipeAddInput.value+'</div>';

        mailRecipientsItems.innerHTML = mailRecipientsItems.innerHTML + newItem; 

        recipentInit();

        recipeAddContainer.classList.remove(dispNoneClassName);
        recipeInputContainer.classList.add(dispNoneClassName);

        recipeAddInput.value=' ';

    });

    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
    const ruleGenerator = new RuleGenerator({
        definedRules: orderAlertifyScript.adminRules, 
        definedStatusesInWoocommerce: orderAlertifyScript.localizeStatuses, 
        definedRulesRenderTargetElement: document.getElementById('definedMailRulesContainer'), 
        definedStatusesRenderTargetElement:document.getElementById('mailTemplatesRightContainer'),
        dropzoneRenderTargetElement: document.getElementById('newMailRuleContainer')
    });

    ruleGenerator.renderDropZones({saveCallback:({oldStatusSlug, newStatusSlug})=>{
        alert('Save: '+oldStatusSlug+' - '+newStatusSlug);
        return true;
    }});

    ruleGenerator.renderStasuses();

    ruleGenerator.renderDefinedRules({
        deleteCallback: async ({oldStatusSlug, newStatusSlug}) => {

            const deleteRule = oldStatusSlug + ' > ' + newStatusSlug;

            const formData = new FormData();
            formData.append('_operation', 'deleteMailRule');
            formData.append('rule', deleteRule);

            const modalData = modalOpen();

            const request = await fetch(orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData
            });

            const response = await request.json();

            modalClose(modalData);

            if(response.status === true){
                sendNotification('success', response.message);
                return true;
            }
            else{
                sendNotification('error', response.message);
                return false;
            }

        },
        goRuleCallback: async ({oldStatusSlug, newStatusSlug}) => {
            alert('Go Rule : '+oldStatusSlug+' - '+newStatusSlug);
        }
    });
    
    // newMailRuleContainer
    /*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
})