<?php
class Df_Admin_Config_Backend_Validator extends Df_Admin_Config_Backend {
	/**
	 * Метод публичен, потому что его использует применяемая валидатором стратегия
	 * (стратегия является отдельным от валидатора объектом,
	 * что позволяет подставлять в валидатор разные стратегии)
	 * @return Mage_Core_Model_Message_Collection
	 */
	public function getMessages() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Mage_Core_Model_Message_Collection();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @overide
	 * @return Df_Admin_Config_Backend_Validator
	 */
	protected function _beforeSave() {
		try {
			// Проводим валидацию только в том случае, если административная опция включена
			if (rm_bool($this->getValue())) {
				$this->validate();
			}
		}
		catch (Exception $e) {
			// Сюда мы попадаем не тогда, когда валидация не пройдена,
			// а когда программный код валидатора дал сбой.
			df_notify_exception($e);
			$this->getMessages()->addMessage(new Mage_Core_Model_Message_Error(df_ets($e)));
		}
		// Показываем администратору все сообщения,
		// которые валидатор счёл нужным предоставить.
		/** @uses Mage_Core_Model_Session_Abstract::addMessage() */
		array_map(array(rm_session(), 'addMessage'), $this->getMessages()->getItems());
		parent::_beforeSave();
		return $this;
	}

	/** @return string */
	private function getStrategyClass() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getFieldConfigParam(
				'df_backend_validator_strategy', $mustBeNonEmpty = true
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Config_Backend_Validator */
	private function validate() {
		/** @var bool $isValid */
		$isValid = true;
		foreach ($this->getStores() as $store) {
			/** @var Df_Core_Model_StoreM $store */
			$isValid = $isValid && $this->validateForStore($store);
			if (!$isValid) {
				break;
			}
		}
		// Если валидация не пройдена, то отключаем опцию.
		$this->setValue($isValid && rm_bool($this->getValue()));
		return $this;
	}

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return bool
	 */
	private function validateForStore(Df_Core_Model_StoreM $store) {
		return Df_Admin_Config_Backend_Validator_Strategy::ic(
			$this->getStrategyClass(), $this, $store
		)->validate();
	}
}