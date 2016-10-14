<?php
class Df_Core_Block_Template_NoCache extends Df_Core_Block_Template {
	/**
	 * @override
	 * @return bool
	 */
	protected function canCache() {return false;}
}