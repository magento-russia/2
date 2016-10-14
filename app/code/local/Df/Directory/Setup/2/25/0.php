<?php
class Df_Directory_Setup_2_25_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		Df_Directory_Setup_Processor_InstallRegions_Kazakhstan::i($this->getSetup())->process();
	}
}