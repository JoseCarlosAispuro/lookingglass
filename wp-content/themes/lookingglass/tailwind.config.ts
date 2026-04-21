// @ts-expect-error
import type { Config } from 'tailwindcss'

export default {
    content: [
        './assets/**/*.{ts,tsx,js,jsx}',
        './**/*.php',
        './blocks/**/*.php',
        './views/**/*.{blade.php,php,html}',
        './templates/**/*.{html,php}',
        './template-parts/**/*.php',
        './src/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    black: '#13140e',
                    white: '#fbfbfb',
                    red: '#f2695c',
                    pink: '#e6a1d7',
                    green: '#219292',
                    yellow: '#f2c75b',
                },
            },
            fontFamily: {
                'arial-narrow': ['Arial Narrow', 'Arial', 'sans-serif'],
                system: [
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"Segoe UI"',
                    'Roboto',
                    'Oxygen-Sans',
                    'Ubuntu',
                    'Cantarell',
                    '"Helvetica Neue"',
                    'sans-serif',
                ],
            },
            maxWidth: {
                content: '620px',
                wide: '1000px',
            },
            spacing: {
                content: '620px',
                wide: '1000px',
            },
            fontWeight: {
                570: '570',
            },
        },
    },
    plugins: [],
} satisfies Config
