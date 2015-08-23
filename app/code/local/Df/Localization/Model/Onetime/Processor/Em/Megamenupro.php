<?php
/**
 * @method EM_Megamenupro_Model_Megamenupro getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Em_Megamenupro
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'name';}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array('text', 'label');}

	/**
	 * @override
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		/** @var array(array(string => mixed)) $content */
		$content = $this->getEntity()->getData('content');
		/** @var bool $translated */
		$translated = false;
		if (is_array($content)) {
			foreach ($content as &$item) {
				/** @var array(string => mixed) $item */
				foreach ($this->getTranslatableProperties() as $propertyName) {
					/** @var string $propertyName */
					/** @var string|null $textOriginal */
					$textOriginal = df_a($item, $propertyName);
					if ($textOriginal) {
						/** @var string|null $textProcessed */
						$textProcessed = $term->translate($textOriginal);
						if (!is_null($textProcessed)) {
							$item[$propertyName] = $textProcessed;
							$translated = true;
						}
					}
				}
			}
		}
		if ($translated) {
			$this->getEntity()->setData('content', $content);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, 'EM_Megamenupro_Model_Megamenupro');
	}
}