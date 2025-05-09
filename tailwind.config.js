/** @type {import('tailwindcss').Config} */

import defaultTheme from 'tailwindcss/defaultTheme'
import typography from '@tailwindcss/typography'
import forms from '@tailwindcss/forms'

module.exports = {
  presets: [
  ],
  content: [
    // You will probably also need these lines
    './resources/**/**/*.blade.php',
    './resources/**/**/*.js',
    './app/View/Components/**/**/*.php',
    './app/Livewire/**/**/*.php',

    './Modules/**/resources/**/**/*.blade.php',
    './Modules/**/resources/**/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    typography,
    forms
  ]
}
