module.exports = {
  size360: {
      options: {
        width: 360,
        height: 360,
        overwrite: true
      },
      src: 'src/images/static/*.{png,jpg,gif}',
      dest: 'build/images/static/360/'
    },
  size800: {
    options: {
      width: 800,
      height: 800,
      overwrite: true
    },
    src: 'src/images/static/*.{png,jpg,gif}',
    dest: 'build/images/static/800/'
  },
  size1200: {
    options: {
      width: 1200,
      height: 1200,
      overwrite: true
    },
    src: 'src/images/static/*.{png,jpg,gif}',
    dest: 'build/images/static/1200/'
  },
  size1920: {
    options: {
      width: 1920,
      height: 1920,
      overwrite: true
    },
    src: 'build/images/static/*.{png,jpg,gif}',
    dest: 'build/images/static/1920/'
  }
};
