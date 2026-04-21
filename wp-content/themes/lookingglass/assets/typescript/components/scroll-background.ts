import ScrollBackground from '../classes/ScrollBackground'

const Init = () => {
    const sections = Array.from(
        document.querySelectorAll<HTMLElement>(
            '[data-bg-color]:not([data-no-change="1"])'
        )
    )

    if (!sections.length) return

    new ScrollBackground(sections).init()
}

export default Init
