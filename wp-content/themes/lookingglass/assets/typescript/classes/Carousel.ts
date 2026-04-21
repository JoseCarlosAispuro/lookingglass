import { animate } from 'motion'
import Swiper from 'swiper'
import {
    Autoplay,
    EffectFade,
    FreeMode,
    Navigation,
    Pagination,
} from 'swiper/modules'
import { textToBoolean } from '@/typescript/utils/validations.ts'

class Carousel {
    private readonly container: HTMLElement
    private readonly parentElement: HTMLElement
    private customCursor: HTMLElement | null
    private readonly slidesPerView?: number | 'auto'
    private readonly slidesPerViewMobile?: number | 'auto'
    private readonly offsetBeforeFromElement: null | Element // Selector to take offsetLeft from that selected element
    private readonly offsetBefore: number
    private readonly offsetBeforeMobile: number
    private readonly offsetAfter: number
    private readonly offsetAfterMobile: number
    private readonly spaceBetweenSlides: number
    private readonly spaceBetweenSlidesMobile: number
    private readonly showCustomCursor: boolean
    private readonly centeredSlides: boolean
    private readonly handleDrag: boolean
    private readonly autoplayDelay: number | null
    private readonly freeMode: boolean
    private readonly freeModeMobile: boolean
    private readonly breakpoint: number
    private readonly effect?: string
    private readonly navNextEl?: HTMLElement | null
    private readonly navPrevEl?: HTMLElement | null
    private progressBar: HTMLElement | null

    constructor(container: HTMLElement) {
        this.container = container
        this.parentElement = this.container.parentElement!
        this.customCursor =
            this.parentElement.querySelector('[data-custom-cursor]') ?? null
        this.slidesPerView = this.container.dataset.slidesPerView
            ? this.container.dataset.slidesPerView === 'auto'
                ? 'auto'
                : Number(this.container.dataset.slidesPerView)
            : 2
        this.slidesPerViewMobile = this.container.dataset.slidesPerViewMobile
            ? this.container.dataset.slidesPerViewMobile === 'auto'
                ? 'auto'
                : Number(this.container.dataset.slidesPerViewMobile)
            : 1
        this.offsetBeforeFromElement = this.container.dataset
            .offsetBeforeFromElement
            ? document.querySelector(
                  this.container.dataset.offsetBeforeFromElement
              )
            : null
        this.offsetBefore = this.container.dataset.offsetBefore
            ? Number(this.container.dataset.offsetBefore)
            : 0
        this.offsetBeforeMobile = this.container.dataset.offsetBeforeMobile
            ? Number(this.container.dataset.offsetBeforeMobile)
            : 0
        this.offsetAfter = this.container.dataset.offsetAfter
            ? Number(this.container.dataset.offsetAfter)
            : 0
        this.offsetAfterMobile = this.container.dataset.offsetAfterMobile
            ? Number(this.container.dataset.offsetAfterMobile)
            : 0
        this.spaceBetweenSlides = this.container.dataset.spaceBetweenSlides
            ? Number(this.container.dataset.spaceBetweenSlides)
            : 24
        this.spaceBetweenSlidesMobile = this.container.dataset
            .spaceBetweenSlidesMobile
            ? Number(this.container.dataset.spaceBetweenSlidesMobile)
            : 16
        this.showCustomCursor = this.container.dataset.showCustomCursor
            ? textToBoolean(this.container.dataset.showCustomCursor)
            : true
        this.handleDrag = this.container.dataset.handleDrag
            ? textToBoolean(this.container.dataset.handleDrag)
            : true
        this.autoplayDelay = this.container.dataset.autoplayDelay
            ? Number(this.container.dataset.autoplayDelay)
            : null
        this.freeMode = this.container.dataset.freeMode
            ? textToBoolean(this.container.dataset.freeMode)
            : true
        this.freeModeMobile = this.container.dataset.freeModeMobile
            ? textToBoolean(this.container.dataset.freeModeMobile)
            : this.freeMode
        this.breakpoint = this.container.dataset.breakpoint
            ? Number(this.container.dataset.breakpoint)
            : 480
        this.effect = this.container.dataset.effect ?? 'slide'
        this.centeredSlides = this.container.dataset.centeredSlides
            ? textToBoolean(this.container.dataset.centeredSlides)
            : false
        this.progressBar = this.container.querySelector('.progress-bar')
        this.navNextEl = this.container.querySelector('.swiper-button-next')
        this.navPrevEl = this.container.querySelector('.swiper-button-prev')
    }

