import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],

  // Replace process.env.NODE_ENV in browser UMD builds (webpack did this automatically).
  define: {
    'process.env.NODE_ENV': JSON.stringify('production')
  },

  resolve: {
    alias: { '@': path.resolve(__dirname, 'src') },
    extensions: ['.mjs', '.js', '.json', '.vue']
  },

  css: {
    preprocessorOptions: {
      scss: {
        additionalData: (content, filepath) => {
          if (filepath.includes('src/scss')) return content
          return `@import "${path.resolve(__dirname, 'src/scss/global.scss')}";\n${content}`
        },
        includePaths: [path.resolve(__dirname, 'node_modules')]
      }
    }
  },

  build: {
    lib: {
      entry: path.resolve(__dirname, 'src/main.js'),
      name: 'activity_finder_4',
      formats: ['umd'],
      fileName: () => 'activity_finder_4.umd.min.js'
    },
    rollupOptions: {
      // D4: Vue 3 + Vue Router bundled inside UMD (no window.Vue conflict with AF3/CF).
      // W4: bootstrap-vue removed from externals (replaced with hand-rolled components).
      external: ['axios'],
      output: {
        globals: {
          axios: 'axios'
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') return 'activity_finder_4.css'
          return assetInfo.name
        }
      }
    }
  }
})
