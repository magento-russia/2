<?php
class Df_Localization_Block_Admin_Theme_Processor_Processed
	extends Df_Localization_Block_Admin_Theme_Processor {
	/**
	 * @override
	 * @return string
	 */
	public function getActionTitle() {return 'перезапустить';}

	/**
	 * @override
	 * @return string
	 */
	public function getLinkTitle() {
		return df_e(
			'уже применялся '
			. df_dts($this->getProcessor()->getTimeOfLastProcessing(), 'dd.MM.y HH:mm:ss')
		);
	}


}