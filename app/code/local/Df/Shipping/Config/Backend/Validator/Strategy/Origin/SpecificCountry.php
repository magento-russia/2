<?php
class Df_Shipping_Config_Backend_Validator_Strategy_Origin_SpecificCountry
	extends Df_Shipping_Config_Backend_Validator_Strategy_Origin {
	/**
	 * @override
	 * @return bool
	 */
	public function validate() {
		/** @var bool $result */
		$result = true;
		if (
				!is_null($this->getLimitationCountry())
			&&
				(
						$this->getLimitationCountry()->getIso2Code()
					!==
						$this->getOrigin()->getCountryId()
				)
		) {
			$result = false;
			$this->getBackend()->getMessages()
				->addMessage(
					new Mage_Core_Model_Message_Error (
						strtr(
							'Модуль «{модуль}» в настоящее время работает только с грузами, '
							.'отправляемыми {из страны}.'
							.'<br/>У Вас же в качестве страны расположения склада магазина указано «{страна}».'
							.'<br/>Вам нужно в качестве страны расположения склада магазина указать {страну}'
							.'<br/>Эта настройка расположена в разделе: '
							.'«Система» > «Настройки» > «Продажи» > «Доставка: общие настройки» > '
							.'«Расположение магазина» > «Страна».'
							,array(
								'{из страны}' => $this->getLimitationCountry()->getNameInFormOrigin()
								,'{страну}' => $this->getLimitationCountry()->getNameInCaseAccusative()
								,'{модуль}' => $this->moduleTitle()
								,'{страна}' =>
									is_null($this->getOrigin()->getCountry())
									? 'пусто'
									: $this->getOrigin()->getCountry()->getNameLocalized()
							)
						)
					)
				)
			;
		}
		return $result;
	}

	/** @return Df_Directory_Model_Country|null */
	private function getLimitationCountry() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $countryId */
			$countryId = $this->getBackend()->getFieldConfigParam('df_origin_country');
			$this->{__METHOD__} = rm_n_set(!$countryId ? null : rm_country($countryId));
		}
		return rm_n_get($this->{__METHOD__});
	}
}