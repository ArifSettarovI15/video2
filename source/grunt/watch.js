module.exports = {

    options: {
        spawn: false,
        livereload: true
    },

    scripts: {
        files: [
            'src/js/**/*.js',
            'src/modules/**/*.js',
            '../app/views/backend/components/**/*.js',
            '../app/views/modules/**/*.js',
            '../app/views/frontend/components/**/*.js',
            '../app/views/modules/**/*.js'
        ],
        tasks: [
            'up'
        ]
    },
    styles: {
        files: [
            'src/css/**/*.css',
            'src/css/**/*.scss',
            '../app/views/modules/**/**/*.css',
            '../app/views/modules/**/**/*.scss',
            '../app/views/frontend/elements/**/*.scss',
            '../app/views/frontend/components/**/*.scss',
            '../app/views/frontend/sections/**/**/*.scss',
            '../app/views/frontend/pages/**/**/*.scss',
            '../app/views/backend/elements/**/*.scss',
            '../app/views/backend/components/**/*.scss',
            '../app/views/backend/sections/**/**/*.scss',
            '../app/views/backend/pages/**/**/*.scss'
        ],
        tasks: [
            'up'
        ]
    },
    images: {
        files: [
            'src/images/**',
           'src/images/svg/*.svg',
            '../app/views/frontend/components/**/assets/images/**',
            '../app/views/backend/components/**/assets/images/**'
        ],
        tasks: [
            'images', 'up'
        ]
    }
};
