class Accordion {
    private readonly container: HTMLElement
    private readonly actionElement: Element | null
    private readonly accordionContent: Element | null
    private isOpen: boolean
    private readonly spacingBetween: number
    private actionHeight?: number
    private contentHeight?: number
    private activeBackground: boolean

    constructor(container: HTMLElement, openOnInit: boolean = false) {
        this.container = container
        this.isOpen = openOnInit ?? false
        this.spacingBetween = 16
        this.accordionContent = this.container.querySelector('[data-content]')
        this.actionElement = this.container.querySelector('[data-action-item]')
        this.actionHeight = this.actionElement?.clientHeight
        this.contentHeight = this.accordionContent?.clientHeight
        this.activeBackground = !!this.container.dataset.activeBackground
    }

    open = () => {
        this.container.style.maxHeight = `${(this.actionHeight ?? 0) + this.spacingBetween + (this.contentHeight ?? 0)}px`
        this.actionElement?.setAttribute('aria-expanded', 'true')
        if (this.activeBackground) {
            this.container.classList.add('bg-black-50', 'px-sm')
        }
    }

    close = () => {
        this.container.style.maxHeight = `${this.actionHeight}px`
        this.actionElement?.setAttribute('aria-expanded', 'false')
        if (this.activeBackground) {
            this.container.classList.remove('bg-black-50', 'px-sm')
        }
    }

    handleClickAction = () => {
        ;(this.actionElement as HTMLElement).addEventListener('click', () => {
            this.isOpen ? this.close() : this.open()
            this.isOpen = !this.isOpen
        })
    }

    handleResize = () => {
        window.addEventListener('resize', () => {
            this.actionHeight = this.actionElement?.clientHeight
            this.contentHeight = this.accordionContent?.clientHeight
            !this.isOpen ? this.close() : this.open()
        })
    }

    initialState = () => {
        if (this.isOpen) {
            this.open()
        } else {
            this.container.style.maxHeight = `${this.actionElement?.clientHeight}px`
        }
    }

    init() {
        this.initialState()
        this.handleClickAction()
        this.handleResize()
    }
}

export default Accordion
