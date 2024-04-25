/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors:{
        "main" : "#EE7823",
        "bd" : "#737373",
        "proceed": "#18D742"
      }
    },
  },
  plugins: [],
}