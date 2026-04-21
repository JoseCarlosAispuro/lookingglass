import { animate, spring, cubicBezier, type AnimationPlaybackControls } from 'motion';

type EasingType = 'easeIn' | 'easeOut' | 'easeInOut' | 'linear' | 'spring' | 'custom' | 'aggressive';

export interface WordAnimationConfig {
    delay?: number;
    stagger?: number;
    duration?: number;

    easing?: EasingType | {
        type: 'cubicBezier';
        p1: number;
        p2: number;
        p3: number;
        p4: number;
    } | {
        type: 'spring';
        stiffness?: number;
        damping?: number;
        mass?: number;
    };

    threshold?: number;
    rootMargin?: string;
    triggerOnce?: boolean;

    from?: {
        opacity?: number;
        y?: number;
        x?: number;
        scale?: number;
        rotate?: number;
        filter?: string;
        color?: string;
    };

    to?: {
        opacity?: number;
        y?: number;
        x?: number;
        scale?: number;
        rotate?: number;
        filter?: string;
        color?: string;
    };

    onStart?: () => void;
    onComplete?: () => void;
    onWordComplete?: (index: number, wordElement: HTMLElement) => void;
}

class MotionWordAnimator {
    private element: HTMLElement;
    private originalHTML: string;
    private config: Required<Omit<WordAnimationConfig, 'onStart' | 'onComplete' | 'onWordComplete'>> & {
        easing: NonNullable<WordAnimationConfig['easing']>;
        onStart: () => void;
        onComplete: () => void;
        onWordComplete: (index: number, wordElement: HTMLElement) => void;
    };

    private observer: IntersectionObserver | null = null;
    private wordElements: HTMLElement[] = [];
    private animationControls: AnimationPlaybackControls[] = [];
    private isAnimating = false;
    private hasAnimated = false;

    constructor(element: HTMLElement, config: WordAnimationConfig = {}) {
        this.element = element;
        this.originalHTML = element.innerHTML;

        this.config = {
            delay: config.delay || 0,
            stagger: config.stagger || 0.1,
            duration: config.duration || 0.6,
            easing: config.easing || 'aggressive',
            threshold: config.threshold || 0.1,
            rootMargin: config.rootMargin || '-50px 0px',
            triggerOnce: config.triggerOnce !== undefined ? config.triggerOnce : true,

            from: {
                opacity: 0,
                y: 20,
                x: 0,
                scale: 1,
                rotate: 0,
                filter: 'blur(0px)',
                color: 'currentColor',
                ...config.from
            },

            to: {
                opacity: 1,
                y: 0,
                x: 0,
                scale: 1,
                rotate: 0,
                filter: 'blur(0px)',
                color: 'currentColor',
                ...config.to
            },

            onStart: config.onStart || (() => {}),
            onComplete: config.onComplete || (() => {}),
            onWordComplete: config.onWordComplete || (() => {})
        };

        this.init();
    }

    private init(): void {
        this.splitIntoWords();
        this.applyInitialStyles();
        // Use requestAnimationFrame to ensure layout is computed before setting up observer
        // This prevents race conditions when multiple elements are initialized simultaneously
        requestAnimationFrame(() => {
            this.setupObserver();
        });
    }

