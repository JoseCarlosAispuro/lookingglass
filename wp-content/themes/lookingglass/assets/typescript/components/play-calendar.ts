import PlayCalendar from '../classes/PlayCalendar'

const playCalendar = () => {
    const playCalendarElements = document.querySelectorAll(
        '[data-plays-calendar]'
    )
    playCalendarElements.forEach(calendar => {
        const PlayCalendarInstance = new PlayCalendar(calendar as HTMLElement)
        PlayCalendarInstance.init()
    })
}
export default playCalendar
