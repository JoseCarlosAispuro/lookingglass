import { Fancybox } from '@fancyapps/ui'
import { animate, cubicBezier } from 'motion'
import Carousel from '@/typescript/classes/Carousel.ts'
import { cursorTracker } from '@/typescript/utils/cursor-tracker.ts'

type FancyboxCarousel = {
    getPlugins: () => {
        Zoomable?: { execute: (action: string) => void }
        Fullscreen?: { toggle: () => void }
        Autoplay?: { toggle: () => void; isEnabled: () => void }
        Thumbs?: { getContainer: () => HTMLElement | undefined }
    }
}

const init = () => {
    const galleries = document.querySelectorAll<HTMLElement>(
        '[data-media-gallery]'
    )

    galleries.forEach(gallery => {
        initSwiper(gallery)
        initCursorSwap(gallery)
    })

    initFancybox()
}

const initSwiper = (gallery: HTMLElement) => {
    const swiper = gallery.querySelector<HTMLElement>('.swiper')
    if (!swiper) return

    const carousel = new Carousel(swiper)
    carousel.init()
}

const materialIcon = (name: string) =>
    `<span class="material-symbols-outlined filled" aria-hidden="true">${name}</span>`

const isTouchDevice = () => matchMedia('(pointer: coarse)').matches

const cursorHtml = `<span class="z-50 hidden md:block cursor-none absolute w-[80px] aspect-square -translate-x-1/2 -translate-y-1/2 pointer-events-none" data-custom-cursor>
    <span class="opacity-0 text-(--app-hl-color) bg-(--app-hl-bg-color) rounded-full transition-all duration-500 ease-in-out w-full h-full flex items-center justify-content-center" data-custom-cursor-inner>
        <span class="w-full button-md font-medium text-center">Drag</span>
    </span>
</span>`

const getArrowDirection = (target: HTMLElement): string | null => {
    const arrow = target.closest?.('.is-prev')
        ? 'Prev'
        : target.closest?.('.is-next')
          ? 'Next'
          : null
    return arrow
}

const initFancyboxCursor = (container: HTMLElement) => {
    if (isTouchDevice()) return

    container.insertAdjacentHTML('beforeend', cursorHtml)
    container.setAttribute('data-custom-cursor', '')

    const dot = container.querySelector<HTMLElement>('[data-custom-cursor]')
    const dotInner = container.querySelector<HTMLElement>(
        '[data-custom-cursor-inner]'
    )
    const cursorText = dotInner?.querySelector('span')
    if (!dot || !dotInner || !cursorText) return

    const tracker = cursorTracker(container, {
        onPositionUpdate: position => {
            animate(
                dot,
                {
                    left: `${position.x}px`,
                    top: `${position.y}px`,
                },
                {
                    ease: cubicBezier(0.22, 1, 0.36, 1),
                    duration: 0.3,
                }
            )

            if (!position.isInside) {
                dotInner.style.opacity = '0'
                dotInner.style.transform = 'scale(0)'
                container.classList.remove('cursor-hidden')
                return
            }

            const arrowDir = position.target
                ? getArrowDirection(position.target)
                : null

            if (arrowDir) {
                cursorText.textContent = arrowDir
                dotInner.style.opacity = '1'
                dotInner.style.transform = 'scale(1)'
                container.classList.add('cursor-hidden')
            } else if (position.hideOnTarget) {
                dotInner.style.opacity = '0'
                dotInner.style.transform = 'scale(0)'
                container.classList.remove('cursor-hidden')
                cursorText.textContent = 'Drag'
            } else {
                cursorText.textContent = 'Drag'
                dotInner.style.opacity = '1'
                dotInner.style.transform = 'scale(1)'
                container.classList.add('cursor-hidden')
            }
        },
    })

    return tracker
}

const initFancybox = () => {
    let cursorDestroy: (() => void) | undefined

    // @ts-expect-error — Fancybox types don't include click callbacks on toolbar items
    Fancybox.bind('[data-fancybox^="media-gallery"]', {
        groupAll: false,
        showClass: 'f-fadeIn',
        hideClass: 'f-fadeOut',
        on: {
            ready: (fancybox: { getContainer: () => HTMLElement }) => {
                const container = fancybox.getContainer()
                if (!container) return

                container
                    .querySelectorAll('.f-button, .f-thumbs')
                    .forEach(el => {
                        ;(el as HTMLElement).dataset.noCustomCursor = ''
                    })

                const tracker = initFancyboxCursor(container)
                cursorDestroy = tracker?.destroy
            },
            close: () => {
                cursorDestroy?.()
                cursorDestroy = undefined
            },
        },
        Carousel: {
            Arrows: {
                prevTpl: materialIcon('arrow_back'),
                nextTpl: materialIcon('arrow_forward'),
            },
            Toolbar: {
                display: {
                    left: ['counter'],
                    middle: [],
                    right: [
                        'toggleFull',
                        'fullscreen',
                        'autoplay',
                        'pause',
                        'thumbs',
                        'close',
                    ],
                },
                items: {
                    toggleFull: {
                        tpl: `<button class="f-button">${materialIcon('zoom_in')}</button>`,
                        click: (carousel: FancyboxCarousel) => {
                            carousel
                                .getPlugins()
                                .Zoomable?.execute('toggleFull')
                        },
                    },
                    fullscreen: {
                        tpl: `<button class="f-button">${materialIcon('fullscreen')}</button>`,
                        click: (carousel: FancyboxCarousel) => {
                            carousel.getPlugins().Fullscreen?.toggle()
                        },
                    },
                    autoplay: {
                        tpl: `<button class="f-button">${materialIcon('play_arrow')}</button>`,
                        click: (carousel: FancyboxCarousel, event: Event) => {
                            const btn = event.currentTarget as HTMLButtonElement

                            const autoplay = carousel.getPlugins().Autoplay
                            autoplay?.toggle()

                            const isActive = autoplay?.isEnabled()

                            btn.innerHTML = materialIcon(
                                isActive ? 'pause' : 'play_arrow'
                            )
                        },
                    },
                    thumbs: {
                        tpl: `<button class="f-button">${materialIcon('widget_small')}</button>`,
                        click: (carousel: FancyboxCarousel) => {
                            const container = carousel
                                .getPlugins()
                                .Thumbs?.getContainer()
                            container?.classList.toggle('is-hidden')
                        },
                    },
                    close: {
                        tpl: `<button class="f-button" data-fancybox-close>${materialIcon('close')}</button>`,
                    },
                },
            },
        },
    })
}

const initCursorSwap = (gallery: HTMLElement) => {
    const cursorText = gallery.querySelector('[data-custom-cursor-inner] span')
    if (!cursorText) return

    const videoSlides = gallery.querySelectorAll('[data-media-type="video"]')

    videoSlides.forEach(slide => {
        slide.addEventListener('mouseenter', () => {
            cursorText.textContent = 'Play'
        })
        slide.addEventListener('mouseleave', () => {
            cursorText.textContent = 'Drag'
        })
    })
}

export default init
