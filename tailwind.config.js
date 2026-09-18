/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./react/src/**/*.{js,jsx}",
  ],
  theme: {
    extend: {
      colors: {
        merah: {
          50: '#FBEEEE',
          100: '#F6D9D9',
          400: '#D6394A',
          500: '#C1121F',
          600: '#A30F1A',
          700: '#780000',
        },
        kertas: {
          DEFAULT: '#FFFFFF',
          off: '#FDFBF7',
        },
        tinta: '#1B1B1B',
        emas: '#E8A33D',
        hijau: '#2D6A4F',
      },
      fontFamily: {
        display: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['IBM Plex Mono', 'ui-monospace', 'monospace'],
      },
    },
  },
  plugins: [],
};
