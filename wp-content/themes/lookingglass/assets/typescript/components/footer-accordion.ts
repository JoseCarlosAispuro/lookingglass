/**
 * Footer Accordion Component
 * Handles collapsible menu sections on mobile
 */

const init = () => {
    const footer = document.querySelector('[data-footer]');
    if (!footer) return;

    const accordionItems = footer.querySelectorAll('[data-accordion-item]');

    accordionItems.forEach((item) => {
        const trigger = item.querySelector('[data-accordion-trigger]');
        const content = item.querySelector('[data-accordion-content]') as HTMLElement;
        const icon = item.querySelector('.accordion-icon');

        if (!trigger || !content) return;

        trigger.addEventListener('click', () => {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';

            if (isOpen) {
                // Close this accordion
                trigger.setAttribute('aria-expanded', 'false');
                content.style.maxHeight = '0';
                if (icon) icon.textContent = '+';
            } else {
                // Close all other accordions first (optional: remove for multi-open)
                accordionItems.forEach((otherItem) => {
                    const otherTrigger = otherItem.querySelector('[data-accordion-trigger]');
                    const otherContent = otherItem.querySelector('[data-accordion-content]') as HTMLElement;
                    const otherIcon = otherItem.querySelector('.accordion-icon');

                    if (otherTrigger && otherContent && otherItem !== item) {
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        otherContent.style.maxHeight = '0';
                        if (otherIcon) otherIcon.textContent = '+';
                    }
                });

                // Open this accordion
                trigger.setAttribute('aria-expanded', 'true');
                content.style.maxHeight = content.scrollHeight + 'px';
                if (icon) icon.textContent = '−'; // Using minus sign
            }
        });
    });
};

export default init;
