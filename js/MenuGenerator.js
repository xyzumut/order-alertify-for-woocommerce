
class MenuGenerator{

    initTemp = 0;
    oaHeader;
    oaHeaderBasePath;
    oaBodyRightElement;
    oaBodyLeftElement;
    oaBodyLeftButtons;
    oaBodyLeftButtonsClassName='settingsButton';
    activeButtonClassName = 'settingsButton-active';
    oaBodyRightContainerClassName = 'ou_body_right_item';
    activeContainerClassName = 'ou_body_right_item-active';
    privateButtons;
    privateContainers; 
    privateClassName = 'privateMenuItem';
    
    constructor({oaHeader, oaBodyLeftElement, oaBodyRightElement}){
        this.oaHeader = oaHeader;
        this.oaHeaderBasePath = this.oaHeader.innerText;
        this.oaBodyLeftElement = oaBodyLeftElement;
        this.oaBodyRightElement = oaBodyRightElement;
    }


    render = () => {
        let renderButton = '';
        let buttonIdArray = [];
        
        for (let index = 0; index < this.oaBodyRightElement.children.length; index++) {
            const container = this.oaBodyRightElement.children[index];
            buttonIdArray.push(container.id+'Button');
            renderButton = renderButton + '<div id="'+(container.id+'Button')+'" class="settingsButton '+(this.initTemp === 0 ? this.activeButtonClassName : '')+' '+(container.classList.contains('privateMenuItem') ? 'privateMenuItem' : '')+'">'+(container.getAttribute('buttonText'))+'</div>'
            if (this.initTemp === 0) {
                container.classList.add(this.activeContainerClassName);
                this.initTemp = this.initTemp + 1;
            }
        }

        this.oaBodyLeftElement.innerHTML = renderButton;

        this.privateButtons = document.querySelectorAll('.'+this.oaBodyLeftButtonsClassName+'.'+this.privateClassName);
        this.privateContainers = document.querySelectorAll('.'+this.oaBodyRightContainerClassName+'.'+this.privateClassName);

        const buttons = document.querySelectorAll('.'+this.oaBodyLeftButtonsClassName);
        
        buttons.forEach( menuButton => { menuButton.addEventListener('click', () => {
            if (menuButton.classList.contains('privateMenuItem')) {
                return;
            }
            const newActiveContainerId = menuButton.id.split('Button')[0];
            const newActiveContainer = document.getElementById(newActiveContainerId);
            this.handleMenuSwitch({newActiveButon:menuButton, newActiveContainer:newActiveContainer});
        })});
    }

    handleMenuSwitch = async ({newActiveButon, newActiveContainer, menuSlug=null}) => {
        let newPath = '';
        if (menuSlug === null) {
            newPath = newActiveButon.innerHTML;
        }    
        else{
            newPath = menuSlug;
        }
        const oldActiveButon = document.getElementsByClassName(this.activeButtonClassName)[0];
        const oldActiveContainer = document.getElementsByClassName(this.activeContainerClassName)[0];
        oldActiveButon.classList.remove(this.activeButtonClassName);
        oldActiveContainer.classList.remove(this.activeContainerClassName);
        newActiveButon.classList.add(this.activeButtonClassName);
        newActiveContainer.classList.add(this.activeContainerClassName);
        this.oaHeader.innerText = this.oaHeaderBasePath + ' > ' + newPath;
    }  
}