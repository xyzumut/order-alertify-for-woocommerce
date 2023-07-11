
class ShortCodes{

    data;
    targetContainer;
    mainTemplate;
    header;


    constructor({data, header, targetContainer}){
        // data => {shortCode:'{short_code}', view:'Short Code'}
        this.data = data;
        this.targetContainer = targetContainer;
        this.header = header;
    }

    render = ({copyText}) => {
        // 250 px genişlikli uzayabilen bir konteynır
        this.mainTemplate = '<div id="infoBoxForMailTemplate"><div id="infoBoxForMailTemplateHeader">'+(this.header)+'</div><div id="infoBoxForMailTemplateBody"></div></div>'
        this.targetContainer.innerHTML = this.mainTemplate;
        const itemContainer = document.getElementById('infoBoxForMailTemplateBody');
        this.data.forEach( item => {
            const template = '<div class="infoBoxItem"><div class="infoBoxItemLeft">'+item.view+'</div><div class="infoBoxItemRight">: '+item.shortCode+' </div></div>';
            itemContainer.innerHTML = itemContainer.innerHTML + template ;
        });
        const infoBoxItemRight = document.querySelectorAll('.infoBoxItemRight');
        infoBoxItemRight.forEach( item => item.addEventListener('click', async () => {
            const value = item.innerText.replace(': ', '');
            await navigator.clipboard.writeText(value);
            sendNotification('info', value +' '+ copyText);
        }))
    }
}

