import { animate, cubicBezier, scroll, stagger } from 'motion'

class Navigation {
    private readonly container: HTMLElement
    private scrollProgress: number
    private scrollDistance: number
    private scrollDirection: string
    private heroHeight: number | null
    private readonly heroSection: NodeListOf<Element>
    private readonly triggerButton: NodeListOf<Element>
    private readonly navigationModal: Element | null
    private navigationItems: NodeListOf<Element>
    private navigationItemsAnchors: NodeListOf<Element>
    private navigationModalSubItemsContainer: Element | null
    private modalIsOpen: boolean
    private activeMenuItem: Element | null
    private navigationLogo: Element | null
    private menusSection: Element | null
    private navigationGalleryElement: HTMLImageElement | null
    private navigationGalleryImages: NavigationGalleryImage[]

    constructor(container: HTMLElement) {
        this.container = container
        this.scrollDistance = 0
        this.scrollProgress = 0
        this.scrollDirection = 'down'
        this.heroSection = document.querySelectorAll(
            '[data-component-id="hero-block"]'
        )
        this.triggerButton = document.querySelectorAll('[data-trigger-button]')
        this.navigationModal = document.querySelector('[data-navigation-modal]')
        this.navigationLogo = this.container.querySelector('.main-logo g')
        this.activeMenuItem = null
        this.navigationModalSubItemsContainer =
            this.navigationModal?.querySelector(
                '[data-navigation-modal-subitems]'
            ) ?? null
        this.menusSection =
            this.navigationModal?.querySelector('[data-menus-section]') ?? null
        this.navigationItems =
            this.navigationModal?.querySelectorAll('.menu-item') ??
            document.querySelectorAll('.menu-item')
        this.navigationItemsAnchors =
            this.navigationModal?.querySelectorAll('.menu-item a') ??
            document.querySelectorAll('.menu-item a')
        this.navigationGalleryElement = this.navigationModal?.querySelector(
            '[data-navigation-gallery-element]'
        ) as HTMLImageElement | null
        this.navigationGalleryImages =
            typeof navigationGallery !== 'undefined' ? navigationGallery : []
        this.modalIsOpen = false
        this.heroHeight =
            this.heroSection && this.heroSection.length > 0
                ? this.heroSection[0].clientHeight
                : null
    }

    show = () => {
        animate(this.container, { y: 0 }, { ease: 'easeOut', duration: 0.5 })
    }

    hide = () => {
        animate(
            this.container,
            { y: '-100%' },
            { ease: 'easeOut', duration: 0.5 }
        )
    }

    hideGalleryImages = () => {
        setTimeout(() => {
            this.navigationGalleryElement?.classList.add('hidden')
        }, 1000)
    }

    showGalleryImage = () => {
        if (
            !this.navigationGalleryElement ||
            this.navigationGalleryImages.length === 0
        )
            return
        const randomIndex = Math.floor(
            Math.random() * this.navigationGalleryImages.length
        )
        const image = this.navigationGalleryImages[randomIndex]
        this.navigationGalleryElement.src = image.url
        this.navigationGalleryElement.alt = image.alt
        this.navigationGalleryElement.classList.remove('hidden')
        animate(
            this.navigationGalleryElement,
            { y: ['100%', 0], opacity: [0, 1] },
            {
                ease: cubicBezier(0.23, 1, 0.32, 1),
                duration: 1,
                delay: 0.5,
            }
        )
    }

    openModal = () => {
        this.showGalleryImage()
        this.navigationModal?.classList.add('translate-y-0')
        this.navigationModal?.classList.remove('-translate-y-full')
        animate(
            this.navigationItemsAnchors,
            { y: ['100%', 0], opacity: [0, 1] },
            {
                ease: cubicBezier(0.23, 1, 0.32, 1),
                duration: 1,
                delay: stagger(0.01, { startDelay: 0.5 }),
            }
        )
    }

    closeModal = () => {
        this.hideGalleryImages()
        this.navigationModal?.classList.remove('translate-y-0')
        this.navigationModal?.classList.add('-translate-y-full')
        animate(
            this.navigationItemsAnchors,
            { y: [0, '100%'], opacity: [1, 0] },
            {
                ease: cubicBezier(0.23, 1, 0.32, 1),
                duration: 1,
            }
        )
    }

