<?php
class Df_Tweaks_Block_Frontend_Js extends Df_Core_Block_Template {
	/**
	 * @override
	 * @see Df_Core_Block_Abstract::cacheKeySuffix()
	 * @used-by Df_Core_Block_Abstract::getCacheKeyInfo()
	 * @return string|string[]
	 */
	public function cacheKeySuffix() {return df_handles();}
	
	/** @return string */
	public function getOptionsAsJson() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Раньше тут стояло
			 * $theme = rm_design_package()->getTheme('skin');
			 * То есть, мы использовали в качестве идентификатора темы
			 * значение опции «Нестандартная папка браузерных файлов».
			 * Однако в оформительской теме Gala TitanShop в одном из демо-примеров
			 * (и в других аналогично) значением опции «Нестандартная папка браузерных файлов»
			 * является «galatitanshop_lingries_style01»,
			 * в то время как опция «Нестандартная папка темы» имеет правильное значение
			 * «galatitanshop».
			 * Поэтому вместо
			 * $theme = rm_design_package()->getTheme('skin');
			 * я решил использовать
			 * $theme = rm_design_package()->getTheme('default');
			 * Передавая в метод getTheme() параметр «default», мы извлекаем значение опции
			 * «Нестандартная папка темы».
			 */
			/** @var array(string => string) $options */
			$options = array(
				'package' => rm_state()->getCurrentDesignPackage()
				,'theme' => rm_state()->getCurrentDesignTheme()
			);
			/**
			 * С другой стороны, значение опции «Нестандартная папка браузерных файлов»
			 * нам тоже может потребоваться: ведь именно значение этой опции определяет,
			 * какие файлы CSS будут загружены.
			 * Поэтому записываем в rm.tweaks.options и это значение тоже,
			 * только не в ключе «theme», а в ключе «skin».
			 */
			/** @var string $skin */
			$skin = rm_design_package()->getTheme('skin');
			if ($skin) {
				$options['skin'] = $skin;
			}
			// быстро узнать версию движка при просмотре страницы
			// нам важно для диагностики
			$options['version'] = array('rm' => df_version(), 'core' => Mage::getVersion());
			$options['formKey'] = df_session_core()->getFormKey();
			/** @var string $result */
			$this->{__METHOD__} = df_output()->json($options);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/tweaks/js.phtml';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
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
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
	}

}