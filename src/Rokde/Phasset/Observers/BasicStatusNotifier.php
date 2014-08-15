<?php
/**
 * phasset
 *
 * @author rok
 * @since 15.08.14
 */

namespace Rokde\Phasset\Observers;


class BasicStatusNotifier extends BaseObserver {

	public function observe()
	{
		$this->events->listen('phasset.target.updating', function ($targetFileCount) {
			$this->command->info('updating ' . $targetFileCount . ' asset files...');
		});
		$this->events->listen('phasset.target.updated', function ($targetFileCount) {
			$this->command->info($targetFileCount . ' asset files updated');
		});
	}
}