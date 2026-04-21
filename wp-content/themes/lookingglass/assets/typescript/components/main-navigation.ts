import Navigation from '../classes/Navigation'

const init = () => {
    const mainNavigation: HTMLElement | null = document.querySelector(
        '[data-component-id="main-navigation"]'
    )

    if (mainNavigation) {
        try {
            new Navigation(mainNavigation).init()
        } catch (e) {
            // eslint-disable-next-line no-console
            console.log(
                'Error initializing main navigation\n',
                (e as Error).message
            )
        }
    }
}

export default init
