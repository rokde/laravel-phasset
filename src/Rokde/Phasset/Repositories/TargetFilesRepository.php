<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Repositories;


use Illuminate\Events\Dispatcher;
use Rokde\Phasset\Assets\TargetFile;

class TargetFilesRepository {

	/**
	 * source files
	 *
	 * @var SourceFilesRepository
	 */
	private $sourceFilesRepository;

	/**
	 * base path
	 *
	 * @var string
	 */
	private $basePath;

	/**
	 * target files
	 *
	 * @var array|TargetFile[]
	 */
	private $targets = [];

	/**
	 * events dispatcher
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	private $events;

	/**
	 * @param SourceFilesRepository $sourceFilesRepository
	 * @param string $basePath
	 * @param \Illuminate\Events\Dispatcher $events
	 */
	public function __construct(SourceFilesRepository $sourceFilesRepository, $basePath, Dispatcher $events)
	{
		$this->sourceFilesRepository = $sourceFilesRepository;
		$this->basePath = $basePath;

		$this->events = $events;
	}

	/**
	 * sets a list of target files consisting source files each
	 *
	 * @param array $targets
	 * @return self
	 */
	public function setTargets(array $targets)
	{
		foreach ($targets as $target => $sourceFiles)
		{
			$this->addTarget($target, $sourceFiles);
		}

		return $this;
	}

	/**
	 * adds a target file with source files
	 *
	 * @param string $target
	 * @param array $sourceFiles
	 * @return self
	 */
	public function addTarget($target, $sourceFiles)
	{
		if (! $target instanceof TargetFile)
		{
			$target = new TargetFile($target, $this->events);
		}
		$target->setBasePath($this->basePath);
		foreach ($sourceFiles as $sourceFile)
		{
			$source = $this->sourceFilesRepository->getOrCreate($sourceFile);
			$target->addSourceFile($source);
		}

		$this->targets[$target->getFilename()] = $target;

		return $this;
	}

	/**
	 * updates all targets containing source file
	 *
	 * @param string $file
	 */
	public function updateTargetsWithSourceFile($file)
	{
		foreach ($this->targets as $target)
		{
			if ($target->hasSourceFile($file))
			{
				$target->write();
			}
		}
	}

	/**
	 * updates all configured targets
	 */
	public function update()
	{
		$targetFileCount = count($this->targets);
		$this->fire('updating', [$targetFileCount]);

		foreach ($this->targets as $target)
		{
			$target->write();
		}

		$this->fire('updated', [$targetFileCount]);
	}

	/**
	 * fires an event
	 *
	 * @param string $event
	 * @param array|string $payload
	 */
	private function fire($event, $payload)
	{
		if ($this->events === null)
			return;

		$this->events->fire('phasset.' . $event, $payload);
	}
}