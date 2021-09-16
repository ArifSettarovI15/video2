module.exports = {
    babel: {
        options: {
            sourceMap: false,
            presets: ['es2015']
        },
        files: [{
            "expand": true,
            "src": ["build/js/*.js"],
            "ext": ".js"
        }]
    }
};