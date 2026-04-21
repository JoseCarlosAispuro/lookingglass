import { animate } from 'motion'
import Swiper from 'swiper'
import { FreeMode, Thumbs } from 'swiper/modules'

const EASE_OUT_EXPO: [number, number, number, number] = [0.22, 1, 0.36, 1]

interface SlideData {
    year: string
    description: string
}

class Timeline {
    private container: HTMLElement
    private slides: SlideData[]
    private cards: HTMLElement[]
    private progressBars: HTMLElement[]
    private separators: HTMLElement[]
    private digitStrips: HTMLElement[]
    private digitHeight: number
    private mobileYear: HTMLElement | null
    private mobileDesc: HTMLElement | null
    private mobileText: HTMLElement | null
    private mobileMore: HTMLElement | null
    private mobileProgressBar: HTMLElement | null
    private modal: HTMLElement | null
    private modalYear: HTMLElement | null
    private modalBody: HTMLElement | null
    private modalClose: HTMLElement | null
    private prevBtn: HTMLElement | null
    private nextBtn: HTMLElement | null

    private mainSwiper: Swiper | null
    private thumbsSwiper: Swiper | null
    private activeIndex: number
    private observer: IntersectionObserver | null
    private abortController: AbortController
    private reducedMotion: boolean
    private modalTrigger: HTMLElement | null
    private hasEnteredViewport: boolean
    private slideDuration: number
    private autoplayTimer: number | null
    private autoplayStartTime: number
    private autoplayRemaining: number
    private mdMediaQuery: MediaQueryList

    constructor(container: HTMLElement) {
        this.container = container
        this.cards = Array.from(
            container.querySelectorAll<HTMLElement>('[data-timeline-card]')
        )
        this.slides = this.cards.map(card => ({
            year:
                card
                    .querySelector<HTMLElement>('[data-timeline-card-year]')
                    ?.textContent?.trim() || '',
            description: card.dataset.fullDesc || '',
        }))

        this.progressBars = Array.from(
            container.querySelectorAll<HTMLElement>('[data-timeline-progress]')
        )
        this.separators = Array.from(
            container.querySelectorAll<HTMLElement>('[data-timeline-separator]')
        )
        this.digitStrips = Array.from(
            container.querySelectorAll<HTMLElement>('[data-timeline-digit]')
        )
        this.digitHeight = 0

        this.mobileYear = container.querySelector('[data-timeline-mobile-year]')
        this.mobileDesc = container.querySelector('[data-timeline-mobile-desc]')
        this.mobileText = container.querySelector('[data-timeline-mobile-text]')
        this.mobileMore = container.querySelector('[data-timeline-mobile-more]')
        this.mobileProgressBar = container.querySelector<HTMLElement>(
            '[data-timeline-mobile-progress]'
        )

        this.modal = container.querySelector('[data-timeline-modal]')
        this.modalYear = container.querySelector('[data-timeline-modal-year]')
        this.modalBody = container.querySelector('[data-timeline-modal-body]')
        this.modalClose = container.querySelector('[data-timeline-modal-close]')

        this.prevBtn = container.querySelector('[data-timeline-prev]')
        this.nextBtn = container.querySelector('[data-timeline-next]')

        this.mainSwiper = null
        this.thumbsSwiper = null
        this.activeIndex = 0
        this.observer = null
        this.abortController = new AbortController()
        this.reducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches
        this.modalTrigger = null
        this.hasEnteredViewport = false
        this.slideDuration = parseInt(
            container.dataset.slideDuration || '5000',
            10
        )
        this.autoplayTimer = null
        this.autoplayStartTime = 0
        this.autoplayRemaining = 0
        this.mdMediaQuery = window.matchMedia('(min-width: 1024px)')
    }

