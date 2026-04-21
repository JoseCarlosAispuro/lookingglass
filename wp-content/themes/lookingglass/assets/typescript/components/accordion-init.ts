import Accordion from '@/typescript/classes/Accordion.ts'

const accordionInit = () => {
    const accordions = document.querySelectorAll('[data-accordion]')
    accordions.forEach((accordion, index) => {
        const AccordionInstance = new Accordion(
            accordion as HTMLElement,
            index === 0
        )
        AccordionInstance.init()
    })
}

export default accordionInit
