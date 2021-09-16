module.exports = {
    dev: {
        files: {
            //helpers
            'src/css/frontend/helpers/_mixins.scss': 'src/css/frontend/helpers/mixins/*.scss',
            'src/css/frontend/helpers/_functions.scss': 'src/css/frontend/helpers/functions/*.scss',
            'src/css/backend/helpers/_mixins.scss': 'src/css/backend/helpers/mixins/*.scss',
            'src/css/backend/helpers/_functions.scss': 'src/css/backend/helpers/functions/*.scss',

            //main
            'src/css/frontend/_elements.scss': 'src/css/frontend/elements/*.scss',
            'src/css/backend/_elements.scss': 'src/css/backend/elements/*.scss',

            '../app/views/frontend/elements/_elements_frontend.scss': '../app/views/frontend/elements/**/*.scss',
            '../app/views/backend/elements/_elements_backend.scss': '../app/views/backend/elements/**/*.scss',

            '../app/views/modules/_modules.scss': '../app/views/modules/**/*.scss',

            '../app/views/frontend/components/_components_frontend.scss': '../app/views/frontend/components/**/*.scss',
            '../app/views/backend/components/_components_backend.scss': '../app/views/backend/components/**/*.scss',

            '../app/views/frontend/sections/_sections_frontend.scss': '../app/views/frontend/sections/**/*.scss',
            '../app/views/backend/sections/_sections_backend.scss': '../app/views/backend/sections/**/*.scss',

            'src/css/backend/_pages_old.scss': 'src/css/backend/pages/*.scss',
            '../app/views/frontend/pages/_pages_frontend.scss': '../app/views/frontend/pages/**/*.scss',
            '../app/views/backend/pages/_pages_backend.scss': '../app/views/backend/pages/**/*.scss'

        },
        options: {
            useSingleQuotes: false
        }
    }
};