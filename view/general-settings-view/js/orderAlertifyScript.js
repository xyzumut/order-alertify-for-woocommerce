window.addEventListener('load', () => {

    class GeneralSettings{
        
        mailToggleInput;
        smsToggleInput;
        telegramToggleInput;
        logs;

        constructor(){
            this.telegramToggleInput = document.getElementById('telegramToggle');
            this.mailToggleInput     = document.getElementById('mailToggle');
            this.smsToggleInput      = document.getElementById('smsToggle');
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

        renderLogs = ({filter = null} = {}) => {

            // TODO Loglara filterleme eklenecek

            if (this.logs.length < 1) {
                return;
            }

            const target = document.getElementById('logRowsContainer'); 
            target.innerHTML = '';

            this.logs.data.filter(log => filter !== null ? filter === log.type : true ).forEach( log => {

                let render = '<div class="accordion-item alert '+(log.status === 'fail' ? 'alert-danger' : 'alert-success')+'"><h2 class="accordion-header">';
                render = render + '<button class="accordion-button text-white '+( log.status === 'fail' ?  ' bg-danger' : 'bg-success')+'" type="button" data-bs-toggle="collapse"'+log.id+' data-bs-target="#collapse'+log.id+'" aria-expanded="true" aria-controls="collapsecollapse'+log.id+'">';
                render = render + (log.type + ' - ' + log.message) + '</button></h2><div id="collapse'+log.id+'" class="accordion-collapse collapse " data-bs-parent="#accordionExample">';
                render = render + '<div class="accordion-body" log-id="'+log.id+'"></div></div></div>';
                target.innerHTML = target.innerHTML + render;
                
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

            this.logs = await this.getLogs()

            console.log('loglar :', this.logs)

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


