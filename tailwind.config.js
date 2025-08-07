/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  safelist: [
    'evento-pago',
    'evento-pendente',
    'evento-apos-meia-noite',
    'fc-event', // fullcalendar base class
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
