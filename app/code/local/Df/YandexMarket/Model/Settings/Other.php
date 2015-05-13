<?php
class Df_YandexMarket_Model_Settings_Other extends Df_YandexMarket_Model_Settings_Yml {
	/** @return string */
	public function getCategoriesReferenceBookUrl() {
		/**
		 * Второй параметр тут важен,
		 * потому что иначе мы на товарной карточке получим неявный сбой
		 * про неуказанный текущий магазин.
		 */
		return $this->getString('categories_reference_book_url', Mage::app()->getStore());
	}

	/** @return string */
	public function getDomain() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getString('domain');
			if ($this->useNonStandardDomain() && !df_check_string_not_empty($result)) {
				df_error(
					'Администратор должен либо указать значение параметра'
					. ' «<b>Домен веб-адресов в файле YML</b>» для магазина «%s» в разделе'
					. ' «Система» -> «Настройки» -> «Российская сборка» -> «Яндекс.Маркет» -> «Другое»,'
					. '<br/>либо отключить опцию'
					. ' «<b>Использовать нестандартный домен для веб-адресов в файле YML?</b>»'
					. ' для этого же магазина в этом же разделе.'
					, $this->getStore()->getName()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function useNonStandardDomain() {return $this->getYesNo('use_non_standard_domain');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/other/';}
	/** @return Df_YandexMarket_Model_Settings_Other */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}