    init() {
        if (this.container.dataset.initialized) return
        if (this.cards.length === 0) return

        this.container.dataset.initialized = 'true'
        const signal = this.abortController.signal

        this.ensureDigitHeight()
        this.setOdometerImmediate(this.slides[0]?.year || '')

        // Defer Swiper init past initial layout to prevent scroll-on-load
        requestAnimationFrame(() => {
            this.initSwipers()
            this.setupVisibilityObserver()
            this.checkDescriptionOverflow()
            this.checkMobileDescriptionOverflow()
        })

        // Handle breakpoint changes (desktop <-> mobile)
        this.mdMediaQuery.addEventListener(
            'change',
            e => this.handleBreakpointChange(e.matches),
            { signal }
        )

        // Desktop "more" buttons
        this.container
            .querySelectorAll<HTMLElement>('[data-timeline-more]')
            .forEach(btn => {
                btn.addEventListener(
                    'click',
                    e => {
                        e.stopPropagation()
                        this.modalTrigger = btn
                        this.openModal()
                    },
                    { signal }
                )
            })

        // Mobile arrows
        this.prevBtn?.addEventListener(
            'click',
            () => {
                if (this.activeIndex <= 0) return
                this.goToSlide(this.activeIndex - 1)
            },
            { signal }
        )
        this.nextBtn?.addEventListener(
            'click',
            () => {
                if (this.activeIndex >= this.cards.length - 1) return
                this.goToSlide(this.activeIndex + 1)
            },
            { signal }
        )

        // Mobile "more" button
        this.mobileMore?.addEventListener(
            'click',
            () => {
                this.modalTrigger = this.mobileMore
                this.openModal()
            },
            { signal }
        )

        // Modal close
        this.modalClose?.addEventListener('click', () => this.closeModal(), {
            signal,
        })

        // Escape closes modal
        document.addEventListener(
            'keydown',
            e => {
                if (e.key === 'Escape') this.closeModal()
            },
            { signal }
        )

        // Focus trap in modal
        this.modal?.addEventListener('keydown', e => this.trapFocus(e), {
            signal,
        })

        // Click outside closes modal
        document.addEventListener(
            'click',
            e => {
                if (!this.modal || this.modal.dataset.state !== 'open') return
                const target = e.target as HTMLElement
                if (
                    target.closest('[data-timeline-modal]') ||
                    target.closest('[data-timeline-more]') ||
                    target.closest('[data-timeline-mobile-more]')
                )
                    return
                this.closeModal()
            },
            { signal }
        )
    }

    private initSwipers() {
        const thumbsEl = this.container.querySelector<HTMLElement>(
            '[data-timeline-thumbs-swiper]'
        )
        const mainEl = this.container.querySelector<HTMLElement>(
            '[data-timeline-main-swiper]'
        )

        if (!thumbsEl || !mainEl) return

        // Init thumbs first
        this.thumbsSwiper = new Swiper(thumbsEl, {
            modules: [FreeMode],
            slidesPerView: Math.min(3, this.cards.length),
            spaceBetween: 24,
            freeMode: true,
            watchSlidesProgress: true,
            allowTouchMove: this.cards.length > 3,
            breakpoints: {
                1440: {
                    slidesPerView: Math.min(4, this.cards.length),
                    allowTouchMove: this.cards.length > 4,
                },
            },
        })

        // Init main swiper linked to thumbs
        this.mainSwiper = new Swiper(mainEl, {
            modules: [Thumbs],
            slidesPerView: 1,
            thumbs: { swiper: this.thumbsSwiper },
            allowTouchMove: true,
            observer: true,
            observeParents: true,
            on: {
                slideChange: swiper => {
                    this.onSlideChange(swiper.activeIndex)
                },
            },
        })
    }

