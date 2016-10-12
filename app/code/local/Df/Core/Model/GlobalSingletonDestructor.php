<?php
/**
 * Этот класс предназначен для деинициализации глобальных объектов-одиночек.
 * Опасно проводить деинициализации глобальных объектов-одиночек в стандартном деструкторе,
 * потому что к моменту вызова деструктора для данного объекта-одиночки
 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
 * требуемые для сохранения кэша.
 */
class Df_Core_Model_GlobalSingletonDestructor extends Df_Core_Model {
	/** @return void */
	public function process() {
		foreach ($this->_objects as $object) {
			/** @var Df_Core_Destructable $object */
			$object->_destruct();
		}
	}

	/**
	 * Обратите внимание, что первый параметр вовсе необязательно будет иметь класс @see Varien_Object.
	 * Например, класс @see Df_Eav_Model_Config,
	 * который вызывает метод @see Df_Core_Model_GlobalSingletonDestructor::register() в своём конструкторе,
	 * не имеет тип @see Varien_Object,
	 * потому что его родительский класс @see Mage_Eav_Model_Config не унаследован от @see Varien_Object.
	 * Вообще, далеко не все системные классы Magento (которые нам приходится перекрывать)
	 * унаследованы от @see Varien_Object.
	 * @param Df_Core_Destructable $object
	 * @return void
	 */
	public function register(Df_Core_Destructable $object) {
		$this->_objects[]= $object;
	}
	/** @var Df_Core_Destructable[] */
	private $_objects = array();

	/** @return Df_Core_Model_GlobalSingletonDestructor */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}