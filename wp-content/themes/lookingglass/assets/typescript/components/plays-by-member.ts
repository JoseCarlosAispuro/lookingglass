import PlaysByMember from "@/typescript/classes/PlaysByMember";

const playsMember = () => {
    const playsByMemberEl = document.querySelectorAll('[data-plays-by-member]')

    if (!playsByMemberEl.length) return false;

    playsByMemberEl.forEach((playsSection) => {
        const loadMoreButton = playsSection.querySelector('[data-load-more-button]')

        if(loadMoreButton) {
            const playsByMember = new PlaysByMember(playsSection as HTMLElement, loadMoreButton as HTMLButtonElement)
            playsByMember.init()
        }
    })
}

export default playsMember
