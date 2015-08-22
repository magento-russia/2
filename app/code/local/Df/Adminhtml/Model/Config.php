<?php
class Df_Adminhtml_Model_Config extends Mage_Adminhtml_Model_Config {
	/**
	 * Цель перекрытия —
	 * возможность указания модуля для перевода надписей в разделе настроек
	 * в том случае, когда сторонний модуль не указал модуль для перевода в файле system.xml.
	 * Magento CE в таком случае использует для перевода модуль Mage_Adminhtml,
	 * однако мы не хотим засорять словарь модуля Mage_Adminhtml
	 * строками для перевода сторонних модулей
	 * (при таком засорении будут конфликты при переводе строк,
	 * идентичных на английском языке, но переводящихся по-разному на русский в разном контексте).
	 * @override
	 * @param Mage_Core_Model_Config_Element|Mage_Core_Model_Config_Element[] $sectionNode
	 * @param Mage_Core_Model_Config_Element|null $groupNode
	 * @param Mage_Core_Model_Config_Element|null $fieldNode
	 * @return string
	 */
	public function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null) {
		/** @var string $result */
		$result = parent::getAttributeModule($sectionNode, $groupNode, $fieldNode);
		if ('adminhtml' === $result) {
			/**
			 * Итак, родительский метод вернул результат по умолчанию.
			 * Теперь смотрим:
			 * 1) относится ли данная ветка настроек к модулю Mage_Adminhtml?
			 * 2) если не относится к Mage_Adminhtml (то есть, относится к строннему модулю)
			 * то имеются ли в настройках Российской сборки Magento какие-либо указания,
			 * кто должен переводить настройки данного модуля?
			 */
			/**
			 * 2014-11-28
			 * Заметил, что в Magento CE 1.9.1.0
			 * сюда в качестве $sectionNode может попадать
			 * не только объект класса Mage_Core_Model_Config_Element,
			 * но и массив из одного элемента класса Mage_Core_Model_Config_Element.
			 * Видимо, так происходит из-за вызова
			 * $this->_sections->xpath($path);
			 * в методе @see Mage_Adminhtml_Model_Config::getSystemConfigNodeLabel().
			 * Странно, что я не получал массив в более ранних версиях Magento CE
			 * при том, что там такой же вызов xpath.
			 */
			if (is_array($sectionNode)) {
				$sectionNode = rm_first($sectionNode);
			}
			if ($sectionNode) {
				/** @var string $sectionName */
				$sectionName = $sectionNode->getName();
				if ($sectionName) {
					/** @var string|null $valueFromMap */
					$valueFromMap =
						Df_Adminhtml_Model_Translator_Config::s()->getHelperModuleMf($sectionName)
					;
					/**
					 * Обратите внимание, что модуль может отсутствовать в системе.
					 * Игнорирование этого может привести к сбою типа
					 * «Warning: include(Mage/Ultraslideshow/Helper/Data.php): failed to open stream»
					 * в методе @see Mage_Adminhtml_Block_System_Config_Tabs::initTabs():
					 	$helperName = $configFields->getAttributeModule($section);
						$label = Mage::helper($helperName)->__((string)$section->label);
					 */
					if ($valueFromMap && self::isItValidModuleNameMf($valueFromMap)) {
						$result = $valueFromMap;
					}
					/**
					 * 2015-08-22
					 * Заметил, что в большинстве (95%) случаев
					 * название секции совпадает с названием модуля в формате Magento.
					 * Чтобы нам не приходилось писать однотипный код типа:
					 * <ves_verticalmenu>ves_verticalmenu</ves_verticalmenu>
					 * пробуем для начала использовать название секции в качестве названия модуля.
					 */
					else if (self::isItValidModuleNameMf($sectionName)){
						$result = $sectionName;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	private static function isItValidModuleNameMf($name) {
		/** @var array(string => bool) */
		static $cache;
		if (!isset($cache[$name])) {
			try {
				Mage::helper($name);
				$cache[$name] = true;
			}
			catch (Exception $e) {
				$cache[$name] = false;
			}
		}
		return $cache[$name];
	}
}