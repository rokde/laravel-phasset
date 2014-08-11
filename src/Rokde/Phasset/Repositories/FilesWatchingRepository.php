<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Repositories;


use Illuminate\Events\Dispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class FilesWatchingRepository {

	const EVENT_PREFIX = 'watcher.files';
	const EVENT_PREFIX_TYPES = 'watcher.types';

	const EVENT_WATCHED = 'watched';
	const EVENT_CREATED = 'created';
	const EVENT_MODIFIED = 'modified';
	const EVENT_REMOVED = 'removed';

	/**
	 * paths to watch on
	 *
	 * @var array
	 */
	protected $paths = [];

	/**
	 * file hashes
	 *
	 * @var array
	 */
	protected $hashes = [];

	/**
	 * interact with the filesystem
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * event dispatcher
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $event;

	/**
	 * instantiate with a base path
	 *
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 * @param \Illuminate\Events\Dispatcher $event
	 */
	public function __construct(Filesystem $filesystem, Dispatcher $event)
	{
		$this->filesystem = $filesystem;
		$this->event = $event;
	}

	/**
	 * adds a base path to watch on
	 *
	 * @param string $path
	 * @throws \Rokde\Phasset\Repositories\DirectoryNotExistsException
	 * @return $this
	 */
	public function addPath($path)
	{
		$realpath = $this->validateDirectory($path);

		if (in_array($realpath, $this->paths))
			return $this;

		$this->paths[] = $realpath;
		if (! array_key_exists($realpath, $this->hashes))
		{
			$this->hashes[$realpath] = new Collection();
		}

		//	initialize all hashes for path
		$this->watchDirectory($realpath, true);

		return $this;
	}

	/**
	 * watch all files in given paths
	 *
	 * @return $this
	 */
	public function watch()
	{
		foreach ($this->paths as $path)
		{
			$this->watchDirectory($path);
		}

		return $this;
	}

	/**
	 * how many files are we watching on
	 *
	 * @return int
	 */
	public function count()
	{
		//	@TODO use array_* functions to count in a more performant way

		$count = 0;
		/** @var Collection $collection */
		foreach ($this->hashes as $collection)
		{
			$count += $collection->count();
		}

		return $count;
	}

	/**
	 * watch a specified directory path
	 *
	 * @param string $path
	 * @param bool $initialize
	 */
	private function watchDirectory($path, $initialize = false)
	{
		$foundFiles = array();
		$hashedFiles = $this->hashes[$path]->lists('file');

		/** @var SplFileInfo $file */
		foreach ($this->filesystem->allFiles($path) as $file)
		{
			if (! $this->proceedFile($file))
			{
				continue;
			}

			$realPath = $file->getRealPath();
			if ($realPath === false)
			{
				//	firing removed event
				$this->fire('removed', $file);
				continue;
			}

			$foundFiles[] = $realPath;

			if (! $this->hashes[$path]->has($realPath))
			{
				$this->hashFile($path, $file);
				//	fire new file created event
				$this->fire($initialize ? self::EVENT_WATCHED : self::EVENT_CREATED, $file);
				continue;
			}

			$hash = $this->hashes[$path]->get($realPath);

			if ($hash['changed'] < $file->getCTime()
				|| $hash['modified'] < $file->getMTime()
				|| $hash['size'] != $file->getSize()
				|| $hash['hash'] != md5_file($realPath))
			{
				$this->hashFile($path, $file);
				//	fire file changed event
				$this->fire(self::EVENT_MODIFIED, $file);
				continue;
			}

			$this->hashFile($path, $file);
		}

		$missingFiles = array_diff($hashedFiles, $foundFiles);
		foreach ($missingFiles as $file)
		{
			$this->fire(self::EVENT_REMOVED, $file);
			$this->hashes[$path]->forget($file);
		}
	}

	/**
	 * add file to hashes
	 *
	 * @param string $path
	 * @param SplFileInfo $file
	 */
	private function hashFile($path, SplFileInfo $file)
	{
		$realpath = $file->getRealPath();

		$this->hashes[$path]->put($realpath, [
			'file' => $realpath,
			'changed' => $file->getCTime(),
			'modified' => $file->getMTime(),
			'size' => $file->getSize(),
			'hash' => md5_file($realpath),
		]);
	}

	/**
	 * fires an event
	 *
	 * @param string $event
	 * @param SplFileInfo|string $file
	 */
	private function fire($event, $file)
	{
		if ($this->event === null)
			return;

		$this->event->fire(self::EVENT_PREFIX . '.' . $event, (string)$file);

		if ($event === self::EVENT_WATCHED)
			return;

		$extension = ($file instanceof SplFileInfo)
			? $file->getExtension()
			: pathinfo($file)['extension'];

		$this->event->fire(self::EVENT_PREFIX_TYPES . '.' . $extension, [(string)$file, $event]);
	}

	/**
	 * proceed file
	 *
	 * @param SplFileInfo $file
	 * @return bool
	 */
	protected function proceedFile(SplFileInfo $file)
	{
		return $file->isFile();
	}

	/**
	 * validates given path
	 *
	 * @param string $path
	 * @return string
	 * @throws DirectoryNotExistsException
	 */
	protected function validateDirectory($path)
	{
		if (!is_dir($path))
			throw new DirectoryNotExistsException($path);

		$realpath = realpath($path);
		if ($realpath === false)
			throw new DirectoryNotExistsException($path);

		return $realpath;
	}
}