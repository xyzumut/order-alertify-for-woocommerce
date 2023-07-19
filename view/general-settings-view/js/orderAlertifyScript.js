window.addEventListener('load', () => {
    // orderAlertifyGeneralScript.noLogText
    class GeneralSettings{
        
        mailToggleInput;
        smsToggleInput;
        telegramToggleInput;
        logs;
        logTypes;
        logContainer;
        filterSelectedInit = 'none';

        constructor(){
            this.telegramToggleInput = document.getElementById('telegramToggle');
            this.mailToggleInput     = document.getElementById('mailToggle');
            this.smsToggleInput      = document.getElementById('smsToggle');
            this.logContainer        = document.getElementById('logRowsContainer');
        }
        
        getLogs = async () => {
            const formData = new FormData();
            formData.append('_operation', 'getLogs');

            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData,
            });
            const response = await request.json();
            return response
        } 
        renderLogFilters = () => {

            document.getElementById('logsMainContainer').insertAdjacentHTML('afterbegin', '<div id="logFilterOptionsContainer"><div class="filterOption"><h4>Filter</h4></div></div>');
            this.logTypes.forEach( (logType) => {
                let render = '<div class="filterOption"><input class="logRadios" type="radio" id="filter'+logType+'" view="'+logType+'" name="filterOption" value="'+logType+'"><label for="filter'+logType+'">'+logType.toUpperCase()+'</label></div>';
                document.getElementById('logFilterOptionsContainer').insertAdjacentHTML('beforeend', render);
            });

            let selected = this.filterSelectedInit;

            document.querySelectorAll('.logRadios').forEach(radio => {
                radio.addEventListener('click', () => {
                    if (selected === radio.getAttribute('view')) {
                        selected = this.filterSelectedInit;
                        radio.checked = false;
                    }
                    else{
                        selected = radio.getAttribute('view');
                    }
                    this.renderLogs({filter:selected});
                });
            });

        }
        renderLogs = ({filter = this.filterSelectedInit} = {}) => {

            this.logContainer.innerHTML = ''

            if (this.logs.length < 1) {
                let render = '<div class="accordion-item alert alert-info"><h2 class="accordion-header">';
                render = render + '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDefault" aria-expanded="true" aria-controls="collapseDefault">';
                render = render + (orderAlertifyGeneralScript.noLogText) + '</button></h2><div id="collapseDefault" class="accordion-collapse collapse " data-bs-parent="#accordionExample">';
                render = render + '<div class="accordion-body">'+(orderAlertifyGeneralScript.noLogText)+'</div></div></div>';
                this.logContainer.insertAdjacentHTML('beforeend', render);
                return;
            }

            this.logs.filter(log => filter !== this.filterSelectedInit ? filter === log.type : true ).reverse().forEach( log => {

                let render = '<div class="accordion-item alert '+(log.status === 'fail' ? 'alert-danger' : 'alert-success')+'"><h2 class="accordion-header">';
                render = render + '<button class="accordion-button text-white '+( log.status === 'fail' ?  ' bg-danger' : 'bg-success')+'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'+log.id+'" aria-expanded="true" aria-controls="collapsecollapse'+log.id+'">';
                render = render + (log.type + ' - ' + log.message) + '</button></h2><div id="collapse'+log.id+'" class="accordion-collapse collapse " data-bs-parent="#accordionExample">';
                render = render + '<div class="accordion-body" log-id="'+log.id+'"></div></div></div>';
                this.logContainer.innerHTML = this.logContainer.innerHTML + render;
                
                document.querySelectorAll('.accordion-body').forEach( element => {
                    if (Number(element.getAttribute('log-id')) === Number(log.id)) {
                        element.innerHTML = log.content.replaceAll("\\", '');
                    }
                });
            });
        }

        start = async () => {
            const formData = new FormData();
            formData.append('_operation', 'getGeneralData');

            const modalData = modalOpen(orderAlertifyGeneralScript.loadingText);

            const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                method:'POST',
                body:formData,
            });
            const response = await request.json();

            const logResponse = await this.getLogs(); 
            console.log('logResponse : ', logResponse);
            this.logs = logResponse.data.logs;
            this.logTypes = logResponse.data.logTypes;

            modalClose(modalData)
            
            if (response.status === false) {
                sendNotification('error', response.message);
            }
            sendNotification('success', response.message);

            if (response.data.isTelegramEnable === 'enable') {
                this.telegramToggleInput.checked = true;
            }

            if (response.data.isMailEnable === 'enable') {
                this.mailToggleInput.checked = true;
            }

            if (response.data.isSmsEnable === 'enable') {
                this.smsToggleInput.checked = true;
            }

            const toggles = [this.smsToggleInput, this.mailToggleInput, this.telegramToggleInput];

            toggles.forEach( toggle => {
                toggle.addEventListener('click', async () => {
                    const formData = new FormData();
                    formData.append('_operation', 'saveOption');
                    formData.append('optionType', toggle.id);// telegramToggle, mailToggle, smsToggle
                    formData.append('value', toggle.checked === true ? 'enable' : 'disable');

                    const modalData = modalOpen();

                    const request = await fetch(orderAlertifyGeneralScript.adminUrl+'admin-ajax.php?action=orderAlertifyAjaxListener',{
                        method:'POST',
                        body:formData,
                    });
                    const response = await request.json();
                    modalClose(modalData)

                    if (response.status === false) {
                        sendNotification('error', response.message);
                    }
                    sendNotification('success', response.message);
                })
            })
            this.renderLogs();
            this.renderLogFilters();

        }
    }

    // Menü Scripti
    const oaHeader = document.getElementById('oa_header'); // duracak
    const oaBodyLeft = document.getElementById('oa_body_left');
    const oaBodyRight = document.getElementById('oa_body_right');
    const menugenerator = new MenuGenerator({oaHeader:oaHeader, oaBodyLeftElement:oaBodyLeft, oaBodyRightElement:oaBodyRight});
    menugenerator.render();
    // Menü Scripti


    const generalSettings = new GeneralSettings();
    generalSettings.start();
})


