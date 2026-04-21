# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress site built with a custom theme architecture that combines traditional WordPress development with modern frontend tooling. The main theme is located at `wp-content/themes/lookingglass/` and uses Vite for asset building, TypeScript, Tailwind CSS 4, and BladeOne templating engine.

## Development Setup

### Working Directory
All theme development happens in: `wp-content/themes/lookingglass/`

### Essential Commands

Navigate to the theme directory first: `cd wp-content/themes/lookingglass`

**Development:**
- `pnpm dev` - Start Vite dev server with HMR on localhost:5174
- `pnpm watch` - Build assets in watch mode
- `pnpm build` - Production build (compiles assets and copies theme.json)

**Code Quality:**
- `pnpm type-check` - Run TypeScript type checking
- `pnpm lint` - Lint code with Biome
- `pnpm lint:fix` - Auto-fix linting issues
- `pnpm format` - Format code with Biome
- `pnpm format:check` - Check code formatting
- `pnpm check` - Run all Biome checks (lint + format)
- `pnpm check:fix` - Auto-fix all Biome issues

**Git Hooks:**
- Pre-commit hook automatically runs `biome check --write` on staged JS/TS/JSON files
- Pre-commit hook runs `tsc --noEmit --skipLibCheck` on staged TypeScript files

## Architecture

### Theme Structure

```
wp-content/themes/lookingglass/
├── src/                          # PHP source files
│   ├── theme.php                 # Theme configuration, menus, block registration
│   ├── acf.php                   # ACF JSON sync configuration
│   ├── helpers.php               # Helper functions (dd, asset paths, nav menus)
│   └── blocks/                   # Custom Gutenberg blocks
│       ├── block.php             # Shared block render template
│       └── [block-name]/         # Individual block directories
│           └── block.json        # Block configuration
├── views/                        # BladeOne templates
│   ├── components/               # Block Blade templates
│   │   └── [block-name].blade.php
│   ├── layouts/                  # Layout templates
│   └── partials/                 # Reusable partials
├── assets/
│   ├── typescript/               # TypeScript source
│   │   ├── main.ts               # Entry point
│   │   ├── classes/              # TypeScript classes
│   │   └── components/           # TS component modules
│   ├── css/                      # CSS files
│   ├── fonts/                    # Web fonts
│   └── images/                   # Image assets
├── acf-json/                     # ACF field group definitions (auto-synced)
├── dist/                         # Vite build output (git-ignored)
├── functions.php                 # Main entry (requires src files)
└── vite.config.ts               # Vite configuration
```

### Templating System

This theme uses **BladeOne** (Laravel Blade syntax) for templating instead of traditional WordPress PHP templates. BladeOne is loaded via the `wp-content/mu-plugins/blade-one/` plugin.

**Key conventions:**
- Block templates are in `views/components/[block-name].blade.php`
- Blocks are rendered via `src/blocks/block.php` which calls `view("components.{$blockName}", $data)`
- Use Blade syntax: `{{ $variable }}`, `@if`, `@foreach`, etc.

### Custom Gutenberg Blocks

Blocks follow the ACF Blocks pattern:

1. **Block Definition**: `src/blocks/[block-name]/block.json`
   - Defines block metadata, ACF integration
   - Points to shared `renderTemplate: "../block.php"`

2. **Block Registration**: Automatic via `init_gutenberg_blocks()` in `src/theme.php`
   - Scans `src/blocks/` directory
   - Registers all subdirectories as block types

3. **Block Rendering**: `src/blocks/block.php`
   - Handles preview images in editor
   - Merges ACF fields with block attributes
   - Renders Blade template: `views/components/[block-name].blade.php`

4. **Block Template**: `views/components/[block-name].blade.php`
   - Receives merged ACF data and block attributes
   - Uses Blade syntax for output

### ACF (Advanced Custom Fields)

- ACF Pro is loaded as a must-use plugin: `wp-content/mu-plugins/advanced-custom-fields-pro/`
- Field groups are stored as JSON in `acf-json/` for version control
- ACF sync is configured in `src/acf.php`
- All custom blocks use ACF for field management

### Asset Building

**Vite Configuration:**
- Entry point: `assets/typescript/main.ts`
- Output: `dist/` directory
- Generates manifest for cache-busted assets
- Copies images from `assets/images/` to `dist/images/`

**Asset Loading:**
- `get_hashed_asset('/app.css')` - Get versioned CSS path
- `get_hashed_asset('/app.js')` - Get versioned JS path
- Assets are enqueued in `remove_wp_global_styles()` function
- Production uses Vite manifest, dev uses HMR

