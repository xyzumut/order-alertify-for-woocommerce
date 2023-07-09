window.addEventListener('load', () => {

    /* Editor Tanımlama */

    const editor = document.querySelector("#content_ifr").contentWindow.document.getElementById('tinymce');

    /* Editor Tanımlama */

    /* Modal Kodları */

    const modalOpen = () => {
        const modal = document.getElementById('orderNotificationLoadingModal');
        const modalActiveClass = 'orderNotificationLoadingModal-active';
        modal.classList.add(modalActiveClass);
        return {'modal': modal, 'modalActiveClass': modalActiveClass}
    }
    const modalClose = ({'modal': modal, 'modalActiveClass': modalActiveClass}) => {
        modal.classList.remove(modalActiveClass);
    }

    /* Modal Kodları */

    /* Ana Yapı için tanımlamalar */

    const generalSettingsButton = document.getElementById('_generalSettings');
    const generalSettingsContainer = document.getElementById('woocommerceOrderNotification-generalSettingContainer');

    const emailButton = document.getElementById('_emailButton');
    const emailContainer = document.getElementById('woocommerceOrderNotification-mailContainer');

    const telegramButton = document.getElementById('_telegramButton'); 
    const telegramContainer = document.getElementById('woocommerceOrderNotification-telegramContainer');

    const smsButton = document.getElementById('_smsButton'); 
    const smsContainer = document.getElementById('woocommerceOrderNotification-smsContainer');

    const activeMenuClass = 'woocommerceOrderNotification-headerItemActive';
    const activeContainerClass = 'woocommerceOrderNotification-activeContainer'; 

    /* Ana Yapı için tanımlamalar */


    /* Mail Kısmı İçin Tanımlamalar */

    const mailSettingsButton = document.getElementById('woocommerceOrderNotification-mailContainer-LeftBar-mailSettingButton');
    const mailSettingsContainer = document.getElementById('woocommerceOrderNotification-mailContainer-RightBarMailSettingsContainer');

    const outlookButton = document.getElementById('woocommerceOrderNotification-mailContainer-LeftBar-outlookButton');
    const outlookContainer = document.getElementById('woocommerceOrderNotification-mailContainer-RightBarOutlookContainer');

    const yandexButton = document.getElementById('woocommerceOrderNotification-mailContainer-LeftBar-yandexButton');
    const yandexContainer = document.getElementById('woocommerceOrderNotification-mailContainer-RightBarYandexConteiner');

    const brevoButton = document.getElementById('woocommerceOrderNotification-mailContainer-LeftBar-BrevoButton');
    const brevoContainer = document.getElementById('woocommerceOrderNotification-mailContainer-RightBarBrevoContainer');

    const activeMailMenuClass = 'woocommerceOrderNotification-mailContainer-LeftBarItemActive';
    const activeMailContainerClass = 'woocommerceOrderNotification-mailContainer-RightBarACtiveContainer';

    /* Mail Kısmı İçin Tanımlamalar */

    /* Menü Geçişi Kodları */
    const handleSwitch = (element, container, type) => {
        
        if (type === 'mainMenu') {
            document.getElementsByClassName(activeMenuClass)[0].classList.remove(activeMenuClass)
            document.getElementsByClassName(activeContainerClass)[0].classList.remove(activeContainerClass)
            element.classList.add(activeMenuClass)
            container.classList.add(activeContainerClass)
        }
        else if (type === 'mailMainSettings'){
            document.getElementsByClassName(activeMailMenuClass)[0].classList.remove(activeMailMenuClass)
            document.getElementsByClassName(activeMailContainerClass)[0].classList.remove(activeMailContainerClass)
            element.classList.add(activeMailMenuClass)
            container.classList.add(activeMailContainerClass)
        }
    }

    const swicthMenuAndContainer = (selectedMenu) => {
        switch (selectedMenu) {
            case 'generalSettingsButton':
                handleSwitch(generalSettingsButton, generalSettingsContainer, 'mainMenu');
                break;
            case 'emailButton':
                handleSwitch(emailButton, emailContainer, 'mainMenu');
                break;
            case 'telegramButton':
                handleSwitch(telegramButton, telegramContainer, 'mainMenu');
                break;
            case 'smsButton':
                handleSwitch(smsButton, smsContainer, 'mainMenu');
                break;
            case 'mailSettingsButton':
                handleSwitch(mailSettingsButton, mailSettingsContainer, 'mailMainSettings');
                break;
            case 'outlookButton':
                handleSwitch(outlookButton, outlookContainer, 'mailMainSettings');
                break;
            case 'yandexButton':
                handleSwitch(yandexButton, yandexContainer, 'mailMainSettings');
                break;
            case 'brevoButton':
                handleSwitch(brevoButton, brevoContainer, 'mailMainSettings');
                break;
            default:
                break;
        }
    }

    generalSettingsButton.addEventListener('click', () => {swicthMenuAndContainer('generalSettingsButton')})
    emailButton.addEventListener('click', () => {swicthMenuAndContainer('emailButton')});
    telegramButton.addEventListener('click', () => {swicthMenuAndContainer('telegramButton')});
    smsButton.addEventListener('click', () => {swicthMenuAndContainer('smsButton')});
    mailSettingsButton.addEventListener('click', () => {swicthMenuAndContainer('mailSettingsButton')});
    outlookButton.addEventListener('click', () => {swicthMenuAndContainer('outlookButton')});
    yandexButton.addEventListener('click', () => {swicthMenuAndContainer('yandexButton')});
    brevoButton.addEventListener('click', () => {swicthMenuAndContainer('brevoButton')});
    /* Menü Geçişi Kodları */

    /* Mail Settings Kodları */
    const localizeOptions = orderNotificationScript.options.mail_options;

    const isOutlookAvailableRadio = document.getElementById('isOutlookAvailable');
    const isYandexAvailableRadio = document.getElementById('isYandexAvailable');
    const isBrevoAvailableRadio = document.getElementById('isBrevoAvailable');
    const noMailRadio = document.getElementById('noMail');


    if (localizeOptions.outlook!==true) {
        isOutlookAvailableRadio.disabled = true;
    }
    if (localizeOptions.yandex!==true) {
        isYandexAvailableRadio.disabled = true;
    }
    if (localizeOptions.brevo!==true) {
        isBrevoAvailableRadio.disabled = true;
    }


    switch (orderNotificationScript.options.mail_options.activeMailOption) {
        case 'useOutlook':
            isOutlookAvailableRadio.checked = true;
            break;
        case 'useYandex':
            isYandexAvailableRadio.checked = true;
            break;
        case 'useBrevo':
            isBrevoAvailableRadio.checked = true;
            break;
        default:
            noMailRadio.checked = true;
            break;
    }



    const handleChangeRadio = async (e) => {
        if (e.currentTarget.checked) {
            const formData = new FormData();
            formData.append('useMailOption', e.currentTarget.value);
            formData.append('operation_', 'useMailSettings_');
            const modalData = modalOpen();
            const request = await fetch(orderNotificationScript.admin_url+'admin-ajax.php?action=mailSettingsListener',{
                method  :  'POST',
                body    :  formData
            })
            const response = await request.json();
            modalClose(modalData);
        }
        else{
            alert('Selam')
        }
    }

    isOutlookAvailableRadio.addEventListener('change', (e) => {handleChangeRadio(e, isOutlookAvailableRadio)});
    isYandexAvailableRadio.addEventListener('change', (e) => {handleChangeRadio(e, isYandexAvailableRadio)});
    isBrevoAvailableRadio.addEventListener('change', (e) => {handleChangeRadio(e, isBrevoAvailableRadio)});
    noMailRadio.addEventListener('change', (e) => {handleChangeRadio(e, noMailRadio)});

    /* Mail Settings Kodları */

    /* Form Submit Kodları */
    const yandexForm = document.getElementById('woocommerceOrderNotification-yandexForm');
    const brevoForm = document.getElementById('woocommerceOrderNotification-BrevoForm');
    const outlookForm = document.getElementById('woocommerceOrderNotification-outlookForm');

    yandexForm.addEventListener('submit', (e) => {handleSubmit(e);});
    brevoForm.addEventListener('submit', (e) => {handleSubmit(e)});
    outlookForm.addEventListener('submit', (e) => {handleSubmit(e)});

    const checkMailValue = (value) => {
        if (value === false){
            return false;
        }
            
        value = value.split(' ').join('')

        if (value === ''){
            return false;
        }

        return true;
    }

    const requestForChangeNoMail = () => {
        const formData = new FormData();
        formData.append('useMailOption', 'dontUseMail');
        formData.append('operation_', 'useMailSettings_');
        fetch(orderNotificationScript.admin_url+'admin-ajax.php?action=mailSettingsListener',{
            method  :  'POST',
            body    :  formData
        })
    }

    const handleSubmit = async (e) => {

        e.preventDefault();

        if (orderNotificationScript.admin_url === null && orderNotificationScript.admin_url === undefined) {
            return;
        }

        const formData = new FormData(e.target);

        const formProps = Object.fromEntries(formData);

        switch (formProps.operation_) {
            case 'outlookFormSubmit_':
                orderNotificationScript.options.mail_options.outlook = checkMailValue(formProps.woocommerceOrderNotificationOutlookAddress) && checkMailValue(formProps.woocommerceOrderNotificationOutlookPassword);
                isOutlookAvailableRadio.disabled = !orderNotificationScript.options.mail_options.outlook;
                if (isOutlookAvailableRadio.disabled === true && isOutlookAvailableRadio.checked === true) {
                    isOutlookAvailableRadio.checked = false;
                    noMailRadio.checked = true;
                    requestForChangeNoMail();
                }
                break;
            case 'yandexFormSubmit_':
                orderNotificationScript.options.mail_options.yandex = checkMailValue(formProps.woocommerceOrderNotificationYandexAddress) && checkMailValue(formProps.woocommerceOrderNotificationYandexPassword);
                isYandexAvailableRadio.disabled = !orderNotificationScript.options.mail_options.yandex;
                if (isYandexAvailableRadio.disabled === true && isYandexAvailableRadio.checked === true) {
                    isYandexAvailableRadio.checked = false;
                    noMailRadio.checked = true;
                    requestForChangeNoMail();
                }
                break;
            case 'brevoFormSubmit_':
                orderNotificationScript.options.mail_options.brevo = checkMailValue(formProps.brevoToken);
                isBrevoAvailableRadio.disabled = !orderNotificationScript.options.mail_options.brevo;
                if (isBrevoAvailableRadio.disabled === true && isBrevoAvailableRadio.checked === true) {
                    isBrevoAvailableRadio.checked = false;
                    noMailRadio.checked = true;
                    requestForChangeNoMail();
                }
                break;
            default:
                requestForChangeNoMail();
                break;
        }

        const modalData = modalOpen();
        
        const $request = await fetch(orderNotificationScript.admin_url+'admin-ajax.php?action=mailSettingsListener',{
            method  :  'POST',
            body    :  formData
        })

        const response = await $request.json();

        modalClose(modalData);
    }
    /* Form Submit Kodları */

    /* Mail Settings Kodları */

    const statusMailTemplate = ({slug}) => {
        
        const statuesContainer = document.getElementById('woocommerceOrderNotification-MailTemplates-status-container');

        subject = orderNotificationScript.mailTemplates.filter(item => item.slug === slug)[0].mailHeader;
        editor.innerHTML = orderNotificationScript.mailTemplates.filter(item => item.slug === slug)[0].mailContent.replaceAll('\\', '');


        let render = '<div class="woocommerceOrderNotification-MailTemplates-status-containerFormItem"><div class="woocommerceOrderNotification-MailTemplates-status-containerFormItemHeader"><p class="woocommerceOrderNotification-MailTemplates-basicText">Mail Subject</p><button type="submit" id="mailTemplateButtonSubmit" slug="'+slug+'" class="formSubmitButton_ submitButtonForSetting">Save</button></div></div>';
        render = render + '<input type="text" name="'+ slug +'-mailHeader" value="'+ subject +'" class="woocommerceOrderNotification-MailTemplates-status-mailHeader">';// inputun name değeri slug-mailHeader oldu, mesela pending için pending-mailHeader 
        render = render + '</div><div class="woocommerceOrderNotification-MailTemplates-status-containerFormItem">';
        // render = render + '<p class="woocommerceOrderNotification-MailTemplates-basicText">Mail</p><textarea name="'+ slug +'-mailContent" class="woocommerceOrderNotification-MailTemplates-status-mailContent">'; // yukarıdaki ile aynı işlem
        // render = render + content +'</textarea></div>'
        
        statuesContainer.innerHTML = render;

        const mailTemplateSaveButton = document.getElementById('mailTemplateButtonSubmit');

        mailTemplateSaveButton.addEventListener('click', async () => {

            const mailContent = editor.innerHTML;
            const mailSubjectInputElement = document.querySelector('.woocommerceOrderNotification-MailTemplates-status-mailHeader')
            const slug = mailTemplateSaveButton.getAttribute('slug')


            const formData = new FormData();
            formData.append('mailTemplateContent', mailContent);
            formData.append('mailTemplateSubject', mailSubjectInputElement.value);
            formData.append('mailTemplateSlug', slug);
            formData.append('operation_', 'saveMailTemplate');

            const modalData = modalOpen();

            await fetch(orderNotificationScript.admin_url+'admin-ajax.php?action=mailSettingsListener',{
                method  : 'POST',
                body    :  formData
            })

            orderNotificationScript.mailTemplates = orderNotificationScript.mailTemplates.filter( item => {
                if (item.slug !== slug) 
                    return item;
                item.mailContent = mailContent;
                item.Header = mailSubjectInputElement.value;
                return item;
            } ) 

            modalClose(modalData)
        });

    }


    const statusSlug = Object.keys(orderNotificationScript.order_statuses)
    const values = Object.values(orderNotificationScript.order_statuses)
    let orderDatas_ = [];
    for (let index = 0; index < statusSlug.length; index++) {
        orderDatas_[index] = ({
            statusSlug:statusSlug[index].replace('wc-', ''),
            value: values[index]
        });
    }
    console.log('data : ', orderDatas_)

    statusMailTemplate({slug:orderDatas_[0].statusSlug, value:orderDatas_[0].value}); // başlangıç render'ı

    const mailTemplatesRightColumn = document.getElementById('woocommerceOrderNotification-MailTemplates-rightColumn');
    let temp = true;
    orderDatas_.forEach( item => {
        mailTemplatesRightColumn.innerHTML = mailTemplatesRightColumn.innerHTML + '<div id="mailTemplatesStatus-'+(item.statusSlug)+'" class="woocommerceOrderNotification-MailTemplates-status '+ (temp ? 'woocommerceOrderNotification-MailTemplates-status-active' : '') +'">'+ item.value +'</div>'
        temp = false;
        // id bilgileri 'mailTemplatesStatus-' ön eki + 'slug'  yani mailTemplatesStatus-processing şeklinde olur
    })

    const renderedStatuButtons = orderDatas_.map( item => {
        const id = 'mailTemplatesStatus-' + item.statusSlug;
        return document.getElementById(id);
    })

    renderedStatuButtons.forEach( element => {
        const buttonsActiveClass = 'woocommerceOrderNotification-MailTemplates-status-active';
        element.addEventListener('click', (ev) => {
            renderedStatuButtons.forEach(element => {
                if (element.classList.contains(buttonsActiveClass)) {
                    element.classList.remove(buttonsActiveClass);
                }
            })
            element.classList.add(buttonsActiveClass)
            
            const elementContext = orderDatas_.filter( item => element.id.replace('mailTemplatesStatus-', '') === item.statusSlug )[0];// o anki aktif elemanın id'sinin içindeki slug ile filtreleme yapıp elemanı çektik

            const slug = elementContext.statusSlug;
            const value = elementContext.value;

            statusMailTemplate({slug:slug, value:value, content:'', subject:value});
        })
    })
    /*
        [
            0 : {statusSlug: 'pending',         value: 'Beklemede'}
            1 : {statusSlug: 'processing',      value: 'Hazırlanıyor'}
            2 : {statusSlug: 'on-hold',         value: 'Ödeme bekleniyor'}
            3 : {statusSlug: 'completed',       value: 'Tamamlandı'}
            4 : {statusSlug: 'cancelled',       value: 'İptal edildi'}
            5 : {statusSlug: 'refunded',        value: 'İade edildi'}
            6 : {statusSlug: 'failed',          value: 'Başarısız'}
            7 : {statusSlug: 'checkout-draft',  value: 'Taslak'}
        ]
    */

    /* Mail Settings Kodları */

    /* */

    /* */
});