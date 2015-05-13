<?php
class Df_Admin_Model_Config_Backend_Validator extends Df_Admin_Model_Config_Backend {
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
	 * @return Df_Admin_Model_Config_Backend_Validator
	 */
	protected function _beforeSave() {
		try {
			// Проводим валидацию только в том случае, если административная опция включена
			if (rm_bool($this->getValue())) {
				$this->validate();
			}
		}
		catch(Exception $e) {
			// Сюда мы попадаем не тогда, когда валидация не пройдена,
			// а когда программный код валидатора дал сбой.
			df_notify_exception($e);
			$this->getMessages()->addMessage(new Mage_Core_Model_Message_Error(rm_ets($e)));
		}
		// Показываем администратору все сообщения,
		// которые валидатор счёл нужным предоставить.
		foreach ($this->getMessages()->getItems() as $message) {
			/** @var Mage_Core_Model_Message_Abstract $message */
			rm_session()->addMessage($message);
		}
		parent::_beforeSave();
		return $this;
	}

	/** @return string */
	private function getStrategyClassMf() {
		/** @var string $result */
		$result = $this->getFieldConfigParam('df_backend_validator_strategy', $mustBeNonEmpty = true);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Df_Admin_Model_Config_Backend_Validator */
	private function validate() {
		/** @var bool $isValid */
		$isValid = true;
		foreach ($this->getStores() as $store) {
			/** @var Mage_Core_Model_Store $store */
			$isValid =
					$isValid
				&&
					$this->validateForStore($store)
			;
		}

		/**
		 * Если валидация не пройдена, то отключаем опцию
		 */
		$this->setValue($isValid && rm_bool($this->getValue()));
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	private function validateForStore(Mage_Core_Model_Store $store) {
		/** @var Df_Admin_Model_Config_Backend_Validator_Strategy $strategy */
		$strategy =
			df_model(
				$this->getStrategyClassMf()
				,array(
					Df_Admin_Model_Config_Backend_Validator_Strategy::P__BACKEND => $this
					,Df_Admin_Model_Config_Backend_Validator_Strategy::P__STORE => $store
				)
			)
		;
		df_assert($strategy instanceof Df_Admin_Model_Config_Backend_Validator_Strategy);
		return $strategy->validate();
	}

	const _CLASS = __CLASS__;
}