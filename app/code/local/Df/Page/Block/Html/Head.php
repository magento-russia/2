<?php
class Df_Page_Block_Html_Head extends Mage_Page_Block_Html_Head {
	/**
	 * Add HEAD Item
	 *
	 * Allowed types:
	 *  - js
	 *  - js_css
	 *  - skin_js
	 *  - skin_css
	 *  - rss
	 * @override
	 * @param string $type
	 * @param string $name
	 * @param string|null $params [optional]
	 * @param string|null $if [optional]
	 * @param string|null $cond [optional]
	 * @return Df_Page_Block_Html_Head
	 */
	public function addItem($type, $name, $params = null, $if = null, $cond = null) {
		if (!df_h()->page()->head()->needSkipItem($type, $name)) {
			if ('prepend' !== $params) {
				parent::addItem($type, $name, $params, $if, $cond);
			}
			else {
				$params = null;
				dfa_unshift_assoc($this->_data['items'], $type.'/'.$name, array(
					'type' => $type
					,'name' => $name
					,'params' => $params
					,'if' => $if
					,'cond' => $cond
				));
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getCssJsHtml() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			/**
			 * 2015-08-25
			 * Раньше тут стояло:
			 * $result[]= rm_action_name();
			 * Крайне неряшливый модуль Ves_Blog
			 * оформительской темы Ves Super Store (ThemeForest 8002349)
			 * ломает инициализацию системы, и в данной точке программы
			 * контроллер может быть ещё не инициализирован.
			 */
			/** @var string $a */
			$a = rm_action_name();
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(__METHOD__, $a ? $a : df_current_url());
			$result = $this->getCache()->loadData($cacheKey);
			if (!$result) {
				$result = parent::getCssJsHtml();
				$this->getCache()->saveData($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 *
	 * Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
	 *
	 * Will attempt to merge into 1 directive, if merging callback is provided. In this case it will generate
	 * filenames, rather than render urls.
	 * The merger callback is responsible for checking whether files exist, merging them and giving result URL
	 *
	 * @param string $format - HTML element format for df_sprintf('<element src="%s"%s />', $src, $params)
	 * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
	 * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
	 * @param callback $mergeCallback
	 * @return string
	 */
	protected function &_prepareStaticAndSkinElements ($format, array $staticItems, array $skinItems, $mergeCallback = null) {
		if (is_null($mergeCallback)) {
			$staticItems = Df_Page_Head::addVersionStamp($staticItems);
			/**
			 * Обратите внимание, что для ресурсов темы мы добавляем параметр v по-другому:
			 * @see Df_Core_Model_Design_PackageM::getSkinUrl
			 *
			 * Здесь нам добавлять v было нельзя: ведь getSkinUrl работает с именами файлов
			 * и просто не найдёт файл с именем file.css?v=1.33.3
			 */
		}
		// Промежуточную переменную $result использовать необходимо,
		// потому что иначе система педупреждает:
		// «Notice: Only variable references should be returned by reference»
		/** @var string $result */
		$result = df_ccc("\n"
			,Df_Page_Head::prependTags($format, $staticItems)
			,parent::_prepareStaticAndSkinElements($format, $staticItems, $skinItems, $mergeCallback)
		);
		return $result;
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Ровно так же и ядро Magento использует значение
			 * @see Mage_Core_Block_Abstract::CACHE_GROUP
			 * для обозначения как типа кэша, так и тэга:
			 * @used-by Mage_Core_Block_Abstract::getCacheTags()
			 */
			$this->{__METHOD__} = Df_Core_Model_Cache::i(Mage_Core_Block_Abstract::CACHE_GROUP, true);
		}
		return $this->{__METHOD__};
	}


	const P__CAN_LOAD_TINY_MCE = 'can_load_tiny_mce';
}