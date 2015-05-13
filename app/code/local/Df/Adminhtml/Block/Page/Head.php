<?php
class Df_Adminhtml_Block_Page_Head extends Mage_Adminhtml_Block_Page_Head {
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
	 * @param string $params
	 * @param string $if
	 * @param string $cond
	 * @return Df_Page_Block_Html_Head
	 */
	public function addItem($type, $name, $params = null, $if = null, $cond = null) {
		if (!df_h()->page()->head()->needSkipItem($type, $name)) {
			if (self::PREPEND !== $params) {
				parent::addItem($type, $name, $params, $if, $cond);
			}
			else {
				$params = null;
				df_array_unshift_assoc(
					$this->_data['items']
					,$type.'/'.$name
					,array(
						'type' => $type
						,'name' => $name
						,'params' => $params
						,'if' => $if
						,'cond' => $cond
					)
				)
				;
			}
		}
		return $this;
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
	 * @param string $format - HTML element format for rm_sprintf('<element src="%s"%s />', $src, $params)
	 * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
	 * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
	 * @param callback $mergeCallback
	 * @return string
	 */
	protected function &_prepareStaticAndSkinElements (
		$format
		,array $staticItems
		,array $skinItems
		,$mergeCallback = null
	) {
		/** @var Df_Page_Model_Html_Head $adjuster */
		$adjuster = Df_Page_Model_Html_Head::s();
		if (is_null($mergeCallback)) {
			$staticItems = $adjuster->addVersionStamp($staticItems);
			/**
			 * Обратите внимание, что для ресурсов темы мы добавляем параметр v по-другому:
			 * в методе @see Df_Core_Model_Design_PackageM::getSkinUrl()
			 * Здесь нам добавлять v было нельзя: ведь getSkinUrl работает с именами файлов
			 * и просто не найдёт файл с именем file.css?v=1.33.3
			 */
		}
		// Промежуточную переменную $result использовать необходимо,
		// потому что иначе система педупреждает:
		// «Notice: Only variable references should be returned by reference»
		/** @var string $result */
		$result = rm_concat_clean("\r\n"
			, $adjuster->prependAdditionalTags($format, $staticItems)
			, parent::_prepareStaticAndSkinElements(
				$format, $staticItems, $skinItems, $mergeCallback
			)
		);
		return $result;
	}

	const _CLASS = __CLASS__;
	const PREPEND = 'prepend';

}