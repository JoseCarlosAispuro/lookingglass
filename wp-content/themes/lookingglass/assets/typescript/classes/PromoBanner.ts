import { animate } from 'motion'

// ---------------------------------------------------------------------------
// Storage
// ---------------------------------------------------------------------------

const SESSION_KEY = 'lg_promo_seen'
const PERSISTENT_KEY_PREFIX = 'lg_promo_dismissed_'
const SUPPRESSION_MS = 7 * 24 * 60 * 60 * 1000 // 7 days in ms

// ---------------------------------------------------------------------------
// Timing
// ---------------------------------------------------------------------------

const POST_ENTRANCE_DELAY_MS = 5_000
const POST_CONFLICT_CLEAR_DELAY_MS = 5_000

// ---------------------------------------------------------------------------
// Dev override — add ?promo to any URL to bypass delays and suppression
// ---------------------------------------------------------------------------

const DEV_FORCE = new URLSearchParams(window.location.search).has('promo')

// ---------------------------------------------------------------------------
// Conflict selectors
// Elements whose presence in the DOM means the promo must wait.
// ---------------------------------------------------------------------------

const CONFLICT_SELECTORS = [
    // Main navigation overlay (open = has translate-y-0 class)
    '[data-navigation-modal].translate-y-0',
    // Existing site modals
    '[data-modal-instance][data-state="open"]',
    // Cookie Notice WordPress plugin
    '#cookie-notice:not(.cookie-notice-hidden)',
    '.cookie-notice-container:not([style*="display: none"])',
    // CookieConsent JS library
    '.cc-window.cc-active',
    // Generic cookie banners
    '[data-cookie-notice]:not([aria-hidden="true"])',
    // Ticketing dialogs
    '[data-ticketing-dialog][aria-hidden="false"]',
    '[data-ticketing-dialog][data-state="open"]',
]

// ---------------------------------------------------------------------------
// PromoBanner class
// ---------------------------------------------------------------------------

// Selectors for interactive elements that can receive focus inside the modal
const FOCUSABLE_SELECTORS = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'textarea:not([disabled])',
    'select:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',')

class PromoBanner {
    private readonly root: HTMLElement
    private readonly backdrop: HTMLElement
    private readonly modal: HTMLElement
    private readonly closeButtons: NodeListOf<HTMLElement>
    private readonly ctaButton: HTMLElement | null
    private readonly campaignImage: HTMLImageElement | null
    private readonly campaignId: string

    // In-memory fallback for when Web Storage is unavailable
    private inMemorySessionFlag = false

    // Holds the active MutationObserver watching for conflict resolution
    private conflictObserver: MutationObserver | null = null

    // The element that held focus before the modal opened, so we can restore it
    private previouslyFocusedElement: HTMLElement | null = null

    // Bound reference so we can remove the listener on close
    private readonly onKeyDown: (e: KeyboardEvent) => void

    constructor(root: HTMLElement) {
        this.root = root
        this.backdrop = root.querySelector<HTMLElement>(
            '[data-promo-backdrop]'
        )!
        this.modal = root.querySelector<HTMLElement>('[data-promo-modal]')!
        this.closeButtons =
            root.querySelectorAll<HTMLElement>('[data-promo-close]')
        this.ctaButton = root.querySelector<HTMLElement>('[data-promo-cta]')
        this.campaignImage =
            root.querySelector<HTMLImageElement>('[data-promo-image]')
        this.campaignId = root.dataset.campaignId ?? 'default'
        this.onKeyDown = this.handleKeyDown.bind(this)
    }

    // -----------------------------------------------------------------------
    // Storage helpers
    // -----------------------------------------------------------------------

    private isSessionSuppressed(): boolean {
        if (this.inMemorySessionFlag) return true
        try {
            return sessionStorage.getItem(SESSION_KEY) === '1'
        } catch {
            return false
        }
    }

    private isPersistentlySuppressed(): boolean {
        try {
            const stored = localStorage.getItem(
                PERSISTENT_KEY_PREFIX + this.campaignId
            )
            if (!stored) return false
            return Date.now() - parseInt(stored, 10) < SUPPRESSION_MS
        } catch {
            return false
        }
    }

