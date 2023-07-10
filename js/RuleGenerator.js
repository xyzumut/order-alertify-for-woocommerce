
class RuleGenerator{

    statusesContainerInit = '<div id="woocommerceStatuesContainer"><div draggable="true" class="woocommerceStatuesContainerItem" id="statusAll" status_slug="*">All</div></div>';
    activeDraggableClassName = 'draggableActive';
    activeDroppableClassName = 'droppableActive';
    droppableOkeyClassName = 'droppableOkey';
    slugAttributeKey = 'status_slug';
    dispNoneClassName = 'dispnone';
    allStatuses;
    droppableMainContainer;
    statuesDropZones;
    dropSaveButton;
    directionArrow;
    definedRulesTemplatesBody;
    deleteRuleButtons;
    goRuleButtons;
    recipeAddInput;
    recideAddPlusContainer;
    infoBoxItemRight;

    constructor(stasuses, statusesContainerRenderTarget, dropZoneContainerTarget, definedRulesTarget, orderAlertifyScript){
        this.statuses = stasuses;
        this.statusesContainerRenderTarget = statusesContainerRenderTarget;// statusesContainerInit'i içine atacağım daha sonra statusesContainerInit'in içine diğer statusesleri atacağız
        this.dropZoneContainerTarget = dropZoneContainerTarget;
        this.orderAlertifyScript = orderAlertifyScript;
        this.ajaxUrl = this.orderAlertifyScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener';
    }

    renderDropZones = () => {
        let render = '<div id="newRuleMainContainer"><div id="oldStatusContainer" class="mailBoxDrop">Old Status</div><div id="statusesMiddleContainer">';
        render = render + '<span id="directionArrow">></span><button id="saveButtonDraggable" class="dispnone">Save</button></div>';
        render = render + '<div id="newStatusContainer" class="mailBoxDrop">New Status</div></div>';
        this.dropZoneContainerTarget.innerHTML = render;
        this.droppableMainContainer = document.getElementById('newRuleMainContainer');
        this.statuesDropZones = document.querySelectorAll('.mailBoxDrop');
        this.dropSaveButton = document.getElementById('saveButtonDraggable');
        this.directionArrow = document.getElementById('directionArrow');
        
        this.statuesDropZones.forEach( dropZone => {
       
            dropZone.addEventListener('drop', (e) => {
                // sürüklenen eleman alıcının üstüne bırakılınca tetikleniyor
                const status = document.getElementsByClassName(this.activeDraggableClassName)[0];
                e.target.innerHTML = status.innerHTML;
    
                e.target.classList.add(droppableOkeyClassName)
                e.target.setAttribute(slugAttributeKey, status.getAttribute(this.slugAttributeKey));
    
                temp = temp-1
                if(temp === 0){
                    // iki seçenekte işaretlenmiştir, kaydet butonunu çıkart
                    this.dropSaveButton.classList.remove(this.dispNoneClassName);
                    this.directionArrow.classList.add(this.dispNoneClassName);
                    this.droppableMainContainer.style.borderColor = 'green';
                    temp = this.statuesDropZones.length
                }
            });
            dropZone.addEventListener('dragover', (e) => {
                // dragover sürüklenen eleman hedefin üstündeyken anlık tetikleniyor, bunu sadece üstteki drop eventi tetiklensin diye tutuyoruz
                e.preventDefault()
            });
        } )
        // render rulesten önce çağrılmalı mutlaka
    }

