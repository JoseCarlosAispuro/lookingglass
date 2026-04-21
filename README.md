# Looking Glass Web

A modern WordPress site built with a custom theme architecture combining traditional WordPress development with contemporary frontend tooling.

## Tech Stack

- **WordPress** - Content management system
- **BladeOne** - Laravel Blade templating engine for PHP
- **Vite** - Fast build tool with HMR support
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS 4** - Utility-first CSS framework
- **ACF Pro** - Advanced Custom Fields for Gutenberg blocks
- **Biome** - Fast formatter and linter

## Project Structure

```
lookingglass-web/
├── wp-content/
│   ├── themes/lookingglass/     # Main custom theme
│   │   ├── src/                 # PHP source files
│   │   │   ├── blocks/          # Custom Gutenberg blocks
│   │   │   ├── theme.php        # Theme configuration
│   │   │   ├── acf.php          # ACF configuration
│   │   │   └── helpers.php      # Helper functions
│   │   ├── views/               # BladeOne templates
│   │   │   ├── components/      # Block templates
│   │   │   ├── layouts/         # Layout templates
│   │   │   └── partials/        # Reusable partials
│   │   ├── assets/
│   │   │   ├── typescript/      # TypeScript source
│   │   │   ├── css/             # Stylesheets
│   │   │   ├── fonts/           # Web fonts
│   │   │   └── images/          # Image assets
│   │   ├── acf-json/            # ACF field groups (version controlled)
│   │   └── dist/                # Build output (git-ignored)
│   ├── mu-plugins/              # Must-use plugins
│   │   ├── blade-one/           # BladeOne templating
│   │   └── advanced-custom-fields-pro/
│   └── plugins/                 # Standard WordPress plugins
└── CLAUDE.md                    # AI assistant instructions
```

## Getting Started

### Prerequisites

- PHP 8.0+
- Node.js 18+
- pnpm 10+
- WordPress 6.0+
- Local WordPress development environment (Local, MAMP, Docker, etc.)

### Installation

1. Clone the repository into your WordPress installation:
   ```bash
   git clone <repository-url> lookingglass-web
   ```

2. Navigate to the theme directory:
   ```bash
   cd wp-content/themes/lookingglass
   ```

3. Install dependencies:
   ```bash
   pnpm install
   ```

4. Start the development server:
   ```bash
   pnpm dev
   ```

5. Activate the theme in WordPress admin.

## Development

All theme development happens in `wp-content/themes/lookingglass/`.

### Commands

| Command | Description |
|---------|-------------|
| `pnpm dev` | Start Vite dev server with HMR (localhost:5174) |
| `pnpm build` | Production build |
| `pnpm watch` | Build in watch mode |
| `pnpm type-check` | Run TypeScript type checking |
| `pnpm lint` | Lint code with Biome |
| `pnpm lint:fix` | Auto-fix linting issues |
| `pnpm format` | Format code with Biome |
| `pnpm check` | Run all Biome checks |
| `pnpm check:fix` | Auto-fix all Biome issues |

### Pre-commit Hooks

The project uses Husky with lint-staged to automatically:
- Run `biome check --write` on staged JS/TS/JSON files
- Run `tsc --noEmit --skipLibCheck` on staged TypeScript files

## Custom Gutenberg Blocks

The theme includes custom ACF-powered Gutenberg blocks:

- **Hero Block** - Main hero section
- **Secondary Hero Marquee** - Animated hero variant
- **Text Image Block** - Text with image layout
- **Two Columns** - Two column layout
- **Multi Card Carousel** - Swiper-powered carousel
- **Stacking Cards** - Animated stacking cards effect
- **Simple Content Callout** - Content highlight section
- **Shop Promo Banner** - Promotional banner
- **What's On Block** - Events/shows listing
- **Casting Apply** - Casting application form
- **Footer Block** - Footer section

### Creating a New Block

1. Create block directory: `src/blocks/[block-name]/`
2. Add `block.json` configuration
3. Create Blade template: `views/components/[block-name].blade.php`
4. Define ACF fields in WordPress admin (auto-syncs to `acf-json/`)

## Styling

The theme uses **Tailwind CSS 4** with custom design tokens:

### Colors
- `primary-black`: #13140e
- `primary-white`: #fbfbfb
- `primary-red`: #f2695c
- `primary-pink`: #e6a1d7
- `primary-green`: #219292
- `primary-yellow`: #f2c75b

### Fonts
- `font-arial-narrow` - Arial Narrow (primary)
- `font-system` - System font stack

### Spacing
- `max-w-content` / `w-content`: 620px
- `max-w-wide` / `w-wide`: 1000px

## Code Style

- **TypeScript/JavaScript**: Biome (4 spaces, single quotes)
- **PHP**: WordPress coding standards
- **Templates**: BladeOne syntax

## Navigation Menus

Four registered menu locations:
- Main navigation
- Footer
- Social media
- Privacy

## Contributing

1. Create a feature branch from `main`
2. Make your changes
3. Run `pnpm check:fix` before committing
4. Submit a pull request using the PR template

## License

Proprietary - All rights reserved.
