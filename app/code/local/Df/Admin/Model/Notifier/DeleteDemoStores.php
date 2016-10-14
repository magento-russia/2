<?php
class Df_Admin_Model_Notifier_DeleteDemoStores extends Df_Admin_Model_Notifier {
	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::needToShow() && $this->getDemoStores();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageTemplate() {
		return implode('<br/>', Df_Admin_Block_Notifier_DeleteDemoStore::renderA($this->getDemoStores()));
	}

	/** @return Df_Core_Model_StoreM[] */
	private function getDemoStores() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				dfa_select(
					Mage::app()->getStores($withDefault = false, $codeKey = true)
					, $this->getDemoStoreCodes()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(mixed => mixed) */
	private function getDemoStoreCodes() {return array('french', 'german', 'spanish');}
}