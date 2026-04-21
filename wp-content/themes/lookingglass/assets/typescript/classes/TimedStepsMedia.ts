class TimedStepsMedia {
    private container: HTMLElement | null
    private steps: HTMLElement[]
    private images: HTMLElement[]
    private activeIndex: number
    private duration: number
    private animationId: number | null
    private startTime: number | null
    private observer: IntersectionObserver | null
    private abortController: AbortController

    constructor(container: HTMLElement) {
        this.container = container
        this.steps = Array.from(
            container.querySelectorAll<HTMLElement>('[data-step]')
        )
        this.images = Array.from(
            container.querySelectorAll<HTMLElement>('[data-step-image]')
        )
        this.activeIndex = 0
        this.duration = Number(container.dataset.stepDuration || '5000')
        this.animationId = null
        this.startTime = null
        this.observer = null
        this.abortController = new AbortController()
    }

    init() {
        if (!this.container) return
        if (this.container.dataset.initialized) return
        if (this.steps.length === 0) return

        this.container.dataset.initialized = 'true'

        const signal = this.abortController.signal

        this.steps.forEach(step => {
            step.addEventListener(
                'click',
                () => {
                    const index = Number(step.dataset.step)
                    this.onStepClick(index)
                },
                { signal }
            )
        })

        this.activateStep(0)
        this.startTimer()

        this.observer = new IntersectionObserver(
            entries => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        this.startTimer()
                    } else {
                        this.cancelTimer()
                    }
                }
            },
            { threshold: 0 }
        )
        this.observer.observe(this.container)
    }

    destroy() {
        this.cancelTimer()
        this.abortController.abort()

        if (this.observer) {
            this.observer.disconnect()
            this.observer = null
        }

        this.steps = []
        this.images = []
        this.container = null
    }

    private activateStep(index: number) {
        this.activeIndex = index

        this.steps.forEach((step, i) => {
            const isActive = i === index
            const description = step.querySelector<HTMLElement>(
                '[data-step-description]'
            )
            const progress = step.querySelector<HTMLElement>(
                '[data-step-progress]'
            )

            if (isActive) {
                step.classList.add('is-active')
                step.setAttribute('aria-expanded', 'true')
                if (description) {
                    description.style.maxHeight = `${description.scrollHeight}px`
                    description.style.opacity = '1'
                }
                if (progress) {
                    progress.style.height = '0%'
                }
            } else {
                step.classList.remove('is-active')
                step.setAttribute('aria-expanded', 'false')
                if (description) {
                    description.style.maxHeight = '0px'
                    description.style.opacity = '0'
                }
                if (progress) {
                    progress.style.height = '0%'
                }
            }
        })

        this.images.forEach((image, i) => {
            if (i === index) {
                image.classList.remove('opacity-0')
                image.classList.add('opacity-100')
            } else {
                image.classList.remove('opacity-100')
                image.classList.add('opacity-0')
            }
        })
    }

    private startTimer() {
        this.cancelTimer()
        this.startTime = performance.now()

        const tick = (now: number) => {
            if (!this.startTime) return

            const elapsed = now - this.startTime
            const progress = Math.min(elapsed / this.duration, 1)

            const activeStep = this.steps[this.activeIndex]
            const progressBar = activeStep?.querySelector<HTMLElement>(
                '[data-step-progress]'
            )
            if (progressBar) {
                progressBar.style.height = `${progress * 100}%`
            }

            if (progress >= 1) {
                this.advanceStep()
                return
            }

            this.animationId = requestAnimationFrame(tick)
        }

        this.animationId = requestAnimationFrame(tick)
    }

    private cancelTimer() {
        if (this.animationId !== null) {
            cancelAnimationFrame(this.animationId)
            this.animationId = null
        }
        this.startTime = null
    }

    private advanceStep() {
        const nextIndex = (this.activeIndex + 1) % this.steps.length
        this.activateStep(nextIndex)
        this.startTimer()
    }

    private onStepClick(index: number) {
        if (index === this.activeIndex) return
        this.cancelTimer()
        this.activateStep(index)
        this.startTimer()
    }
}

export default TimedStepsMedia
