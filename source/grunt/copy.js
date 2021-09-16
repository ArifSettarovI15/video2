module.exports = {
    fonts: {
        files : [
            {
                expand : true,
                cwd : 'src/fonts',
                src : '**',
                dest : 'build/fonts'
            }
        ]
    },
    vendors: {
        files : [
            {
                expand : true,
                cwd : 'bower_components',
                src : '**',
                dest : '../assets/vendors'
            },
            {
                expand : true,
                cwd : 'src/js/',
                src : 'vendors/**',
                dest : '../assets'
            }
        ]
    },
    deploy: {
        files : [
            {
                cwd: 'build',
                expand : true,
                src : '**',
                dest : '../assets/'
            }
        ]
    }
};
