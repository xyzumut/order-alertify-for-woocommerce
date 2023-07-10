// window.addEventListener('load', () => {
    class RuleGenerator{

        /* statuses */
        definedStatusesInWoocommerce; // Localize Aracılığı ile gelen tanımlı woocommerce statülerini tutar
        definedStatusesRenderTargetElement; // tanımlı sürüklenebilir statülerin içine render edileceği hedef element
        definedRules;
        /* statuses */

        /* dropZone */
        definedRulesRenderTargetElement;
        /* dropZone */

        /* Genel Tanımlamalar */
        activeDraggableClassName = 'draggableActive';
        activeDroppableClassName = 'droppableActive';
        droppableOkeyClassName = 'droppableOkey';
        slugAttributeKey = 'status_slug';
        /* Genel Tanımlamalar */
    


        constructor({definedStatusesInWoocommerce, definedRules, definedStatusesRenderTargetElement, definedRulesRenderTargetElement}){
            this.definedStatusesInWoocommerce = definedStatusesInWoocommerce;
            this.definedStatusesRenderTargetElement = definedStatusesRenderTargetElement;
            this.definedRulesRenderTargetElement = definedRulesRenderTargetElement;
            this.definedRules = definedRules;
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
                    statuesDropZones.forEach( item => {
                        item.classList.add(this.activeDroppableClassName);
                    });
                });
                status.addEventListener('dragend', (e) => {
                    // Bu event sürüklenecek eleman sürüklenmeye başlayıp daha sonra herhangi bir şekilde bırakılınca çağrılıyor
                    e.target.classList.remove(this.activeDraggableClassName)
                    statuesDropZones.forEach( item => {
                        item.classList.remove(this.activeDroppableClassName);
                    });
                })
            });
            
        }

        renderDefinedRules = (deleteCallback, goRuleCallback) => {
            // 700 genişlik, min 250 yükseklik
            const renderDefineRulesContainer = '<div id="definedRulesTemplates"><div id="definedRulesTemplatesHeader">Defined Rules</div><div id="definedRulesTemplatesBody"></div></div>';

            this.definedRulesRenderTargetElement.innerHTML = renderDefineRulesContainer;
            
            const definedRulesTemplatesBody = document.getElementById('definedRulesTemplatesBody'); // üst satırlardan gelecek
            
            if (this.definedRules.length === 0) {
                return;
            }


            this.definedRules.forEach( item => {
        
                const oldStatusSlug = item.split(' > ')[0];
                const newStatusSlug = item.split(' > ')[1];
                
                console.log('this.definedStatusesInWoocommerce : ', this.definedStatusesInWoocommerce)

                const oldView = this.definedStatusesInWoocommerce.find(item => item.slug===oldStatusSlug).view;
                const newView = this.definedStatusesInWoocommerce.find(item => item.slug===newStatusSlug).view;
    
                let render = '<div class="definedRulesRows">  <div class="definedGroup">';
                render = render + '<div class="definedGroupItem">'+oldView+'</div> <div class="definedGroupItemArrow">></div> <div class="definedGroupItem">'+newView+'</div></div>';
                render = render + ' <div id="definedGroupOptions"> <button class="ruleButton deleteRule"  newstatusslug="'+newStatusSlug+'" oldstatusslug="'+oldStatusSlug+'">Delete Rule</button> <button class="ruleButton goRule"  newStatusSlug="'+newStatusSlug+'" oldStatusSlug="'+oldStatusSlug+'">Go Rule</button></div></div>';
                definedRulesTemplatesBody.innerHTML = definedRulesTemplatesBody.innerHTML + render;
    
            });

            const deleteButtons = document.querySelectorAll('.deleteRule'); 
            const goRuleButtons = document.querySelectorAll('.goRule'); 

            deleteButtons.forEach( button => {
                button.addEventListener('click', () => {deleteCallback();})
            })

            goRuleButtons.forEach( button => {
                button.addEventListener('click', () => {goRuleCallback();})
            })
        }

        renderDropZones = () => {
            // <div id="newMailMainContainer">
            //     <div id="oldStatusContainer" class="mailBoxDrop">
            //         Old Status                </div>

            //     <div id="statusesMiddleContainer">
            //         <span id="directionArrow">&gt;</span>
            //         <button id="saveButtonDraggable" class="dispnone">Save</button>
            //     </div>

            //     <div id="newStatusContainer" class="mailBoxDrop">
            //         New Status                </div>
            // </div>
        }
    }
// })