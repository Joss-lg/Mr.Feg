/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class', 

  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'luxury-bg': 'var(--bg-color)',
        'luxury-card': 'var(--card-color)',
        'luxury-border': 'var(--border-color)',
        'luxury-accent': '#3b82f6', 
        'luxury-text': 'var(--text-color)',
        'luxury-muted': 'var(--text-muted)',
      },
    },
  },
  plugins: [],
}