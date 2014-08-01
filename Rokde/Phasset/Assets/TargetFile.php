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
}