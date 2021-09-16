module.exports = {
  cwebp: {
      options: {
        q: 85
      },
      files: [{
        expand: true,
        cwd: 'src/images/static/',
        src: ['*.{png,jpg,gif}'],
        dest: 'build/images/static/'
      }]
  }
};
