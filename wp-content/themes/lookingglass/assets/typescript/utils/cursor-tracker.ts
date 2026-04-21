interface TrackerOptions {
    throttleMs?: number
    onPositionUpdate?: (data: {
        x: number
        y: number
        isInside: boolean
        isDown: boolean
        element: HTMLElement
        hideOnTarget: boolean
        target: HTMLElement | null
    }) => void
}

interface CursorPosition {
    x: number
    y: number
    isInside: boolean
    isDown: boolean
    element: HTMLElement
    hideOnTarget: boolean
    target: HTMLElement | null
}

export const cursorTracker = (
    element: HTMLElement,
    options: TrackerOptions = {}
): { destroy: () => void; getCurrentPosition: () => CursorPosition } => {
    const {
        throttleMs = 16, // ~60fps
        onPositionUpdate,
    } = options

    let lastCallTime = 0
    let isDown = false
    let currentPosition: CursorPosition = {
        x: 0,
        y: 0,
        isInside: false,
        isDown: false,
        element,
        hideOnTarget: false,
        target: null,
    }

    const targetCheck = (target: EventTarget | null): boolean => {
        const el = target as HTMLElement | null
        return !!el?.closest?.('[data-no-custom-cursor]')
    }

    const updatePosition = (event: MouseEvent | TouchEvent) => {
        const now = Date.now()
        if (now - lastCallTime < throttleMs) return
        lastCallTime = now

        let clientX: number, clientY: number

        if (event instanceof MouseEvent) {
            clientX = event.clientX
            clientY = event.clientY
        } else {
            // Touch event
            if (event.touches.length === 0) return
            clientX = event.touches[0].clientX
            clientY = event.touches[0].clientY
        }

        const rect = element.getBoundingClientRect()
        const x = clientX - rect.left
        const y = clientY - rect.top

        const position: CursorPosition = {
            x,
            y,
            isInside: true,
            isDown,
            element,
            hideOnTarget: targetCheck(event.target),
            target: event.target as HTMLElement | null,
        }

        currentPosition = position

        if (onPositionUpdate) {
            onPositionUpdate(position)
        }
    }

    const handleMouseMove = (event: MouseEvent) => updatePosition(event)
    const handleTouchMove = (event: TouchEvent) => updatePosition(event)
    const handleMouseEnter = () => {
        currentPosition.isInside = true
    }
    const handleMouseLeave = () => {
        currentPosition.isInside = false
        isDown = false
        if (onPositionUpdate) {
            onPositionUpdate({
                ...currentPosition,
                isInside: false,
                isDown: false,
            })
        }
    }
    const handleMouseDown = () => {
        isDown = true
        currentPosition.isDown = true
        currentPosition.isInside = true
    }
    const handleMouseUp = () => {
        isDown = false
        currentPosition.isDown = false
        currentPosition.isInside = true
    }

    // Add event listeners
    element.addEventListener('mousemove', handleMouseMove, { passive: true })
    element.addEventListener('touchmove', handleTouchMove, { passive: true })
    element.addEventListener('mouseenter', handleMouseEnter)
    element.addEventListener('mouseleave', handleMouseLeave)
    element.addEventListener('mousedown', handleMouseDown)
    element.addEventListener('mouseup', handleMouseUp)

    // Return control methods
    return {
        destroy: () => {
            element.removeEventListener('mousemove', handleMouseMove)
            element.removeEventListener('touchmove', handleTouchMove)
            element.removeEventListener('mouseenter', handleMouseEnter)
            element.removeEventListener('mouseleave', handleMouseLeave)
            element.removeEventListener('mousedown', handleMouseDown)
            element.removeEventListener('mouseup', handleMouseUp)
        },
        getCurrentPosition: () => ({ ...currentPosition }),
    }
}
