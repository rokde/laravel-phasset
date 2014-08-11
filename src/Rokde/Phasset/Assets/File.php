<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Assets;


use Illuminate\Events\Dispatcher;

abstract class File {

	/**
	 * base path
	 *
	 * @var string
	 */
	protected $basePath = '';

	/**
	 * filename
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * events dispatcher
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * initializes with a filename
	 *
	 * @param string $filename
	 * @param \Illuminate\Events\Dispatcher $events
	 */
	public function __construct($filename, Dispatcher $events)
	{
		$this->filename = $filename;

		$this->events = $events;
	}

	/**
	 * returns filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->basePath . $this->filename;
	}

	/**
	 * sets base path
	 *
	 * @param string $basePath
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * fires an event
	 *
	 * @param string $event
	 * @param array|string $payload
	 */
	protected function fire($event, $payload)
	{
		if ($this->events === null)
			return;

		$this->events->fire('phasset.' . $event, $payload);
	}
}