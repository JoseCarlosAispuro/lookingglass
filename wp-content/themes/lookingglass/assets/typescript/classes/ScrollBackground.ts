class ScrollBackground {
    private sections: HTMLElement[];
    private observer: IntersectionObserver;

    constructor(sections: HTMLElement[]) {
        this.sections = sections;

        this.observer = new IntersectionObserver(this.onIntersect.bind(this), {
            root: null,
            rootMargin: '-55% 0px -45% 0px',
            threshold: 0
        });
    }

    public init() {
        this.sections.forEach(section => this.observer.observe(section));
    }

    private onIntersect(entries: IntersectionObserverEntry[]) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            const color = entry.target.getAttribute('data-bg-color');
            if (!color) return;

            this.updateGlobalTheme(color);
        });
    }

    private updateGlobalTheme(bg: string) {
        const isDark = this.isDarkColor(bg);
    
        const theme = isDark ? 'black' : 'white';
        const fg = isDark ? 'var(--color-white)' : 'var(--color-black)';
        const hlBg = isDark ? 'var(--color-orange)' : 'var(--color-black)';
        const hl = isDark ? 'var(--color-black)' : 'var(--color-white)' ;
    
        document.documentElement.style.setProperty('--app-bg-color', bg);
        document.documentElement.style.setProperty('--app-fg-color', fg);
        document.documentElement.style.setProperty('--app-hl-bg-color', hlBg);
        document.documentElement.style.setProperty('--app-hl-color', hl);
    
        document.body.setAttribute('data-dynamic-bg', 'true');
        document.body.setAttribute('data-dynamic-bg-theme', theme);
    }
    
    private isDarkColor(color: string): boolean {
        const c = color.toLowerCase();
    
        return (
            c === '#000' ||
            c === '#000000' ||
            c === 'black' ||
            c === 'rgb(0, 0, 0)'
        );
    }
}

export default ScrollBackground