    private checkOverflow(): boolean {
        const wrapperEl = this.container.querySelector(
            '.swiper-wrapper'
        ) as HTMLElement | null
        if (!wrapperEl) return false
        return wrapperEl.scrollWidth > this.container.clientWidth
    }

    private updateCentering(swiper: Swiper): void {
        const wrapperEl = this.container.querySelector(
            '.swiper-wrapper'
        ) as HTMLElement | null
        if (!wrapperEl) return

        const isOverflowing = this.checkOverflow()

        if (isOverflowing) {
            wrapperEl.style.justifyContent = ''
            swiper.params.allowTouchMove = this.handleDrag
            if (this.customCursor) {
                this.customCursor.style.display = ''
                this.parentElement.removeAttribute('data-cursor-disabled')
            }
        } else {
            wrapperEl.style.justifyContent = 'center'
            swiper.params.allowTouchMove = false
            if (this.customCursor) {
                this.customCursor.style.display = 'none'
                this.parentElement.setAttribute('data-cursor-disabled', '')
            }
        }
    }

    init() {
        const swiper = new Swiper(this.container, {
            modules: [Navigation, Pagination, Autoplay, FreeMode, EffectFade],
            freeMode: this.freeModeMobile ?? this.freeMode ?? true,
            autoplay: this.autoplayDelay
                ? {
                      delay: this.autoplayDelay,
                  }
                : false,
            effect: this.effect,
            ...(this.effect === 'fade' && {
                observer: true,
                observeParents: true,
            }),
            watchSlidesProgress: true,
            lazyPreloadPrevNext: 1,
            centeredSlides: this.centeredSlides,
            slidesOffsetBefore: this.offsetBeforeMobile,
            slidesOffsetAfter: this.offsetAfterMobile,
            spaceBetween: this.spaceBetweenSlidesMobile,
            slidesPerView: this.slidesPerViewMobile,
            allowTouchMove: this.handleDrag,
            navigation: {
                nextEl: this.navNextEl,
                prevEl: this.navPrevEl,
                addIcons: false,
            },
            fadeEffect: {
                crossFade: true,
            },
            pagination: {
                el: '.swiper-pagination',
                type: 'custom',
                renderCustom: (_swiper, current, total) =>
                    `${current}/${total}`,
            },
            breakpoints: {
                [this.breakpoint]: {
                    slidesPerView: this.slidesPerView,
                    spaceBetween: this.spaceBetweenSlides,
                    slidesOffsetBefore: this.offsetBeforeFromElement
                        ? (this.offsetBeforeFromElement as HTMLElement)
                              .offsetLeft
                        : this.offsetBefore,
                    slidesOffsetAfter: this.offsetAfter,
                    freeMode: this.freeMode ?? true,
                },
            },
            ...({
                freeModeMomentum: true,
                freeModeMomentumRatio: 0.5,
                freeModeMomentumBounce: true,
                freeModeMomentumBounceRatio: 0.5,
                freeModeSticky: false,
                freeModeMinimumVelocity: 0.02,
            } as any),
            // Initial slide
            initialSlide: 0,
            on: {
                touchMove: (_swiper, event) => {
                    if (this.showCustomCursor && this.customCursor) {
                        const rect = this.container.getBoundingClientRect()
                        const clientX = 'clientX' in event ? event.clientX : 0
                        const clientY = 'clientY' in event ? event.clientY : 0
                        animate(
                            this.customCursor,
                            {
                                left: `${clientX - rect.left}px`,
                                top: `${clientY - rect.top}px`,
                            },
                            { ease: 'linear', duration: 0.1 }
                        )
                    }
                },
                slideChange: () => {
                    if (this.progressBar) {
                        this.progressBar?.classList.remove('start')
                        setTimeout(() => {
                            this.progressBar?.classList.add('start')
                        }, 300)
                    }
                },
                init: () => {
                    if (this.progressBar) {
                        this.progressBar?.classList.add('start')
                    }
                    if (this.effect !== 'fade') {
                        requestAnimationFrame(() =>
                            this.updateCentering(swiper)
                        )
                    }
                },
                resize: () => {
                    if (this.effect !== 'fade') {
                        this.updateCentering(swiper)
                    }
                },
            },
        })
    }
}

export default Carousel