**WordPress theme.json Integration:**
- Uses `@roots/vite-plugin` to sync Tailwind config with `theme.json`
- Build command includes: `cp dist/theme.json theme.json`
- This enables Tailwind colors/fonts in Gutenberg editor

### Styling

**Tailwind CSS 4:**
- Configuration: `tailwind.config.js` (JavaScript for compatibility with @roots/vite-plugin)
- Custom colors: `primary.black`, `primary.white`, `primary.red`, `primary.pink`, `primary.green`, `primary.yellow`
- Custom fonts: `font-arial-narrow`, `font-system`
- Custom spacing: `max-w-content` (620px), `max-w-wide` (1000px)
- Content paths include all PHP, Blade, and TS files

**Editor Styles:**
- Custom styles injected into Gutenberg editor via `setup_gutenberg_preview_stylesheets()`
- Uses same CSS as frontend for WYSIWYG accuracy

### Navigation Menus

Four menu locations registered (defined in `src/theme.php`):
- `NAV_MENUS_MAIN` - Main navigation
- `NAV_MENUS_FOOTER` - Footer
- `NAV_MENUS_SOCIAL` - Social media
- `NAV_MENUS_PRIVACY` - Privacy

Helper functions:
- `get_nav_menu_by_location($location, $args)` - Render menu by location
- `get_nav_menu_items_by_location($location)` - Get menu items array

### WordPress Customizations

**Block Restrictions:**
- `set_allowed_block_types()` controls which blocks are available per post type
- Pages/services/security/framework: Only ACF blocks (except legal-page template)
- Legal pages: Limited core blocks (headings, paragraphs, lists, etc.)
- Posts: Core content blocks (embed, quote, gallery, image, etc.)

**Admin Access:**
- `restrict_admin_with_redirect()` restricts admin to administrators only
- Non-admins redirected to homepage when accessing /wp-admin

**Login Screen:**
- Custom logo via WordPress Customizer: `login_screen_logo` setting
- Applied via `set_login_screen_logo()`

## Helper Functions

Located in `src/helpers.php`:

- `dd(...$args)` - Dump and die for debugging
- `optional($value)` - Laravel-style optional helper
- `redirect_404()` - Manually trigger 404
- `get_asset_path($fileName)` - Get asset path from dist/
- `get_hashed_asset($fileName)` - Get cache-busted asset from Vite manifest
- `get_nav_menu_by_location($location, $args)` - Get rendered menu HTML
- `get_nav_menu_items_by_location($location)` - Get menu items array

## Code Style

**TypeScript/JavaScript:**
- Formatter: Biome
- Indentation: 4 spaces
- Quotes: Single quotes
- Semicolons: As needed (ASI)
- Line width: 80 characters
- Configuration: `biome.json`

**TypeScript:**
- Strict mode enabled
- Path alias: `@/*` maps to `assets/*`
- Target: ES2020
- Module resolution: Bundler mode

**PHP:**
- Standard WordPress coding conventions
- Use BladeOne syntax in `.blade.php` files
- ACF field retrieval: `get_field()`, `get_fields()`

## Creating New Blocks

To add a new custom Gutenberg block:

1. Create directory: `src/blocks/[block-name]/`
2. Create `src/blocks/[block-name]/block.json`:
   ```json
   {
       "name": "acf/block-name",
       "title": "Block Name",
       "acf": {
           "mode": "edit",
           "renderTemplate": "../block.php"
       },
       "supports": {
           "anchor": true,
           "align": ["full"]
       }
   }
   ```
3. Create Blade template: `views/components/block-name.blade.php`
4. Create ACF field group in WordPress admin (auto-saves to `acf-json/`)
5. Block auto-registers on next page load

## Common Tasks

**Add new TypeScript module:**
- Create in `assets/typescript/` or `assets/typescript/classes/`
- Import in `assets/typescript/main.ts`
- Use `@/` alias for imports: `import { foo } from '@/typescript/module'`

**Add new Blade component:**
- Create in `views/components/` or `views/partials/`
- Include with: `@include('partials.component-name')`
- Pass data: `@include('partials.name', ['key' => $value])`

**Modify theme colors/fonts:**
- Edit `tailwind.config.js`
- Run `pnpm build` to regenerate `theme.json`
- New tokens available in both code and Gutenberg editor

**Debug ACF block:**
- Use `dd($field_name)` in Blade template
- Check `acf-json/` for field group configuration
- Preview image: Set `preview_image` field in block to show placeholder in editor

## Visual Component Patterns

### Blade Wrapper Pattern