    private handleBreakpointChange(isDesktop: boolean) {
        if (isDesktop) {
            // Entering desktop: reinitialize thumbs swiper and relink
            const thumbsEl = this.container.querySelector<HTMLElement>(
                '[data-timeline-thumbs-swiper]'
            )
            if (thumbsEl && !this.thumbsSwiper?.destroyed) {
                this.thumbsSwiper?.update()
            } else if (thumbsEl) {
                this.thumbsSwiper = new Swiper(thumbsEl, {
                    modules: [FreeMode],
                    slidesPerView: Math.min(3, this.cards.length),
                    spaceBetween: 24,
                    freeMode: true,
                    watchSlidesProgress: true,
                    allowTouchMove: this.cards.length > 3,
                    breakpoints: {
                        1440: {
                            slidesPerView: Math.min(4, this.cards.length),
                            allowTouchMove: this.cards.length > 4,
                        },
                    },
                })
                if (this.mainSwiper) {
                    this.mainSwiper.thumbs.swiper = this.thumbsSwiper
                    this.mainSwiper.thumbs.init()
                    this.mainSwiper.thumbs.update(true)
                }
            }
        } else {
            // Entering mobile: destroy thumbs swiper, remove reference
            if (this.thumbsSwiper && !this.thumbsSwiper.destroyed) {
                this.thumbsSwiper.destroy(true, true)
            }
            this.thumbsSwiper = null
        }

        // Update main swiper layout
        this.mainSwiper?.update()
        this.updateMobileDisplay()

        // Recalculate digit height (mobile/desktop sizes differ) and reposition
        this.digitHeight = 0
        this.setOdometerImmediate(this.slides[this.activeIndex]?.year || '')
    }

    private onSlideChange(newIndex: number) {
        const prevIndex = this.activeIndex
        this.activeIndex = newIndex

        // Animate odometer
        this.ensureDigitHeight()
        this.animateYear(
            this.slides[prevIndex]?.year || '',
            this.slides[newIndex]?.year || ''
        )

        // Reset all progress bars, then restart autoplay for new slide
        this.progressBars.forEach(bar => {
            bar.style.transition = 'none'
            bar.style.width = '0%'
        })
        if (this.mobileProgressBar) {
            this.mobileProgressBar.style.transition = 'none'
            this.mobileProgressBar.style.width = '0%'
        }

        // Update separator opacity
        this.separators.forEach((sep, i) => {
            sep.classList.toggle('opacity-40', i === newIndex)
            sep.classList.toggle('opacity-20', i !== newIndex)
        })

        // Update mobile
        this.updateMobileDisplay()

        // Close modal on slide change
        this.closeModal()

        // Restart autoplay timer
        this.restartAutoplay()
    }

    private goToSlide(index: number) {
        if (index === this.activeIndex) return
        if (index < 0 || index >= this.cards.length) return

        // Use main swiper if available (desktop), otherwise manual
        if (this.mainSwiper) {
            this.mainSwiper.slideTo(index)
        } else {
            this.onSlideChange(index)
        }
    }

    destroy() {
        this.stopAutoplay()
        this.mainSwiper?.destroy(true, true)
        this.thumbsSwiper?.destroy(true, true)
        this.abortController.abort()
        if (this.observer) {
            this.observer.disconnect()
            this.observer = null
        }
    }

    // --- Year odometer ---

    private ensureDigitHeight() {
        if (this.digitHeight > 0) return
        const firstDigit = this.digitStrips[0]?.children[0] as
            | HTMLElement
            | undefined
        if (firstDigit) {
            this.digitHeight = firstDigit.offsetHeight
        }
    }

    private animateYear(from: string, to: string) {
        if (this.reducedMotion) {
            this.setOdometerImmediate(to)
            return
        }

        this.ensureDigitHeight()
        if (this.digitHeight === 0) return

        const fromDigits = from.split('')
        const toDigits = to.split('')

        this.digitStrips.forEach((strip, i) => {
            const fromDigit = parseInt(fromDigits[i] || '0', 10)
            const toDigit = parseInt(toDigits[i] || '0', 10)

            if (fromDigit === toDigit) return

            const fromY = -(fromDigit * this.digitHeight)
            const toY = -(toDigit * this.digitHeight)

            animate(
                strip,
                {
                    transform: [
                        `translateY(${fromY}px)`,
                        `translateY(${toY}px)`,
                    ],
                },
                {
                    duration: 0.6,
                    ease: EASE_OUT_EXPO,
                    delay: i * 0.08,
                }
            )
        })
    }

    private setOdometerImmediate(year: string) {
        this.ensureDigitHeight()
        if (this.digitHeight === 0) return

        const digits = year.split('')
        this.digitStrips.forEach((strip, i) => {
            const digit = parseInt(digits[i] || '0', 10)
            strip.style.transform = `translateY(${-(digit * this.digitHeight)}px)`
        })
    }

