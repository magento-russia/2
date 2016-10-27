<?php
namespace Df\Shipping\Config\Backend\Validator\Strategy\Origin;
class SpecificCountry extends \Df\Shipping\Config\Backend\Validator\Strategy\Origin {
	/**
	 * @override
	 * @see \Df\Shipping\Config\Backend\Validator\Strategy\Origin::validate()
	 * @return bool
	 */
	public function validate() {
		/** @var bool $result */
		$result =
			!$this->country()
			|| $this->country()->getIso2Code() === $this->origin()->getCountryId()
		;
		if (!$result) {
			$this->getBackend()->getMessages()->addMessage(new \Mage_Core_Model_Message_Error(strtr(
				'Модуль «{модуль}» в настоящее время работает только с грузами, '
				.'отправляемыми {из страны}.'
				.'<br/>У Вас же в качестве страны расположения склада магазина указано «{страна}».'
				.'<br/>Вам нужно в качестве страны расположения склада магазина указать {страну}'
				.'<br/>Эта настройка расположена в разделе: '
				.'«Система» → «Настройки» → «Продажи» → «Доставка: общие настройки» → '
				.'«Расположение магазина» → «Страна».'
				,[
					'{из страны}' => $this->country()->getNameInFormOrigin()
					,'{страну}' => $this->country()->getNameInCaseAccusative()
					,'{модуль}' => $this->moduleTitle()
					,'{страна}' =>
						!$this->origin()->getCountry()
						? 'пусто'
						: $this->origin()->getCountry()->getNameLocalized()
				]
			)));
		}
		return $result;
	}

	/** @return \Df_Directory_Model_Country|null */
	private function country() {return dfc($this, function() {return
		df_country($this->getBackend()->getFieldConfigParam('df_origin_country'), false)
	;});}
}