<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Repositories;


use Rokde\Phasset\Repositories\Contracts\FilterableRepository;
use Symfony\Component\Finder\SplFileInfo;

class FilteredFilesWatchingRepository extends FilesWatchingRepository implements FilterableRepository {

	/**
	 * filters array per base path
	 *
	 * @var array
	 */
	protected $filters = [];

	/**
	 * adds a path with optional filters
	 *
	 * @param string $path
	 * @param array $filters
	 * @return $this
	 */
	public function setFilter($path, array $filters = [])
	{
		$realpath = $this->validateDirectory($path);

		if (! array_key_exists($realpath, $this->filters))
			$this->filters[$realpath] = [];

		$this->filters[$realpath] = array_merge($this->filters[$realpath], $filters);

		return $this;
	}

	/**
	 * proceed file
	 *
	 * @param SplFileInfo $file
	 * @return bool
	 */
	protected function proceedFile(SplFileInfo $file)
	{
		foreach ($this->filters as $basePath => $filters)
		{
			if (! $this->fileExistsInBasePath($file, $basePath))
				continue;

			if ($this->fileMatchesFilter($file, $filters))
				return true;
		}

		return false;
	}

	/**
	 * does a given file exists in a base path
	 *
	 * @param SplFileInfo $file
	 * @param string $basePath
	 * @return bool
	 */
	private function fileExistsInBasePath(SplFileInfo $file, $basePath)
	{
		return substr_count($file->getRealPath(), $basePath, 0, strlen($basePath)) !== false;
	}

	/**
	 * checks if the file belongs to the given filter, returns FALSE when not match
	 *
	 * @param SplFileInfo $file
	 * @param array $filters
	 * @return bool
	 */
	private function fileMatchesFilter(SplFileInfo $file, $filters)
	{
		foreach ($filters as $filter)
		{
			if (fnmatch($filter, $file->getRealPath()))
				return true;
		}

		return false;
	}
}