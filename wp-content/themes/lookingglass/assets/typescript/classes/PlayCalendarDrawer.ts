class PlayCalendarDrawer {
    private readonly container: HTMLElement
    private readonly wrapper: HTMLElement | null
    private readonly actionElement: Element | null
    private readonly calendar: Element | null
    private readonly closeButton: Element | null
    private readonly orangeRibbon: Element | null
    private isOpen: boolean
    private closedDrawerHeight: number
    private openedDrawerHeight: number

    constructor(container: HTMLElement) {
        this.container = container
        this.isOpen = false
        this.closedDrawerHeight = 163
        this.openedDrawerHeight = 704
        this.calendar = this.container.querySelector('[data-plays-calendar]')
        this.actionElement = this.container.querySelector(
            '[data-mobile-calendar-button]'
        )
        this.closeButton = this.container.querySelector(
            '[data-mobile-calendar-close-button]'
        )
        this.orangeRibbon = this.container.querySelector(
            '[data-mobile-calenda-orange-ribbon]'
        )
        this.wrapper = this.container.querySelector(
            '[data-mobile-calendar-wrapper]'
        )
    }

    open = () => {
        this.container.style.maxHeight = `${this.openedDrawerHeight}px`
        this.container.classList.remove('bottom-16')
        this.container.classList.add('-bottom-0')

        this.wrapper?.classList.remove('max-w-full', 'overflow-hidden')
        this.wrapper?.classList.add('-left-sm', 'overflow-auto')

        setTimeout(() => {
            this.calendar?.classList.remove('absolute', '-z-1')
            this.calendar?.classList.add('relative', 'z-1')
            this.calendar?.classList.remove('opacity-0')
        }, 300)

        this.orangeRibbon?.classList.add('hidden')

        this.closeButton?.classList.remove('hidden')
        this.closeButton?.classList.add('flex')

        this.actionElement?.parentElement?.classList.add('opacity-0')

        setTimeout(() => {
            this.actionElement?.parentElement?.classList.add('hidden')
        }, 300)

        this.actionElement?.setAttribute('aria-expanded', 'true')
    }

    close = () => {
        this.container.style.maxHeight = `${this.closedDrawerHeight}px`
        this.container.classList.remove('bottom-0')
        this.container.classList.add('bottom-16')

        this.wrapper?.classList.add('max-w-full', 'overflow-hidden')
        this.wrapper?.classList.remove('-left-sm', 'overflow-auto')

        this.calendar?.classList.add('opacity-0')

        setTimeout(() => {
            this.calendar?.classList.remove('relative', 'z-1')
            this.calendar?.classList.add('absolute', '-z-1')
        }, 100)

        this.orangeRibbon?.classList.remove('hidden')

        this.closeButton?.classList.add('hidden')
        this.closeButton?.classList.remove('flex')

        this.actionElement?.parentElement?.classList.remove('hidden')

        setTimeout(() => {
            this.actionElement?.parentElement?.classList.remove('opacity-0')
        }, 300)

        this.actionElement?.setAttribute('aria-expanded', 'false')
    }

    handleClickAction = () => {
        ;(this.actionElement as HTMLElement).addEventListener('click', () => {
            this.isOpen ? this.close() : this.open()
            this.isOpen = !this.isOpen
        })

        ;(this.closeButton as HTMLElement).addEventListener('click', () => {
            this.close()
            this.isOpen = false
        })
    }

    initialState = () => {
        this.container.style.maxHeight = `${this.closedDrawerHeight}px`
    }

    init() {
        this.container.classList.remove('opacity-0')
        this.initialState()
        this.handleClickAction()
    }
}

export default PlayCalendarDrawer
