<?php
abstract class Df_Localization_Block_Admin_Theme_Processor extends Df_Core_Block_Admin {
	/** @return string */
	protected function getActionTitle() {return '';}

	/** @return string */
	protected function getCssClass() {return 'rm-' . $this->getProcessor()->getType();}

	/**
	 * @override
	 * @return null|string
	 */
	protected function getDefaultTemplate() {return 'df/localization/theme/processor.phtml';}

	/** @return string */
	protected function getLink() {return $this->getProcessor()->getLink();}

	/** @return string */
	protected function getLinksHtml() {
		return implode(df_map(
			'rm_tag'
			,array_map(array('self', 'getLinkHtml'), $this->getLinksParameters())
			,$paramsToAppend = array()
			,$paramsToPrepend = array('div', array())
		));
	}

	/** @return string */
	protected function getLinkTitle() {return '';}

	/** @return Df_Localization_Model_Onetime_Processor */
	protected function getProcessor() {return $this->cfg(self::P__PROCESSOR);}

	/** @return string */
	protected function getTitle() {return df_escape($this->getProcessor()->getTitle());}

	/** @return array(array(string => string)) */
	private function getLinksParameters() {
		return array(
			array(
				Df_Core_Model_Output_Html_A::P__HREF => $this->getLink()
				,Df_Core_Model_Output_Html_A::P__ANCHOR => $this->getActionTitle()
				,Df_Core_Model_Output_Html_A::P__TITLE => $this->getLinkTitle()
			)
			,array(
				Df_Core_Model_Output_Html_A::P__HREF => $this->getUrl_Demo()
				,Df_Core_Model_Output_Html_A::P__ANCHOR => 'демо'
				,Df_Core_Model_Output_Html_A::P__TARGET => '_blank'
			)
			,array(
				Df_Core_Model_Output_Html_A::P__HREF => $this->getUrl_Forum()
				,Df_Core_Model_Output_Html_A::P__ANCHOR => 'форум'
				,Df_Core_Model_Output_Html_A::P__TARGET => '_blank'
			)
			,array(
				Df_Core_Model_Output_Html_A::P__HREF => $this->getUrl_OfficialSite()
				,Df_Core_Model_Output_Html_A::P__ANCHOR => 'купить'
				,Df_Core_Model_Output_Html_A::P__TARGET => '_blank'
			)
		);
	}

	/** @return string[] */
	private function getUrl_Demo() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $resultAsString */
			$resultAsString = df_trim(df_nts(df_a($this->getProcessor()->getUrl(), 'demo')));
			$this->{__METHOD__} =
				!$resultAsString
				? array()
				: df_text()->parseTextarea($resultAsString)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	protected function getUrl_Forum() {return df_a($this->getProcessor()->getUrl(), 'forum');}

	/** @return string|null */
	protected function getUrl_OfficialSite() {return df_a($this->getProcessor()->getUrl(), 'official_site');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PROCESSOR, Df_Localization_Model_Onetime_Processor::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__PROCESSOR = 'processor';

	/**
	 * @param Df_Localization_Model_Onetime_Processor $processor
	 * @return string
	 */
	public static function getBlockClass(Df_Localization_Model_Onetime_Processor $processor) {
		return __CLASS__ . '_' . ucfirst($processor->getType());
	}

	/**
	 * @param string[] $urls
	 * @return array(array(string => string))
	 */
	public static function getDemoLinksParameters(array $urls) {
		/** @var array(array(string => string)) $result */
		$result = array();
		/** @var int $i */
		$i = 1;
		foreach ($urls as $url) {
			/** @var string $url */
			$result[]= array(
				Df_Core_Model_Output_Html_A::P__HREF => $url
				,Df_Core_Model_Output_Html_A::P__ANCHOR => 'демо ' . $i++
				,Df_Core_Model_Output_Html_A::P__TARGET => '_blank'
			);
		}
		return $result;
	}

	/**
	 * @param array(string => string|string[]) $parameters
	 * @return string
	 */
	private static function getLinkHtml(array $parameters) {
		/** @var string|string[]|null $href */
		$href = df_a($parameters, Df_Core_Model_Output_Html_A::P__HREF);
		if (is_array($href)) {
			if (2 > count($href)) {
				$href = rm_first($href);
				$parameters[Df_Core_Model_Output_Html_A::P__HREF] = $href;
			}
		}
		/** @var string $elementId */
		$elementId = 'dropdown-' . rm_uniqid(5);
		return
			!is_array($href)
			? rm_tag_a($parameters)
			:
				/**
			 	 * Разметка для плагина Dropdown:
				 * @link http://labs.abeautifulsite.net/jquery-dropdown/
			 	 */
				rm_tag('a', df_clean(array_merge($parameters, array(
					'href' => '#'
					,'data-dropdown' => '#' . $elementId
					,'target' => null
					,'anchor' => null
					,'class' => 'rm-dropdown'
				))), df_a($parameters, Df_Core_Model_Output_Html_A::P__ANCHOR))
			.
				rm_tag(
					'div'
					, array('class' => 'dropdown dropdown-tip', 'id' => $elementId)
					, rm_tag(
						'ul'
						, array('class' => 'dropdown-menu')
						, implode(df_map(
							'rm_tag'
							, array_map('rm_tag_a', self::getDemoLinksParameters($href))
							, $paramsToAppend = array()
							, $paramsToPrepend = array('li', array())
						))
					)
				)
		;
	}
}