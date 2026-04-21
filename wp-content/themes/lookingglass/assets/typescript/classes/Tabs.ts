class Tab {
    public tabs: HTMLElement[]
    private tabsElement: HTMLElement
    private resizeObserver?: ResizeObserver

    constructor(tabsElement: HTMLElement) {
        this.tabsElement = tabsElement
        this.tabs = Array.from(
            this.tabsElement.querySelectorAll<HTMLElement>('[data-tab]')
        )
    }

    init() {
        this.tabs.forEach(tab => {
            const button = tab.querySelector<HTMLButtonElement>('button')
            if (button) {
                button.addEventListener('click', () => {
                    this.toggleTab(tab)
                })
            }
        })

        this.resizeObserver = new ResizeObserver(() => {
            this.updateOpenTabWidth()
        })

        this.resizeObserver.observe(this.tabsElement)

        this.updateOpenTabWidth()
    }

    private toggleTab(activeTab: HTMLElement) {
        this.tabs.forEach(tab => {
            const innerEl = tab.querySelector('[data-tab-inner]')

            if (tab === activeTab) {
                tab.classList.add('open')

                setTimeout(() => {
                    innerEl?.classList.add('opacity-100')
                    innerEl?.classList.remove('opacity-0')
                }, 300)
            } else {
                tab.classList.remove('open')
                innerEl?.classList.remove('opacity-100')
                innerEl?.classList.add('opacity-0')
            }
        })
    }

    private updateOpenTabWidth() {
        const openTab =
            this.tabsElement.querySelector<HTMLElement>('[data-tab].open')

        if (!openTab) return

        const openTabBaseWidth = openTab.offsetWidth
        const openWidth = openTabBaseWidth - 32 // tab padding

        this.tabsElement.style.setProperty('--open-width', `${openWidth}px`)
    }
}

export default Tab
