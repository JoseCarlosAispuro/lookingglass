import TimedStepsMedia from '../classes/TimedStepsMedia'

const init = () => {
    document
        .querySelectorAll<HTMLElement>(
            '[data-timed-steps-media]:not([data-initialized])'
        )
        .forEach(el => {
            new TimedStepsMedia(el).init()
        })
}

export default init
