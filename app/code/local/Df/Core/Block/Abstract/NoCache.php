<?php
class Df_Core_Block_Abstract_NoCache extends Df_Core_Block_Abstract {
	/**
	 * @override
	 * @return bool
	 */
	protected function canCache() {return false;}
}