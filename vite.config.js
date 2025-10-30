import {defineConfig} from 'vite'

export default defineConfig(({command}) => {
    return {
        server: {
            host: '0.0.0.0',
            port: 5173,
        },
        alias: {
            alias: [{find: '@', replacement: './client/src'}],
        },
        base: './',
        build: {
          // cssCodeSplit: false,
            outDir: './client/dist',
            manifest: false,
            sourcemap: true,
            rollupOptions: {
                input: {
                    'main.js': './client/src/js/main.js',
                    'main.css': './client/src/scss/main.scss'
                },
                output: {
                    entryFileNames: '[name]',
                    chunkFileNames: '[name]',
                    assetFileNames: '[name].[ext]',
                }
            },
        },
        plugins: [],
        css: {
            devSourcemap: true,
        }
    }
})
