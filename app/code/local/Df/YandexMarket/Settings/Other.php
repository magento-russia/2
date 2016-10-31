<?php
namespace Df\YandexMarket\Settings;
class Other extends Yml {
	/** @return string */
	public function getCategoriesReferenceBookUrl() {return
		// Второй параметр тут важен,
		// потому что иначе мы на товарной карточке получим неявный сбой
		// про неуказанный текущий магазин.
		$this->v('categories_reference_book_url', df_store())
	;}

	/** @return string */
	public function getDomain() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->v('domain');
		if ($this->useNonStandardDomain() && !df_check_string_not_empty($result)) {
			df_error(
				'Администратор должен либо указать значение параметра'
				. ' «<b>Домен веб-адресов в файле YML</b>» для магазина «%s» в разделе'
				. ' «Система» -> «Настройки» -> «Российская сборка» -> «Яндекс.Маркет» -> «Другое»,'
				. '<br/>либо отключить опцию'
				. ' «<b>Использовать нестандартный домен для веб-адресов в файле YML?</b>»'
				. ' для этого же магазина в этом же разделе.'
				, $this->store()->getName()
			);
		}
		return $result;
	});}

	/** @return bool */
	public function useNonStandardDomain() {return $this->getYesNo('use_non_standard_domain');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/other/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}