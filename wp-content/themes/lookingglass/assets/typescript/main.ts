// Main TypeScript entry point
import '../css/main.css'
import marqueeHero from '@/typescript/components/marquee-hero.ts'
import accordion from './components/accordion-init.ts'
import backToTop from './components/back-to-top.ts'
import carousel from './components/carousel.ts'
import customCarousel from './components/custom-carousel.ts'
import customCursor from './components/custom-cursor.ts'
import embedVideos from './components/embed-videos.ts'
import footerAccordion from './components/footer-accordion.ts'
import heroVideo from './components/hero-video.ts'
import iconCta from './components/icon-cta.ts'
import animatedInfiniteShowHide from './components/inifinite-show-hide.ts'
import jobsListing from './components/jobs-listing.ts'
import nav from './components/main-navigation'
import mediaGallery from './components/media-gallery.ts'
import mobileAccordion from './components/mobile-accordion.ts'
import modal from './components/modal.ts'
import pageLoader from './components/page-loader.ts'
import playCalendar from './components/play-calendar.ts'
import playsByMember from './components/plays-by-member.ts'
import playsGrid from './components/plays-grid.ts'
import rippleBanner from './components/ripple-banner.ts'
import scrollBackground from './components/scroll-background.ts'
import scrolledPinFocusAreas from './components/scrolled-pin-focus-areas.ts'
import slidesDisplay from './components/slides-display.ts'
import stackingCards from './components/stacking-cards.ts'
import tabs from './components/tabs.ts'
import timedStepsMedia from './components/timed-steps-media.ts'
import timeline from './components/timeline-init.ts'
import whatsonCarousel from './components/whatson-carousel.ts'
import { autoAnimateWords } from './utils/text-animation-manager.ts'

// WordPress theme initialization
document.addEventListener('DOMContentLoaded', () => {
    console.log('UI Blocks theme loaded with Vite + TypeScript')
    // Initialize loader first, before other components
    pageLoader()
    heroVideo()
    init()
    stackingCards()
    animatedInfiniteShowHide()
    customCursor()
    carousel()
    whatsonCarousel()
    marqueeHero()
    embedVideos()
    autoAnimateWords()
    backToTop()
    footerAccordion()
    scrollBackground()
    rippleBanner()
    tabs()
    iconCta()
    jobsListing()
    customCarousel()
    accordion()
    modal()
    playsGrid()
    timedStepsMedia()
    timeline()
    scrolledPinFocusAreas()
    mediaGallery()
    slidesDisplay()
    playsByMember()
    playCalendar()
    mobileAccordion()
})

function init(): void {
    nav()
}
