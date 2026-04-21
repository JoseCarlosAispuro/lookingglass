import Navigation from '../classes/Navigation.ts'

const navigation = () => {
    const navigationElements = document.querySelectorAll('nav')

    navigationElements.forEach((navigationElement, _index) => {
        const navigationInstance = new Navigation(
            navigationElement as HTMLElement
        )
        navigationInstance.init()
    })
}

export default navigation
