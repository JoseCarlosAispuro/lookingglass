import { animate } from 'motion'
import Swiper from 'swiper'
import {
    Autoplay,
    EffectCreative,
    EffectFade,
    FreeMode,
    Manipulation,
    Navigation,
    Pagination,
} from 'swiper/modules'

const customCarousel = () => {
    const customCarouselElement = document.querySelectorAll('.swiper-custom')

    customCarouselElement.forEach((carousel, _index) => {
        const customCarouselWrapper = carousel.querySelector('.swiper-wrapper')
        const customCarouselSlides = carousel.querySelectorAll('.swiper-slide')
        const customCursor =
            carousel.parentElement?.querySelector('[data-custom-cursor]') ??
            null

        if (!customCarouselWrapper) return
        generateForwardClones(
            customCarouselSlides,
            customCarouselWrapper,
            carousel,
            true
        )
        generateForwardClones(
            customCarouselSlides,
            customCarouselWrapper,
            carousel,
            true
        )

        const customCarouselSlidesElementsNew =
            carousel.querySelectorAll('.swiper-slide')

        const navNextEl: HTMLElement | null = carousel.querySelector(
            '.swiper-button-next'
        )
        const navPrevEl: HTMLElement | null = carousel.querySelector(
            '.swiper-button-prev'
        )

        new Swiper(carousel as HTMLElement, {
            modules: [
                Navigation,
                Pagination,
                Autoplay,
                FreeMode,
                EffectFade,
                EffectCreative,
                Manipulation,
            ],
            loop: false,
            freeMode: false,
            slidesPerView: 'auto',
            initialSlide: customCarouselSlidesElementsNew.length / 2,
            centeredSlides: true,
            effect: 'creative',
            watchSlidesProgress: true,
            lazyPreloadPrevNext: 1,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: navNextEl,
                prevEl: navPrevEl,
                addIcons: false,
            },
            creativeEffect: {
                limitProgress: 5,
                prev: {
                    translate: [-364, 16, 0],
                    rotate: [0, 0, -2],
                    opacity: 0.8,
                },
                next: {
                    translate: [364, 16, 0],
                    rotate: [0, 0, 2],
                    opacity: 0.8,
                },
            },
            on: {
                touchMove: (_swiper, event) => {
                    if (customCursor) {
                        const rect = carousel.getBoundingClientRect()
                        const clientX = 'clientX' in event ? event.clientX : 0
                        const clientY = 'clientY' in event ? event.clientY : 0
                        animate(
                            customCursor,
                            {
                                left: `${clientX - rect.left}px`,
                                top: `${clientY - rect.top}px`,
                            },
                            { ease: 'linear', duration: 0.1 }
                        )
                    }
                },
                realIndexChange: swiper => {
                    if (
                        (swiper as any).visibleSlidesIndexes.includes(
                            swiper.slides.length - 2
                        )
                    ) {
                        const wrapper =
                            carousel.querySelector('.swiper-wrapper')
                        if (wrapper)
                            generateForwardClones(
                                customCarouselSlides,
                                wrapper,
                                carousel,
                                true,
                                swiper
                            )
                        swiper.update()
                    }

                    if ((swiper as any).visibleSlidesIndexes.includes(2)) {
                        const wrapper =
                            carousel.querySelector('.swiper-wrapper')
                        if (wrapper)
                            generateForwardClones(
                                customCarouselSlides,
                                wrapper,
                                carousel,
                                false,
                                swiper
                            )
                        swiper.update()
                    }
                },
            },
        })
    })
}

const generateForwardClones = (
    customCarouselSlides: NodeListOf<Element>,
    customCarouselWrapper: Element,
    _carousel: Element,
    isForward = true,
    swiper: Swiper | null = null
) => {
    const clonedElements = Array.from(customCarouselSlides).map(element => {
        return element.cloneNode(true)
    }) as HTMLElement[]

    if (swiper) {
        isForward
            ? swiper.appendSlide(clonedElements)
            : swiper.prependSlide(clonedElements)
    } else {
        clonedElements.forEach(clonedElement => {
            isForward
                ? customCarouselWrapper.appendChild(clonedElement)
                : customCarouselSlides[0].insertBefore(
                      customCarouselSlides[0],
                      clonedElement
                  )
        })
    }
}

export default customCarousel
