import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  define: {
    'process.env.NODE_ENV': JSON.stringify('production'),
    'process.env': '{}',
  },
  build: {
    outDir: '../public/assets/js',
    emptyOutDir: false,
    lib: {
      entry: 'src/main.jsx',
      formats: ['es'],
      fileName: () => 'app.js',
    },
  },
});