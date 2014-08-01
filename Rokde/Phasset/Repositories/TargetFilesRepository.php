<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Repositories;


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
	 * @param SourceFilesRepository $sourceFilesRepository
	 * @param string $basePath
	 */
	public function __construct(SourceFilesRepository $sourceFilesRepository, $basePath)
	{
		$this->sourceFilesRepository = $sourceFilesRepository;
		$this->basePath = $basePath;
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
			$target = new TargetFile($target);
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
		foreach ($this->targets as $target)
		{
			$target->write();
		}
	}
}