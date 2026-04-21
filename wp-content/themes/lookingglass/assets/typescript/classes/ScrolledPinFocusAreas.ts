import { animate, scroll } from 'motion'

class ScrolledPinFocusAreas {
    private readonly container: HTMLElement
    private readonly imageArea: HTMLElement | null
    private readonly overlay: HTMLElement | null
    private readonly content: HTMLElement | null

    constructor(container: HTMLElement) {
        this.container = container
        this.imageArea = container.querySelector('[data-scrolled-pin-image]')
        this.overlay = container.querySelector('[data-scrolled-pin-overlay]')
        this.content = container.querySelector('[data-scrolled-pin-content]')
    }

    init() {
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches
        const prefersReducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches

        if (!isDesktop || prefersReducedMotion) return
        if (!this.imageArea || !this.overlay || !this.content) return

        this.setupAnimations(this.imageArea, this.overlay, this.content)
    }

    private setupAnimations(
        imageArea: HTMLElement,
        overlay: HTMLElement,
        content: HTMLElement
    ) {
        const target = this.container
        const vw = window.innerWidth
        const vh = window.innerHeight

        // Starting size: 60% viewport width, 16:9 aspect ratio
        const startW = vw * 0.6
        const startH = startW * (9 / 16)

        // Set initial dimensions
        imageArea.style.width = `${startW}px`
        imageArea.style.height = `${startH}px`

        // Lerp smoothing for width/height
        const lerpFactor = 0.1
        let targetW = startW
        let currentW = startW
        let targetH = startH
        let currentH = startH

        const lerpLoop = () => {
            currentW += (targetW - currentW) * lerpFactor
            currentH += (targetH - currentH) * lerpFactor
            imageArea.style.width = `${currentW}px`
            imageArea.style.height = `${currentH}px`
            requestAnimationFrame(lerpLoop)
        }
        requestAnimationFrame(lerpLoop)

        // Grow image from small to full viewport over 0-25%
        scroll(
            (progress: number) => {
                const t = Math.min(progress / 0.25, 1)
                targetW = startW + (vw - startW) * t
                targetH = startH + (vh - startH) * t
            },
            { target }
        )

        // Overlay fades in quickly at 30-33%
        scroll(
            animate(overlay, { opacity: [0, 0, 0.4, 0.4] }, { ease: 'linear' }),
            { target, offset: [0, 0.3, 0.33, 1] }
        )

        // Content slides up starting at 33%
        scroll(
            animate(content, { y: ['100%', '100%', '0%'] }, { ease: 'linear' }),
            { target, offset: [0, 0.33, 1] }
        )
    }
}

export default ScrolledPinFocusAreas
