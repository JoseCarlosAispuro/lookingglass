/**
 * Back to Top smooth scroll functionality
 */
export default function backToTop(): void {
    const buttons = document.querySelectorAll<HTMLElement>('[data-back-to-top]')

    if (!buttons.length) return

    buttons.forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault()

            // Check for reduced motion preference
            const prefersReducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches

            window.scrollTo({
                top: 0,
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
            })

            // Focus on the body or first focusable element for accessibility
            const firstFocusable = document.querySelector<HTMLElement>(
                'a[href], button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            )
            if (firstFocusable) {
                firstFocusable.focus({ preventScroll: true })
            }
        })
    })
}