    // --- Mobile display ---

    private updateMobileDisplay() {
        const slide = this.slides[this.activeIndex]
        if (!slide) return

        if (this.mobileYear) this.mobileYear.textContent = slide.year
        if (this.mobileText) this.mobileText.textContent = slide.description

        this.checkMobileDescriptionOverflow()
    }

    private checkDescriptionOverflow() {
        this.cards.forEach(card => {
            const container = card.querySelector<HTMLElement>(
                '[data-timeline-card-desc]'
            )
            const textSpan = card.querySelector<HTMLElement>(
                '[data-timeline-card-text]'
            )
            const moreBtn = card.querySelector<HTMLElement>(
                '[data-timeline-more]'
            )
            if (!container || !textSpan || !moreBtn) return

            const fullText = textSpan.textContent || ''

            // Check if text overflows without the button
            if (container.scrollHeight <= container.clientHeight) return

            // Show button, then trim text until it fits within the clamp
            moreBtn.classList.remove('hidden')

            let text = fullText
            while (
                container.scrollHeight > container.clientHeight &&
                text.length > 0
            ) {
                text = text.slice(0, -1)
                textSpan.textContent = `${text.trimEnd()}\u2026 `
            }
        })
    }

    private checkMobileDescriptionOverflow() {
        if (!this.mobileDesc || !this.mobileText || !this.mobileMore) return

        const fullText = this.slides[this.activeIndex]?.description || ''
        this.mobileText.textContent = fullText
        this.mobileMore.classList.add('hidden')

        requestAnimationFrame(() => {
            if (!this.mobileDesc || !this.mobileText || !this.mobileMore) return
            if (this.mobileDesc.scrollHeight <= this.mobileDesc.clientHeight)
                return

            this.mobileMore.classList.remove('hidden')

            let text = fullText
            while (
                this.mobileDesc.scrollHeight > this.mobileDesc.clientHeight &&
                text.length > 0
            ) {
                text = text.slice(0, -1)
                this.mobileText.textContent = `${text.trimEnd()}\u2026 `
            }
        })
    }

    // --- Modal ---

    private openModal() {
        if (!this.modal) return

        const slide = this.slides[this.activeIndex]
        if (!slide) return

        if (this.modalYear) this.modalYear.textContent = slide.year

        if (this.modalBody) {
            while (this.modalBody.firstChild) {
                this.modalBody.removeChild(this.modalBody.firstChild)
            }
            const paragraphs = slide.description.split('\n').filter(Boolean)
            for (const text of paragraphs) {
                const p = document.createElement('p')
                p.textContent = text
                this.modalBody.appendChild(p)
            }
        }

        this.modal.dataset.state = 'open'
        this.pauseAutoplay()

        // Position modal above the relevant anchor
        const containerRect = this.container.getBoundingClientRect()
        if (!this.mdMediaQuery.matches && this.mobileDesc) {
            // Mobile: bottom aligns with bottom of mobile desc
            const descRect = this.mobileDesc.getBoundingClientRect()
            this.modal.style.bottom = `${containerRect.bottom - descRect.bottom}px`
        } else {
            // Desktop: just above the rail container
            const rail = this.container.querySelector<HTMLElement>(
                '[data-timeline-rail-container]'
            )
            if (rail) {
                const railRect = rail.getBoundingClientRect()
                this.modal.style.bottom = `${containerRect.bottom - railRect.top + 16}px`
            }
        }

        if (!this.reducedMotion) {
            animate(
                this.modal,
                {
                    opacity: [0, 1],
                    transform: ['translateY(24px)', 'translateY(0)'],
                },
                { duration: 0.3, ease: EASE_OUT_EXPO }
            )
        }

        this.modalClose?.focus()
    }

