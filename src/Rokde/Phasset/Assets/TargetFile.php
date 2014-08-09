<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Assets;


class TargetFile extends File implements Writable {

	/**
	 * source files for target
	 *
	 * @var array|SourceFile[]
	 */
	private $sourceFiles = [];

	/**
	 * do compression
	 *
	 * @var bool
	 */
	private $compression = true;

	/**
	 * compression level
	 *
	 * @var int
	 */
	private $compressionLevel = 9;

	/**
	 * adds a source file
	 * @param SourceFile $sourceFile
	 * @return self
	 */
	public function addSourceFile(SourceFile $sourceFile)
	{
		$this->sourceFiles[] = $sourceFile;

		return $this;
	}

	/**
	 * writes the target file
	 */
	public function write()
	{
		$content = [];
		foreach ($this->sourceFiles as $sourceFile)
		{
			$content[] = $sourceFile->read();
		}

		if (! is_dir(dirname($this->getFilename())))
			mkdir(dirname($this->getFilename()), 0777, true);

		file_put_contents($this->getFilename(), $content);

		if ($this->compression)
		{
			file_put_contents($this->getFilename() . '.gz',
				gzencode(implode('', $content),
				$this->compressionLevel)
			);
		}
	}

	/**
	 * does this target has a concrete source file
	 *
	 * @param string $relativeFilename
	 * @return bool
	 */
	public function hasSourceFile($relativeFilename)
	{
		foreach ($this->sourceFiles as $sourceFile)
		{
			if ($sourceFile->getFilename() === $relativeFilename)
				return true;
		}

		return false;
	}

	/**
	 * sets and gets compression
	 *
	 * @param bool|null $flag
	 * @param int|null $level
	 * @return bool
	 */
	public function compression($flag = null, $level = null)
	{
		if ($flag !== null)
		{
			$this->compression = $flag === true;
			if ($level !== null)
			{
				$this->compressionLevel = ($level >= 0 && $level <= 9)
					? $level
					: $this->compressionLevel;
			}
		}

		return $this->compression;
	}
}