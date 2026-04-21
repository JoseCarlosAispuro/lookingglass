import Carousel from '@/typescript/classes/Carousel.ts'

const init = () => {
    const carousels = document.querySelectorAll(
        '.swiper:not([data-timeline-main-swiper]):not([data-timeline-thumbs-swiper]):not(.swiper-custom)'
    )
    carousels.forEach(carousel => {
        const carouselInstance = new Carousel(carousel as HTMLElement)
        carouselInstance.init()
    })
}

export default init