    renderRules = () => {
        // Statülerin içerisinde olacağı ana konteynırı hedef htmlin içine bastık
        this.statusesContainerRenderTarget.innerHTML = this.statusesContainerInit;
        // Statülerin içerisinde olacağı ana konteynırı hedef htmlin içine bastık
        
        // backendden gelen localize scripte göre bir önceki adımda render ettiğim konteynırın içerisine statüleri bastık
        this.orderAlertifyScript.localizeStatuses.forEach( status => {
            const render = '<div draggable="true" class="woocommerceStatuesContainerItem" status_slug="'+status.slug+'">'+status.view+'</div>'
            statusesContainer.innerHTML = statusesContainer.innerHTML + render;
        }); 
        // backendden gelen localize scripte göre bir önceki adımda render ettiğim konteynırın içerisine statüleri bastık

        // Statüleri Seçtik
        this.allStatuses = document.querySelectorAll('.woocommerceStatuesContainerItem'); // Bir önceki adımda render edilenleri alıyor
        // Statüleri Seçtik

        this.allStatuses.forEach( status => {
            status.addEventListener('dragstart', (e) => {
                // Bu event sürüklenecek eleman sürüklenmeye başladığında tek seferlik çağrılıyor
                e.target.classList.add(this.activeDraggableClassName)
                this.statuesDropZones.forEach( item => {
                    item.classList.add(this.activeDroppableClassName);
                });
            });
            status.addEventListener('dragend', (e) => {
                // Bu event sürüklenecek eleman sürüklenmeye başlayıp daha sonra herhangi bir şekilde bırakılınca çağrılıyor
                e.target.classList.remove(this.activeDraggableClassName)
                this.statuesDropZones.forEach( item => {
                    item.classList.remove(this.activeDroppableClassName);
                });
            })
        });




    }

    renderDefinedRules = () => {
        let render = '<div id="definedRulesTemplates"><div id="definedRulesTemplatesHeader">Defined Rules</div>';
        render = render + '<div id="definedRulesTemplatesBody"></div></div>';

        this.definedRulesTemplatesBody = document.getElementById('definedRulesTemplatesBody');
        this.definedRulesTemplatesBody.innerHTML = '';
        if (this.orderAlertifyScript.adminRules.length === 0) {
            return;
        }

        this.orderAlertifyScript.adminRules.forEach( item => {

            this.orderAlertifyScript.localizeStatuses.push({slug:'*', view: document.getElementById('statusAll').innerText});

            const oldStatusSlug = item.split(' > ')[0];
            const newStatusSlug = item.split(' > ')[1];
            

            const oldView = this.orderAlertifyScript.localizeStatuses.find(item => item.slug===oldStatusSlug).view;
            const newView = this.orderAlertifyScript.localizeStatuses.find(item => item.slug===newStatusSlug).view;


            let render = '<div class="definedRulesRows">  <div class="definedGroup">';
            render = render + '<div class="definedGroupItem">'+oldView+'</div> <div class="definedGroupItemArrow">></div> <div class="definedGroupItem">'+newView+'</div></div>';
            render = render + ' <div id="definedGroupOptions"> <button class="ruleButton deleteRule"  newstatusslug="'+newStatusSlug+'" oldstatusslug="'+oldStatusSlug+'">Delete Rule</button> <button class="ruleButton goRuleTemplate"  newStatusSlug="'+newStatusSlug+'" oldStatusSlug="'+oldStatusSlug+'">Go Rule</button></div></div>';
            this.definedRulesTemplatesBody.innerHTML = definedRulesTemplatesBody.innerHTML + render;

        });

        this.deleteRuleButtons = document.querySelectorAll('.deleteRule');

        this.deleteRuleButtons.forEach( deleteButton => {

            deleteButton.addEventListener('click', async () => {

                const newSlug = deleteButton.getAttribute('newstatusslug');
                const oldSlug = deleteButton.getAttribute('oldstatusslug');
                const deleteRule = oldSlug + ' > ' + newSlug;

                const formData = new FormData();
                formData.append('_operation', 'deleteMailRule');
                formData.append('rule', deleteRule);

                const modalData = modalOpen();

                const request = await fetch(this.ajaxUrl,{
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

                this.orderAlertifyScript.adminRules = orderAlertifyScript.adminRules.filter( rule => rule !== deleteRule);
                
                this.renderDefinedRules();
            })
        });

        // Bunları al ne yaparsan yap
        const editor = document.getElementById('content_ifr').contentDocument.getElementById('tinymce') || document.getElementById('content_ifr').contentWindow.document.getElementById('tinymce');
        const subjectInput = document.getElementById('mailTemplateSubject');
        // Bunları al ne yaparsan yap

        this.goRuleButtons = document.querySelectorAll('.goRuleTemplate');

        this.goRuleButtons.forEach( goRulebutton  =>  {
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
    
                    this.recipientsContainer = document.querySelectorAll('.mailRecipientsItem');
                    this.recipientValues = [];
                    
                    this.recipientsContainer.forEach( element => {
                        this.recipientValues.push(element.innerText);
                    })
                    const recipientsFinal = recipientValues.join('{|}');

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
}