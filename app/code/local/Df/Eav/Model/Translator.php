<?php
class Df_Eav_Model_Translator extends Df_Core_Model_DestructableSingleton {
	/**
	 * @override
	 * @see Df_Core_Model::_destruct()
	 * @return void
	 */
	public function _destruct() {
		if (
			df_my_local()
			&& !df_is_admin()
			&& Mage::isInstalled()
			&& $this->_untranslated
		) {
			Mage::log(
				'Добавьте в файл app/locale/ru_DF/Df/Eav.csv'
				. " перевод следующих экранных названий свойств:\n"
				. df_cc_n(dfa_unique_fast($this->_untranslated))
				, null
				, 'rm.translation.log'
			);
		}
		parent::_destruct();
	}

	/**
	 * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
	 * @return void
	 */
	public function translateAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute) {
		if (
				df_h()->eav()->isAttributeBelongsToProduct($attribute)
			&&
				!isset($attribute->{self::$RM_TRANSLATED})
			&&
				(
						df_is_admin()
					||
						df_bool($attribute->getIsVisibleOnFront())
					||
						(
								// Для свойства «Special Price» («special_price») is_visible_on_front = false,
								// но is_visible = true
								df_bool($attribute->getData('is_visible'))
							&&
								// В магазине rukodeling.ru для нестандартного свойства «diametrdyr»
								// is_visible почему-то равно true.
								// Отбраковываем все нестандартные свойства,
								// у которых is_visible_on_front = false.
								!df_bool($attribute->getData('is_user_defined'))
						)
				)
		) {
			$attribute->addData($this->translateLabels(dfa_select($attribute->getData(), self::$LABEL_NAMES)));
			$attribute->{self::$RM_TRANSLATED} = true;
		}
		/**
		 * Решает проблему http://magento-forum.ru/topic/4494/
		 * После доработок кэширования стала получаться такая ситуация:
			Mage_Catalog_Model_Resource_Eav_Attribute(
				[frontend_label] => Название
				[store_label] =>
			)
		 * А панель инструментов списка товаров
		 * берёт подлежащие упорядочиванию товарные свойства именно из store_label:
		 * @see Mage_Catalog_Model_Config::getAttributeUsedForSortByArray():
		 * $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
		 */
		/** @var string|null $storeLabel */
		$storeLabel = $attribute->getData('store_label');
		/** @var string|null $frontendLabel */
		$frontendLabel = $attribute->getData('frontend_label');
		if (!$storeLabel && $frontendLabel) {
			$attribute->setData('store_label', $frontendLabel);
		}
		else if ($storeLabel && !$frontendLabel) {
			$attribute->setData('frontend_label', $storeLabel);
		}
	}

	/**
	 * @param array(string => mixed) $attributeData
	 * @return void
	 */
	public function translateAttributeAssoc(array &$attributeData) {
		/** @var array(string => mixed $attributeData) */
		if (
				df_is_admin()
			||
				df_bool(dfa($attributeData, 'is_visible_on_front'))
			||
				(
						// Для свойства «Special Price» («special_price») is_visible_on_front = false,
						// но is_visible = true
						df_bool(dfa($attributeData, 'is_visible'))
					&&
						// В магазине rukodeling.ru для нестандартного свойства «diametrdyr»
						// is_visible почему-то равно true.
						// Отбраковываем все нестандартные свойства,
						// у которых is_visible_on_front = false.
						!df_bool(dfa($attributeData, 'is_user_defined'))
				)
		) {
			$attributeData =
				$this->translateLabels(dfa_select($attributeData, self::$LABEL_NAMES)) + $attributeData
			;
		}
	}

	/**
	 * @param string $label
	 * @param bool $logUntranslated [optional]
	 * @return string
	 */
	public function translateLabel($label, $logUntranslated = true) {
		if (!isset($this->{__METHOD__}[$label])) {
			/**
			 * Раньше тут стояло:
				$result = df_mage()->catalogHelper()->__($label);
				if ($result === $label) {
					$result = df_h()->eav()->__($label);
				}
			 * Изменил код ради ускорения.
			 */
			/** @var string $result */
			$result = df_translate_simple($label, 'Mage_Catalog');
			if ($result === $label) {
				$result = df_translate_simple($label, 'Df_Eav');
			}
			if (
					$logUntranslated
				&&
					df_my_local()
				&&
					($result === $label)
				&&
					!df_t()->isTranslated($label)
			) {
				$this->_untranslated[]= $label;
			}
			else {
				//Mage::log(sprintf('«%s» => «%s»', $label, $result));
			}
			$this->{__METHOD__}[$label] = $result;
		}
		return $this->{__METHOD__}[$label];
	}

	/**
	 * @param array(string => string) $labels
	 * @return array(string => string)
	 */
	public function translateLabels(array $labels) {
		/** @var array(string => string) $result */
		$result = [];
		/** @var string|null $translatedValue */
		$translatedValue = null;
		foreach ($labels as $labelName => $labelValue) {
			/** @var string $labelName */
			/** @var string $labelValue */
			if ($labelValue) {
				/**
				 * В магазине rukodeling.ru заметил такую ситуацию:
					Array
					(
						[frontend_label] => Rasmerlista
						[store_label] => Размер
					)
				 * Наш алгоритм позволяет перевести оба названия
				 * (обратите внимание, что в $LABEL_NAMES
				 * ключ store_label предшествует ключу frontend_label).
				 */
				/** @var string $translatedValueCurrent */
				$translatedValueCurrent =
					$this->translateLabel($labelValue, $logUntranslated = !$translatedValue)
				;
				if (!df_t()->isTranslated($translatedValueCurrent)) {
					$result[$labelName] = $translatedValue ? $translatedValue : $labelValue;
				}
				else {
					$result[$labelName] = $translatedValueCurrent;
					if (!$translatedValue) {
						$translatedValue = $translatedValueCurrent;
					}
				}
			}
			else {
				// Если !$labelValue,
				// то нам всё равно надо инициализировать в результирующем массиве ключ $labelName.
				$result[$labelName] = $labelValue;
			}
		}
		return $result;
	}

	/**
	 * @param array(string => mixed) $optionData
	 * @return void
	 */
	public function translateOptionAssoc(array &$optionData) {
		foreach (self::$OPTION_LABEL_NAMES as $labelName) {
			/** @var string $labelName */
			/** @var string|null $labelValue */
			$labelValue = dfa($optionData, $labelName);
			if ($labelValue) {
				$optionData[$labelName] = $this->translateLabel($labelValue);
			}
		}
	}

	/** @var string[] */
	private $_untranslated = [];

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
	/** @var string[] */
	private static $LABEL_NAMES = array('store_label', 'frontend_label');
	/** @var string[] */
	private static $OPTION_LABEL_NAMES = array('title', 'default_title');
	/** @var string */
	private static $RM_TRANSLATED = 'df_translated';
}