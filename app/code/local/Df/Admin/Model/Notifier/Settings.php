<?php
abstract class Df_Admin_Model_Notifier_Settings extends Df_Admin_Model_Notifier {
	/** @return string */
	protected function getUrlSettingsSuffix() {return '';}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	abstract protected function isStoreAffected(Mage_Core_Model_Store $store);

	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::needToShow() && (0 < $this->getStoresAffectedCount());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMessage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::getMessage();
			if ($this->getUrlSettingsSuffix()) {
				$this->{__METHOD__} .=
					df_output()->processLink(
						'<br/><span class="rm-url-settings">[[открыть раздел настроек]]</span>'
						, rm_url_admin(
							'adminhtml/system_config/edit/section/' . $this->getUrlSettingsSuffix()
						)
					)
				;
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(parent::getMessageVariables(), array(
				self::MESSAGE_VAR__STORES_AFFECTED =>
					Mage::app()->isSingleStoreMode()
					? ''
					: rm_concat_clean(' '
						, (1 === $this->getStoresAffectedCount()) ? ' для магазина ' : 'для магазинов'
						, Df_Core_Model_Resource_Store_Collection::getNamesStatic(
							$this->getStoresAffected()
						)
					)
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Store[] */
	private function getStoresAffected() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Store[] $result */
			$result = array();
			foreach (Mage::app()->getStores() as $store) {
				/** @var Mage_Core_Model_Store $store */
				if ($this->isStoreAffected($store)) {
					$result[]= $store;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getStoresAffectedCount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = count($this->getStoresAffected());
		}
		return $this->{__METHOD__};
	}

	const MESSAGE_VAR__STORES_AFFECTED = '{перечисление магазинов}';
}