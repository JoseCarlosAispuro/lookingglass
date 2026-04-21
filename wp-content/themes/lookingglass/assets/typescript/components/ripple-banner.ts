import Ripples from '../classes/Ripples';

const Init = () => {
    const banners: NodeListOf<HTMLElement> = document.querySelectorAll(
        '[data-component-id="ripple-banner"]'
    );

    banners.forEach((banner) => {
        const bannerRippleDiv = banner.querySelector('[data-ripple-banner]') as HTMLElement

        new Ripples(bannerRippleDiv);
    });
};

export default Init