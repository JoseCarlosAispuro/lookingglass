export default class Ripples {
    private $el: JQuery
    private observer?: ResizeObserver
    private isMobile: boolean
    private dropsActive: boolean

    constructor(element: HTMLElement) {
        this.$el = jQuery(element)
        this.isMobile = window.matchMedia('(max-width: 1023px)').matches
        this.dropsActive = this.isMobile

        if (!this.$el.length) return

        this.init()

        if (this.isMobile) {
            this.initMobileDrops()
            this.observeResize(element)
        }
    }

    private init(): void {
        this.$el.ripples({
            resolution: 512,
            dropRadius: this.isMobile ? 5 : 20,
            perturbance: this.isMobile ? 0.01 : 0.04,
        })
    }

    private initMobileDrops(): void {
        setInterval(() => {
            if (!this.dropsActive) return

            const width = this.$el.outerWidth() || 0
            const height = this.$el.outerHeight() || 0
            const strength = 0.04 + Math.random() * 0.04

            this.$el.ripples(
                'drop',
                Math.random() * width,
                Math.random() * height,
                10,
                strength
            )
        }, 4000)
    }

    private observeResize(element: HTMLElement): void {
        this.observer = new ResizeObserver(() => {
            this.refresh()
        })

        this.observer.observe(element)
    }

    private refresh(): void {
        this.isMobile = window.matchMedia('(max-width: 1023px)').matches
        this.dropsActive = this.isMobile
        this.$el.ripples('updateSize')
    }

    public destroy(): void {
        this.$el.ripples('destroy')
    }
}
