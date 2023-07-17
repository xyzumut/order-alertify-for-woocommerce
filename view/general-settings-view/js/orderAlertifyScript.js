window.addEventListener('load', () => {

    class GeneralSettings{
        
        mailToggleInput;
        smsToggleInput;
        telegramToggleInput;
        
        constructor(){
            this.telegramToggleInput = document.getElementById('telegramToggle');
            this.mailToggleInput     = document.getElementById('mailToggle');
            this.smsToggleInput      = document.getElementById('smsToggle');
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