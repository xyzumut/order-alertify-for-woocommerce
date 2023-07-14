window.addEventListener('load', () => {
    // TODO frontendine inputu eklendikten sonra alıcılar için script yazılacak
    class SmsSettings{
        
        smsApiBaseUrlInput;
        smsLoginEndpoint;
        smsSendMessageEndpoint;
        smsLoginInput;
        smsLoginPasswordInput;

        constructor({smsApiBaseUrlInput,smsLoginEndpoint,smsSendMessageEndpoint,smsLoginInput,smsLoginPasswordInput, saveSmsSettingsButton}){
            this.smsSendMessageEndpoint = smsSendMessageEndpoint;
            this.smsLoginPasswordInput  = smsLoginPasswordInput;
            this.smsApiBaseUrlInput     = smsApiBaseUrlInput;
            this.smsLoginEndpoint       = smsLoginEndpoint;
            this.smsLoginInput          = smsLoginInput;
        }

        editUrl = ({baseUrl, endPoint}) => {
            if(baseUrl.includes('http://') !== true &&  baseUrl.includes('https://') !== true){
              baseUrl = 'https://' + baseUrl;
            }
            if(endPoint[0] !== '/'){
              endPoint = '/'+endPoint;  
            }
            baseUrl = baseUrl.split('//');
            baseUrl[1] = baseUrl[1].includes('/') === true ? baseUrl[1].split('/')[0] : baseUrl[1];
            return baseUrl[0]+'//'+baseUrl[1]+endPoint;
        }

        init = async () => {
            
            const modalData = modalOpen();

            const formData = new FormData();
            formData.append('_operation', 'getSmsSettings');
            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body: formData
            });

            const response = await request.json();  

            modalClose(modalData);

            if(response.status !== true){
                sendNotification('error', response.message);
            }
            sendNotification('success', response.message);
            this.smsSendMessageEndpoint.value = response.data.smsSendMessageEndpoint;
            this.smsLoginPasswordInput.value  = response.data.smsLoginPassword;
            this.smsApiBaseUrlInput.value     = response.data.smsBaseApiUrl;
            this.smsLoginEndpoint.value       = response.data.smsLoginEndpoint;
            this.smsLoginInput.value          = response.data.smsLoginUsername;

            const saveSmsSettingsButton = document.getElementById('saveSmsSettingsButton');

            saveSmsSettingsButton.addEventListener('click', async () => {
                const modalData = modalOpen();
                
                const token = await this.loginSms({
                    username: this.smsLoginInput.value,
                    password: this.smsLoginPasswordInput.value,
                    url     : this.editUrl({baseUrl:this.smsApiBaseUrlInput.value, endPoint:this.smsLoginEndpoint.value})
                })

                const formData = new FormData();
                formData.append('_operation', 'saveSmsSettings');
                formData.append('smsJwt', token === undefined ? 'noToken' : token);// login olunamadı ise token = noToken dönüyor
                formData.append('smsLoginUsername', this.smsLoginInput.value);
                formData.append('smsLoginPassword', this.smsLoginPasswordInput.value);
                formData.append('smsBaseApiUrl', this.smsApiBaseUrlInput.value);
                formData.append('smsLoginEndpoint', this.smsLoginEndpoint.value);
                formData.append('smsSendMessageEndpoint', this.smsSendMessageEndpoint.value);

                const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                    method:'POST',
                    body:formData
                });

                const response = await request.json();  

                modalClose(modalData);

                if(response.status !== true){
                    sendNotification('error', response.message);
                }
                sendNotification('success', response.message);
            });
        }

        loginSms = async ({url, username, password}) => {
            let myReturn = 'noToken';
            try {
                const request = await fetch(url,{
                    method:'POST', 
                    headers: {
                      'Accept': 'application/json',
                      'Content-Type': 'application/json'
                    },
                    body:JSON.stringify({username: username, password: password})
                });
    
                const response = await request.json();
    
                myReturn = response.JwtToken
            } catch (error) {}
            console.log('myReturn : ', myReturn)
            return myReturn;
        }
    }

    
    const sms = new SmsSettings({
        smsSendMessageEndpoint: document.getElementById('smsSendMessageEndpoint'),
        smsLoginPasswordInput : document.getElementById('smsLoginPasswordInput'),
        saveSmsSettingsButton : document.getElementById('saveSmsSettingsButton'),
        smsApiBaseUrlInput    : document.getElementById('smsApiBaseUrlInput'),
        smsLoginEndpoint      : document.getElementById('smsLoginEndpoint'),
        smsLoginInput         : document.getElementById('smsLoginInput')
    });

    sms.init();

    // Menü Scripti
    const oaHeader = document.getElementById('oa_header'); // duracak
    const oaBodyLeft = document.getElementById('oa_body_left');
    const oaBodyRight = document.getElementById('oa_body_right');
    const menugenerator = new MenuGenerator({oaHeader:oaHeader, oaBodyLeftElement:oaBodyLeft, oaBodyRightElement:oaBodyRight});
    menugenerator.render();
    // Menü Scripti

    const rulegenerator = new RuleGenerator({
        definedRules: smsSettingsScript.definedSmsRules,
        definedStatusesInWoocommerce: orderAlertifyGeneralScript.localizeStatuses, 
        definedRulesRenderTargetElement: document.getElementById('definedsmsRulesContainer'),
        definedStatusesRenderTargetElement: document.getElementById('smsTemplatesRightContainer'),
        dropzoneRenderTargetElement:document.getElementById('newsmsRuleContainer')
    })

    rulegenerator.renderDropZones({
        saveCallback: async ({oldStatusSlug, newStatusSlug}) => {

            const formData = new FormData();
            
            formData.append('_operation', 'addSmsRule');
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
            alert('Hello')
            sendNotification('error', response.message);
            return false;
        }
    })

    rulegenerator.renderStasuses();

    rulegenerator.renderDefinedRules({
        deleteCallback: async ({oldStatusSlug, newStatusSlug}) => {

            const formData = new FormData();

            formData.append('_operation', 'deleteSmsRule');
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

            formData.append('_operation', 'getSmsTemplate');
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


            menugenerator.handleMenuSwitch({
                newActiveButon:document.getElementById('smsTemplateSettingsContainerButton'), 
                newActiveContainer:document.getElementById('smsTemplateSettingsContainer'), 
                menuSlug:'Edit Sms Message'
            });
            
            const smsTextArea = document.getElementById('smsMessageTextArea');

            smsTextArea.value = response.data.smsMessage;

            const smsSaveButton = document.getElementById('smsMessageSaveButton');
            const temp_text = smsSaveButton.innerText ;
            const saveButtonCopy = smsSaveButton.cloneNode(false);
            saveButtonCopy.innerText = temp_text; 
            smsSaveButton.remove();
            document.getElementById('smsMessageLeftBarHeader').insertAdjacentElement('beforeend', saveButtonCopy)

            document.getElementById('smsMessageSaveButton').addEventListener('click',async () => {
                const formData = new FormData();
                formData.append('_operation', 'smsMessageSave');
                formData.append('target' , oldStatusSlug+' > '+newStatusSlug);
                formData.append('newsmsMessage', smsTextArea.value);

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

    const shortCodesGenerator = new ShortCodes({data:shordCodes, header:shortCodesGeneratorSMSHeaderText, targetContainer:document.getElementById('infoBoxContainer')});
    shortCodesGenerator.render({copyText:copyText});

})