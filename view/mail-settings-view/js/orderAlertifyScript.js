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
    

    const droppableMainContainer = document.getElementById('newMailMainContainer');
    const dropSaveButton = document.getElementById('saveButtonDraggable');
    const statusesContainer = document.getElementById('woocommerceStatuesContainer');
    const statuesDropZones = document.querySelectorAll('.mailBoxDrop'); 
    const directionArrow = document.getElementById('directionArrow');
    const activeDraggableClassName = 'draggableActive';
    const activeDroppableClassName = 'droppableActive';
    const droppableOkeyClassName = 'droppableOkey';
    const slugAttributeKey = 'status_slug';
    const droppableMainContainerBaseborderColor = droppableMainContainer.style.borderColor;
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

    // Sürükle bırak kodları render yeri
    orderAlertifyScript.localizeStatuses.forEach( status => {
        const render = '<div draggable="true" class="woocommerceStatuesContainerItem" status_slug="'+status.slug+'">'+status.view+'</div>'
        statusesContainer.innerHTML = statusesContainer.innerHTML + render;
    }); 
    const allStatuses = document.querySelectorAll('.woocommerceStatuesContainerItem');
    allStatuses.forEach( status => {
        status.addEventListener('dragstart', (e) => {
            // Bu event sürüklenecek eleman sürüklenmeye başladığında tek seferlik çağrılıyor
            e.target.classList.add(activeDraggableClassName)
            statuesDropZones.forEach( item => {
                item.classList.add(activeDroppableClassName);
            });
        });
        status.addEventListener('dragend', (e) => {
            // Bu event sürüklenecek eleman sürüklenmeye başlayıp daha sonra herhangi bir şekilde bırakılınca çağrılıyor
            e.target.classList.remove(activeDraggableClassName)
            statuesDropZones.forEach( item => {
                item.classList.remove(activeDroppableClassName);
            });
        })
    });
    // Sürükle bırak kodları render yeri
    let temp = statuesDropZones.length
    statuesDropZones.forEach( dropZone => {
       
        dropZone.addEventListener('drop', (e) => {
            // sürüklenen eleman alıcının üstüne bırakılınca tetikleniyor
            const status = document.getElementsByClassName(activeDraggableClassName)[0];
            e.target.innerHTML = status.innerHTML;

            e.target.classList.add(droppableOkeyClassName)
            e.target.setAttribute(slugAttributeKey, status.getAttribute(slugAttributeKey));
            temp = temp-1
            if(temp === 0){
                // iki seçenekte işaretlenmiştir, kaydet butonunu çıkart
                dropSaveButton.classList.remove(dispNoneClassName);
                directionArrow.classList.add(dispNoneClassName);
                droppableMainContainer.style.borderColor = 'green';
                temp = statuesDropZones.length
            }
        });
        dropZone.addEventListener('dragover', (e) => {
            // dragover sürüklenen eleman hedefin üstündeyken anlık tetikleniyor, bunu sadece üstteki drop eventi tetiklensin diye tutuyoruz
            e.preventDefault()
        });
    } )


    const ruleRender = () => {

        const definedRulesTemplatesBody = document.getElementById('definedRulesTemplatesBody');

        definedRulesTemplatesBody.innerHTML = '';
        
        if (orderAlertifyScript.adminRules.length === 0) {
            return;
        }

        orderAlertifyScript.adminRules.forEach( item => {

            orderAlertifyScript.localizeStatuses.push({slug:'*', view: document.getElementById('statusAll').innerText});

            const oldStatusSlug = item.split(' > ')[0];
            const newStatusSlug = item.split(' > ')[1];
            

            const oldView = orderAlertifyScript.localizeStatuses.find(item => item.slug===oldStatusSlug).view;
            const newView = orderAlertifyScript.localizeStatuses.find(item => item.slug===newStatusSlug).view;


            let render = '<div class="definedRulesRows">  <div class="definedGroup">';
            render = render + '<div class="definedGroupItem">'+oldView+'</div> <div class="definedGroupItemArrow">></div> <div class="definedGroupItem">'+newView+'</div></div>';
            render = render + ' <div id="definedGroupOptions"> <button class="ruleButton deleteRule"  newstatusslug="'+newStatusSlug+'" oldstatusslug="'+oldStatusSlug+'">Delete Rule</button> <button class="ruleButton goRuleTemplate"  newStatusSlug="'+newStatusSlug+'" oldStatusSlug="'+oldStatusSlug+'">Go Rule</button></div></div>';
            definedRulesTemplatesBody.innerHTML = definedRulesTemplatesBody.innerHTML + render;

        });

        const deleteRuleButtons = document.querySelectorAll('.deleteRule');
        deleteRuleButtons.forEach( deleteButton => {

            deleteButton.addEventListener('click', async (e) => {

                const newSlug = deleteButton.getAttribute('newstatusslug');
                const oldSlug = deleteButton.getAttribute('oldstatusslug');
                const deleteRule = oldSlug + ' > ' + newSlug;

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
                }
                else{
                    sendNotification('error', response.message);
                }

                orderAlertifyScript.adminRules = orderAlertifyScript.adminRules.filter( rule => rule !== deleteRule);
                ruleRender();
            })
        });

        const editor = document.getElementById('content_ifr').contentDocument.getElementById('tinymce') || document.getElementById('content_ifr').contentWindow.document.getElementById('tinymce');
        const subjectInput = document.getElementById('mailTemplateSubject');

        
        const goRuleButtons = document.querySelectorAll('.goRuleTemplate');

        goRuleButtons.forEach( goRulebutton  =>  {
            goRulebutton.addEventListener('click', async (e) => {

                recipeAddInput.value=' ';
                recipeAddContainer.classList.remove(dispNoneClassName);
                recipeInputContainer.classList.add(dispNoneClassName);


                const newSlug = goRulebutton.getAttribute('newstatusslug');
                const oldSlug = goRulebutton.getAttribute('oldstatusslug');
                const target = oldSlug + ' > ' + newSlug;

                const formData = new FormData();
                formData.append('_operation', 'getMailTemplate');
                formData.append('rule', target);

                const modalData = modalOpen('Yükleniyor. . .');

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

            })



        })
    }

    dropSaveButton.addEventListener('click', async () => {
        const oldStatusElement = document.getElementById('oldStatusContainer');
        const newStatusElement = document.getElementById('newStatusContainer');

        if (oldStatusElement.innerHTML === newStatusElement.innerHTML) {
            sendNotification('warning', 'Önceki ve Sonraki Statü Aynı Olamaz')            
            return;
        }

        const newStatusSlug = newStatusElement.getAttribute('status_slug');
        const oldStatusSlug = oldStatusElement.getAttribute('status_slug');


        /* Araylama ve İstek İşleri Bitti */

        const formData = new FormData();

        formData.append('newStatusSlug' , newStatusSlug );
        formData.append('oldStatusSlug' , oldStatusSlug );
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
        }
        else{
            sendNotification('error', response.message);
        }

        if (response.status === true) {
            orderAlertifyScript.adminRules.push(response.data)
            ruleRender();
        }


        /* Araylama ve İstek İşleri Bitti */

        droppableMainContainer.style.borderColor = droppableMainContainerBaseborderColor
        statuesDropZones.forEach( item => {
            item.classList.remove(droppableOkeyClassName);
            item.removeAttribute(slugAttributeKey);
        });
        statuesDropZones[0].innerHTML = 'Old Status'
        statuesDropZones[1].innerHTML = 'New Status'
        
        directionArrow.classList.remove(dispNoneClassName);
        dropSaveButton.classList.add(dispNoneClassName);
    })

    ruleRender();

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


    
    






})