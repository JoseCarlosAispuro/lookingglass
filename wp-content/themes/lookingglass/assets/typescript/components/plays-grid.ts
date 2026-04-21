import PlaysGrid from "@/typescript/classes/PlaysGrid.ts";

const playsGrid = () => {
    const playsGridElements = document.querySelectorAll('[data-plays-grid]')

    playsGridElements.forEach((playsGridElement) => {
        const PlaysGridInstance = new PlaysGrid(playsGridElement as HTMLElement)
        PlaysGridInstance.init()
    })
}

export default playsGrid