    handleHideShow = () => {
        if (this.heroHeight !== null && this.scrollDistance < this.heroHeight) {
            this.show()
            document.body.setAttribute('data-nav', 'visible')
            this.navigationLogo?.classList.add('!fill-white')
            this.container.classList.add(
                '!bg-transparent',
                '!text-white',
                '!border-none'
            )
            this.container.classList.remove(
                '!text-(--app-fg-color)',
                'bg-(--app-bg-color)'
            )
        } else {
            this.navigationLogo?.classList.remove('!fill-white')
            this.container.classList.remove(
                '!bg-transparent',
                '!text-white',
                '!border-none'
            )
            this.container.classList.add(
                '!text-(--app-fg-color)',
                'bg-(--app-bg-color)'
            )
            document.body.setAttribute(
                'data-nav',
                this.scrollDirection === 'down' ? 'hidden' : 'visible'
            )

            if (this.scrollProgress <= 0) {
                this.show()
            } else {
                this.scrollDirection === 'down' ? this.hide() : this.show()
            }
        }
    }
    handleScroll = () => {
        scroll((progress, info) => {
            this.scrollDirection =
                this.scrollProgress < progress ? 'down' : 'up'
            this.scrollProgress = progress
            this.scrollDistance = info.y.current
            this.handleHideShow()
        })
    }

    handleNavigationModal = () => {
        this.triggerButton.forEach(button => {
            button.addEventListener('click', () => {
                this.modalIsOpen ? this.closeModal() : this.openModal()
                this.modalIsOpen = !this.modalIsOpen
            })
        })
    }

    createPlusMinusIcon = (item: Element) => {
        const horizontalBar = document.createElement('span')
        horizontalBar.classList.add('part', 'horizontal-bar')
        const verticalBar = document.createElement('span')
        verticalBar.classList.add('part', 'vertical-bar')
        const plusMinusContainer = document.createElement('div')
        plusMinusContainer.classList.add('plus-minus-icon')

        plusMinusContainer.append(verticalBar)
        plusMinusContainer.append(horizontalBar)

        item.firstElementChild?.appendChild(plusMinusContainer)
    }

    handleMenuItems = () => {
        this.navigationItems.forEach((item, _index) => {
            if (item.classList.contains('menu-item-has-children')) {
                this.createPlusMinusIcon(item)
            }

            ;(item as HTMLElement).addEventListener('mouseenter', () => {
                item.classList.add('active')

                if (this.activeMenuItem !== item) {
                    if (this.navigationModalSubItemsContainer) {
                        this.navigationModalSubItemsContainer.innerHTML = ''
                    }
                    this.activeMenuItem?.classList.remove('active')
                }

                if (item.classList.contains('menu-item-has-children')) {
                    if (
                        !this.navigationModalSubItemsContainer?.hasChildNodes()
                    ) {
                        const subItems = item.querySelector('.sub-menu')
                        const subItemsClone = subItems?.cloneNode(true)
                        if (subItemsClone) {
                            this.navigationModalSubItemsContainer?.appendChild(
                                subItemsClone
                            )
                        }
                    }
                }

                this.activeMenuItem = item
            })
        })
    }

    handleMouseOutside = () => {
        if (!this.menusSection) return
        ;(this.menusSection as HTMLElement).addEventListener(
            'mouseleave',
            _event => {
                this.navigationItems.forEach(item => {
                    item.classList.remove('active')
                })
                if (this.navigationModalSubItemsContainer) {
                    this.navigationModalSubItemsContainer.innerHTML = ''
                }
            }
        )
    }

    handleClick = () => {
        this.navigationItems.forEach(item => {
            if (item.classList.contains('menu-item-has-children')) {
                const anchorElement = item.querySelector('a')
                const submenu = item.querySelector('.sub-menu')
                if (!anchorElement) return
                ;(item as HTMLElement).style.maxHeight =
                    `${anchorElement.clientHeight}px`

                anchorElement.addEventListener('click', event => {
                    event.preventDefault()

                    if (item.classList.contains('open')) {
                        ;(item as HTMLElement).style.maxHeight =
                            `${anchorElement.clientHeight}px`
                        item.classList.remove('open')
                    } else {
                        ;(item as HTMLElement).style.maxHeight =
                            `${anchorElement.clientHeight + (submenu?.clientHeight ?? 0) + 16}px`
                        item.classList.add('open')
                    }
                })
            }
        })
    }

    handleResizing = () => {
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1024) {
                setTimeout(() => {
                    this.navigationItems.forEach(item => {
                        if (item.classList.contains('menu-item-has-children')) {
                            const anchorElement = item.querySelector('a')
                            if (!anchorElement) return
                            ;(item as HTMLElement).style.maxHeight =
                                `${anchorElement.clientHeight}px`
                        }
                    })
                }, 200)
            }
        })
    }

    init() {
        this.handleScroll()
        this.handleNavigationModal()
        this.handleMenuItems()
        this.handleMouseOutside()
        this.handleClick()
        this.handleResizing()
    }

    // Add more methods as needed
}

export default Navigation
