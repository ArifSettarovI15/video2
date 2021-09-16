module.exports = {
    browserSync: {
        bsFiles: {
            src : [
                '../assets/css/**/*.css',
                '../assets/js/**/*.js',
                '../app/views/**/*.twig',
                '../app/**/*.php'
            ]
        },
        options: {
            watchTask: true,
            proxy: "udarnik.tiger2"
        }
    }
};