All block templates use the shared wrapper:
```blade
@component('partials.block', compact('block_name', 'class_name'))
    {{-- Block content --}}
@endcomponent
```
- Set `$class_name` in `@php` block before the component call
- Always `@unset()` variables at the end of the template

### Color System

- Background: `bg-{$background_color}` where values are `white`, `black`, `black-100`
- Foreground: `text-(--app-fg-color)` — automatically adapts to background
- Borders: `border-(--app-fg-color)` — also adapts
- Opacity variants: `text-(--app-fg-color)/80`, `border-(--app-fg-color)/40`

### Typography Classes

**Headings** (Saans font):
`headline-1` through `headline-7` — each has responsive mobile/desktop sizes

**Display** (Cambon Condensed font):
`display-xs` (32→56px), `display-sm` (48→72px), `display-md` (64→96px)

**Body** (Saans font):
`body-sm` (16px), `body-md` (18→20px), `body-lg` (20→24px), `body-xl` (24→32px)

**Buttons** (Saans font):
`button-sm` (16px), `button-md` (20px), `button-lg` (20→24px)

**Fonts**: `font-saans` (primary body/heading), `font-cambon-condensed` (display)

### Container Classes

- `container-narrow`: 1024px max-width
- `container`: 1440px max-width (default)
- `container-wide`: 1676px max-width
- `container-ultrawide`: 1920px max-width

### Spacing Tokens

`xs`: 8px, `sm`: 16px, `md`: 24px, `lg`: 48px, `xl`: 80px, `gutter`: responsive (16–24px)

Use as: `p-sm`, `mt-lg`, `gap-md`, `px-gutter`, etc.

### Grid Pattern

Standard 12-column grid:
```html
<div class="md:grid md:grid-cols-12 md:gap-x-md">
    <div class="md:col-span-5 md:col-start-1">Left</div>
    <div class="md:col-span-6 md:col-start-7">Right</div>
</div>
```

### Image Handling

- ACF image fields return arrays; get ID via `$img['ID'] ?? ($img['id'] ?? null)`
- Use `wp_get_attachment_image($id, 'size', false, ['class' => '...', 'sizes' => '...'])` for responsive images
- Always set `sizes` attribute when max display width is known (e.g., `sizes="221px"`)
- Add `'loading' => 'lazy'` and `'aria-hidden' => 'true'` for decorative images

### Word Animation

```html
<h2 data-word-animate data-animate-preset="wordUp"
    data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1"
    class="headline-4 font-semibold uppercase">{{ $heading }}</h2>
```

**Presets**: `wordUp`, `fadeUp`, `fadeIn`, `slideLeft`, `slideRight`, `scaleUp`, `blurIn`, `bounce`, `rotateIn`

### Button/Link Partials

**Link** (`partials.link`): `$url`, `$label`, `$target`, `$link_size` (`sm`/`md`/`lg`), `$containerClass`, `$insideAnchor`
**Animated Link** (`partials.animated-link`): `$text`, `$url`, `$arrow`, `$target`, `$class`

### Rich Text Wrapper

```html
<div data-rich-text>
    {!! $content !!}
</div>
```
Auto-styles `<b>`, `<a>` (with external link icons), `<ul>` (disc lists). Modifiers: `.no-ul-spacing`

### Data Attribute Conventions

- Use `data-{component-name}` on root element for JS targeting (e.g., `data-contact-form`)
- Use `data-{component}-{child}` for child elements (e.g., `data-contact-submit`)

### AJAX Pattern

1. Embed nonce as `data-` attribute: `data-form-nonce="{{ wp_create_nonce('action_name') }}"`
2. Embed AJAX URL: `data-ajax-url="{{ admin_url('admin-ajax.php') }}"`
3. POST with `URLSearchParams` body including `action` and `nonce`
4. Handler: verify nonce with `wp_verify_nonce()`, sanitize inputs, return `wp_send_json_success()`/`wp_send_json_error()`
5. Register with `add_action('wp_ajax_*')` and `add_action('wp_ajax_nopriv_*')`

### Accessibility Patterns

- Hidden labels: `<label class="sr-only">` for inputs with visible placeholders
- `aria-required="true"` on required fields
- `aria-describedby` linking inputs to error message elements
- `role="alert"` on error messages for screen reader announcements
- `aria-hidden="true"` on decorative images
- `aria-expanded` for toggle/accordion states

## WordPress Configuration Notes

- jQuery is deregistered on frontend (`remove_wp_global_styles()`)
- Admin bar hidden for non-administrators
- JSON uploads enabled for media library
- Block styles support enabled for Gutenberg
- Post thumbnails enabled