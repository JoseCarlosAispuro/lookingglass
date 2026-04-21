import { animate } from 'motion'

const LOADER_KEY = 'lg_loader_shown'
const COOLDOWN_MS = 24 * 60 * 60 * 1000
const DEV_FORCE_PARAM = 'loader'

const MIN_DISPLAY_MS = 750

const LOGO_VIEWBOX_W = 1938
const LOGO_VIEWBOX_H = 753

function shouldShow(): boolean {
    if (new URLSearchParams(window.location.search).has(DEV_FORCE_PARAM))
        return true
    const last = localStorage.getItem(LOADER_KEY)
    if (!last) return true
    return Date.now() - Number.parseInt(last, 10) > COOLDOWN_MS
}

interface AboveFoldMedia {
    images: HTMLImageElement[]
    videos: HTMLVideoElement[]
    iframes: HTMLIFrameElement[]
}

function getAboveFoldMedia(): AboveFoldMedia {
    const vh = window.innerHeight
    const isAboveFold = (el: Element) => {
        const rect = el.getBoundingClientRect()
        if (rect.top >= vh || el.closest('[data-loader]')) return false
        // Skip lazy images — browser won't fetch them while scroll is locked
        if (el instanceof HTMLImageElement && el.loading === 'lazy')
            return false
        return true
    }

    return {
        images: Array.from(
            document.querySelectorAll<HTMLImageElement>('img')
        ).filter(isAboveFold),
        videos: Array.from(
            document.querySelectorAll<HTMLVideoElement>('video')
        ).filter(isAboveFold),
        iframes: Array.from(
            document.querySelectorAll<HTMLIFrameElement>('iframe')
        ).filter(isAboveFold),
    }
}

function waitForMedia(): Promise<void> {
    const startTime = Date.now()
    const { images, videos, iframes } = getAboveFoldMedia()

    const imagePromises = images.map(img => {
        if (img.complete) return Promise.resolve()
        return new Promise<void>(resolve => {
            img.addEventListener('load', () => resolve(), {
                once: true,
            })
            img.addEventListener('error', () => resolve(), {
                once: true,
            })
        })
    })

    const videoPromises = videos.map(video => {
        if (video.readyState >= 3) return Promise.resolve()
        return new Promise<void>(resolve => {
            video.addEventListener('canplay', () => resolve(), {
                once: true,
            })
            video.addEventListener('error', () => resolve(), {
                once: true,
            })
        })
    })

    const iframePromises = iframes.map(iframe => {
        return new Promise<void>(resolve => {
            iframe.addEventListener('load', () => resolve(), {
                once: true,
            })
            iframe.addEventListener('error', () => resolve(), {
                once: true,
            })
        })
    })

    const allMedia = [...imagePromises, ...videoPromises, ...iframePromises]
    const timeout = new Promise<void>(resolve => setTimeout(resolve, 5000))
    const mediaReady = Promise.race([
        Promise.all(allMedia).then(() => {}),
        timeout,
    ])

    const minTime = new Promise<void>(resolve => {
        const elapsed = Date.now() - startTime
        const remaining = Math.max(0, MIN_DISPLAY_MS - elapsed)
        setTimeout(resolve, remaining)
    })

    return Promise.all([mediaReady, minTime]).then(() => {})
}

function getDisplayWidth(): number {
    return window.innerWidth >= 1024 ? window.innerWidth * 0.72 : 280
}

function getOffsetX(vw: number): number {
    return window.innerWidth >= 1024
        ? LOGO_VIEWBOX_W / 2 - vw * 0.027
        : LOGO_VIEWBOX_W / 2 - vw * 0.12
}

function getOffsetY(vh: number): number {
    return window.innerWidth >= 1024
        ? LOGO_VIEWBOX_H / 2 - vh * 0.047
        : LOGO_VIEWBOX_H / 2
}

function positionMaskLogo(
    maskGroup: SVGGElement,
    revealLayer: SVGElement
): void {
    const vw = window.innerWidth
    const vh = window.innerHeight
    const displayWidth = getDisplayWidth()
    const scale = displayWidth / LOGO_VIEWBOX_W

    const tx = vw / 2
    const ty = vh / 2
    const ox = getOffsetX(vw)
    const oy = getOffsetY(vh)

    maskGroup.setAttribute(
        'transform',
        `translate(${tx}, ${ty}) scale(${scale}) translate(${-ox}, ${-oy})`
    )

    revealLayer.setAttribute('viewBox', `0 0 ${vw} ${vh}`)
    revealLayer.style.transformOrigin = '46% 49%'
}

function cleanup(loader: Element): void {
    ;(loader as HTMLElement).style.display = 'none'
    document.documentElement.style.overflow = ''
}

async function reveal(
    loader: Element,
    loadingLayer: HTMLElement,
    revealLayer: SVGElement
): Promise<void> {
    const pulseSvg = loadingLayer.querySelector<SVGElement>(
        '.animate-loader-pulse'
    )
    if (pulseSvg) {
        pulseSvg.classList.remove('animate-loader-pulse')
    }

    // Show mask layer behind, then fade out loading layer on top to reveal it
    loadingLayer.style.zIndex = '1'
    revealLayer.style.opacity = '1'

    const crossFade = animate(
        loadingLayer,
        { opacity: [1, 0] },
        { duration: 0.4 }
    )
    await crossFade.finished
    loadingLayer.style.display = 'none'

    // Scale entire reveal SVG from center — the mask hole grows with it
    // Fade out overlaps with the end of the scale transition
    const scaleDuration = 1
    const fadeDuration = 0.3
    const fadeDelay = scaleDuration - fadeDuration

    const scaleAnim = animate(
        revealLayer,
        { transform: ['scale(1)', 'scale(500)'] },
        { duration: scaleDuration, ease: [0.45, 0, 0.55, 1] }
    )

    const fadeOut = animate(
        loader as HTMLElement,
        { opacity: [1, 0] },
        { duration: fadeDuration, delay: fadeDelay }
    )

    await Promise.all([scaleAnim.finished, fadeOut.finished])

    cleanup(loader)
}

export default function pageLoader() {
    const loader = document.querySelector('[data-loader]')

    if (!loader || !shouldShow()) {
        if (loader) loader.remove()
        return
    }

    document.documentElement.style.overflow = 'hidden'

    const loadingLayer = loader.querySelector<HTMLElement>(
        '[data-loader-loading]'
    )
    const revealLayer = loader.querySelector<SVGElement>('[data-loader-reveal]')
    const maskGroup = loader.querySelector<SVGGElement>(
        '[data-loader-mask-group]'
    )

    if (!loadingLayer || !revealLayer || !maskGroup) {
        loader.remove()
        return
    }

    positionMaskLogo(maskGroup, revealLayer)
    revealLayer.style.opacity = '1'
    ;(loader as HTMLElement).style.backgroundColor = 'transparent'

    localStorage.setItem(LOADER_KEY, Date.now().toString())

    const safetyTimeout = setTimeout(() => cleanup(loader), 8000)

    waitForMedia()
        .then(() => reveal(loader, loadingLayer, revealLayer))
        .catch(() => cleanup(loader))
        .finally(() => clearTimeout(safetyTimeout))
}
