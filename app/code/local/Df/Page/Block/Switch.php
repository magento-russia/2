<?php
class Df_Page_Block_Switch extends Mage_Page_Block_Switch {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->pageSwitch()
			&&
				$this->isTemplateStandard()
		) {
			$result[]= get_class($this);
			if (self::TEMPLATE__STORES === $this->getTemplate()) {
				$result[]= $this->getCurrentGroupId();
			}
			else {
				$result[]= $this->getCurrentStoreId();
				$result[]= $this->getRequest()->getRequestUri();
			}
		}
		return $result;
	}

	/**
	 * Используем этот метод вместо установки cache_lifetime в конструкторе,
	 * потому что конструкторе мы ещё не знаем шаблон блока
	 * @override
	 * @return int|bool
	 */
	public function getCacheLifetime() {
		/** @var int|bool $result */
		$result =
				(
						df_module_enabled(Df_Core_Module::SPEED)
					&&
						df_cfg()->speed()->blockCaching()->pageSwitch()
					&&
						$this->isTemplateStandard()
				)
			?
				/**
				 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
				 * продолжительность хранения кэша надо указывать обязательно,
				 * потому что значением продолжительности по умолчанию является «null»,
				 * что в контексте @see Mage_Core_Block_Abstract
				 * (и в полную противоположность Zend Framework
				 * и всем остальным частям Magento, где используется кэширование)
				 * означает, что блок не удет кэшироваться вовсе!
				 * @used-by Mage_Core_Block_Abstract::_loadCache()
				 */
				Df_Core_Block_Template::CACHE_LIFETIME_STANDARD
			:
				parent::getCacheLifetime()
		;
		return $result;
	}

	/** @return bool */
	private function isTemplateStandard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				in_array(
					$this->getTemplate()
					, array(self::TEMPLATE__FLAGS, self::TEMPLATE__LANGUAGES, self::TEMPLATE__STORES)
				)
			;
		}
		return $this->{__METHOD__};
	}


	const TEMPLATE__FLAGS = 'page/switch/flags.phtml';
	const TEMPLATE__LANGUAGES = 'page/switch/languages.phtml';
	const TEMPLATE__STORES = 'page/switch/stores.phtml';
}