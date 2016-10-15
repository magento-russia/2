<?php
abstract class Df_Core_Model_Resource_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	/**
	 * 2015-02-13
	 * Цель перекрытия —
	 * устранение дефекта родительского метода.
	 * Родительский метод вызывает $this->getConnection()->fetchCol($idsSelect)
	 * @uses Varien_Db_Adapter_Interface::fetchCol()
	 * без второго параметра $bind, и это может приводить к сбою:
	 * «Invalid parameter number: no parameters were bound, query was:
	 * SELECT `main_table`.`region_id` FROM `directory_country_region` AS `main_table`
	 * LEFT JOIN `directory_country_region_name` AS `rname`
	 * ON main_table.region_id = rname.region_id AND rname.locale = :region_locale
	 * WHERE (main_table.country_id = 'RU')».
	 * @see Df_Directory_Model_Handler_ProcessRegionsAfterLoading::clearCollection()
	 * @override
	 * @return int[]|string[]
	 */
	public function getAllIds() {
		$idsSelect = clone $this->getSelect();
		$idsSelect->reset(Zend_Db_Select::ORDER);
		$idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
		$idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
		$idsSelect->reset(Zend_Db_Select::COLUMNS);
		$idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
		return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
	}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует.
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::getModelName()
	 * @override
	 * @param mixed $args [optional]
	 * @return string
	 */
	public function getModelName($args = array()) {df_should_not_be_here(__METHOD__);}

	/**
	 * 2015-02-09
	 * Пусть инициализация ресурсной модели будет именно такой.
	 * В то же время есть 2 других возможных альтернативных способа инициализации:
	 *
	 * 1) Посредством вызова @see _init() в @see _construct()
	 * Этот способ использовался ранее, например:
			$this->_init(Df_Admin_Model_Role::mf(), Df_Admin_Model_Resource_Role::mf());
	 * Он обладает тем недостатком,
	 * что при создании коллекции посредством new() без параметров
	 * ресурсная модель перестаёт быть объектом-одиночкой и конструируется заново
	 * (по переданному в mf() имени в фомате Magento, например «admin/role»).
	 * Это бьёт по производительности двояко:
	 * а) необходимость самого вызова mf() и трансляция имени в формате Magento
	 * (например «admin/role») в имя класса (например, «Df_Admin_Model_Role»)
	 * б) намного более существенный пункт, чем пункт «а»:
	 * при повторном конструировании ресурсной модели
	 * мы не имеем доступа к кэшу ранее созданной ресурсной модели.
	 *
	 * Обратите внимание, что ядро Magento
	 * всегда разумно использует ресурсные модели как объекты-одиночки.
	 * В частности, метод @see Mage_Core_Model_Abstract::_getResource()
	 * использует для получения ресурсной модели вызов
	 * @see Mage::getResourceSingleton(), а не Mage::getResourceModel(),
	 * а когда модель ядра Magento создаёт коллекцию посредством
	 * посредством @see Mage_Core_Model_Abstract::getCollection()
	 * и @see Mage_Core_Model_Abstract::getResourceCollection(),
	 * модель ядра Magento передаёт свою ресурсную модель (объект-одиночку) в коллекцию.
	 * Таким образом, ресурсная модель остаётся объектом-одиночкой.
	 *
	 * Обратите внимание, что имеется возможность обойти этот недостаток
	 * посредством указания ресурсной модели в качестве параметра конструктора коллекции.
	 * Обратите внимание на реализацию родительского конструктора
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::__construct():
		public function __construct($resource = null) {
			parent::__construct();
			$this->_construct();
			$this->_resource = $resource;
			$this->setConnection($this->getResource()->getReadConnection());
			$this->_initSelect();
		}
	 * Передавая в качестве параметра $resource объект-одиночку ресурсной модели
	 * мы таким образом инициализируем ресурсню модель коллекции объектом-одиночкой.
	 *
	 * 2) Второй альтернативой является перекрытие родительского конструктора
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::__construct().
	 * Снова обратите внимание на устройство родительского конструктора:
			public function __construct($resource = null) {
				parent::__construct();
				$this->_construct();
				$this->_resource = $resource;
				$this->setConnection($this->getResource()->getReadConnection());
				$this->_initSelect();
			}
	 * Во-первых, заметьте, что мы не можем в перекрытом конструкторе
	 * просто установить вручную значение поля @see _resource,
	 * потому что если мы после этого вызовем родительский конструктор без параметров,
	 * то он перетрёт значение поля @see _resource и установить туда значение «null».
	 * Если же мы вызовем родительский конструктор без параметров перед нашим,
	 * то сбой произойдёт на строчке
			$this->setConnection($this->getResource()->getReadConnection());
	 * ведь ресурная модель не была инициализирована.
	 *
	 * Однако есть более хитрый способ перекрытия родительского конструктора:
	 * можно в нашем конструкторе сначала вызвать родительский конструктор,
	 * но не без параметров, а с параметром в виде объекта-одиночки ресурсной модели.
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::getResource().
	 * @override
	 * @return Df_Core_Model_Resource
	 */
	public function getResource() {df_abstract(__METHOD__);}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует, кроме перекрытого нами родительского метода
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::getResource().
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::getResourceModelName().
	 * @override
	 * @return string
	 */
	public function getResourceModelName() {df_should_not_be_here(__METHOD__);}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует, кроме перекрытого нами метода
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::_init().
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::setModel().
	 * @override
	 * @param string $model
	 * @return Df_Core_Model_Resource_Collection
	 */
	public function setModel($model) {df_should_not_be_here(__METHOD__);}

	/**
	 * 2015-02-09
	 * Этот метод никто в Magento не использует, кроме перекрытого нами метода
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::_init().
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::setResourceModel().
	 * @override
	 * @param string $model
	 * @return void
	 */
	public function setResourceModel($model) {df_should_not_be_here(__METHOD__);}

	/**
	 * 2015-02-09
	 * Этот метод никто извне класса не использует,
	 * и классы-предки тоже его не используют,
	 * а классы-потомки не должны его использовать,
	 * потому что архитектура инициализации коллекций Российской сборке Magento
	 * дне подразумевает использования метода @see _init().
	 * Читайте комментарий к методу @see getResource().
	 * @see Df_Core_Model::::_init()
	 * Родительский метод: @see Mage_Core_Model_Mysql4_Collection_Abstract::_init().
	 * @override
	 * @param string $model
	 * @param string|null $resourceModel [optional]
	 * @return Df_Core_Model_Resource_Collection
	 */
	protected function _init($model, $resourceModel = null) {df_should_not_be_here(__METHOD__);}

}


