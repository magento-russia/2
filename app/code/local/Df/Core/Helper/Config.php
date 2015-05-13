<?php
class Df_Core_Helper_Config extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $keyAsString
	 * @return string[]
	 */
	public function explodeKey($keyAsString) {
		df_param_string($keyAsString, 0);
		return explode(self::PATH_SEPARATOR, $keyAsString);
	}

	/**
	 * Обратите внимание, что Magento кэширует объект-результат!
	 * @param string $key
	 * @return Mage_Core_Model_Config_Element|null
	 */
	public function getNodeByKey($key) {
		df_param_string_not_empty($key, 0);
		/** @var Mage_Core_Model_Config_Element|null $result */
		$result = Mage::getConfig()->getNode($key);
		/** @see Mage_Core_Model_Config::getNode() в случае отсутствия ветки возвращает false */
		return $result ? $result : null;
	}

	/**
	 * @param Mage_Core_Model_Config_Element|null $node
	 * @return string
	 */
	public function getNodeValueAsString($node) {
		if (!is_null($node)) {
			df_assert($node instanceof Mage_Core_Model_Config_Element);
		}
		return
			is_null($node)
			? ''
			:
				df_trim(
					/**
					 * Обратите внимание, что у классов
					 * Mage_Core_Model_Config_Element и SimpleXMLElement
					 * отсутствует метод __toString
					 * Так же, к некорректным результатам ведёт asXML()
					 * Однако (string) почему-то работает :-)
					 *
					 * Однако в функции df_module_enabled(string) не работает,
					 * а работает asXML
					 */
					(string)$node
				)
		;
	}

	const _CLASS = __CLASS__;
	const PATH_SEPARATOR = '/';
	/** @return Df_Core_Helper_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}