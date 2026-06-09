import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [
    vue({
      // W3: @vue/compat MODE 2 — allows Vue 2 filter/mixin/component syntax in templates.
      // Remove when W5 filter rewrite is complete and @vue/compat is dropped.
      template: {
        compilerOptions: {
          compatConfig: { MODE: 2 }
        }
      }
    })
  ],

  // Replace process.env.NODE_ENV in browser UMD builds (webpack did this automatically).
  define: {
    'process.env.NODE_ENV': JSON.stringify('production')
  },

  resolve: {
    // W3: alias vue → @vue/compat for compat MODE 2 runtime support.
    // Remove alias when @vue/compat is dropped (after W5).
    alias: {
      vue: '@vue/compat',
      '@': path.resolve(__dirname, 'src')
    },
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
      // W3 flip: 'vue' and 'vue-router' removed from externals vs W2.
      external: ['axios', 'bootstrap-vue'],
      output: {
        globals: {
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
