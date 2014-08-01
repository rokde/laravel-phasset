<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Commands;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command {

	/**
	 * overwriting command name when necessary
	 *
	 * @param string|null $name
	 */
	public function __construct($name = null)
	{
		if ($name !== null)
			$this->name = $name;

		parent::__construct();
	}

	/**
	 * Write a string as information output when verbosity level given.
	 *
	 * @param  string $string
	 * @param int $verbosityLevel
	 * @return void
	 */
	public function info($string, $verbosityLevel = 1)
	{
		if ($this->getOutput()->getVerbosity() < $verbosityLevel)
			return;

		parent::info($string);
	}

	/**
	 * Write a string as comment output.
	 *
	 * @param  string $string
	 * @param int $verbosityLevel
	 * @return void
	 */
	public function comment($string, $verbosityLevel = 1)
	{
		if ($this->getOutput()->getVerbosity() < $verbosityLevel)
			return;

		parent::comment($string);
	}

	/**
	 * Write a string as standard output.
	 *
	 * @param  string $string
	 * @param int $verbosityLevel
	 * @return void
	 */
	public function line($string, $verbosityLevel = 1)
	{
		if ($this->getOutput()->getVerbosity() < $verbosityLevel)
			return;

		parent::line($string);
	}
}