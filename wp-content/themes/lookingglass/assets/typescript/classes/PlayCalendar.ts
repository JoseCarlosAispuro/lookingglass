import { Calendar } from 'vanilla-calendar-pro'
import PlayCalendarDrawer from './PlayCalendarDrawer'

enum EventStatus {
    AVAILABlE = 'available',
    SOLD_OUT = 'sold_out',
    NA = 'na',
}

type FetchApiEventTimesResponseT = {
    data: {
        html: string
    }
    success: boolean
}

type EventTime = {
    day_status: EventStatus
    default_selected: boolean
    external_url: string
    price: string
    time_label: string
}

type EventT = {
    date: string
    has_events: boolean
    day_status: EventStatus
    day_status_text: string
    times: EventTime[]
}

type FetchApiEventsResponseT = {
    data: {
        events: EventT[]
    }
    success: boolean
}

class PlayCalendar {
    private readonly container: HTMLElement
    private events: EventT[]
    private drawerLoaded: boolean
    private disabledDates: Date[]
    private availableDates: Date[]
    private readonly playId: string
    private activeDate: Date | string
    private calendar: HTMLElement | null
    private calendarTimes: HTMLElement | null
    private readonly api: WPApiT = wpApi

    constructor(container: HTMLElement) {
        this.container = container
        this.events = []
        this.disabledDates = []
        this.availableDates = []
        this.activeDate = new Date()
        this.drawerLoaded = false
        this.playId = this.container.dataset.playId ?? ''
        this.calendar = this.container.querySelector('[data-calendar]')
        this.calendarTimes = this.container.querySelector(
            '[data-calendar-times]'
        )
    }

    replaceNavigationArrows = () => {
        const navigationArrows = this.container.querySelectorAll('.vc-arrow')

        navigationArrows.forEach((arrow, index) => {
            arrow.classList.add('material-symbols-outlined')
            arrow.textContent = index === 0 ? 'arrow_left' : 'arrow_right'
        })
    }

    shouldDisableEntireDay = (
        event: EventT,
        eventDateTime: number,
        activeDateTime: number
    ): boolean => {
        return (
            eventDateTime < activeDateTime ||
            !event.has_events ||
            event.day_status === EventStatus.SOLD_OUT ||
            event.day_status === EventStatus.NA
        )
    }

    areAllTimesDisabled = (event: EventT): boolean => {
        if (!event.times?.length) return false

        return event.times.every(
            time =>
                time.day_status === EventStatus.SOLD_OUT ||
                time.day_status === EventStatus.NA
        )
    }

    getEventsDates = () => {
        const today = new Date(this.activeDate)
        today.setHours(0, 0, 0, 0)
        const activeDateTime = today.getTime()

        this.events.forEach(event => {
            const eventDate = new Date(`${event.date}T00:00:00`)
            const eventDateTime = eventDate.getTime()

            // Check if date should be disabled
            if (
                this.shouldDisableEntireDay(
                    event,
                    eventDateTime,
                    activeDateTime
                )
            ) {
                this.disabledDates.push(eventDate)
                return
            }

            // Check if all times are disabled
            if (this.areAllTimesDisabled(event)) {
                this.disabledDates.push(eventDate)
                return
            }

            if (event.has_events) {
                this.availableDates.push(eventDate)
            }
        })
    }

    eventsDates = (dateEl: HTMLElement) => {
        const isDisabled = dateEl.getAttribute('data-vc-date-disabled')
        const dayDate = dateEl.getAttribute('data-vc-date')
        const dayDateInstance = new Date(dayDate ? `${dayDate}T00:00:00` : '')

        const isDateInArray = this.availableDates.some(
            date => date.getTime() === dayDateInstance.getTime()
        )

        if (isDisabled == null && isDateInArray) {
            const btnEl = dateEl.querySelector(
                '[data-vc-date-btn]'
            ) as HTMLButtonElement
            const day = btnEl.innerText
            btnEl.style.position = 'relative'
            btnEl.innerHTML = `
          <span>${day}</span>
          <span class="absolute bottom-1 w-2 h-2 bg-orange rounded-full"></span>
        `
        }
    }

