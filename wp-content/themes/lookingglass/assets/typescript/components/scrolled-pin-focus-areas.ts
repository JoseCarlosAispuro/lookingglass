import ScrolledPinFocusAreas from '../classes/ScrolledPinFocusAreas.ts'

const Init = () => {
    const elements: NodeListOf<HTMLElement> = document.querySelectorAll(
        '[data-scrolled-pin]'
    )

    elements.forEach(el => {
        const instance = new ScrolledPinFocusAreas(el)
        instance.init()
    })
}

export default Init
