import { wordpressThemeJson } from '@roots/vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path'
import { defineConfig } from 'vite'
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    // Set base path for WordPress theme structure
    base: '/wp-content/themes/lookingglass/dist/',
    plugins: [
        tailwindcss(),
        wordpressThemeJson({
            // Base theme.json file to extend
            baseThemeJsonPath: './theme.json',
            // Output path for generated theme.json
            outputPath: 'theme.json',
            // Path to Tailwind config
            tailwindConfig: './tailwind.config.js',
            // Custom labels for the WordPress editor
            shadeLabels: {
                50: 'Lightest',
                100: 'Lighter',
                200: 'Light',
                300: 'Medium Light',
                400: 'Medium',
                500: 'Base',
                600: 'Medium Dark',
                700: 'Dark',
                800: 'Darker',
                900: 'Darkest',
            },
            fontLabels: {
                'arial-narrow': 'Arial Narrow',
                system: 'System Font',
            },
            // Enable all transformations
            disableTailwindColors: false,
            disableTailwindFonts: false,
            disableTailwindFontSizes: false,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'assets/images',
                    dest: '.',
                },
                {
                    src: 'assets/jquery-plugins/jquery.ripples-min.js',
                    dest: '.',
                }
            ],
        }),
    ],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            external: ['jquery'],
            input: {
                app: resolve(__dirname, 'assets/typescript/main.ts'),
            },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: assetInfo => {
                    if (assetInfo.name?.endsWith('.css')) {
                        return '[name].css'
                    }
                    return '[name].[ext]'
                },
                globals: {
                    jquery: 'jQuery',
                },
            },
        },
        manifest: true,
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'assets'),
        },
    },
    server: {
        host: 'localhost',
        port: 5174,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
})
