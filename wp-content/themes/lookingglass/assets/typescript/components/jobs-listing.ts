const Init = () => {
    const jobsListings: NodeListOf<HTMLElement> = document.querySelectorAll('[data-jobs-listing]')

    jobsListings.forEach((container) => {
        const batchSize = parseInt(container.dataset.batchSize || '4', 10)
        const totalJobs = parseInt(container.dataset.totalJobs || '0', 10)
        const loadMoreBtn = container.querySelector<HTMLButtonElement>('[data-load-more-btn]')
        const jobItems = container.querySelectorAll<HTMLElement>('[data-job-item]')

        if (!loadMoreBtn || jobItems.length === 0) return

        let visibleCount = batchSize

        const updateVisibility = () => {
            jobItems.forEach((item, index) => {
                if (index < visibleCount) {
                    item.classList.remove('hidden')
                }
            })

            if (visibleCount >= totalJobs) {
                loadMoreBtn.style.display = 'none'
            }
        }

        loadMoreBtn.addEventListener('click', () => {
            visibleCount += batchSize
            updateVisibility()
        })
    })
}

export default Init
