type MarqueeRoot = HTMLElement;

function setupMarquee(root: MarqueeRoot): void {
    const track = root.querySelector<HTMLElement>("[data-marquee-track]");
    if (!track) return;

    const textEl = track.querySelector<HTMLElement>("[data-marquee-text]");
    if (!textEl) return;

    const speedAttr = root.getAttribute("data-marquee-speed");
    const pxPerSecond = speedAttr ? Number(speedAttr) : 70;

    if (!Number.isFinite(pxPerSecond) || pxPerSecond <= 0) return;

    // Get dimensions
    const containerWidth = root.offsetWidth;
    const textWidth = textEl.getBoundingClientRect().width;

    if (!textWidth || textWidth < 10) return;

    // Calculate how many copies needed to fill viewport + 1 extra for seamless loop
    const copiesNeeded = Math.ceil(containerWidth / textWidth) + 1;

    // Remove previously cloned elements (keep only the original)
    const clones = track.querySelectorAll("[data-marquee-clone]");
    clones.forEach((clone) => clone.remove());

    // Clone text elements to fill the space
    for (let i = 0; i < copiesNeeded; i++) {
        const clone = textEl.cloneNode(true) as HTMLElement;
        clone.setAttribute("data-marquee-clone", "true");
        clone.setAttribute("aria-hidden", "true");
        track.appendChild(clone);
    }

    // Set animation distance (one text block width in pixels)
    track.style.setProperty("--marquee-distance", `-${textWidth}px`);

    // Duration = distance / speed
    const durationSeconds = textWidth / pxPerSecond;
    track.style.setProperty("--marquee-duration", `${Math.max(3, durationSeconds)}s`);
}

export default function marqueeHero(): void {
    const roots = Array.from(document.querySelectorAll<HTMLElement>("[data-marquee]"));
    if (!roots.length) return;

    const run = () => {
        // Use requestAnimationFrame to ensure layout is complete
        requestAnimationFrame(() => {
            roots.forEach((root) => setupMarquee(root));
        });
    };

    // Initial run after fonts are ready
    if (document.fonts?.ready) {
        document.fonts.ready.then(run);
    } else {
        run();
    }

    // Recalculate on resize (debounced)
    let resizeTimeout: ReturnType<typeof setTimeout>;
    const ro = new ResizeObserver(() => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(run, 100);
    });
    roots.forEach((root) => ro.observe(root));

    // Fallback for late-loading content
    window.addEventListener("load", run, { once: true });
}
