module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
  darkMode: false, // or 'media' or 'class'
  theme: {
      fontFamily: {
          'sans': ['"Proxima Nova"', 'sans-serif'],
      },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
