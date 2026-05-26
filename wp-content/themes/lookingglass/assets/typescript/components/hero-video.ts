const DESKTOP_QUERY = '(min-width: 768px)'
const POSTER_HOLD = 3000
const FADE_DURATION = 700

export default function heroVideo(): void {
    const desktopVideo = document.querySelector<HTMLVideoElement>(
        '[data-hero-video="desktop"]'
    )
    const mobileVideo = document.querySelector<HTMLVideoElement>(
        '[data-hero-video="mobile"]'
    )
    const desktopPoster = document.querySelector<HTMLImageElement>(
        '[data-hero-poster="desktop"]'
    )
    const mobilePoster = document.querySelector<HTMLImageElement>(
        '[data-hero-poster="mobile"]'
    )

    if (!desktopVideo || !mobileVideo || !desktopPoster || !mobilePoster) return

    const mql = window.matchMedia(DESKTOP_QUERY)
    let loopId = 0
    let holdTimer: ReturnType<typeof setTimeout> | null = null

    function wait(ms: number, id: number): Promise<boolean> {
        return new Promise(resolve => {
            holdTimer = setTimeout(() => resolve(loopId === id), ms)
        })
    }

    function fade(
        el: HTMLImageElement,
        to: number,
        id: number
    ): Promise<boolean> {
        return new Promise(resolve => {
            el.style.transition = `opacity ${FADE_DURATION}ms ease`
            el.style.opacity = String(to)
            setTimeout(() => resolve(loopId === id), FADE_DURATION)
        })
    }

    function waitForEnd(video: HTMLVideoElement, id: number): Promise<boolean> {
        return new Promise(resolve => {
            video.addEventListener('ended', () => resolve(loopId === id), {
                once: true,
            })
        })
    }

    async function runLoop(
        video: HTMLVideoElement,
        poster: HTMLImageElement,
        id: number
    ): Promise<void> {
        // Hold poster for 3 seconds
        if (!(await wait(POSTER_HOLD, id))) return

        // Fade out poster to reveal video beneath
        if (!(await fade(poster, 0, id))) return

        // Load and play video
        const src = video.dataset.src
        if (!src) return
        if (video.getAttribute('src') !== src) {
            video.src = src
            video.load()
        }

        try {
            await video.play()
        } catch {
            return
        }

        // Wait for video to finish
        if (!(await waitForEnd(video, id))) return

        // Fade poster back in
        if (!(await fade(poster, 1, id))) return

        // Loop
        runLoop(video, poster, id)
    }

    function start(e: MediaQueryList | MediaQueryListEvent): void {
        loopId++
        if (holdTimer !== null) clearTimeout(holdTimer)

        const currentId = loopId

        // Stop both videos
        for (const v of [desktopVideo!, mobileVideo!]) {
            v.pause()
            v.removeAttribute('src')
            v.load()
        }

        // Reset both posters instantly (no transition)
        for (const p of [desktopPoster!, mobilePoster!]) {
            p.style.transition = 'none'
            p.style.opacity = '1'
        }

        const [video, poster] = e.matches
            ? [desktopVideo!, desktopPoster!]
            : [mobileVideo!, mobilePoster!]

        runLoop(video, poster, currentId)
    }

    start(mql)
    mql.addEventListener('change', start)
}
