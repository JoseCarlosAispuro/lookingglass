import Player from '../../../node_modules/@vimeo/player'

declare global {
    interface Window {
        onYouTubeIframeAPIReady?: () => void
        YT?: {
            Player: new (
                element: Element | null,
                options: {
                    events?: {
                        onStateChange?: (e: { data: number }) => void
                    }
                }
            ) => void
        }
    }
}

const embedVideos = () => {
    const videosContainer = document.querySelectorAll('[data-embed-video]')

    videosContainer.forEach(videoContainer => {
        const video = videoContainer.querySelector('[data-video-player]')
        const posterContainer = videoContainer.querySelector(
            '[data-poster-image-container]'
        )

        const videoType = (video as HTMLElement).dataset.videoType

        if (videoType === 'vimeo') {
            try {
                const vimeoInstance = new Player(video as HTMLElement)
                vimeoInstance.on('play', () => {
                    ;(posterContainer as HTMLElement).style.display = 'none'
                    vimeoInstance.setVolume(0).catch(error => {
                        console.error('Could not set volume', error)
                    })
                })
            } catch (error) {
                console.log('Vimeo Loading Error::', (error as Error).message)
            }
        } else {
            window.onYouTubeIframeAPIReady = () => {
                if (window.YT) {
                    new window.YT.Player(video, {
                        events: {
                            onStateChange: (e: { data: number }) => {
                                if (e.data) {
                                    ;(
                                        posterContainer as HTMLElement
                                    ).style.display = 'none'
                                }
                            },
                        },
                    })
                }
            }
        }
    })
}

export default embedVideos
