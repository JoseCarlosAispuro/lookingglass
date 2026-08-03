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
        // Only top-level anchors — sub-menu anchors must not be animated
        // or motion will persist y/opacity state on them, breaking their
        // behaviour once they become visible inside the mobile accordion.
        this.navigationItemsAnchors =
            this.navigationModal?.querySelectorAll(
                '#menu-main-navigation > .menu-item > a, .menu-support > .menu-item > a'
            ) ?? document.querySelectorAll('.menu-item a')
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
        this.resetAccordions()
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

    resetAccordions = () => {
        const topLevelItems =
            this.navigationModal?.querySelectorAll<HTMLElement>(
                '#menu-main-navigation > .menu-item-has-children, .menu-support > .menu-item-has-children'
            ) ?? []

        topLevelItems.forEach(item => {
            if (!item.classList.contains('open')) return
            const anchor = item.querySelector<HTMLElement>(':scope > a')
            item.classList.remove('open')
            if (anchor) {
                item.style.maxHeight = `${anchor.clientHeight}px`
            }
        })
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
            // Plus/minus icon only on direct top-level items
            if (item.classList.contains('menu-item-has-children')) {
                const isTopLevel = !item.closest('.sub-menu')
                if (isTopLevel) this.createPlusMinusIcon(item)
            }

            // 4th level items (inside a nested sub-menu under a
            // menu-item-has-children) must never receive active state —
            // they are always visually consistent and need no interaction.
            const isFourthLevel = !!item.closest(
                '.sub-menu .menu-item-has-children > .sub-menu'
            )
            if (isFourthLevel) return

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

    // After any item opens or closes, walk up the DOM and recalculate the
    // max-height of every open ancestor so nested content is never clipped.
    syncAncestorHeights = (fromItem: HTMLElement) => {
        let ancestor = fromItem.parentElement?.closest<HTMLElement>(
            '.menu-item-has-children'
        )

        while (ancestor) {
            if (ancestor.classList.contains('open')) {
                const anchor = ancestor.querySelector<HTMLElement>('a')
                const submenu = ancestor.querySelector<HTMLElement>('.sub-menu')
                if (anchor && submenu) {
                    // scrollHeight forces a synchronous reflow so the value
                    // already reflects the child's newly applied max-height.
                    ancestor.style.maxHeight = `${anchor.clientHeight + submenu.scrollHeight + 16}px`
                }
            }
            ancestor = ancestor.parentElement?.closest<HTMLElement>(
                '.menu-item-has-children'
            )
        }
    }

    handleClick = () => {
        // Query only direct children of the top-level menu lists so nested
        // items (2nd level and deeper) are never wired with accordion logic.
        const topLevelItems =
            this.navigationModal?.querySelectorAll<HTMLElement>(
                '#menu-main-navigation > .menu-item-has-children, .menu-support > .menu-item-has-children'
            ) ?? []

        topLevelItems.forEach(item => {
            // :scope > a ensures we only get the item's own direct anchor,
            // never a nested anchor from a deeper level.
            const anchorElement = item.querySelector<HTMLElement>(':scope > a')
            const submenu = item.querySelector('.sub-menu')
            if (!anchorElement) return
            item.style.maxHeight = `${anchorElement.clientHeight}px`

            anchorElement.addEventListener('click', event => {
                event.preventDefault()

                if (item.classList.contains('open')) {
                    item.style.maxHeight = `${anchorElement.clientHeight}px`
                    item.classList.remove('open')
                } else {
                    // Use scrollHeight (not clientHeight) so we measure
                    // the submenu's full content, not the clipped portion.
                    item.style.maxHeight = `${anchorElement.clientHeight + (submenu?.scrollHeight ?? 0) + 16}px`
                    item.classList.add('open')
                }

                this.syncAncestorHeights(item)
            })
        })
    }

    // Delegated handler — ensures nested menu-item-has-children anchors always
    // navigate, regardless of any upstream preventDefault call. Runs at the
    // modal level so it catches all clicks inside the overlay.
    handleNestedMenuNavigation = () => {
        this.navigationModal?.addEventListener('click', event => {
            const anchor = (event.target as Element).closest<HTMLAnchorElement>(
                '.sub-menu .menu-item-has-children > a'
            )
            if (!anchor) return

            const href = anchor.getAttribute('href')
            if (!href || href === '#') return

            window.location.href = href
        })
    }

    handleResizing = () => {
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1024) {
                setTimeout(() => {
                    const topLevelItems =
                        this.navigationModal?.querySelectorAll<HTMLElement>(
                            '#menu-main-navigation > .menu-item-has-children, .menu-support > .menu-item-has-children'
                        ) ?? []

                    topLevelItems.forEach(item => {
                        const anchorElement =
                            item.querySelector<HTMLElement>('a')
                        if (!anchorElement) return
                        item.style.maxHeight = `${anchorElement.clientHeight}px`
                    })
                }, 200)
            }
        })
    }

    init() {
        this.handleScroll()
        this.handleNavigationModal()
        this.handleMenuItems()
        this.handleClick()
        this.handleNestedMenuNavigation()
        this.handleResizing()
    }

    // Add more methods as needed
}

export default Navigation
