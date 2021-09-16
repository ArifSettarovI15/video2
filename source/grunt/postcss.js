module.exports = {
    dev: {
        processors: [
            require('autoprefixer')({
                browsers: ['last 2 versions']
            })
        ],
        options: {
            map: false,
            processors: [
                require('autoprefixer')({
                    browsers: ['last 2 versions']
                })
            ]
        },
        expand:true,
        src: 'build/css/*.css',
        filter: 'isFile'
    }
};