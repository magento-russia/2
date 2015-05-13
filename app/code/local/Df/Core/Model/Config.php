<?php
class Df_Core_Model_Config extends Df_Core_Model_Abstract {
	/**
	 * Извлекает простые строковые значения настроек по заданному пути.
	 * Например, пусть есть настроечная ветка:
		<df>
			<admin>
				<notifiers>
					<class_rewrite_conflicts>Df_Admin_Model_Notifier_ClassRewriteConflicts</class_rewrite_conflicts>
					<delete_demo_stores>Df_Admin_Model_Notifier_DeleteDemoStores</delete_demo_stores>
				</notifiers>
			</admin>
		</df>
	 * Тогда Df_Core_Model_Config::s()->getStringNodes('df/admin/notifiers')
	 * вернёт ассоциативный массив с двумя элементами:
	 * array(
	 		'class_rewrite_conflicts' => 'Df_Admin_Model_Notifier_ClassRewriteConflicts'
	  		, 'delete_demo_stores' => 'Df_Admin_Model_Notifier_DeleteDemoStores'
	   )
	 *
	 * @param string $path
	 * @return string[]
	 */
	public function getStringNodes($path) {
		if (!isset($this->{__METHOD__}[$path])) {
			df_param_string_not_empty($path, 0);
			/** @var Mage_Core_Model_Config_Element|bool $node */
			$node = Mage::getConfig()->getNode($path);
			/** @var string[] $result */
			$result = array();
			if ($node) {
				/**
				 * @see Mage_Core_Model_Config::getNode()
				 * в случае отсутствия ветки возвращает false
				 */
				/** @var array(string => string)|string $nodeAsArray */
				/**
				 * Вызываем именно @see Varien_Simplexml_Element::asCanonicalArray(),
				 * а не @see Varien_Simplexml_Element::asArray(),
				 * потому что @see Varien_Simplexml_Element::asArray()
				 * делает то же, что и @see Varien_Simplexml_Element::asCanonicalArray(),
				 * но дополнительно смотрит, есть ли у настроечных элементов атрибуты
				 * и при их наличии добавляет их в массив.
				 * Когда атрибутов у настроечных элементов заведомо нет,
				 * то выгоднее вызывывать @see Varien_Simplexml_Element::asCanonicalArray() —
				 * этот метод работает быстрее, чем @see Varien_Simplexml_Element::asArray().
				 */
				$nodeAsArray = $node->asCanonicalArray();
				/**
				 * @see Varien_Simplexml_Element::asCanonicalArray()
				 * может вернуть не только массив, но и строку.
				 * Обратите внимание, что если
				 * @see Varien_Simplexml_Element::asCanonicalArray() возвращает массив,
				 * то этот массив — ассоциативный:
				 * его ключами являются имена настрочных узлов.
				 */
				if (is_array($nodeAsArray)) {
					$result = $nodeAsArray;
				}
			}
			$this->{__METHOD__}[$path] = $result;
		}
		return $this->{__METHOD__}[$path];
	}

	/** @return Df_Core_Model_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}