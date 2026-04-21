import Modal from "../classes/Modal"

const init = () => {
    const modal = document.querySelector('[data-modal-instance]')
    const triggers = document.querySelectorAll('[data-modal-target]')

    if(!modal || !triggers) return false

    new Modal(modal as HTMLElement, triggers as NodeListOf<HTMLElement>).init()
}

export default init