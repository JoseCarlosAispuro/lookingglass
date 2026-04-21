class MobileAccordion {
    private trigger: HTMLElement
    private content: HTMLElement
    private expanded = false

    private readonly BREAKPOINT = 768

    constructor(trigger: HTMLElement, content: HTMLElement) {
        this.trigger = trigger
        this.content = content
    }

    public init(): void {
        this.trigger.setAttribute('aria-expanded', 'false')

        this.trigger.addEventListener('click', this.handleClick)
        window.addEventListener('resize', this.handleResize)

        if (this.isMobile()) {
            this.prepareClosed()
        } else {
            this.resetDesktop()
        }
    }

    private isMobile(): boolean {
        return window.innerWidth < this.BREAKPOINT
    }

    private handleClick = (): void => {
        if (!this.isMobile()) return

        this.expanded ? this.close() : this.open()
    }

    private handleResize = (): void => {
        if (!this.isMobile()) {
            this.resetDesktop()
        }
    }

    private open(): void {
        this.expanded = true
        this.trigger.setAttribute('aria-expanded', 'true')
        this.trigger.dataset.expanded = 'true'

        const height = this.content.scrollHeight
        this.content.style.maxHeight = height + 'px'

        this.content.addEventListener(
            'transitionend',
            () => {
                this.content.style.maxHeight = 'none'
            },
            { once: true }
        )
    }

    private close(): void {
        this.expanded = false
        this.trigger.setAttribute('aria-expanded', 'false')
        this.trigger.dataset.expanded = 'false'

        const height = this.content.getBoundingClientRect().height
        this.content.style.maxHeight = height + 'px'

        requestAnimationFrame(() => {
            this.content.style.maxHeight = '0'
        })
    }

    private prepareClosed(): void {
        this.content.style.maxHeight = '0'
    }

    private resetDesktop(): void {
        this.expanded = true
        this.trigger.setAttribute('aria-expanded', 'true')

        this.content.style.maxHeight = ''
    }
}

export default MobileAccordion
