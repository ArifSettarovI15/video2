module.exports = {
  cssmin: {
    options: {
      level: {
        1: {
          specialComments: 0
        }
      }
    },
    files: [{
      expand: true,
      cwd: 'build/css',
      src: 'frontend.css',
      dest: 'build/css',
      ext: '.min.css'
    }
    ]
  }
};
