interface InfiniteShowHideOptions {
    showDuration?: number;
    onShow?: (el: HTMLElement) => void;
    onHide?: (el: HTMLElement) => void;
}

const infiniteShowHide = (
    elements: NodeListOf<Element> | Element[] | string,
    options: InfiniteShowHideOptions = {},
) => {
    const {
        showDuration = 1500,
        onShow = (el: HTMLElement) => {
            el.style.visibility = 'visible'
        },
        onHide = (el: HTMLElement) => {
            el.style.visibility = 'hidden'
        },
    } = options

    const els = Array.from(
        Array.isArray(elements)
            ? elements
            : typeof elements === 'string'
              ? document.querySelectorAll(elements)
              : elements,
    ) as HTMLElement[]

    if (els.length === 0) return { stop: () => {} }

    let currentIndex = 0
    let timeoutId: ReturnType<typeof setTimeout> | null = null

    // Initialize: first image visible, rest hidden
    els.forEach((el, i) => {
        if (i === 0) {
            onShow(el)
        } else {
            onHide(el)
        }
    })

    function rotate() {
        onHide(els[currentIndex])
        currentIndex = (currentIndex + 1) % els.length
        onShow(els[currentIndex])

        timeoutId = setTimeout(rotate, showDuration)
    }

    // Start the first rotation after showDuration
    timeoutId = setTimeout(rotate, showDuration)

    return {
        stop: () => {
            if (timeoutId !== null) {
                clearTimeout(timeoutId)
                timeoutId = null
            }
        },
    }
}

const initInfiniteScroll = () => {
    const animatedContainer = document.querySelectorAll(
        '[data-infinit-hide-show]',
    )
    animatedContainer.forEach((element) => {
        const animatedElements = element.querySelectorAll('img')
        infiniteShowHide(animatedElements)
    })
}

export default initInfiniteScroll
