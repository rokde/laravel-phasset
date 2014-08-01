<?php

return [
	/**
	 * which folder should be watched
	 */
	'watchFolder' => [
		/**
		 * concrete existing folder
		 * - with optional filter
		 */
		'app/assets' => [
			'*.css',
			'*.js',
		],

		'vendor/twbs/bootstrap/dist' => [
			'*.min.css',
		],
	],

	/**
	 * how to proceed the assets
	 */
	'sources' => [
		/**
		 * concrete asset
		 * - with the necessary processing filters
		 */
		'app/assets/stylesheets/forms.css' => [
			'minify-css'
		],
		'app/assets/test.css' => [
			'minify-css'
		],
		'app/assets/test.js' => [
			'minify-js'
		],
		'vendor/twbs/bootstrap/dist/js/bootstrap.min.js' => [
			'minify-js'
		],
	],

	/**
	 * where to store which assets
	 */
	'targets' => [
		/**
		 * concrete target asset: will be written and updated on source changes
		 */
		'public/assets/dir/dir/appWatcher.css' => [
			'vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
			'app/assets/stylesheets/forms.css',
			'app/assets/test.css',
		],
		'public/assets/dir/dir/appWatcher.js' => [
			'vendor/twbs/bootstrap/dist/js/bootstrap.min.js',
			'app/assets/test.js'
		],
	],

	/**
	 * filter definition
	 * id => fully qualified class name
	 * or
	 * id => [class => full qualified class name, options => options for constructor]
	 *
	 * id will be used in sources array for each configured file
	 */
	'filters' => [
		'minify-css' => 'Rokde\Phasset\Filters\Css\MinifyFilter',
		'minify-js' => 'Rokde\Phasset\Filters\Js\MinifyFilter',
	],
];