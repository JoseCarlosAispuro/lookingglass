import { animate, cubicBezier, scroll } from 'motion'

type FetchApiResponseT = {
    data: {
        page: number
        html: string
        max_num_posts: number
    }
    success: boolean
}

class PlaysGrid {
    private currentPage: number
    private readonly leftHeading: HTMLElement
    private headingAnimated: boolean
    private readonly rightHeading: HTMLElement
    private stickyHeading: HTMLElement
    private loadMoreButton: HTMLElement
    private playsGridResults: HTMLElement
    private readonly container: HTMLElement
    private readonly api: WPApiT = wpApi
    private readonly exceptionCategory?: string
    private readonly tagId?: string

    constructor(container: HTMLElement) {
        this.currentPage = 1
        this.container = container
        this.headingAnimated = false
        this.stickyHeading = this.container.querySelector(
            '[data-sticky-heading]'
        )!
        this.leftHeading = this.stickyHeading.querySelector(
            '[data-heading-left]'
        )!
        this.rightHeading = this.stickyHeading.querySelector(
            '[data-heading-right]'
        )!
        this.loadMoreButton = this.container.querySelector(
            '[data-load-more-button]'
        )!
        this.playsGridResults = this.container.querySelector(
            '[data-plays-grid-results]'
        )!
        this.exceptionCategory = this.container.dataset.categoryId
        this.tagId = this.container.dataset.tagId
    }

    handleHeadingAnimation = () => {
        scroll(
            (progress, _info) => {
                if (progress >= 0.15 && !this.headingAnimated) {
                    const fluidEase = cubicBezier(0.22, 1, 0.36, 1)
                    animate(
                        this.leftHeading,
                        { left: ['-120%', 0] },
                        { duration: 1, ease: fluidEase }
                    )
                    animate(
                        this.rightHeading,
                        { left: ['120%', 0] },
                        { duration: 1, ease: fluidEase }
                    )
                    this.headingAnimated = true
                }

                this.stickyHeading.style.opacity =
                    this.stickyHeading.getBoundingClientRect().top +
                        this.container.getBoundingClientRect().top <=
                    0
                        ? '0.4'
                        : '1'
            },
            { target: this.container, offset: ['start end', 'end start'] }
        )
    }

    createResultsCards = (htmlString: string) => {
        const node = document.createElement('div')
        node.innerHTML = htmlString
        node.childNodes.forEach(element => {
            this.playsGridResults.appendChild(element)
        })
    }

    loadingState = (isLoading: boolean) => {
        const loadingAnimation = animate(
            this.loadMoreButton,
            { opacity: 0.4 },
            {
                duration: 1,
                ease: 'linear',
                repeatType: 'reverse',
                repeat: Infinity,
            }
        )
        ;(this.loadMoreButton as HTMLButtonElement).disabled = true

        if (!isLoading) {
            loadingAnimation.stop()
            ;(this.loadMoreButton as HTMLButtonElement).disabled = false
        }
    }

    handleLoadMore = () => {
        const { url } = this.api
        const { action, nonce } = this.api.actions.fetch_past_plays

        this.loadMoreButton?.addEventListener('click', () => {
            const resultsPosts =
                this.container.querySelectorAll('[data-result-card]')
            this.loadingState(true)

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    nonce: nonce,
                    action: action,
                    offset: String(resultsPosts.length),
                    tag: String(this.tagId),
                    exceptionCategory: String(this.exceptionCategory),
                    currentPage: String(this.currentPage + 1),
                    perPage: String((this.currentPage + 1) % 2 !== 0 ? 11 : 10),
                }),
            })
                .then(res => res.json())
                .then(({ data }: FetchApiResponseT) => {
                    this.createResultsCards(data.html)
                    const totalPosts =
                        this.container.querySelectorAll('[data-result-card]')
                    this.currentPage = this.currentPage + 1

                    if (totalPosts.length === data.max_num_posts) {
                        this.loadMoreButton.style.display = 'none'
                    }

                    this.loadingState(false)
                })
        })
    }

    init() {
        this.handleHeadingAnimation()
        this.handleLoadMore()
    }
}

export default PlaysGrid
