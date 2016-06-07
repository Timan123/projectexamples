module.exports = function(grunt)
{
	/**
	 * Initialize the configuration for grunt.
	 * Reads the project package.json and hardcoded
	 * paths to various groups.
	 */
	grunt.initConfig(
	{
		pkg: grunt.file.readJSON('package.json'),

		paths:
		{
			app:    'app',
			bower:  'public/vendor',
			build:  'public/build',
			public: 'public',
		},

		files:
		{
			css:    '<%= paths.build %>/cogent.css',
			js:     '<%= paths.build %>/cogent.js',
			router: '<%= paths.build %>/cogent-app-router.js',
			less:   '<%= paths.build %>/cogent-less.css',

			min:
			{
				css: '<%= paths.public %>/cogent.min.css',
				js:  '<%= paths.public %>/cogent.min.js',
			}
		}
	});

	/**
	 * List of CSS files.
	 * These are commented-grouped and order does matter.
	 */
	var CSS_FILES = 
	[
		// 3rd Party Packages
		'<%= paths.bower %>/bootstrap-datepicker/css/datepicker3.css',

		// Cogent Framework
		'<%= paths.public %>/packages/cogent/cogent/css/**/*.css',

		// Project specific
		'<%= paths.public %>/css/plugins/**/*.css',

		// This is generated via grunt; Should always be last
		'<%= files.less %>'
	];

	/**
	 * List of LESS files.
	 * These are commented-grouped and order does matter.
	 */
	var LESS_FILES =
	[
		// Cogent Framework
		'<%= paths.public %>/less/cogent.less',

		// Project Specific
		'<%= paths.public %>/less/plugins/**/*.less'
	];

	/**
	 * List of Javascript files.
	 * These are commented-grouped and order does matter.
	 */
	var JS_FILES =
	[
		//------------------------------
		// Bower
		//------------------------------

		// No Dependencies
		'<%= paths.bower %>/moment/moment.js',
		'<%= paths.bower %>/inflection/lib/inflection.js',

		// Lodash
		'<%= paths.bower %>/lodash/lodash.js',
		'<%= paths.bower %>/underscore.string/dist/underscore.string.js',

		// jQuery
		'<%= paths.bower %>/jquery/dist/jquery.js',
		'<%= paths.bower %>/jquery-extendext/jQuery.extendext.js',
		'<%= paths.bower %>/jquery-form/jquery.form.js',
		'<%= paths.bower %>/jquery-serializeObject/jquery.serializeObject.js',

		// Bootstrap
		'<%= paths.bower %>/bootstrap/dist/js/bootstrap.js',
		'<%= paths.bower %>/bootstrap-datepicker/js/bootstrap-datepicker.js',
		'<%= paths.bower %>/bootstrap-validator/dist/validator.js',
		'<%= paths.bower %>/remarkable-bootstrap-notify/dist/bootstrap-notify.js',
		'<%= paths.bower %>/bootbox/bootbox.js',
		'<%= paths.bower %>/bootstrap-multiselect/dist/js/bootstrap-multiselect.js',

		// Knockout
		'<%= paths.bower %>/knockout/dist/knockout.js',
		'<%= paths.bower %>/bower-knockout-mapping/dist/knockout.mapping.js',
		'<%= paths.bower %>/ko.ninja/dist/ko.ninja.js',

		// Typeahead
		'<%= paths.bower %>/typeahead.js/dist/typeahead.bundle.js',

		//------------------------------
		// Cogent / Project Specific
		//------------------------------
		
		// Project Specific Plugins
		// Typically are custom plugins or ones that cannot be loaded via bower
		'<%= paths.public %>/js/plugins/**/*.js',

		// Cogent Framework(always be next to last)
		'<%= paths.public %>/packages/cogent/cogent/js/**/*.js',

		// Project Specific Router (this is generated via a grunt task)
		'<%= files.router %>',

		// Cogent Framework Initialization (we need this last in the cogent framework group)
		'!<%= paths.public %>/packages/cogent/cogent/js/cogent-initializer.js',
		'<%= paths.public %>/packages/cogent/cogent/js/cogent-initializer.js',

		// Project Specific (Always be last)
		'<%= paths.public %>/js/cogent.js'
	];

	//------------------------------
	// Banner
	//------------------------------

	grunt.loadNpmTasks('grunt-banner');
	grunt.config('usebanner',
	{
		options:
		{
			position: 'top',
			banner:   '/* <%= pkg.description %> | <%= pkg.version %> | <%= grunt.template.today("yyyy-mm-dd") %> */'
		},

		files:
		{
			src:
			[
				'<%= files.css %>',
				'<%= files.js %>',
				'<%= files.min.css %>',
				'<%= files.min.js %>'
			]
		}
	});

	//------------------------------
	// Clean
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.config('clean',
	{
		build: [ '<%= paths.build %>/**/*' ]
	});

	//------------------------------
	// Concatenation
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.config('concat',
	{
		css:
		{
			files:
			{
				'<%= files.css %>': CSS_FILES
			}
		},

		js:
		{
			files:
			{
				'<%= files.js %>': JS_FILES
			}
		}
    });

	//------------------------------
	// Copy
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.config('copy',
	{
		bower:
		{
			files:
			[
				{
					expand:  true,
					flatten: true,
					cwd:     '<%= paths.bower %>/bootstrap/fonts/',
					src:     '**',
					dest:    '<%= paths.public %>/fonts/',
					filter:  'isFile'
				},

				{
					expand:  true,
					flatten: true,
					cwd:     '<%= paths.bower %>/font-awesome/fonts/',
					src:     '**',
					dest:    '<%= paths.public %>/fonts/',
					filter:  'isFile'
				}
			]
		}
    });

	//------------------------------
	// CSS Minification
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.config('cssmin',
	{
		options:
		{
			compress:            true,
			keepSpecialComments: '0'
		},

		css:
		{
			files:
			{
				'<%= files.min.css %>': [ '<%= files.css %>' ]
			}
		}
	});

	//------------------------------
	// Less
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.config('less',
	{
		options:
		{
			compress: false
		},

		less:
		{
			files:
			{
				'<%= files.less %>': LESS_FILES
			}
		}
	});

	//------------------------------
	// SVN Bump
	//------------------------------

	grunt.loadNpmTasks('grunt-svn-bump');
	grunt.config('bump',
	{
		options:
		{
			filepaths:    [ 'package.json', 'bower.json', 'composer.json' ],
			syncVersions: true,

			commit:        false,
			commitMessage: 'Bump version for release ({%= version %})'
		}
	});

	//------------------------------
	// PHP Version
	//------------------------------

	grunt.loadNpmTasks('grunt-php-set-constant');
	grunt.config('setPHPConstant',
	{
		version:
		{
			constant: 'VERSION',
			value:    "<%= grunt.file.readJSON('package.json').version %>",
			file:     'app/start/global.php'
		}
	});

	//------------------------------
	// Execution
	//------------------------------

	grunt.loadNpmTasks('grunt-exec');
	grunt.config('exec',
	{
		/**
		 * Generate javascript router
		 */
		router:
		{
			command: function()
			{
				return 'php artisan cogent:js-router --file="' + this.config.get('files.router') + '"';
			}
		}
	});

	//------------------------------
	// SVN Tag
	//------------------------------

	grunt.loadNpmTasks('grunt-svn-tag');
	grunt.config('svn_tag',
	{
		options:
		{
			tag:           '{%= version %}',
			commitMessage: 'Tag for release ({%= version %})'
		}
	});

	//------------------------------
	// Uglify
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.config('uglify',
	{
		options:
		{
			compress: {}
		},

		js:
		{
			files:
			{
				'<%= files.min.js %>': '<%= files.js %>'
			}
		}
	});

	//------------------------------
	// Watch
	//------------------------------

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.config('watch',
	{
		options:
		{
			livereload: true
		},

		js:
		{
			files:
			[
				'<%= paths.app %>/routes.php',
				'<%= paths.public %>/packages/cogent/cogent/js/**/*.js',
				'<%= paths.public %>/js/**/*.js'
			],

			tasks: [ 'exec:router', 'concat:js', 'uglify' ]
		},

		css:
		{
			files:
			[
				'<%= paths.public %>/packages/cogent/cogent/css/**/*.css',
				'<%= paths.public %>/css/**/*.css'
			],

			tasks: [ 'concat:css', 'cssmin' ]
		},

		less:
		{
			files:
			[
				'<%= paths.public %>/packages/cogent/cogent/less/**/*.less',
				'<%= paths.public %>/less/**/*.less'
			],

			tasks: [ 'less', 'concat:css', 'cssmin' ]
		}
	});

	//------------------------------
	// Tasks
	//------------------------------

	grunt.registerTask('default', [ 'build', 'watch' ]);
	grunt.registerTask('build', [ 'clean', 'copy', 'exec:router', 'less', 'concat', 'cssmin', 'uglify', 'usebanner' ]);
	grunt.registerTask('svntag', [ 'build', 'svn_tag' ]);

	grunt.registerTask('versionbump', 'Bump the version', function()
	{
		var args = (arguments.length > 0) ? ':' + Array.prototype.slice.call(arguments).join(':') : '',
			tasks = [ 'bump' + args, 'setPHPConstant' ];

		grunt.task.run.apply(grunt.task, tasks);
	});
};