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
		return implode('<br/>', array_map(
			'Df_Admin_Block_Notifier_DeleteDemoStore::render', $this->getDemoStores()
		));
	}

	/** @return Mage_Core_Model_Store[] */
	private function getDemoStores() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_intersect_key(
					Mage::app()->getStores($withDefault = false, $codeKey = true)
					,array_flip($this->getDemoStoreCodes())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(mixed => mixed) */
	private function getDemoStoreCodes() {return array('french', 'german', 'spanish');}
}