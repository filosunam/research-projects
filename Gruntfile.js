'use strict';

module.exports = function (grunt) {

  require('load-grunt-tasks')(grunt);
  
  grunt.initConfig({
    wp_readme_to_markdown: {
      target: {
        files: {
          'README.md': 'README.txt'
        }
      },
    },
    release: {
      options: {
        commit: true,
        push: false,
        pushTags: false,
        npm: false,
        npmtag: false,
        commitMessage: 'Release <%= version %>',
        tagMessage: 'Version <%= version %>'
      }
    }
  });

};
