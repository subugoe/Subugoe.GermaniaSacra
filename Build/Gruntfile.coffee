module.exports = (grunt) ->

	grunt.initConfig

		compass:
			compile:
				options:
					sassDir: '../Resources/Private/Scss'
					cssDir: '../Resources/Public/CSS'
					noLineComments: true
					outputStyle: 'expanded'

		coffee:
			compile:
				expand: true
				cwd: '../Resources/Private/CoffeeScript'
				src: '**/*.coffee'
				dest: '../Resources/Public/JavaScript'
				ext: '.js'
				options:
					bare: true
					header: true
					preserve_dirs: true
					line_comments: false

		watch:
			compass:
				files: '<%= compass.compile.options.sassDir %>/**/*.scss'
				tasks: ['compass']
			coffee:
				files: '<%= coffee.compile.cwd %>/<%= coffee.compile.src %>'
				tasks: ['coffee']
			#options:
				#livereload: true

	# load plugins
	grunt.loadNpmTasks 'grunt-contrib-compass'
	grunt.loadNpmTasks 'grunt-contrib-coffee'
	grunt.loadNpmTasks 'grunt-contrib-watch'

	# tasks
	grunt.registerTask 'default', ['compass', 'coffee', 'watch']
