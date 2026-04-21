import { scroll, animate } from "motion"

class StackingCards {
    private readonly container: HTMLElement
    private movementMatrix: string[][] = []
    public cards: NodeListOf<Element> | null

    constructor(container: HTMLElement) {
        this.container = container
        this.cards = this.container.querySelectorAll('[data-stacking-card]')
    }

    getMoveMatrix = async () => {
        this.cards?.forEach((_card, index) => {
            if(index === 0){
                return
            }
            const cardArrayPosition: string[] = []
            const cardsLength = this.cards?.length ?? 0
            for(let i = 1; i <= cardsLength; i ++) {
                if(i <= index){
                    cardArrayPosition.push('100%')
                } else {
                    cardArrayPosition.push('0')
                }
            }

            this.movementMatrix.push(cardArrayPosition)
        })
    }

    animateCards = async () => {
        this.cards?.forEach((card, index) => {
            if(index !== 0) {
                scroll(animate(card, {y: this.movementMatrix[index-1]}, {ease: 'linear'}), {target: this.container})
            }
        })
    }

    init = async () => {
        await this.getMoveMatrix()
        this.animateCards()
    }
}

export default StackingCards
