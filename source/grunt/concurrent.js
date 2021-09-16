module.exports = {

    // Опции
    options: {
        limit: 3
    },

    // Задачи разработки
    devFirst: [
        'clean:dev'
    ],
    devFirst1: [
        'copy:dev'
    ],
    devSecond1: [
        'postcss:dev'
    ],
    dev3a: [
        'clean:temp'
    ],
    dev3: [
        'copy:deploy'
    ],

    imgFirst: [
        'imagemin'
    ]
};