    renderCalendar = () => {
        if (this.calendar) {
            this.getEventsDates()
            this.activeDate =
                this.availableDates && this.availableDates.length > 0
                    ? this.availableDates[0]
                    : new Date()

            const calendar = new Calendar(this.calendar, {
                type: 'default',
                dateToday: new Date(),
                selectedDates: [this.activeDate],
                firstWeekday: 0,
                // @ts-expect-error
                selectedMonth: this.activeDate.getMonth(),
                disableDates: this.disabledDates,
                selectionMonthsMode: 'only-arrows',
                onClickDate: self => {
                    this.activeDate = self.context.selectedDates[0]
                    this.requestEventTimes()
                },
                onCreateDateEls: (_self, dateEl) => this.eventsDates(dateEl),
            })

            calendar.init()
        }
    }

    createEventsTime = (htmlString: string) => {
        const node = document.createElement('div')
        node.innerHTML = htmlString
        this.calendarTimes?.replaceChildren(node)
    }

    requestEvents = () => {
        const { url } = this.api
        const { action, nonce } = this.api.actions.fetch_play_events

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                nonce: nonce,
                action: action,
                playId: this.playId,
            }),
        })
            .then(res => res.json())
            .then(({ data }: FetchApiEventsResponseT) => {
                this.events = data.events

                if (this.events) {
                    this.renderCalendar()
                    this.replaceNavigationArrows()
                    this.requestEventTimes()
                } else {
                    const mobileCalendar = document.querySelector(
                        '[data-mobile-calendar]'
                    )
                    mobileCalendar?.remove()
                }
            })
    }

    resetAllInputContainers = (inputContainers: NodeListOf<Element>) => {
        inputContainers.forEach(container => {
            ;(container as HTMLElement).classList.remove('bg-black-100')
        })
    }

    handleFormEvents = () => {
        const form = this.container.querySelector('[data-play-time-form]')
        const submitButton = form?.querySelector(
            '.cta-button'
        ) as HTMLAnchorElement
        const radioInputContainers = form?.querySelectorAll(
            '[data-radio-button-container]'
        )

        form?.addEventListener('change', e => {
            const inputElement = e.target as HTMLElement
            const inputElementURL = inputElement.dataset.optionUrl
            const inputElementLabel = inputElement.dataset.radioButtonLabel
            const targetGrandparent = inputElement.parentNode?.parentNode

            if (radioInputContainers)
                this.resetAllInputContainers(radioInputContainers)
            ;(targetGrandparent as HTMLElement).classList.add('bg-black-100')

            if (submitButton) {
                submitButton.href = inputElementURL ?? ''
                submitButton.classList.remove(
                    'bg-black-100',
                    'text-disabled',
                    'border-black-100',
                    'cursor-not-allowed',
                    'pointer-events-none',
                    '!transform-none'
                )
                submitButton.setAttribute('aria-disabled', 'false')
                submitButton.text = inputElementLabel ?? 'Book now'
            }
        })
    }

    requestEventTimes = () => {
        const { url } = this.api
        const { action, nonce } = this.api.actions.fetch_play_event_times

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                nonce: nonce,
                action: action,
                events: JSON.stringify(this.events),
                date:
                    typeof this.activeDate === 'string'
                        ? this.activeDate
                        : this.activeDate.toISOString(),
            }),
        })
            .then(res => res.json())
            .then(({ data }: FetchApiEventTimesResponseT) => {
                this.createEventsTime(data.html)
                this.handleFormEvents()

                if (this.availableDates && this.availableDates.length > 0) {
                    if (!this.drawerLoaded) {
                        const mobileCalendar = document.querySelector(
                            '[data-mobile-calendar]'
                        )
                        new PlayCalendarDrawer(
                            mobileCalendar as HTMLElement
                        ).init()
                    }
                    this.container.parentElement?.classList.remove('opacity-0')
                } else {
                    const mobileCalendar = document.querySelector(
                        '[data-mobile-calendar]'
                    )

                    this.container.parentElement?.remove()
                    mobileCalendar?.remove()
                }

                this.drawerLoaded = true
            })
    }

    init() {
        this.requestEvents()
    }
}

export default PlayCalendar
