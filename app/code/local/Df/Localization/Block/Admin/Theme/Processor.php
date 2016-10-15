<?php
abstract class Df_Localization_Block_Admin_Theme_Processor extends Df_Core_Block_Admin {
	/** @return string */
	protected function getActionTitle() {return '';}

	/** @return string */
	protected function getCssClass() {return 'rm-' . $this->getProcessor()->getType();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/localization/theme/processor.phtml';}

	/** @return string */
	protected function getLink() {return $this->getProcessor()->getLink();}

	/** @return string */
	protected function getLinksHtml() {
		return implode(df_map(
			'df_tag'
			/** @uses getLinkHtml() */
			,array_map(
				array(__CLASS__, 'getLinkHtml')
				, array_keys($this->getLinksParameters())
				, array_values($this->getLinksParameters())
			)
			,$paramsToAppend = array()
			,$paramsToPrepend = array('div', array())
		));
	}

	/** @return string */
	protected function getLinkTitle() {return '';}

	/** @return Df_Localization_Onetime_Processor */
	protected function getProcessor() {return $this->cfg(self::P__PROCESSOR);}

	/** @return string */
	protected function getTitle() {return df_e($this->getProcessor()->getTitle());}

	/** @return array(array(string => string)) */
	private function getLinksParameters() {
		return array(
			$this->getActionTitle() => array('href' => $this->getLink(), 'title' => $this->getLinkTitle())
			,'демо' => array('href' => $this->getUrl_Demo(), 'target' => '_blank')
			,'форум' => array('href' => $this->getUrl_Forum(), 'target' => '_blank')
			,'купить' => array('href' => $this->getUrl_OfficialSite(), 'target' => '_blank')
		);
	}

	/** @return string[] */
	private function getUrl_Demo() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $resultAsString */
			$resultAsString = df_trim(df_nts(dfa($this->getProcessor()->getUrl(), 'demo')));
			$this->{__METHOD__} =
				!$resultAsString
				? array()
				: df_t()->parseTextarea($resultAsString)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	protected function getUrl_Forum() {return dfa($this->getProcessor()->getUrl(), 'forum');}

	/** @return string|null */
	protected function getUrl_OfficialSite() {return dfa($this->getProcessor()->getUrl(), 'official_site');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PROCESSOR, Df_Localization_Onetime_Processor::class);
	}

	const P__PROCESSOR = 'processor';

	/**
	 * @param Df_Localization_Onetime_Processor $processor
	 * @return string
	 */
	public static function getBlockClass(Df_Localization_Onetime_Processor $processor) {
		return __CLASS__ . '_' . df_ucfirst($processor->getType());
	}

	/**
	 * @param string[] $urls
	 * @return array(string => array(string => string))
	 */
	public static function getDemoLinksParameters(array $urls) {
		/** @var array(string => array(string => string)) $result */
		$result = array();
		/** @var int $i */
		$i = 1;
		foreach ($urls as $url) {
			/** @var string $url */
			$result['демо ' . $i++] = array('href' => $url, 'target' => '_blank');
		}
		return $result;
	}

	/**
	 * @param string $content
	 * @param array(string => string|string[]) $attributes
	 * @return string
	 */
	private static function getLinkHtml($content, array $attributes) {
		/** @var string|string[]|null $href */
		$href = dfa($attributes, 'href');
		if (is_array($href)) {
			if (2 > count($href)) {
				$href = df_first($href);
				$attributes['href'] = $href;
			}
		}
		/** @var string $elementId */
		$elementId = 'dropdown-' . df_uid(5);
		/** разметка для плагина Dropdown: http://labs.abeautifulsite.net/jquery-dropdown/ */
		return
			!is_array($href)
			? df_tag('a', $attributes, $content)
			:
				df_tag('a', df_clean(array(
					'href' => '#'
					, 'data-dropdown' => '#' . $elementId
					, 'target' => null
					, 'class' => 'rm-dropdown'
				) + $attributes), $content)
			.
				df_tag(
					'div'
					, array('class' => 'dropdown dropdown-tip', 'id' => $elementId)
					, df_tag(
						'ul'
						, array('class' => 'dropdown-menu')
						, implode(df_map(
							'df_tag'
							, df_map(
								'df_tag'
								, self::getDemoLinksParameters($href)
								, $paramsToAppend = array()
								, $paramsToPrepend = array('a')
								, $keyPosition = DF_AFTER
							)
							, $paramsToAppend = array()
							, $paramsToPrepend = array('li', array())
						))
					)
				)
		;
	}
}