    private closeModal() {
        if (!this.modal || this.modal.dataset.state !== 'open') return

        this.resumeAutoplay()

        const finish = () => {
            if (!this.modal) return
            this.modal.dataset.state = 'closed'
            this.modal.style.opacity = ''
            this.modal.style.transform = ''
            this.modal.style.bottom = ''
            this.modalTrigger?.focus()
            this.modalTrigger = null
        }

        if (this.reducedMotion) {
            finish()
        } else {
            animate(
                this.modal,
                {
                    opacity: [1, 0],
                    transform: ['translateY(0)', 'translateY(24px)'],
                },
                { duration: 0.2, ease: EASE_OUT_EXPO }
            ).then(finish)
        }
    }

    private trapFocus(e: KeyboardEvent) {
        if (e.key !== 'Tab' || !this.modal) return
        if (this.modal.dataset.state !== 'open') return

        const focusable = this.modal.querySelectorAll<HTMLElement>(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        )
        if (focusable.length === 0) return

        const first = focusable[0]
        const last = focusable[focusable.length - 1]

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault()
            last.focus()
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault()
            first.focus()
        }
    }

    // --- Autoplay ---

    private startAutoplay(remaining?: number) {
        this.stopAutoplay()
        const duration = remaining ?? this.slideDuration
        this.autoplayStartTime = performance.now()
        this.autoplayRemaining = duration

        this.animateProgressBar(duration, remaining !== undefined)
        this.autoplayTimer = window.setTimeout(() => {
            const next =
                this.activeIndex < this.cards.length - 1
                    ? this.activeIndex + 1
                    : 0
            this.goToSlide(next)
        }, duration)
    }

    private stopAutoplay() {
        if (this.autoplayTimer !== null) {
            clearTimeout(this.autoplayTimer)
            this.autoplayTimer = null
        }
    }

    private pauseAutoplay() {
        if (this.autoplayTimer === null) return
        const elapsed = performance.now() - this.autoplayStartTime
        this.autoplayRemaining = Math.max(0, this.autoplayRemaining - elapsed)
        this.stopAutoplay()
        this.freezeProgressBar()
    }

    private resumeAutoplay() {
        if (!this.hasEnteredViewport) return
        if (this.autoplayRemaining <= 0) return
        this.startAutoplay(this.autoplayRemaining)
    }

    private restartAutoplay() {
        if (!this.hasEnteredViewport) return
        this.stopAutoplay()
        // Allow layout to flush the reset width before starting the animation
        requestAnimationFrame(() => this.startAutoplay())
    }

    private animateProgressBar(duration: number, resume: boolean) {
        const bar = this.progressBars[this.activeIndex]
        if (bar) {
            if (!resume) {
                bar.style.transition = 'none'
                bar.style.width = '0%'
            }
            requestAnimationFrame(() => {
                bar.style.transition = `width ${duration}ms linear`
                bar.style.width = '100%'
            })
        }

        if (this.mobileProgressBar) {
            if (!resume) {
                this.mobileProgressBar.style.transition = 'none'
                this.mobileProgressBar.style.width = '0%'
            }
            requestAnimationFrame(() => {
                if (!this.mobileProgressBar) return
                this.mobileProgressBar.style.transition = `width ${duration}ms linear`
                this.mobileProgressBar.style.width = '100%'
            })
        }
    }

    private freezeProgressBar() {
        const freeze = (bar: HTMLElement | undefined) => {
            if (!bar) return
            const currentWidth = bar.getBoundingClientRect().width
            const parentWidth =
                bar.parentElement?.getBoundingClientRect().width || 1
            bar.style.transition = 'none'
            bar.style.width = `${(currentWidth / parentWidth) * 100}%`
        }

        freeze(this.progressBars[this.activeIndex])
        freeze(this.mobileProgressBar ?? undefined)
    }

    // --- Visibility observer ---

    private setupVisibilityObserver() {
        this.observer = new IntersectionObserver(
            entries => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        if (!this.hasEnteredViewport) {
                            this.hasEnteredViewport = true
                        }
                        this.startAutoplay()
                    } else {
                        this.stopAutoplay()
                    }
                }
            },
            { threshold: 0 }
        )
        this.observer.observe(this.container)
    }
}

export default Timeline
