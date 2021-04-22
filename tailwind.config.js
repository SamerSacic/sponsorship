module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        boxShadow: {
          'outline': '0 0 0 3px rgba(52,144,220,0.5)',
          'outline-indigo': '0 0 0 3px rgba(79, 70, 229,0.5)',
        },
        extend: {},
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
