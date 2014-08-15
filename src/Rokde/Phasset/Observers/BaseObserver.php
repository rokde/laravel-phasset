<?php
/**
 * phasset
 *
 * @author rok
 * @since 15.08.14
 */

namespace Rokde\Phasset\Observers;


use Illuminate\Events\Dispatcher;
use Rokde\Phasset\Commands\BaseCommand;

abstract class BaseObserver {

	/**
	 * command reference
	 *
	 * @var \Rokde\Phasset\Commands\BaseCommand
	 */
	protected $command;

	/**
	 * event listener
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * constructs, defines the verbosity level(s) to start observing
	 *
	 * @param BaseCommand $command
	 * @param Dispatcher $events
	 * @param int|array $verbosityLevel
	 */
	public function __construct(BaseCommand $command, Dispatcher $events, $verbosityLevel)
	{
		$this->command = $command;
		$this->events = $events;

		if (! is_array($verbosityLevel))
		{
			$verbosityLevel = array($verbosityLevel);
		}

		if (in_array($command->getOutput()->getVerbosity(), $verbosityLevel))
		{
			$this->observe();
		}
	}

	abstract public function observe();

}