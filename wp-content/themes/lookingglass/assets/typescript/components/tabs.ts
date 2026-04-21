import Tab from '../classes/Tabs';

const init = () => {
    const tabsElements: NodeListOf<Element> = document.querySelectorAll('[data-tabs-wrapper]');
    
    tabsElements.forEach((tabsElement: Element) => {
        new Tab(tabsElement as HTMLElement).init();
    });   
};

export default init