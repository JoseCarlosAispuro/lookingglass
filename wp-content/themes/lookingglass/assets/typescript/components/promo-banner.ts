import PromoBanner from '../classes/PromoBanner'

const init = () => {
    const root = document.querySelector<HTMLElement>('[data-promo-banner]')

    if (!root) return

    new PromoBanner(root).init().catch(err => {
        console.error('PromoBanner init error:', err)
    })
}

export default init
