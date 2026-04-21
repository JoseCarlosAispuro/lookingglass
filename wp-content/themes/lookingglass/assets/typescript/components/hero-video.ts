const DESKTOP_QUERY = '(min-width: 768px)'

export default function heroVideo(): void {
    const desktopVideo = document.querySelector<HTMLVideoElement>(
        '[data-hero-video="desktop"]'
    )
    const mobileVideo = document.querySelector<HTMLVideoElement>(
        '[data-hero-video="mobile"]'
    )

    if (!desktopVideo || !mobileVideo) return

    const mql = window.matchMedia(DESKTOP_QUERY)

    function activate(
        active: HTMLVideoElement,
        inactive: HTMLVideoElement
    ): void {
        inactive.pause()
        inactive.removeAttribute('src')
        inactive.load()

        const src = active.dataset.src
        if (!src) return

        if (active.getAttribute('src') !== src) {
            active.src = src
            active.load()
        }
        active.play()
    }

    function handleChange(e: MediaQueryList | MediaQueryListEvent): void {
        if (!desktopVideo || !mobileVideo) return
        if (e.matches) {
            activate(desktopVideo, mobileVideo)
        } else {
            activate(mobileVideo, desktopVideo)
        }
    }

    handleChange(mql)
    mql.addEventListener('change', handleChange)
}
