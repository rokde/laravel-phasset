<?php
/**
 * phasset
 *
 * @author rok
 * @since 15.08.14
 */

namespace Rokde\Phasset\Observers;


class TargetFileWrittenNotifier extends BaseObserver {

	/**
	 * observes the events
	 */
	public function observe()
	{
		$this->events->listen('phasset.target.written', function ($file, $step, $targetFileCount) {
			$this->command->info($file . ' written');
		});
	}
}