    private splitIntoWords(): void {
        const text = this.element.textContent || '';

        // Split text into words, preserving spaces and punctuation
        const words = text.split(/(\s+)/).filter(word => word.trim().length > 0);

        // Store original display before clearing
        const computedStyle = window.getComputedStyle(this.element);
        const originalDisplay = computedStyle.display;
        const isBlockLevel = originalDisplay === 'block' || originalDisplay === 'flex' ||
                            originalDisplay === 'grid' || this.element.tagName.match(/^(H[1-6]|P|DIV|SECTION|ARTICLE)$/i);

        // Clear element
        this.element.innerHTML = '';

        this.element.style.cssText = `
            display: ${isBlockLevel ? 'block' : 'inline-block'};
            overflow: hidden;
            will-change: transform, opacity, filter;
        `;

        words.forEach((word, index) => {
            const wordSpan = document.createElement('span');
            wordSpan.className = 'motion-word';
            wordSpan.textContent = word;
            wordSpan.style.cssText = `
                display: inline-block;
                white-space: pre-wrap;
                will-change: transform, opacity, filter, color;
                backface-visibility: hidden;
                max-width: 100%;
                overflow-wrap: break-word;
            `;

            // Add spacing between words (but not after punctuation)
            if (index < words.length - 1 && !/[.,!?;:]$/.test(word)) {
                wordSpan.style.marginRight = '0.25em';
            }

            this.element.appendChild(wordSpan);
            this.wordElements.push(wordSpan);
        });
    }

    private applyInitialStyles(): void {
        this.wordElements.forEach(wordElement => {
            const { from } = this.config;

            wordElement.style.opacity = (from.opacity ?? 0).toString();

            // Build transform string
            const transforms: string[] = [];
            if (from.x !== 0) transforms.push(`translateX(${from.x}px)`);
            if (from.y !== 0) transforms.push(`translateY(${from.y}px)`);
            if (from.scale !== 1) transforms.push(`scale(${from.scale})`);
            if (from.rotate !== 0) transforms.push(`rotate(${from.rotate}deg)`);

            if (transforms.length > 0) {
                wordElement.style.transform = transforms.join(' ');
            }

            if (from.filter) {
                wordElement.style.filter = from.filter;
            }

            if (from.color && from.color !== 'currentColor') {
                wordElement.style.color = from.color;
            }
        });
    }

    private getEasing(): any {
        const easing = this.config.easing;

        if (typeof easing === 'string') {
            switch (easing) {
                case 'easeIn':
                    return cubicBezier(0.42, 0, 1, 1);
                case 'easeOut':
                    return cubicBezier(0, 0, 0.58, 1);
                case 'easeInOut':
                    return cubicBezier(0.42, 0, 0.58, 1);
                case 'linear':
                    return cubicBezier(0, 0, 1, 1);
                case 'spring':
                    return spring();
                case 'aggressive':
                    return cubicBezier(0.82, 0, 0.24, 1.02);
                default:
                    return cubicBezier(0, 0, 0.58, 1); // Default to easeOut
            }
        }

        if (typeof easing === 'object') {
            if (easing.type === 'cubicBezier') {
                return cubicBezier(easing.p1, easing.p2, easing.p3, easing.p4);
            } else if (easing.type === 'spring') {
                return spring(
                    easing.stiffness || 200,
                    easing.damping || 15
                );
            }
        }

        // Default fallback
        return cubicBezier(0, 0, 0.58, 1);
    }

