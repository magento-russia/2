<?php
class Df_Localization_Block_Admin_Theme_Notifier extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/localization/theme/notifier.phtml';}

	/** @return Df_Localization_Model_Onetime_Processor[] */
	protected function getProcessors() {return $this->cfg(self::$P__PROCESSORS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PROCESSORS, self::V_ARRAY);
	}
	/** @var string */
	private static $P__PROCESSORS = 'processors';
	/**
	 * @param Df_Localization_Model_Onetime_Processor[] $processors
	 * @return string
	 */
	public static function render(array $processors) {
		return df_block_render(__CLASS__, '', array(self::$P__PROCESSORS => $processors));
	}
}


 