module.exports = {
    static: {
        options: {
            progressive: true
        },
        files: [{
            expand: true,
            cwd: 'src/',
            src: ['images/*.{png,jpg,gif,ico}','images/**/*.{png,jpg,gif,ico}'],
            dest: 'build'
        }]
    },
    svg: {
        options: {
            svgoPlugins : [
                {
                    removeViewBox: true
                },
                {
                    removeDimensions:true
                },
                {
                    removeStyleElement:false
                },
                {
                    removeTitle:true
                }
            ]
        },
        files: [{
            expand: true,
            cwd: 'src/',
            src: ['images/svg/*.svg','images/**/*.svg'],
            dest: 'build/'
        }]
    }
};