    private setupObserver(): void {
        this.observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateIn();
                        if (this.config.triggerOnce) {
                            this.observer?.disconnect();
                        }
                    } else if (!this.config.triggerOnce) {
                        this.animateOut();
                    }
                });
            },
            {
                threshold: this.config.threshold,
                rootMargin: this.config.rootMargin
            }
        );

        this.observer.observe(this.element);
    }

    private async animateIn(): Promise<void> {
        if (this.isAnimating || (this.hasAnimated && this.config.triggerOnce)) {
            return;
        }

        this.isAnimating = true;
        this.hasAnimated = true;
        this.config.onStart();

        const easing = this.getEasing();
        const animationPromises = this.wordElements.map((wordElement, index) => {
            return new Promise<void>((resolve) => {
                setTimeout(async () => {
                    const { from, to, duration } = this.config;

                    const targets: Record<string, any> = {};

                    // Only animate properties that are different
                    if (from.opacity !== to.opacity) {
                        targets.opacity = [from.opacity, to.opacity];
                    }

                    if (from.y !== to.y) {
                        targets.y = [from.y, to.y];
                    }

                    if (from.x !== to.x) {
                        targets.x = [from.x, to.x];
                    }

                    if (from.scale !== to.scale) {
                        targets.scale = [from.scale, to.scale];
                    }

                    if (from.rotate !== to.rotate) {
                        targets.rotate = [from.rotate, to.rotate];
                    }

                    if (from.filter !== to.filter) {
                        targets.filter = [from.filter, to.filter];
                    }

                    if (from.color !== to.color) {
                        targets.color = [from.color, to.color];
                    }

                    const animation = animate(
                        wordElement,
                        targets,
                        {
                            duration,
                            ease: easing,
                            onComplete: () => {
                                // Performance optimization: remove will-change after animation
                                setTimeout(() => {
                                    wordElement.style.willChange = 'auto';
                                }, 100);

                                this.config.onWordComplete(index, wordElement);
                                resolve();
                            }
                        }
                    );

                    this.animationControls.push(animation);
                }, (this.config.delay + (index * this.config.stagger)) * 1000);
            });
        });

        await Promise.all(animationPromises);
        this.isAnimating = false;
        this.config.onComplete();
    }

    private async animateOut(): Promise<void> {
        if (this.isAnimating) return;

        this.isAnimating = true;

        const easing = this.getEasing();
        const animationPromises = this.wordElements.map((wordElement) => {
            const { from, duration } = this.config;

            const targets: Record<string, any> = {};

            if (from.opacity !== undefined) targets.opacity = from.opacity;
            if (from.y !== undefined) targets.y = from.y;
            if (from.x !== undefined) targets.x = from.x;
            if (from.scale !== undefined) targets.scale = from.scale;
            if (from.rotate !== undefined) targets.rotate = from.rotate;
            if (from.filter) targets.filter = from.filter;
            if (from.color) targets.color = from.color;

            return animate(
                wordElement,
                targets,
                { duration, ease: easing }
            ).finished;
        });

        await Promise.all(animationPromises);
        this.isAnimating = false;
    }

    // Public API Methods
    public async play(): Promise<void> {
        return this.animateIn();
    }

    public async reverse(): Promise<void> {
        return this.animateOut();
    }

    public reset(): void {
        this.cancel();
        this.wordElements.forEach(wordElement => {
            wordElement.style.willChange = 'transform, opacity, filter, color';
        });
        this.applyInitialStyles();
        this.hasAnimated = false;
    }

    public cancel(): void {
        this.animationControls.forEach(control => control.stop());
        this.animationControls = [];
        this.isAnimating = false;
    }

    public setEasing(easing: WordAnimationConfig['easing']): void {
        this.config.easing = easing || 'easeOut';
    }

    public setCubicBezier(p1: number, p2: number, p3: number, p4: number): void {
        this.config.easing = {
            type: 'cubicBezier',
            p1,
            p2,
            p3,
            p4
        };
    }

    public setSpring(config?: { stiffness?: number; damping?: number; mass?: number }): void {
        this.config.easing = {
            type: 'spring',
            stiffness: config?.stiffness || 200,
            damping: config?.damping || 15,
            mass: config?.mass || 1
        };
    }

    public updateConfig(newConfig: Partial<WordAnimationConfig>): void {
        Object.assign(this.config, newConfig);
        this.rebuild();
    }

    private rebuild(): void {
        this.cancel();
        this.wordElements = [];
        this.animationControls = [];
        this.splitIntoWords();
        this.applyInitialStyles();
    }

    public destroy(): void {
        this.cancel();

        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }

        this.element.innerHTML = this.originalHTML;
        this.element.style.cssText = '';

        this.wordElements = [];
        this.animationControls = [];
    }

    public getWordCount(): number {
        return this.wordElements.length;
    }

    public getAnimationState(): 'idle' | 'animating' | 'completed' {
        if (this.isAnimating) return 'animating';
        return this.hasAnimated ? 'completed' : 'idle';
    }
}

export default MotionWordAnimator;
