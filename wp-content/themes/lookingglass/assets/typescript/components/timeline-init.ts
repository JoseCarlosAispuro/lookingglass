import Timeline from '../classes/Timeline'

const init = () => {
    document
        .querySelectorAll<HTMLElement>(
            '[data-timeline]:not([data-initialized])'
        )
        .forEach(el => {
            new Timeline(el).init()
        })
}

export default init
