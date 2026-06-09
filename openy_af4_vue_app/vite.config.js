import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue2'
import path from 'path'

export default defineConfig({
  plugins: [vue()],

  resolve: {
    alias: { '@': path.resolve(__dirname, 'src') },
    extensions: ['.mjs', '.js', '.json', '.vue']
  },

  css: {
    preprocessorOptions: {
      scss: {
        // Make Bootstrap SCSS + AF4 variables available in every component.
        // Skip injection for files already inside src/scss/ to avoid circular imports.
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
      // W2: keep same externals as vue.config.js (Vue 2 contract unchanged).
      // W3 flip: remove 'vue' and 'vue-router' to bundle Vue 3 (D1/D4).
      external: ['vue', 'vue-router', 'axios', 'bootstrap-vue'],
      output: {
        globals: {
          vue: 'Vue',
          'vue-router': 'VueRouter',
          axios: 'axios',
          'bootstrap-vue': 'BootstrapVue'
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') return 'activity_finder_4.css'
          return assetInfo.name
        }
      }
    }
  }
})
