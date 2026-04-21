import Swiper from 'swiper'
import { FreeMode, Navigation } from 'swiper/modules'

interface WhatsonCarouselElements {
    container: HTMLElement
    swiperEl: HTMLElement
    wrapperEl: HTMLElement
    cursorEl: HTMLElement | null
    prevBtn: HTMLElement | null
    nextBtn: HTMLElement | null
    paginationEl: HTMLElement | null
}

function isTouchDevice(): boolean {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0
}

function isMobile(): boolean {
    return window.innerWidth < 1024
}

function initCarousel(elements: WhatsonCarouselElements): void {
    const { swiperEl, wrapperEl, cursorEl, prevBtn, nextBtn, paginationEl } =
        elements

    let swiper: Swiper | null = null
    let isOverflowing = false

    const checkOverflow = (): boolean => {
        if (isMobile()) return false // Mobile always has single slide, no overflow concept
        const wrapperWidth = wrapperEl.scrollWidth
        const containerWidth = swiperEl.clientWidth
        return wrapperWidth > containerWidth
    }

    const updatePagination = (current: number) => {
        if (!paginationEl) return
        const currentEl = paginationEl.querySelector('[data-current]')
        if (currentEl) {
            currentEl.textContent = String(current + 1).padStart(2, '0')
        }
    }

    const updateCentering = () => {
        if (isMobile()) {
            // Mobile: always left aligned, single slide
            wrapperEl.style.justifyContent = ''
            return
        }

        isOverflowing = checkOverflow()

        if (isOverflowing) {
            // Has overflow - left align, enable swiper
            wrapperEl.style.justifyContent = ''
            if (swiper) {
                swiper.enable()
                swiper.params.allowTouchMove = true
            }
        } else {
            // No overflow - center items, disable swiper dragging
            wrapperEl.style.justifyContent = 'center'
            if (swiper) {
                swiper.params.allowTouchMove = false
            }
        }
    }

    // Initialize Swiper with responsive config
    swiper = new Swiper(swiperEl, {
        modules: [FreeMode, Navigation],
        // Mobile: single slide
        slidesPerView: 1,
        spaceBetween: 16,
        // Desktop: auto width with freeMode
        breakpoints: {
            1024: {
                slidesPerView: 'auto',
                freeMode: {
                    enabled: true,
                    sticky: false,
                    momentumRatio: 0.5,
                    momentumVelocityRatio: 0.5,
                },
            },
        },
        navigation: {
            prevEl: prevBtn,
            nextEl: nextBtn,
        },
        lazyPreloadPrevNext: 1,
        grabCursor: false,
        watchOverflow: true,
        on: {
            init: () => {
                requestAnimationFrame(updateCentering)
            },
            resize: () => {
                updateCentering()
            },
            slideChange: s => {
                updatePagination(s.activeIndex)
            },
        },
    })

    // Custom cursor logic (desktop only, when overflow exists)
    if (cursorEl && !isTouchDevice()) {
        let isInsideCarousel = false

        const moveCursor = (e: MouseEvent) => {
            if (isMobile() || !isOverflowing || !isInsideCarousel) return

            cursorEl.style.left = `${e.clientX}px`
            cursorEl.style.top = `${e.clientY}px`
            cursorEl.style.opacity = '1'
        }

        const handleMouseEnter = () => {
            if (isMobile()) return
            isInsideCarousel = true
            updateCentering() // Re-check overflow
            if (isOverflowing) {
                document.body.style.cursor = 'none'
                swiperEl.style.cursor = 'none'
            }
        }

        const handleMouseLeave = () => {
            isInsideCarousel = false
            cursorEl.style.opacity = '0'
            document.body.style.cursor = ''
            swiperEl.style.cursor = ''
        }

        // Check for elements that should hide cursor (like links)
        const handleMouseOver = (e: MouseEvent) => {
            if (isMobile()) return
            const target = e.target as HTMLElement
            const shouldHide =
                target.closest('[data-hide-cursor]') || target.closest('a')

            if (shouldHide) {
                cursorEl.style.opacity = '0'
                document.body.style.cursor = ''
            } else if (isOverflowing && isInsideCarousel) {
                cursorEl.style.opacity = '1'
                document.body.style.cursor = 'none'
            }
        }

        // Event listeners
        swiperEl.addEventListener('mouseenter', handleMouseEnter)
        swiperEl.addEventListener('mouseleave', handleMouseLeave)
        swiperEl.addEventListener('mousemove', moveCursor)
        swiperEl.addEventListener('mouseover', handleMouseOver)

        // Recalculate on resize
        const ro = new ResizeObserver(() => {
            updateCentering()
            // Hide cursor when switching to mobile
            if (isMobile()) {
                cursorEl.style.opacity = '0'
                document.body.style.cursor = ''
            }
        })
        ro.observe(swiperEl)
    }

    // Initial centering check after fonts load
    if (document.fonts?.ready) {
        document.fonts.ready.then(updateCentering)
    }
}

export default function whatsonCarousel(): void {
    const carousels = document.querySelectorAll<HTMLElement>(
        '[data-whatson-carousel]'
    )

    carousels.forEach(container => {
        const swiperEl = container.querySelector<HTMLElement>('.whatson-swiper')
        const wrapperEl = container.querySelector<HTMLElement>(
            '[data-whatson-wrapper]'
        )
        const cursorEl = container.querySelector<HTMLElement>(
            '[data-drag-cursor-element]'
        )
        const prevBtn = container.querySelector<HTMLElement>(
            '[data-whatson-prev]'
        )
        const nextBtn = container.querySelector<HTMLElement>(
            '[data-whatson-next]'
        )
        const paginationEl = container.querySelector<HTMLElement>(
            '[data-whatson-pagination]'
        )

        if (!swiperEl || !wrapperEl) return

        initCarousel({
            container,
            swiperEl,
            wrapperEl,
            cursorEl,
            prevBtn,
            nextBtn,
            paginationEl,
        })
    })
}
