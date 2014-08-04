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
	],

	/**
	 * how to proceed the assets
	 */
	'sources' => [
		/**
		 * concrete asset
		 * - with the necessary processing filters
		 */
		'app/assets/stylesheets/application.css' => [
			'minify-css'
		],
		'app/assets/javascripts/application.js' => [
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
		'public/assets/application.css' => [
			'app/assets/stylesheets/application.css',
		],
		'public/assets/application.js' => [
			'app/assets/javascripts/application.js'
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
		'less' => 'Rokde\Phasset\Filters\Less\LessCompiler',
		'sass' => 'Rokde\Phasset\Filters\Sass\ScssCompiler',
	],
];