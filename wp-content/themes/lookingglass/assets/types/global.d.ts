type WPApiT = {
    url: string
    actions: Record<string, { action: string; nonce: string }>
}

declare const wpApi: WPApiT

type NavigationGalleryImage = {
    url: string
    alt: string
}

declare const navigationGallery: NavigationGalleryImage[]
