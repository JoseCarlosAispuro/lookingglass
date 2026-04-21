import { animate, cubicBezier } from 'motion'
import { cursorTracker } from '../utils/cursor-tracker.ts'

const isTouchDevice = () => matchMedia('(pointer: coarse)').matches

const showCursor = (
    cursor: HTMLElement,
    cursorInner: HTMLElement,
    container: HTMLElement,
    left: number,
    top: number,
    isDown: boolean
) => {
    if (isDown) {
        cursor.style.left = `${left}px`
        cursor.style.top = `${top}px`
    } else {
        animate(
            cursor,
            { left: `${left}px`, top: `${top}px` },
            { ease: cubicBezier(0.22, 1, 0.36, 1), duration: 0.3 }
        )
    }
    cursorInner.style.opacity = '1'
    cursorInner.style.transform = 'scale(1)'
    container.classList.add('cursor-hidden')
}

const hideCursor = (cursorInner: HTMLElement, container: HTMLElement) => {
    cursorInner.style.opacity = '0'
    cursorInner.style.transform = 'scale(0)'
    container.classList.remove('cursor-hidden')
}

const init = () => {
    if (isTouchDevice()) return

    const customCursorContainers = document.querySelectorAll(
        '[data-custom-cursor]'
    )

    customCursorContainers.forEach(container => {
        const dot = container.querySelector('[data-custom-cursor]')
        const dotInner = container.querySelector('[data-custom-cursor-inner]')
        const htmlDot = dot as HTMLElement
        const htmlDotInner = dotInner as HTMLElement
        const htmlContainer = container as HTMLElement

        htmlDotInner.style.opacity = '0'
        htmlDotInner.style.transform = 'scale(0)'

        cursorTracker(htmlContainer, {
            onPositionUpdate: position => {
                if (htmlDot) {
                    if (
                        position.isInside &&
                        !position.hideOnTarget &&
                        !htmlContainer.hasAttribute('data-cursor-disabled')
                    ) {
                        showCursor(
                            htmlDot,
                            htmlDotInner,
                            htmlContainer,
                            position.x,
                            position.y,
                            position.isDown
                        )
                    } else {
                        hideCursor(htmlDotInner, htmlContainer)
                    }
                }
            },
        })
    })
}

export default init
