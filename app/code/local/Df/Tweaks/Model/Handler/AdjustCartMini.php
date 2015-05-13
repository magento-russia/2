<?php
class Df_Tweaks_Model_Handler_AdjustCartMini extends Df_Tweaks_Model_Handler_Remover {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getBlockNames() {return array('cart_sidebar');}

	/**
	 * @override
	 * @return Df_Tweaks_Model_Settings_Remove
	 */
	protected function getSettings() {return df_cfg()->tweaks()->cart();}

	/**
	 * @override
	 * @return bool
	 */
	protected function hasDataToShow() {
		/** @var bool $result */
		$result =
				!is_null($this->getBlock())
			&&
				(0 < $this->getBlock()->getSummaryCount())
			&&
				($this->getBlock()->getIsNeedToDisplaySideBar())
		;
		return $result;
	}

	/** @return Mage_Checkout_Block_Cart_Sidebar|null */
	private function getBlock() {
		/** @var Mage_Checkout_Block_Cart_Sidebar|null $result */
		$result = rm_layout()->getBlock($this->getBlockName());
		if (!($result instanceof Mage_Checkout_Block_Cart_Sidebar)) {
			$result = null;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
}