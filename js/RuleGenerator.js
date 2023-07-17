class RuleGenerator{

    /* Genel Tanımlamalar */
    activeDraggableClassName = 'draggableActive';
    activeDroppableClassName = 'droppableActive';
    droppableOkeyClassName = 'droppableOkey';
    slugAttributeKey = 'status_slug';
    dispNoneClassName = 'dispnone';
    oldStatusString = 'Old Status';
    newStatusString = 'New Status'
    /* Genel Tanımlamalar */

    /* localize */
    definedStatusesInWoocommerce; // Localize Aracılığı ile gelen tanımlı woocommerce statülerini tutar
    /* localize */

    /* statuses */
    definedStatusesRenderTargetElement; // tanımlı sürüklenebilir statülerin içine render edileceği hedef element
    definedRules;
    /* statuses */

    /* defined rule */
    definedRulesRenderTargetElement;
    /* defined rule */

    /* dropzone */
    dropzoneRenderTargetElement;
    statuesDropZones;
    /* dropzone */

    /* callback fonksiyonlar */
    deleteCallback
    goRuleCallback  
    /* callback fonksiyonlar */

    constructor({definedStatusesInWoocommerce, definedRules, definedStatusesRenderTargetElement, definedRulesRenderTargetElement, dropzoneRenderTargetElement}){
        this.definedStatusesInWoocommerce = definedStatusesInWoocommerce;
        this.definedStatusesRenderTargetElement = definedStatusesRenderTargetElement;
        this.definedRulesRenderTargetElement = definedRulesRenderTargetElement;
        this.definedRules = definedRules;
        this.dropzoneRenderTargetElement = dropzoneRenderTargetElement;
    }

    renderStasuses = () => { // Localize Aracılığı ile gelen tanımlı woocommerce statüleri ile all seçeneğini render eder
        // 250 px genişlik gerek, yükseklik ihtiyaca göre uzuyor
        const renderStasusesContainer = '<div id="woocommerceStatuesContainer"></div>'; // Statülerin Konteynırını Hazırladık
        
        this.definedStatusesRenderTargetElement.innerHTML = renderStasusesContainer; // Statülerin konteynırını hedefe renderladık

        const woocommerceStatuesContainer = document.getElementById('woocommerceStatuesContainer'); // Statülerin renderlanan konteynırını seçtik

        // woocommerceStatuesContainer.innerHTML = '<div draggable="true" class="woocommerceStatuesContainerItem" status_slug="'+('*')+'">'+('All')+'</div>';
        this.definedStatusesInWoocommerce.forEach( status => {
            const temp = woocommerceStatuesContainer.innerHTML;
            woocommerceStatuesContainer.innerHTML = temp + '<div draggable="true" class="woocommerceStatuesContainerItem" status_slug="'+status.slug+'">'+status.view+'</div>';
        })

        const allStatuses = document.querySelectorAll('.woocommerceStatuesContainerItem'); // Tüm statüleri seçtik

        allStatuses.forEach( status => {
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

    renderDefinedRules = ({deleteCallback, goRuleCallback}) => {
        // 700 genişlik, min 250 yükseklik

        this.deleteCallback = deleteCallback;
        this.goRuleCallback = goRuleCallback;

        const renderDefineRulesContainer = '<div id="definedRulesTemplates"><div id="definedRulesTemplatesHeader">Defined Rules</div><div id="definedRulesTemplatesBody"></div></div>';

        this.definedRulesRenderTargetElement.innerHTML = renderDefineRulesContainer;
        
        const definedRulesTemplatesBody = document.getElementById('definedRulesTemplatesBody'); // üst satırlardan gelecek
        
        if (this.definedRules.length === 0) {
            return;
        }

        this.definedRules.forEach( item => {
    
            const oldStatusSlug = item.split(' > ')[0];
            const newStatusSlug = item.split(' > ')[1];
            
            const oldView = this.definedStatusesInWoocommerce.find(item => item.slug===oldStatusSlug).view;
            const newView = this.definedStatusesInWoocommerce.find(item => item.slug===newStatusSlug).view;

            let render = '<div class="definedRulesRows">  <div class="definedGroup">';
            render = render + '<div class="definedGroupItem">'+oldView+'</div> <div class="definedGroupItemArrow">></div> <div class="definedGroupItem">'+newView+'</div></div>';
            render = render + ' <div id="definedGroupOptions"> <button class="ruleButton deleteRule"  newstatusslug="'+newStatusSlug+'" oldstatusslug="'+oldStatusSlug+'">'+orderAlertifyGeneralScript.deleteRuleText+'</button> <button class="ruleButton goRule"  newStatusSlug="'+newStatusSlug+'" oldStatusSlug="'+oldStatusSlug+'">'+orderAlertifyGeneralScript.goRuleText+'</button></div></div>';
            definedRulesTemplatesBody.innerHTML = definedRulesTemplatesBody.innerHTML + render;
            
        });

        const deleteButtons = document.querySelectorAll('.deleteRule'); 
        const goRuleButtons = document.querySelectorAll('.goRule'); 

        deleteButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const newStatusSlug = button.getAttribute('newstatusslug');
                const oldStatusSlug = button.getAttribute('oldstatusslug');
                const response = await deleteCallback({oldStatusSlug, newStatusSlug});
                if (response === true) {
                    button.parentNode.parentNode.remove();   
                }
                this.definedRules = this.definedRules.filter( item => item !== oldStatusSlug+' > '+newStatusSlug);
            })
        })

        goRuleButtons.forEach( (button) => {
            button.addEventListener('click', async () => {
                const newStatusSlug = button.getAttribute('newstatusslug');
                const oldStatusSlug = button.getAttribute('oldstatusslug');
                await goRuleCallback({oldStatusSlug, newStatusSlug});
            })
        })
    }

    renderDropZones = ({saveCallback}) => {
        // min 400 genişlik ve 100 statik yükseklik
        let renderDropZoneContainer = '<div id="newRuleMainContainer"><div id="oldStatusContainer" class="dropZones">'+(this.oldStatusString)+'</div>';
        renderDropZoneContainer = renderDropZoneContainer + '<div id="statusesMiddleContainer"><span id="directionArrow">&gt;</span>';
        renderDropZoneContainer = renderDropZoneContainer + '<button id="saveButtonDraggable" class="dispnone">Save</button></div>';
        renderDropZoneContainer = renderDropZoneContainer + '<div id="newStatusContainer" class="dropZones">'+(this.newStatusString)+'</div></div>';                        
        
        this.dropzoneRenderTargetElement.innerHTML = renderDropZoneContainer;
        
        const droppableMainContainer = document.getElementById('newRuleMainContainer');
        const droppableMainContainerBaseBorderColor = droppableMainContainer.style.borderColor;
        const dropSaveButton = document.getElementById('saveButtonDraggable');

        this.statuesDropZones = document.querySelectorAll('.dropZones');

        let temp = this.statuesDropZones.length

        this.statuesDropZones.forEach( dropZone => {
           
            dropZone.addEventListener('drop', (e) => {
                // sürüklenen eleman alıcının üstüne bırakılınca tetikleniyor
                const status = document.getElementsByClassName(this.activeDraggableClassName)[0];
                e.target.innerHTML = status.innerHTML;
    
                e.target.classList.add(this.droppableOkeyClassName)
                e.target.setAttribute(this.slugAttributeKey, status.getAttribute(this.slugAttributeKey));
                
                temp = temp-1
                if(temp === 0){
                    // iki seçenekte işaretlenmiştir, kaydet butonunu çıkart
                    dropSaveButton.classList.remove(this.dispNoneClassName);
                    directionArrow.classList.add(this.dispNoneClassName);
                    droppableMainContainer.style.borderColor = 'green';
                    temp = this.statuesDropZones.length
                }
            });
            dropZone.addEventListener('dragover', (e) => {
                // dragover sürüklenen eleman hedefin üstündeyken anlık tetikleniyor, bunu sadece üstteki drop eventi tetiklensin diye tutuyoruz
                e.preventDefault()
            });
        })

        dropSaveButton.addEventListener('click', async () => {
            const oldStatusElement = document.getElementById('oldStatusContainer');
            const newStatusElement = document.getElementById('newStatusContainer');
    
            const newStatusSlug = newStatusElement.getAttribute('status_slug');
            const oldStatusSlug = oldStatusElement.getAttribute('status_slug');

            const response = await saveCallback({oldStatusSlug: oldStatusSlug, newStatusSlug: newStatusSlug});

            if (response === true) {

                this.definedRules.push(oldStatusSlug+' > '+newStatusSlug)

                this.renderDefinedRules({deleteCallback:this.deleteCallback, goRuleCallback:this.goRuleCallback});

                this.dropZoneClear(dropSaveButton, directionArrow, droppableMainContainer, droppableMainContainerBaseBorderColor);
            }
        })

    }

    dropZoneClear(dropSaveButton, directionArrow, droppableMainContainer, droppableMainContainerBaseBorderColor){
        dropSaveButton.classList.add(this.dispNoneClassName);
        directionArrow.classList.remove(this.dispNoneClassName);
        droppableMainContainer.style.borderColor = droppableMainContainerBaseBorderColor;
        document.querySelectorAll('.'+this.droppableOkeyClassName).forEach( el => { el.classList.remove(this.droppableOkeyClassName);});
        document.getElementById('oldStatusContainer').innerHTML = this.oldStatusString;
        document.getElementById('newStatusContainer').innerHTML = this.newStatusString;
    }
}