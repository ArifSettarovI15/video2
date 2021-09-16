module.exports = {
    dev: {
        options: {
            outputStyle: 'nested',
            sourceMap: 'none'
        },
        files: {
            'build/css/frontend.css': 'src/css/frontend/main.scss',
            'build/css/backend.css': 'src/css/backend/main.scss'
        }
    },
    prod: {
        options: {
            outputStyle: 'compressed',
            sourceMap: 'none'
        },
        files: {
            'build/css/frontend.css': 'src/css/frontend/main.scss',
            'build/css/backend.css': 'src/css/backend/main.scss'
        }
    }
};