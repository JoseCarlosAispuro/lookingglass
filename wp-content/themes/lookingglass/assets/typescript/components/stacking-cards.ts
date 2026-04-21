import StackingCards from "../classes/StackingCards.ts";

const Init = () => {
    const stackingCardsElements: NodeListOf<HTMLElement> = document.querySelectorAll('[data-stacking-cards]')

    stackingCardsElements.forEach((stackingCardsElement) => {
        const stackingCardsClassInstance = new StackingCards(stackingCardsElement)
        stackingCardsClassInstance.init()
    })
}

export default Init
