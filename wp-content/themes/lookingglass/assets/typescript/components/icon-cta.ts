const init = () => {
    const buttons = document.querySelectorAll<HTMLButtonElement>('button[data-icon-cta]');

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const textToCopy = button.getAttribute('data-copy-this-text')?.trim() ?? '';

            if (textToCopy) {
                try {
                    await navigator.clipboard.writeText(textToCopy);

                    // Show tooltip
                    const tooltip = button.querySelector<HTMLElement>('[data-cta-tooltip]');
                    if (tooltip) {
                        tooltip.classList.add('active');
                        setTimeout(() => {
                            tooltip.classList.remove('active');
                        }, 1500);
                    }
                } catch (e) {
                    // Optionally handle copy error
                    console.error('Copy to clipboard failed', e);
                }
            }
        });
    });
}

export default init
