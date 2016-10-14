<?php
class Df_Adminhtml_Block_Widget_Grid_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @throws Exception
	 * @param string $fileName
	 * @return string
	 */
	public function fetchView($fileName) {
		/** @var string $result */
		try {
			$result = parent::fetchView($fileName);
		}
		catch (Exception $e) {
			// clean output buffer
			while (ob_get_level()) {
				ob_end_clean() ;
			}
			df_error($e);
		}
		return $result;
	}
}