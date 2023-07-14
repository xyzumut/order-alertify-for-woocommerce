window.addEventListener('load', () => {
    orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener';
    
    class TelegramSettings{

        saveTelegramTokenButton;
        telegramTokenInput

        activeTelegramUsers;//[...{nameSurname, username, chatId}]
        pendingRequestUsers;//[...{nameSurname, username, chatId}]

        activeTelegramUsersBodyRows;// aktif kullanıcı satırlarının renderlandığı konteynır
        activeTelegramUsersBodyRowsDefaultHTML;// hiç aktif kullanıcı olmadığındaki html içeriği

        pendingRequestBody;
        pendingRequestBodyDefaultHTML

        checkMethod = '/getUpdates';

        oldChatIdList = [];

        constructor(){
            this.pendingRequestBody = document.getElementById('pendingRequestBody');
            this.pendingRequestBodyDefaultHTML = this.pendingRequestBody.innerHTML;
            this.telegramTokenInput = document.getElementById('telegramTokenInput');
            this.saveTelegramTokenButton = document.getElementById('saveTelegramTokenButton');
            this.activeTelegramUsersBodyRows = document.getElementById('activeTelegramUsersBodyRows');
            this.activeTelegramUsersBodyRowsDefaultHTML = this.activeTelegramUsersBodyRows.innerHTML;
        }

        start = async () => {
            
            const formData = new FormData();
            formData.append('_operation', 'telegramMainSettingsInit');

            const modalData = modalOpen('Veriler Getiriliyor');

            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData,
            });
            const response = await request.json();
            modalClose(modalData)

            this.activeTelegramUsers = response.data.activeUsers; //[...{nameSurname, username, chatId}]
            this.telegramTokenInput.value = response.data.telegramToken

            this.saveTelegramTokenButton.addEventListener('click', async () => {
                const formData = new FormData();
                formData.append('_operation', 'saveTelegramToken');
                formData.append('newToken', this.telegramTokenInput.value);
                const modalData = modalOpen();
                const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                    method:'POST',
                    body:formData,
                });
                const response = await request.json();
                modalClose(modalData);

                if (response.status === true) {
                    sendNotification('success', response.message);
                }
                else{
                    sendNotification('error', response.message);
                }
            })

            await this.activeUsersInit();

            this.check();
        }
        
        activeUsersInit = async () => {

            if (this.activeTelegramUsers.length < 1 ) {
                return;
            }

            let renderActiveTelegramUsersRow = '';

            this.activeTelegramUsers.forEach( activeUser => {
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="activeTelegramUsersBodyRow">';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramNameSurname">'+activeUser.nameSurname+'</div>';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramUsername">'+activeUser.username+'</div>';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramChatId" >'+activeUser.chatId+'</div>';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramButtons">';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<button class="telegramRemoveUserButton" uN="'+activeUser.username+'" nS="'+activeUser.nameSurname+'" ch="'+activeUser.chatId+'">';
                renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'Remove</button></div></div>';
            });

            this.activeTelegramUsersBodyRows.innerHTML = renderActiveTelegramUsersRow;

            const removeButtons = document.querySelectorAll('.telegramRemoveUserButton');
            removeButtons.forEach( removeButton => {
                removeButton.addEventListener('click', () => {this.removeButtonAction(removeButton)})
            })
        }

        check = async () => {
            
            const telegramResponse = await this.checkTelegram();

            if (telegramResponse.ok === false) {
                return;
            }

            const allChatId = new Set();

            telegramResponse.result.forEach( result => {
                if (Object.keys(result).includes('message')) {
                    allChatId.add(result.message.chat.id);
                }
            }); 


            allChatId.forEach( async (chat_id) => {
                const formData = new FormData();
                formData.append('_operation', 'checkChatId');
                formData.append('chat_id', chat_id);
                const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                    method:'POST',
                    body:formData
                });
                const response = await request.json();
                
                if (response.status === true) {
                    // Buraya girdi ise eşleşme olmuştur, bekleme ekranına al  
                    const url = 'https://api.telegram.org/bot'+(this.telegramTokenInput.value)+'/getChatMember?chat_id='+(chat_id)+'&user_id='+(chat_id)
                    const request = await fetch(url);
                    const response = await request.json();
                    if (response.ok === true) {
                        this.renderPendingRequest({chat_id:chat_id, username:response.result.user.username, nameSurname:response.result.user.first_name + ' ' + response.result.user.last_name});
                    }
                }
            })

            setTimeout(() => {
                this.check();
            }, 10000);
        }

        checkTelegram = async () => {
            const telegramRequest = await fetch('https://api.telegram.org/bot'+this.telegramTokenInput.value+this.checkMethod);
            const telegramResponse = await telegramRequest.json();
            return telegramResponse;
        }

        renderPendingRequest = async ({chat_id, username, nameSurname}) => {

            const temp = this.oldChatIdList.find( oldChat_id => oldChat_id === chat_id )

            if (temp) {
                return;
            }
            else{
                this.oldChatIdList.push(chat_id);
                let renderPendingRequestRow = '<div class="pendingRequestRow"><div class="pendingRequestCol telegramPendingNameSurname">'+(nameSurname);
                renderPendingRequestRow = renderPendingRequestRow +'</div><div class="pendingRequestCol telegramPendingUsername">'+(username)+'</div>'
                renderPendingRequestRow = renderPendingRequestRow +'<div class="pendingRequestCol telegramPendingChatId">'+(chat_id)+'</div>'
                renderPendingRequestRow = renderPendingRequestRow +'<div class="pendingRequestCol telegramPendingButtons"><div><button class="telegramRejectButton telegramRequestButtons">';
                renderPendingRequestRow = renderPendingRequestRow +'Reject</button><button class="telegramAcceptButton telegramRequestButtons" ch="'+(chat_id)+'" nS="'+(nameSurname)+'" uS="'+(username)+'">';
                renderPendingRequestRow = renderPendingRequestRow +'Accept</button></div></div></div>';

                if (this.pendingRequestBody.innerHTML === this.pendingRequestBodyDefaultHTML) {
                    this.pendingRequestBody.innerHTML = renderPendingRequestRow;
                }
                else{
                    this.pendingRequestBody.innerHTML = this.pendingRequestBody.innerHTML + renderPendingRequestRow;
                }

                const rejectButtons = document.querySelectorAll('.telegramRejectButton');
                rejectButtons.forEach(rejectButton => {
                    rejectButton.addEventListener('click', () => {
                        rejectButton.parentElement.parentElement.parentElement.remove();
                    });
                });

                const acceptButtons = document.querySelectorAll('.telegramAcceptButton');
                acceptButtons.forEach( acceptButton => {
                    acceptButton.addEventListener('click', async () => {
                        const username = acceptButton.getAttribute('uS');
                        const nameSurname = acceptButton.getAttribute('nS');
                        const chat_id = acceptButton.getAttribute('ch');

                        const formData = new FormData();
                        formData.append('_operation', 'addTelegramUser');
                        formData.append('newTelegramUser', nameSurname+'@'+username+'@'+chat_id);
            
                        const modalData = modalOpen();
            
                        const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                            method:'POST',
                            body:formData,
                        });

                        const response = await request.json();
                        modalClose(modalData);

                        if (response.status === true) {
                            sendNotification('success', response.message);
                            acceptButton.parentElement.parentElement.parentElement.remove();
                            this.addActiveNewUser({username:username, nameSurname:nameSurname, chat_id:chat_id})
                        }
                        else{
                            sendNotification('error', response.message);
                        }
                    })
                });
            }
        }

        addActiveNewUser = ({chat_id, username, nameSurname}) => {

            let renderActiveTelegramUsersRow = '';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="activeTelegramUsersBodyRow">';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramNameSurname">'+nameSurname+'</div>';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramUsername">'+username+'</div>';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramChatId" >'+chat_id+'</div>';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<div class="telegramBodyCol telegramButtons">';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'<button class="telegramRemoveUserButton" uN="'+username+'" nS="'+nameSurname+'" ch="'+chat_id+'">';
            renderActiveTelegramUsersRow = renderActiveTelegramUsersRow +'Remove</button></div></div>';


            if (this.activeTelegramUsers.length !== 0) {
                this.activeTelegramUsersBodyRows.innerHTML = this.activeTelegramUsersBodyRows.innerHTML + renderActiveTelegramUsersRow;
            }
            else{
                this.activeTelegramUsersBodyRows.innerHTML = renderActiveTelegramUsersRow;
            }

            this.activeTelegramUsers.push({nameSurname:nameSurname, username:username, chatId:chat_id});

            const removeButtons = document.querySelectorAll('.telegramRemoveUserButton');
            removeButtons.forEach( removeButton => {
                removeButton.addEventListener('click', () => { this.removeButtonAction(removeButton);})
            })

        }

        removeButtonAction = async (removeButton) => {
            const formData = new FormData();
            formData.append('_operation', 'deleteTelegramUser');
            formData.append('user', removeButton.getAttribute('nS')+'@'+removeButton.getAttribute('uN')+'@'+removeButton.getAttribute('ch'));
            const modalData = modalOpen();
            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData,
            });
            const response = await request.json();
            modalClose(modalData);
            if (response.status === true) {
                this.activeTelegramUsers = this.activeTelegramUsers.filter( item => item.chatId !== removeButton.getAttribute('ch'));
                sendNotification('success', response.message);
                removeButton.parentNode.parentNode.remove();
                if (this.activeTelegramUsers.length < 1) {
                    this.activeTelegramUsersBodyRows.innerHTML = this.activeTelegramUsersBodyRowsDefaultHTML;
                }
            }
            else{
                sendNotification('error', response.message);
            }
        }
    }

    const telegram = new TelegramSettings();
    telegram.start();


    // Menü Scripti
    const oaHeader = document.getElementById('oa_header'); // duracak
    const oaBodyLeft = document.getElementById('oa_body_left');
    const oaBodyRight = document.getElementById('oa_body_right');
    const menugenerator = new MenuGenerator({oaHeader:oaHeader, oaBodyLeftElement:oaBodyLeft, oaBodyRightElement:oaBodyRight});
    menugenerator.render();
    // Menü Scripti

    // {definedStatusesInWoocommerce, definedRules, definedStatusesRenderTargetElement, definedRulesRenderTargetElement, dropzoneRenderTargetElement}){

    const rulegenerator = new RuleGenerator({
        definedRules: telegramSettingsScript.definedTelegramRules,
        definedStatusesInWoocommerce: orderAlertifyGeneralScript.localizeStatuses, 
        definedRulesRenderTargetElement: document.getElementById('definedtelegramRulesContainer'),
        definedStatusesRenderTargetElement: document.getElementById('telegramTemplatesRightContainer'),
        dropzoneRenderTargetElement:document.getElementById('newtelegramRuleContainer')
    })

    rulegenerator.renderDropZones({
        saveCallback: async ({oldStatusSlug, newStatusSlug}) => {

            const formData = new FormData();

            formData.append('_operation', 'addTelegramRule');
            formData.append('oldStatusSlug' , oldStatusSlug );
            formData.append('newStatusSlug' , newStatusSlug );

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
    })

    rulegenerator.renderStasuses();

    rulegenerator.renderDefinedRules({
        deleteCallback: async ({oldStatusSlug, newStatusSlug}) => {

            const formData = new FormData();

            formData.append('_operation', 'deleteTelegramRule');
            formData.append('rule' , oldStatusSlug+' > '+newStatusSlug);

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

            const formData = new FormData();

            formData.append('_operation', 'getTelegramTemplate');
            formData.append('rule' , oldStatusSlug+' > '+newStatusSlug);

            const modalData = modalOpen();
    
            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData
            });
    
            const response = await request.json();
    
            modalClose(modalData);
    
            if(response.status !== true){
                sendNotification('error', response.message);
                return;
            }
            sendNotification('success', response.message);

            /* Veri Alındıktan sonra yapılacaklar burada */


            menugenerator.handleMenuSwitch({newActiveButon:document.getElementById('mailTemplateSettingsContainerButton'), newActiveContainer:document.getElementById('mailTemplateSettingsContainer'), menuSlug:'Edit Telegram Message'});
            
            const telegramTextArea = document.getElementById('telegramMessageTextArea');

            telegramTextArea.value = response.data.telegramMessage;

            const telegramSaveButton = document.getElementById('telegramMessageSaveButton');
            const temp_text = telegramSaveButton.innerText ;
            const saveButtonCopy = telegramSaveButton.cloneNode(false);
            saveButtonCopy.innerText = temp_text; 
            telegramSaveButton.remove();
            document.getElementById('telegramMessageLeftBarHeader').insertAdjacentElement('beforeend', saveButtonCopy)

            document.getElementById('telegramMessageSaveButton').addEventListener('click',async () => {
                const formData = new FormData();
                formData.append('_operation', 'telegramMessageSave');
                formData.append('target' , oldStatusSlug+' > '+newStatusSlug);
                formData.append('newTelegramMessage', telegramTextArea.value);

                const modalData = modalOpen();
                
                const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener', {
                    method:'POST',
                    body:formData
                })

                const response = await request.json();

                modalClose(modalData);
    
                if(response.status !== true){
                    sendNotification('error', response.message);
                    return;
                }
                sendNotification('success', response.message);
            });
            



        }
    })


    const shortCodesGenerator = new ShortCodes({data:shordCodes, header:shortCodesGeneratorTelegramHeaderText, targetContainer:document.getElementById('infoBoxContainer')});
    shortCodesGenerator.render({copyText:copyText});

})