import MotionWordAnimator, { type WordAnimationConfig } from '../classes/TextAnimator';
import { cubicBezier } from 'motion';

export type { WordAnimationConfig } from '../classes/TextAnimator';

// Predefined cubicBezier easing functions
export const easingFunctions = {
    // Standard curves
    linear: cubicBezier(0, 0, 1, 1),
    easeIn: cubicBezier(0.42, 0, 1, 1),
    easeOut: cubicBezier(0, 0, 0.58, 1),
    easeInOut: cubicBezier(0.42, 0, 0.58, 1),

    // Material Design curves
    standard: cubicBezier(0.4, 0, 0.2, 1),
    decelerate: cubicBezier(0, 0, 0.2, 1),
    accelerate: cubicBezier(0.4, 0, 1, 1),

    // Bounce curves
    bounceOut: cubicBezier(0.34, 1.56, 0.64, 1),

    // Elastic curves
    elasticOut: cubicBezier(0.68, -0.55, 0.265, 1.55),

    // Custom curves
    smooth: cubicBezier(0.25, 0.1, 0.25, 1),
    snappy: cubicBezier(0.2, 0.8, 0.2, 1),
    dramatic: cubicBezier(0.4, 0, 0, 1),

    // Create your own
    createCustom: (p1: number, p2: number, p3: number, p4: number) =>
        cubicBezier(p1, p2, p3, p4)
};

// Word animation presets
export const wordPresets = {
    fadeUp: {
        from: { opacity: 0, y: 30 },
        to: { opacity: 1, y: 0 },
        duration: 0.8,
        easing: {
            type: 'cubicBezier',
            p1: 0.25,
            p2: 0.1,
            p3: 0.25,
            p4: 1
        },
        stagger: 0.1
    },

    fadeIn: {
        from: { opacity: 0 },
        to: { opacity: 1 },
        duration: 0.6,
        easing: {
            type: 'cubicBezier',
            p1: 0.42,
            p2: 0,
            p3: 0.58,
            p4: 1
        },
        stagger: 0.08
    },

    slideLeft: {
        from: { opacity: 0, x: -40 },
        to: { opacity: 1, x: 0 },
        duration: 0.7,
        easing: {
            type: 'cubicBezier',
            p1: 0.34,
            p2: 1.56,
            p3: 0.64,
            p4: 1
        },
        stagger: 0.12
    },

    slideRight: {
        from: { opacity: 0, x: 40 },
        to: { opacity: 1, x: 0 },
        duration: 0.7,
        easing: {
            type: 'cubicBezier',
            p1: 0.34,
            p2: 1.56,
            p3: 0.64,
            p4: 1
        },
        stagger: 0.12
    },

    scaleUp: {
        from: { opacity: 0, scale: 0.5 },
        to: { opacity: 1, scale: 1 },
        duration: 0.6,
        easing: {
            type: 'cubicBezier',
            p1: 0.68,
            p2: -0.55,
            p3: 0.265,
            p4: 1.55
        },
        stagger: 0.09
    },

    blurIn: {
        from: { opacity: 0, filter: 'blur(10px)' },
        to: { opacity: 1, filter: 'blur(0px)' },
        duration: 0.9,
        easing: {
            type: 'cubicBezier',
            p1: 0.25,
            p2: 0.1,
            p3: 0.25,
            p4: 1
        },
        stagger: 0.08
    },

    bounce: {
        from: { opacity: 0, y: -50, scale: 0.3 },
        to: { opacity: 1, y: 0, scale: 1 },
        duration: 0.8,
        easing: {
            type: 'spring',
            stiffness: 200,
            damping: 15,
            mass: 1
        },
        stagger: 0.15
    },

    rotateIn: {
        from: { opacity: 0, rotate: -10 },
        to: { opacity: 1, rotate: 0 },
        duration: 0.7,
        easing: {
            type: 'cubicBezier',
            p1: 0.68,
            p2: -0.55,
            p3: 0.265,
            p4: 1.55
        },
        stagger: 0.1
    },

    wordUp: {
        from: { opacity: 0, y: 80 },
        to: { opacity: 1, y: 0 },
        duration: 1.5,
        delay: 0,
        easing: {
            type: 'cubicBezier',
            p1: 0.82,
            p2: 0,
            p3: 0.24,
            p4: 1.02
        },
        stagger: 0.1
    }

} as const;

class MotionWordManager {
    private instances: Map<HTMLElement, MotionWordAnimator> = new Map();

    create(
        element: HTMLElement | string,
        config: WordAnimationConfig = {}
    ): MotionWordAnimator | null {
        const el = typeof element === 'string'
            ? document.querySelector<HTMLElement>(element)
            : element;

        if (!el) {
            console.warn(`Element not found: ${element}`);
            return null;
        }

        if (this.instances.has(el)) {
            this.destroy(el);
        }

        const instance = new MotionWordAnimator(el, config);
        this.instances.set(el, instance);
        return instance;
    }

    createFromPreset(
        element: HTMLElement | string,
        preset: keyof typeof wordPresets,
        options: Partial<WordAnimationConfig> = {}
    ): MotionWordAnimator | null {
        const presetConfig = wordPresets[preset];
        return this.create(element, { ...presetConfig, ...options });
    }

