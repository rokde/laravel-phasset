<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Repositories;


use Rokde\Phasset\Assets\SourceFile;

class SourceFilesRepository {

	/**
	 * filter repository
	 *
	 * @var FilterRepository
	 */
	private $filterRepository;

	/**
	 * base path
	 *
	 * @var string
	 */
	private $basePath;

	/**
	 * source files
	 *
	 * @var array|SourceFile[]
	 */
	private $sources = [];

	/**
	 * @param FilterRepository $filterRepository
	 * @param string $basePath
	 */
	public function __construct(FilterRepository $filterRepository, $basePath)
	{
		$this->filterRepository = $filterRepository;
		$this->basePath = $basePath;
	}

	/**
	 * sets source files
	 *
	 * @param array $sources
	 * @return self
	 */
	public function setSources(array $sources)
	{
		foreach ($sources as $source => $filters)
		{
			$this->addSource($source, $filters);
		}

		return $this;
	}

	/**
	 * adds a source file
	 *
	 * @param string|SourceFile $source
	 * @param array $filters
	 * @return self
	 */
	public function addSource($source, array $filters = [])
	{
		if (! $source instanceof SourceFile)
		{
			$source = new SourceFile($source);
		}
		$source->setBasePath($this->basePath);
		foreach ($filters as $filter)
		{
			$source->addFilter($this->filterRepository->getFilter($filter));
		}

		$this->sources[$source->getFilename()] = $source;

		return $this;
	}

	/**
	 * contains source file
	 *
	 * @param string $sourceFile
	 * @return bool
	 */
	public function contains($sourceFile)
	{
		return array_key_exists($sourceFile, $this->sources);
	}

	/**
	 * returns source file when found
	 *
	 * @param string $sourceFile
	 * @return null|SourceFile
	 */
	public function get($sourceFile)
	{
		return $this->contains($sourceFile)
			? $this->sources[$sourceFile]
			: null;
	}

	/**
	 * returns found source file or creates a new instance
	 * - checks absolute and relative source file argument
	 *
	 * @param string $sourceFile
	 * @return SourceFile
	 */
	public function getOrCreate($sourceFile)
	{
		if ($this->contains($sourceFile))
			return $this->sources[$sourceFile];

		$absolutePath = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sourceFile;
		if ($this->contains($absolutePath))
			return $this->sources[$absolutePath];

		$source = new SourceFile($sourceFile);
		$source->setBasePath($this->basePath);

		return $source;
	}
}