    private markSessionSeen(): void {
        if (DEV_FORCE) return
        this.inMemorySessionFlag = true
        try {
            sessionStorage.setItem(SESSION_KEY, '1')
        } catch {
            // Storage unavailable — in-memory flag acts as fallback
        }
    }

    private markPersistentDismissed(): void {
        if (DEV_FORCE) return
        try {
            localStorage.setItem(
                PERSISTENT_KEY_PREFIX + this.campaignId,
                Date.now().toString()
            )
        } catch {
            // Storage unavailable — session-level suppression is still active
        }
    }

    // -----------------------------------------------------------------------
    // Conflict detection
    // -----------------------------------------------------------------------

    private isAnyConflictActive(): boolean {
        return CONFLICT_SELECTORS.some(selector => {
            try {
                return document.querySelector(selector) !== null
            } catch {
                return false
            }
        })
    }

    private waitForConflictsToClear(): Promise<void> {
        if (!this.isAnyConflictActive()) return Promise.resolve()

        return new Promise(resolve => {
            this.conflictObserver = new MutationObserver(() => {
                if (!this.isAnyConflictActive()) {
                    this.stopConflictObserver()
                    resolve()
                }
            })

            this.conflictObserver.observe(document.body, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: [
                    'class',
                    'style',
                    'aria-hidden',
                    'data-state',
                ],
            })
        })
    }

    private stopConflictObserver(): void {
        this.conflictObserver?.disconnect()
        this.conflictObserver = null
    }

    // -----------------------------------------------------------------------
    // Entrance animation detection
    // Resolves once the page loader hides itself (display: none),
    // or immediately if no loader is present.
    // -----------------------------------------------------------------------

    private waitForEntranceAnimation(): Promise<void> {
        const loader = document.querySelector<HTMLElement>('[data-loader]')

        if (!loader || loader.style.display === 'none') {
            return Promise.resolve()
        }

        return new Promise(resolve => {
            const observer = new MutationObserver(() => {
                if (loader.style.display === 'none') {
                    observer.disconnect()
                    resolve()
                }
            })

            observer.observe(loader, {
                attributes: true,
                attributeFilter: ['style'],
            })
        })
    }

    // -----------------------------------------------------------------------
    // Image preloading
    // Ensures the campaign image is decoded before the modal fades in,
    // avoiding a visible placeholder-to-image swap.
    // -----------------------------------------------------------------------

    private waitForCampaignImage(): Promise<void> {
        const img = this.campaignImage
        if (!img) return Promise.resolve()
        if (img.complete && img.naturalWidth > 0) return Promise.resolve()

        return new Promise(resolve => {
            img.addEventListener('load', () => resolve(), { once: true })
            img.addEventListener('error', () => resolve(), { once: true })
        })
    }

    // -----------------------------------------------------------------------
    // Utility
    // -----------------------------------------------------------------------

    private delay(ms: number): Promise<void> {
        return new Promise(resolve => setTimeout(resolve, ms))
    }

    private isOpen(): boolean {
        return this.root.dataset.state === 'open'
    }

    private lockScroll(): void {
        document.documentElement.classList.add('overflow-hidden')
    }

    private unlockScroll(): void {
        document.documentElement.classList.remove('overflow-hidden')
    }

    // -----------------------------------------------------------------------
    // Focus management
    // -----------------------------------------------------------------------

    private getFocusableElements(): HTMLElement[] {
        return Array.from(
            this.modal.querySelectorAll<HTMLElement>(FOCUSABLE_SELECTORS)
        )
    }

    private focusFirstElement(): void {
        const focusable = this.getFocusableElements()
        focusable[0]?.focus()
    }

    private restoreFocus(): void {
        this.previouslyFocusedElement?.focus()
        this.previouslyFocusedElement = null
    }

    private trapFocus(e: KeyboardEvent): void {
        const focusable = this.getFocusableElements()
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

    private handleKeyDown(e: KeyboardEvent): void {
        if (e.key === 'Escape') this.closeModal()
        if (e.key === 'Tab') this.trapFocus(e)
    }

    // -----------------------------------------------------------------------
    // Open animation
    // 1. Reveal root (makes it interactive)
    // 2. Fade in backdrop
    // 3. Fade + scale in modal card
    // -----------------------------------------------------------------------

    private async openModal(): Promise<void> {
        await this.waitForCampaignImage()

        // Start the 7-day suppression clock the moment the modal is shown,
        // regardless of how or whether the visitor dismisses it
        this.markPersistentDismissed()

        // Store the element that currently has focus so we can return to it later
        this.previouslyFocusedElement = document.activeElement as HTMLElement

        // Make the overlay interactive and visible before animating
        this.root.classList.remove('invisible', 'pointer-events-none')
        this.root.setAttribute('aria-hidden', 'false')
        this.root.dataset.state = 'open'
        this.lockScroll()
        document.addEventListener('keydown', this.onKeyDown)

        await animate(
            this.backdrop,
            { opacity: [0, 1] },
            { duration: 0.3, ease: 'easeOut' }
        ).finished

        await animate(
            this.modal,
            { opacity: [0, 1], scale: [0.98, 1] },
            { duration: 0.35, ease: [0.23, 1, 0.32, 1] }
        ).finished

        // Move focus into the modal once the card is visible
        this.focusFirstElement()
    }

    // -----------------------------------------------------------------------
    // Close animation (reverse of open)
    // -----------------------------------------------------------------------

    private async closeModal(): Promise<void> {
        if (!this.isOpen()) return

        document.removeEventListener('keydown', this.onKeyDown)

        await animate(
            this.modal,
            { opacity: [1, 0], scale: [1, 0.98] },
            { duration: 0.25, ease: 'easeIn' }
        ).finished

        await animate(
            this.backdrop,
            { opacity: [1, 0] },
            { duration: 0.2, ease: 'easeIn' }
        ).finished

        // Hide the overlay before restoring page state
        this.root.classList.add('invisible', 'pointer-events-none')
        this.root.setAttribute('aria-hidden', 'true')
        this.root.dataset.state = 'closed'
        this.unlockScroll()

        // Return focus to the element that was active before the modal opened
        this.restoreFocus()
    }

    // -----------------------------------------------------------------------
    // Event binding
    // -----------------------------------------------------------------------

    private bindCloseButtons(): void {
        this.closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.closeModal())
        })
    }

    private bindBackdropClick(): void {
        this.backdrop.addEventListener('click', () => this.closeModal())
    }

    private bindCtaClick(): void {
        this.ctaButton?.addEventListener('click', () => this.closeModal())
    }

    private bindEvents(): void {
        this.bindCloseButtons()
        this.bindBackdropClick()
        this.bindCtaClick()
        // Escape + Tab are added/removed dynamically in openModal/closeModal
        // so they only fire while the modal is actually open
    }

    // -----------------------------------------------------------------------
    // Init — main sequencing logic
    // -----------------------------------------------------------------------

    async init(): Promise<void> {
        // DEV: bypass everything with ?promo in the URL
        if (DEV_FORCE) {
            this.bindEvents()
            await this.openModal()
            return
        }

        if (this.isSessionSuppressed() || this.isPersistentlySuppressed())
            return

        this.bindEvents()

        // Step 1: wait for the entrance (page loader) animation to finish
        await this.waitForEntranceAnimation()

        // Step 2: wait the required post-entrance delay
        await this.delay(POST_ENTRANCE_DELAY_MS)

        // Step 3: if a conflicting overlay is active, wait for it to clear
        // then add an additional courtesy delay
        if (this.isAnyConflictActive()) {
            await this.waitForConflictsToClear()
            await this.delay(POST_CONFLICT_CLEAR_DELAY_MS)
        }

        // Step 4: re-check suppression (another tab may have dismissed during the wait)
        if (this.isSessionSuppressed() || this.isPersistentlySuppressed())
            return

        // Step 5: mark as seen for this session, then open
        this.markSessionSeen()
        await this.openModal()
    }
}

export default PromoBanner
