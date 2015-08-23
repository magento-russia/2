<?php
/**
 * @method Df_Widget_Model_Widget_Instance getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Cms_Widget
	extends Df_Localization_Model_Onetime_Processor_Cms {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array('title');}

	/**
	 * @override
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		parent::processTerm($term);
		/** @var array(string => mixed) $widgetParams */
		$widgetParams = $this->getEntity()->getWidgetParameters();
		/** @var bool $translated */
		$translated = false;
		foreach ($this->getTranslatableWidgetParameters() as $paramName) {
			/** @var string $paramName */
			/** @var string|mixed|null $textOriginal */
			$textOriginal = df_a($widgetParams, $paramName);
			if ($textOriginal && is_string($textOriginal)) {
				/** @var string|null $textProcessed */
				$textProcessed = $term->translate($textOriginal);
				if (!is_null($textProcessed)) {
					$widgetParams[$paramName] = $textProcessed;
					$translated = true;
				}
			}
		}
		/** @var int $titleCount */
		$titleCount = 10;
		for ($i = 1; $i <= $titleCount; $i++) {
			/** @var string $titleKey */
			$titleKey = 'title_' . $i;
			/** @var array(string => string)|null $titleValue */
			$titleValue = df_a($widgetParams, $titleKey);
			if (!$titleValue || !is_array($titleValue)) {
				break;
			}
			/** @var string|mixed|null $textOriginal */
			$textOriginal = df_a($titleValue, 0);
			if ($textOriginal && is_string($textOriginal)) {
				/** @var string|null $textProcessed */
				$textProcessed = $term->translate($textOriginal);
				if (!is_null($textProcessed)) {
					$titleValue[0] = $textProcessed;
					$widgetParams[$titleKey] = $titleValue;
					$translated = true;
				}
			}
		}
		if ($translated) {
			$this->getEntity()->setWidgetParameters($widgetParams);
		}
	}

	/**
	 * @override
	 * @return string[]
	 */
	private function getTranslatableWidgetParameters() {return array('frontend_title');}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return bool
	 */
	private function translatedCustomTitles(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		/** @var bool $result */
		$result = false;
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Widget_Model_Widget_Instance::_CLASS);
	}
}