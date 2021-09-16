module.exports = {
    all: {
        files: [{
            expand: true,
            cwd: 'build/js',
            src: 'frontend.js',
            dest: 'build/js',
            ext: '.min.js'
        }
        ]
    }
};
