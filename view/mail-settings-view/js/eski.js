window.addEventListener('load', async  () => {

    const recipentInit = () => { document.querySelectorAll('.mailRecipientsItem').forEach(element => element.addEventListener('click', () => {element.remove()})) }


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
    const recipeAddContainer = document.getElementById('recipeAddContainer');
    const recipeInputContainer = document.getElementById('recipeInputContainer');
    const mailRecipientsItems = document.getElementById('mailRecipientsItems');
    const recipeAddInput = document.getElementById('recipeAddInput');
    const recideAddPlusContainer = document.getElementById('recideAddPlusContainer');


    const oaHeader = document.getElementById('oa_header');
    const oaHeaderBasePath = document.getElementById('oa_header').innerText;

    const mailGeneralSettingsContainerButton = document.getElementById('mailGeneralSettingsContainerButton');
    const mailRuleSettingsContainerButton = document.getElementById('mailRuleSettingsContainerButton');
    const activeButtonClassName = 'settingsButton-active';

    const mailGeneralSettingsContainer = document.getElementById('mailGeneralSettingsContainer');
    const mailRuleSettingsContainer = document.getElementById('mailRuleSettingsContainer');
    const activeContainerClassName = 'ou_body_right_item-active';

    const mailTemplatePage = document.querySelectorAll('.ou_body_right_item')[document.querySelectorAll('.ou_body_right_item').length-1]
    const mailTemplateButton = document.getElementById('mailTemplateSettingsContainerButton');

    mailGeneralSettingsContainerButton.addEventListener('click', () => { handleMenuSwitch(mailGeneralSettingsContainerButton, mailGeneralSettingsContainer); });
    mailRuleSettingsContainerButton.addEventListener('click', () => { handleMenuSwitch(mailRuleSettingsContainerButton, mailRuleSettingsContainer); });

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
        const firstButton = document.querySelectorAll('.settingsButton')[0];
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
            sendNotification(mailRecipeWarningMessageText);
            return;
        }

        const newItem = '<div class="mailRecipientsItem" >'+recipeAddInput.value+'</div>';

        mailRecipientsItems.innerHTML = mailRecipientsItems.innerHTML + newItem; 

        recipentInit();

        recipeAddContainer.classList.remove(dispNoneClassName);
        recipeInputContainer.classList.add(dispNoneClassName);

        recipeAddInput.value=' ';

    });

    const ruleGenerator = new RuleGenerator({
        definedRules: orderAlertifyScript.adminRules, 
        definedStatusesInWoocommerce: orderAlertifyScript.localizeStatuses, 
        definedRulesRenderTargetElement: document.getElementById('definedMailRulesContainer'), 
        definedStatusesRenderTargetElement:document.getElementById('mailTemplatesRightContainer'),
        dropzoneRenderTargetElement: document.getElementById('newMailRuleContainer')
    });

    ruleGenerator.renderDropZones({
        saveCallback:async ({oldStatusSlug, newStatusSlug}) => {
            if (oldStatusSlug === newStatusSlug) {
                sendNotification('warning', dragAndDropChooseDifferentOptionText);
                return false;
            }

            const formData = new FormData();

            formData.append('oldStatusSlug' , oldStatusSlug );
            formData.append('newStatusSlug' , newStatusSlug );
            formData.append('_operation', 'addMailRule')
    
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

            sendNotification('error', response.message);
            return false;
        }
    });

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
            sendNotification('error', response.message);
            return false;

        },
        goRuleCallback: async ({oldStatusSlug, newStatusSlug}) => {

            recipeAddInput.value=' ';
            recipeAddContainer.classList.remove(dispNoneClassName);
            recipeInputContainer.classList.add(dispNoneClassName);

            const target = oldStatusSlug + ' > ' + newStatusSlug;

            const formData = new FormData();
            formData.append('_operation', 'getMailTemplate');
            formData.append('rule', target);

            const modalData = modalOpen(loadingText);

            const request = await fetch(orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData
            });

            const response = await request.json();

            const templateData = response.data;
            const recipients = templateData.recipients !== 'false' ? templateData.recipients.split('{|}') : null;
            mailRecipientsItems.innerHTML = '';
            if (recipients !== null) {
                recipients.forEach( recipient => {
                    if (recipient !== '') {
                        mailRecipientsItems.innerHTML = mailRecipientsItems.innerHTML + '<div class="mailRecipientsItem">'+recipient+'</div>';
                        recipentInit();
                    }
                });
            }
            
            const editor = document.getElementById('content_ifr').contentDocument.getElementById('tinymce') || document.getElementById('content_ifr').contentWindow.document.getElementById('tinymce');
            const subjectInput = document.getElementById('mailTemplateSubject');

            editor.innerHTML = templateData.mailContent.replaceAll('\\', '');
            subjectInput.value = templateData.mailSubject;

            const saveButton = document.getElementById('saveMailTemplateBtn');
            const temp_text = saveButton.innerText ;
            const saveButtonCopy = saveButton.cloneNode(false);
            saveButtonCopy.innerText = temp_text; 
            saveButton.remove();
            document.getElementById('mailTemplateRightColumnHeader').insertAdjacentElement('afterbegin', saveButtonCopy)

            document.getElementById('saveMailTemplateBtn').addEventListener('click', async () => {

                const newContent = editor.innerHTML;
                const newSubject = subjectInput.value;

                const modalData = modalOpen();

                const recipientsContainer = document.querySelectorAll('.mailRecipientsItem');
                const recipientValues = [];
                recipientsContainer.forEach( element => {
                    recipientValues.push(element.innerText);
                })
                const recipientsFinal = recipientValues.join('{|}');
                console.log('resipientsFinal : ', recipientsFinal)
                const formData = new FormData();
                formData.append('_operation', 'saveMailTemplate');
                formData.append('newContent', newContent);
                formData.append('newSubject', newSubject);
                formData.append('recipients', recipientsFinal)
                formData.append('target', target);

                const request = await fetch(orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                    method:'POST',
                    body:formData
                });

                const response = await request.json();

                modalClose(modalData)

                console.log(response)

                if(response.status === true){
                    sendNotification('success', response.message);
                }
                else{
                    sendNotification('error', response.message);
                }
            });

            await handleMenuSwitch(mailTemplateButton, mailTemplatePage, 'Edit of : '+'[ '+target+' ]');

            modalClose(modalData);

            if(response.status === true){
                sendNotification('success', response.message);
            }
            else{
                sendNotification('error', response.message);
            }
        }
    });
    

    const shortCodesGenerator = new ShortCodes({data:shordCodes, header:shortCodesGeneratorMailHeaderText, targetContainer:document.getElementById('infoBoxContainer')});
    shortCodesGenerator.render({copyText:copyText});
})