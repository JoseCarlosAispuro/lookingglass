import MobileAccordion from '../classes/MobileAccordion'

const init = () => {
    const accordions = document.querySelectorAll<HTMLElement>(
        '[data-mobile-accordion]'
    )

    accordions.forEach(accordion => {
        const trigger = accordion.querySelector<HTMLElement>(
            '[data-mobile-accordion-title]'
        )
        const content = accordion.querySelector<HTMLElement>(
            '[data-mobile-accordion-content]'
        )!

        if (!trigger || !content) return

        console.log(trigger, content)

        new MobileAccordion(trigger, content).init()
    })
}

export default init
