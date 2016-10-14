<?php
class Df_Admin_Config_Form_FieldInstance_Info_Urls extends Df_Admin_Config_Form_FieldInstance {
	/** @return array(string => string) */
	public function getUrls() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$result = array();
			foreach (Mage::app()->getStores() as $store) {
				/** @var Df_Core_Model_StoreM $store */
				$result[$store->getName()] = $this->getUrlForStore($store);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	private function getUrlForStore(Df_Core_Model_StoreM $store) {
		return Mage::getUrl($this->getUrlPath($store), $this->getUrlParams($store));
	}

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return array(string => string)
	 */
	private function getUrlParams(Df_Core_Model_StoreM $store) {
		/** @var array(mixed => mixed) $result */
		$result = array(
			'_nosid' => true
			/**
			 * Указывание значения @uses Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK
			 * вместо значения по умолчанию @see Mage_Core_Model_Store::URL_TYPE_LINK
			 * позволяет нам избежать включения в адрес кода магазина:
			 * @used-by Mage_Core_Model_Store::getBaseUrl()
			 */
			, '_type' => Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK
			/**
			 * Указание магазина обязательно
			 * для корректного исключения из адресов index.php при необходимости,
			 * потому что иначе система сочтёт магазин административным,
			 * а для административного магазина
			 * она никогда не исключает index.php из адресов:
			 * @see Mage_Core_Model_Store::_updatePathUseRewrites()
			 */
			, '_store' => $store
		);
		if (!Mage::app()->isSingleStoreMode() && $this->needPassParametersAsQuery()) {
			$result['_query'] = array('store-view' => $store->getCode());
		}
		return $result;
	}

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	private function getUrlPath(Df_Core_Model_StoreM $store) {
		/** @var string $result */
		if (Mage::app()->isSingleStoreMode()) {
			$result = $this->getUrlPathBase();
		}
		else {
			/**
			 * 2014-11-21
			 * При работе с демо-данными оформительской темы Fortis заметил,
			 * что прежний алгоритм (не учитывавший $prefix), работал неправильно с этими демо-данными.
			 * Прежний алгоритм формировал адреса вида:
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=default
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=default_de
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=second
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=third
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=fourth
			 * http://localhost.com:859/df-yandex-market/yml/?store-view=fifth
			 * Однако по таким адресам Magento не передавало управление модулю Яндекс.Маркет
			 * (и, аналогично, модулю 1C).
			 * Правильные адреса должны в начале пути содержать код магазина:
			 * http://localhost.com:859/default/df-yandex-market/yml/?store-view=default
			 * http://localhost.com:859/default_de/df-yandex-market/yml/?store-view=default_de
			 * http://localhost.com:859/second/df-yandex-market/yml/?store-view=second
			 * http://localhost.com:859/third/df-yandex-market/yml/?store-view=third
			 * http://localhost.com:859/fourth/df-yandex-market/yml/?store-view=fourth
			 * http://localhost.com:859/fifth/df-yandex-market/yml/?store-view=fifth
			 */
			/** @var string $baseUrlWithStoreCode */
			$baseUrlWithStoreCode = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			/** @var string $baseUrlWithStoreCode */
			$baseUrlWithoutStoreCode = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			/** @var string $prefix */
			$prefix = str_replace($baseUrlWithoutStoreCode, '', $baseUrlWithStoreCode);
			/** @var string $result */
			$result =
				!$prefix
				? $this->getUrlPathBase()
				: $prefix . $this->getUrlPathBase()
			;
			if (!$this->needPassParametersAsQuery()) {
				$result = df_cc_path($result, 'store-view', $store->getCode());
			}
		}
		return $result;
	}

	/** @return string */
	private function getUrlPathBase() {return $this->getConfigParam('rm_url_path_base', true);}

	/** @return bool */
	private function needPassParametersAsQuery() {
		return $this->isConfigNodeExist('rm_url_pass_parameters_as_query');
	}

	/** @used-by Df_Admin_Block_Field_Info_Urls::getInstanceClass() */
	const _C = __CLASS__;
}