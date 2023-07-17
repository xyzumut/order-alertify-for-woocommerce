window.addEventListener('load', async  () => {

    class MailSettings{
        constructor(){
            this.generalMailSettingsMailInput = document.getElementById('mailAddressInput');
            this.generalMailSettingsPasswordInput = document.getElementById('mailPasswordInput');
            this.smtpHostInput = document.getElementById('smtpHostInput');
            this.smtpPortInput = document.getElementById('smtpPortInput');
            this.mailHostSecureOptions = document.getElementById('mailHostSecureOptions');
            this.recipeAddContainer = document.getElementById('recipeAddContainer');
            this.recipeInputContainer = document.getElementById('recipeInputContainer');
            this.mailRecipientsItems = document.getElementById('mailRecipientsItems');
            this.recipeAddInput = document.getElementById('recipeAddInput');
            this.recideAddPlusContainer = document.getElementById('recideAddPlusContainer');
        }

        recipentInit = () => { document.querySelectorAll('.mailRecipientsItem').forEach(element => element.addEventListener('click', () => {element.remove()})) }

        render = () => {
    
            this.recipeAddContainer.addEventListener('click', () => {
                recipeAddContainer.classList.add(dispNoneClassName);
                recipeInputContainer.classList.remove(dispNoneClassName);
            })

            this.recideAddPlusContainer.addEventListener('click', () => {

                if (recipeAddInput.value.length < 5) {
                    sendNotification(mailRecipeWarningMessageText);
                    return;
                }
        
                const newItem = '<div class="mailRecipientsItem" >'+recipeAddInput.value+'</div>';
        
                mailRecipientsItems.innerHTML = mailRecipientsItems.innerHTML + newItem; 
        
                this.recipentInit();

                recipeAddContainer.classList.remove(dispNoneClassName);
                recipeInputContainer.classList.add(dispNoneClassName);
        
                recipeAddInput.value=' ';
        
            });
        }
    }

    // Sayfanın Kendi Kodları
    const mailSettingsPage = new MailSettings();
    mailSettingsPage.render();
    // Sayfanın Kendi Kodları



    // Menü Scripti
    const oaHeader = document.getElementById('oa_header'); // duracak
    const oaBodyLeft = document.getElementById('oa_body_left');
    const oaBodyRight = document.getElementById('oa_body_right');
    const menugenerator = new MenuGenerator({oaHeader:oaHeader, oaBodyLeftElement:oaBodyLeft, oaBodyRightElement:oaBodyRight});
    menugenerator.render();
    // Menü Scripti



    // Kural Tanımlama Scripti
    const ruleGenerator = new RuleGenerator({
        definedRules: mailSettingsScript.adminRules, 
        definedStatusesInWoocommerce: orderAlertifyGeneralScript.localizeStatuses, 
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
    
            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
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

            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
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

            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
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
                        mailSettingsPage.recipentInit();
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
                const formData = new FormData();
                formData.append('_operation', 'saveMailTemplate');
                formData.append('newContent', newContent);
                formData.append('newSubject', newSubject);
                formData.append('recipients', recipientsFinal)
                formData.append('target', target);

                const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                    method:'POST',
                    body:formData
                });

                const response = await request.json();

                modalClose(modalData)

                if(response.status === true){
                    sendNotification('success', response.message);
                }
                else{
                    sendNotification('error', response.message);
                }
            });

            menugenerator.handleMenuSwitch({newActiveButon: menugenerator.privateButtons[0], newActiveContainer: menugenerator.privateContainers[0], menuSlug:'Edit of rule'});

            modalClose(modalData);

            if(response.status === true){
                sendNotification('success', response.message);
            }
            else{
                sendNotification('error', response.message);
            }
        }
    });
    // Kural Tanımlama Scripti



    // Short Code Scripti
    const shortCodesGenerator = new ShortCodes({data:shordCodes, header:shortCodesGeneratorMailHeaderText, targetContainer:document.getElementById('infoBoxContainer')});
    shortCodesGenerator.render({copyText:copyText});
    // Short Code Scripti
})