    createWithCubicBezier(
        element: HTMLElement | string,
        p1: number,
        p2: number,
        p3: number,
        p4: number,
        config: Omit<WordAnimationConfig, 'easing'> = {}
    ): MotionWordAnimator | null {
        return this.create(element, {
            ...config,
            easing: {
                type: 'cubicBezier',
                p1,
                p2,
                p3,
                p4
            }
        });
    }

    createWithSpring(
        element: HTMLElement | string,
        springConfig?: { stiffness?: number; damping?: number; mass?: number },
        config: Omit<WordAnimationConfig, 'easing'> = {}
    ): MotionWordAnimator | null {
        return this.create(element, {
            ...config,
            easing: {
                type: 'spring',
                stiffness: springConfig?.stiffness || 200,
                damping: springConfig?.damping || 15,
                mass: springConfig?.mass || 1
            }
        });
    }

    autoInitialize(selector = '[data-word-animate]'): void {
        const elements = document.querySelectorAll<HTMLElement>(selector);

        elements.forEach((element) => {
            // Skip elements that are not visible (display: none or hidden)
            const style = window.getComputedStyle(element);
            if (style.display === 'none' || style.visibility === 'hidden') {
                return;
            }

            // Also check if any parent is hidden
            let parent = element.parentElement;
            let parentHidden = false;
            while (parent) {
                const parentStyle = window.getComputedStyle(parent);
                if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') {
                    parentHidden = true;
                    break;
                }
                parent = parent.parentElement;
            }
            if (parentHidden) return;

            const preset = element.dataset.animatePreset as keyof typeof wordPresets || 'fadeUp';

            const config: any = {};

            // Parse easing from data attributes
            if (element.dataset.animateEasing && element.dataset.animateEasing.startsWith('cubicBezier:')) {
                const points = element.dataset.animateEasing.replace('cubicBezier:', '').split(',').map(Number);
                if (points.length === 4) {
                    config.easing = {
                        type: 'cubicBezier',
                        p1: points[0],
                        p2: points[1],
                        p3: points[2],
                        p4: points[3]
                    };
                }
            }

            // Parse other attributes
            if (element.dataset.animateDelay) {
                config.delay = parseFloat(element.dataset.animateDelay);
            }
            if (element.dataset.animateStagger) {
                config.stagger = parseFloat(element.dataset.animateStagger);
            }
            if (element.dataset.animateDuration) {
                config.duration = parseFloat(element.dataset.animateDuration);
            }
            if (element.dataset.animateThreshold) {
                config.threshold = parseFloat(element.dataset.animateThreshold);
            }
            if (element.dataset.animateTriggerOnce !== undefined) {
                config.triggerOnce = element.dataset.animateTriggerOnce === 'true';
            }

            this.createFromPreset(element, preset, config);
        });
    }

    get(element: HTMLElement | string): MotionWordAnimator | undefined {
        const el = typeof element === 'string'
            ? document.querySelector<HTMLElement>(element)
            : element;

        return el ? this.instances.get(el) : undefined;
    }

    setEasing(element: HTMLElement | string, easing: WordAnimationConfig['easing']): void {
        const animator = this.get(element);
        animator?.setEasing(easing);
    }

    setCubicBezier(element: HTMLElement | string, p1: number, p2: number, p3: number, p4: number): void {
        const animator = this.get(element);
        animator?.setCubicBezier(p1, p2, p3, p4);
    }

    playAll(): Promise<void[]> {
        const promises: Promise<void>[] = [];
        this.instances.forEach(animator => {
            promises.push(animator.play());
        });
        return Promise.all(promises);
    }

    resetAll(): void {
        this.instances.forEach(animator => animator.reset());
    }

    destroy(element?: HTMLElement | string): void {
        if (element) {
            const el = typeof element === 'string'
                ? document.querySelector<HTMLElement>(element)
                : element;

            if (el && this.instances.has(el)) {
                this.instances.get(el)?.destroy();
                this.instances.delete(el);
            }
        } else {
            this.instances.forEach(animator => animator.destroy());
            this.instances.clear();
        }
    }
}

// Singleton instance
const wordManager = new MotionWordManager();

// Export functions
export function animateWords(
    element: HTMLElement | string,
    config: WordAnimationConfig = {}
): MotionWordAnimator | null {
    return wordManager.create(element, config);
}

export function animateWordsPreset(
    element: HTMLElement | string,
    preset: keyof typeof wordPresets = 'fadeUp',
    options: Partial<WordAnimationConfig> = {}
): MotionWordAnimator | null {
    return wordManager.createFromPreset(element, preset, options);
}

export function animateWithCubicBezier(
    element: HTMLElement | string,
    p1: number,
    p2: number,
    p3: number,
    p4: number,
    config: Omit<WordAnimationConfig, 'easing'> = {}
): MotionWordAnimator | null {
    return wordManager.createWithCubicBezier(element, p1, p2, p3, p4, config);
}

export function animateWithSpring(
    element: HTMLElement | string,
    springConfig?: { stiffness?: number; damping?: number; mass?: number },
    config: Omit<WordAnimationConfig, 'easing'> = {}
): MotionWordAnimator | null {
    return wordManager.createWithSpring(element, springConfig, config);
}

export function autoAnimateWords(selector?: string): void {
    wordManager.autoInitialize(selector);
}

export function getWordAnimation(element: HTMLElement | string): MotionWordAnimator | undefined {
    return wordManager.get(element);
}

export { MotionWordAnimator, MotionWordManager };
