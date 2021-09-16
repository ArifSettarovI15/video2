module.exports = {
    grunticon: {
        files: [{
            expand: true,
            cwd: 'build/images/svg',
            src: ['*.svg'],
            dest: "build/images/sprites"
        }],
        options: {
            enhanceSVG:true,
            defaultHeight:"180px",
            defaultWidth:"180px"
        }
    }
};