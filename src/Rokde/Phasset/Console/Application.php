<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 01.08.14
 */

namespace Rokde\Phasset\Console;


use Symfony\Component\Console\Application as ConsoleApplication;
use Rokde\Phasset\Commands\NotifyCommand;
use Rokde\Phasset\Commands\UpdateCommand;
use Rokde\Phasset\Commands\WatchCommand;

class Application extends ConsoleApplication
{
	const VERSION = '1.0.0';

	private static $logo = "       _                        _
 _ __ | |__   __ _ ___ ___  ___| |_
| '_ \| '_ \ / _` / __/ __|/ _ \ __|
| |_) | | | | (_| \__ \__ \  __/ |_
| .__/|_| |_|\__,_|___/___/\___|\__|
|_|   ";

	/**
	 * start the application
	 */
	public function __construct()
	{
		parent::__construct('phasset', self::VERSION);
	}

	/**
	 * @return string
	 */
	public function getHelp()
	{
		return self::$logo . parent::getHelp();
	}

	/**
	 * returns the default commands within the console application
	 *
	 * @return array|\Symfony\Component\Console\Command\Command[]
	 */
	protected function getDefaultCommands()
	{
		$commands = parent::getDefaultCommands();

		$commands[] = new NotifyCommand('notify');
		$commands[] = new UpdateCommand('update');
		$commands[] = new WatchCommand('watch');

		return $commands;
	}
}