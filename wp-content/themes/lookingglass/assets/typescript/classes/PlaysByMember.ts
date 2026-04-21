import { animate } from 'motion'

type FetchApiResponseT = {
    data: {
        page: number
        html: string
        max_num_posts: number
    }
    success: boolean
}

class PlaysByMember {
    private readonly teamMember: string
    private currentPage: number
    private loadMoreButton: HTMLButtonElement
    private playsContainer: HTMLElement
    private readonly container: HTMLElement
    private postsPerPage: number
    private readonly api: WPApiT = wpApi

    constructor(container: HTMLElement, loadMoreButton: HTMLButtonElement) {
        this.currentPage = 1
        this.container = container
        this.loadMoreButton = loadMoreButton
        this.playsContainer = this.container.querySelector(
            '[data-plays-results]'
        ) as HTMLElement
        this.postsPerPage = Number(this.container.dataset.playsPerPage) || 5
        this.teamMember = String(this.container.dataset.teamMember) || ''
    }

    appendElements = (htmlString: string) => {
        const node = document.createElement('div')
        node.innerHTML = htmlString
        node.childNodes.forEach(element => {
            this.playsContainer.appendChild(element)
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
        this.loadMoreButton.disabled = true

        if (!isLoading) {
            loadingAnimation.stop()
            this.loadMoreButton.disabled = false
        }
    }

    handleLoadMore = () => {
        const { url } = this.api
        const { action, nonce } = this.api.actions.fetch_plays_by_member

        this.loadMoreButton.addEventListener('click', () => {
            const resultsPosts =
                this.playsContainer.querySelectorAll(':scope > li')

            this.loadingState(true)

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: action,
                    nonce: nonce,
                    offset: String(resultsPosts.length),
                    perPage: String(this.postsPerPage),
                    teamMember: this.teamMember,
                }),
            })
                .then(res => res.json())
                .then(({ data }: FetchApiResponseT) => {
                    this.appendElements(data.html)

                    this.currentPage++

                    const totalPosts =
                        this.playsContainer.querySelectorAll(':scope > li')
                    if (totalPosts.length === data.max_num_posts) {
                        this.loadMoreButton.classList.add('hidden')
                    }

                    this.loadingState(false)
                })
                .catch(() => {
                    this.loadingState(false)
                })
        })
    }

    init() {
        this.handleLoadMore()
    }
}

export default PlaysByMember
