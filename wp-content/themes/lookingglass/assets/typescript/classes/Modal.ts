class Modal {
    modal: HTMLElement
    triggers: NodeListOf<HTMLElement>
    content: HTMLElement
    closeButtons: NodeListOf<HTMLElement>
    activeTrigger: HTMLElement | null = null
    focusableEls: HTMLElement[] = []
    firstFocusable!: HTMLElement
    lastFocusable!: HTMLElement
    scrollIndicator: HTMLElement | null

    constructor(modal: HTMLElement, triggers: NodeListOf<HTMLElement>) {
        this.modal = modal;
        this.triggers = triggers;
        this.content = this.modal.querySelector('[data-modal-content]')!
        this.closeButtons = this.modal.querySelectorAll('[data-modal-close]')
        this.scrollIndicator = null
    }

    init() {
        this.bindEvents()
    }

    bindEvents() {
        this.triggers.forEach((trigger) => {
            trigger.addEventListener('click', (e) => {
                const trigger = (e.target as HTMLElement).closest('[data-modal-target]')
                if (!trigger) return

                e.preventDefault()
                this.open(trigger as HTMLElement)
            })
        })

        this.closeButtons.forEach(btn =>
            btn.addEventListener('click', () => this.close())
        )

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.isHidden()) {
                this.close()
            }

            if (e.key === 'Tab' && !this.isHidden()) {
                this.trapFocus(e)
            }
        })
    }

    open(trigger: HTMLElement) {
        this.activeTrigger = trigger

        const templateId = trigger.dataset.modalTarget!
        const tpl = document.getElementById(templateId) as HTMLTemplateElement

        this.content.innerHTML = ''
        this.content.appendChild(tpl.content.cloneNode(true))

        this.modal.setAttribute('data-state', 'open')
        this.modal.setAttribute('aria-hidden', 'false')

        this.scrollIndicator = this.modal.querySelector('[data-content-scroll-indicator]')
        
        this.content.addEventListener('scroll', () => {
            const atEnd = this.content.scrollHeight - this.content.scrollTop <= this.content.clientHeight + 90;
            this.scrollIndicator?.classList.toggle('opacity-0!', atEnd);
        });

        this.setFocusableElements()
        this.firstFocusable.focus()

        document.documentElement.classList.add('overflow-hidden')
    }

    close() {
        this.modal.setAttribute('aria-hidden', 'true')
        this.modal.setAttribute('data-state', 'closed')

        this.content.onscroll = null;

        this.activeTrigger?.focus()

        setTimeout(() => {
            this.content.innerHTML = ''
        }, 300)

        document.documentElement.classList.remove('overflow-hidden')
    }

    toggleScrollIndicator() {
        console.log(this.content)
        
    }

    isHidden() {
        return this.modal.getAttribute('aria-hidden') === 'true'
    }

    setFocusableElements() {
        const selectors = [
            'a[href]',
            'button:not([disabled])',
            'input:not([disabled])',
            'textarea:not([disabled])',
            'select:not([disabled])',
            '[tabindex]:not([tabindex="-1"])'
        ]

        this.focusableEls = Array.from(
            this.modal.querySelectorAll(selectors.join(','))
        ) as HTMLElement[]

        this.firstFocusable = this.focusableEls[0]
        this.lastFocusable = this.focusableEls[this.focusableEls.length - 1]
    }

    trapFocus(e: KeyboardEvent) {
        if (this.focusableEls.length === 0) return

        if (e.shiftKey && document.activeElement === this.firstFocusable) {
            e.preventDefault()
            this.lastFocusable.focus()
        }

        if (!e.shiftKey && document.activeElement === this.lastFocusable) {
            e.preventDefault()
            this.firstFocusable.focus()
        }
    }
}

export default Modal
