<?php
/**
 * Объект данного класса способен добавлять новые блоки на страницу товарного раздела
 * без каких-либо правок, перекрытий и наследований системных файлов.
 * @used-by Df_Catalog_Observer::core_block_abstract_to_html_after()
 */
class Df_Catalog_Model_Category_Content_Inserter extends Df_Core_Model {
	/**
	 * @used-by insert()
	 * @return bool
	 */
	private function _insert() {
		/** @var bool $result */
		$result = $this->getMethodOfUpdate();
		if ($result) {
			call_user_func(array($this, $this->getMethodOfUpdate()), $this->contentNew());
		}
		return $result;
	}

	/**
	 * @used-by _insert()
	 * @param string $content
	 * @return void
	 */
	private function append($content) {$this->setContent($this->contentTarget() . $content);}

	/**
	 * @used-by _insert()
	 * @param string $content
	 * @return void
	 */
	private function appendAndPrepend($content) {
		$this->setContent($content . $this->contentTarget() . $content);
	}

	/** @return Mage_Core_Block_Abstract */
	private function getBlock() {return $this->_block;}

	/** @return int|null */
	private function getBlockId() {return $this->getBlock()->getData('block_id');}

	/** @return string */
	private function contentTarget() {return $this->_transport['html'];}

	/** @return int|null */
	private function getLandingPageId() {
		return
			!df_state()->hasCategory()
			? null
			: df_state()->getCurrentCategory()->getData('landing_page')
		;
	}

	/** @return string|null */
	private function getMethodOfUpdate() {
		if (!isset($this->{__METHOD__})) {
			/** @var (string|null)[] $map */
			$map = array();
			if ($this->isItProductListBlock()) {
				$map = array(
					Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_AFTER_PRODUCTS => self::$METHOD__APPEND
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_PRODUCTS => self::$METHOD__PREPEND
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_STATIC_BLOCK => null
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_AND_AFTER_PRODUCTS => 'appendAndPrepend'
				);
			}
			else if ($this->isItCategoryStaticBlock()) {
				$map = array(
					Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_AFTER_PRODUCTS => null
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_PRODUCTS => self::$METHOD__APPEND
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_STATIC_BLOCK => self::$METHOD__PREPEND
					,Df_Catalog_Model_Config_Source_Category_Content_Position
						::DF_BEFORE_AND_AFTER_PRODUCTS => null
				);
			}
			$this->{__METHOD__} = df_n_set(dfa($map, df_cfg()->catalog()->navigation()->getPosition()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает HTML нового блока
	 * @return string
	 */
	private function contentNew() {return Df_Catalog_Block_Category_Navigation::r();}

	/** @return bool */
	private function isItCategoryStaticBlock() {
		return
			$this->getBlock() instanceof Mage_Cms_Block_Block
			&& df_state()->hasCategory()
			&& $this->getBlockId()
			&& $this->getBlockId() === $this->getLandingPageId()
		;
	}

	/** @return bool */
	private function isItProductListBlock() {
		return 'product_list' === $this->getBlock()->getNameInLayout();
	}

	/**
	 * @used-by _insert()
	 * @param string $content
	 * @return void
	 */
	private function prepend($content) {$this->setContent($content . $this->contentTarget());}

	/**
	 * @used-by append()
	 * @used-by appendAndPrepend()
	 * @used-by prepend()
	 * @param string $content
	 * @return void
	 */
	private function setContent($content) {$this->_transport['html'] = $content;}

	/** @var Mage_Core_Block_Abstract */
	private $_block;

	/**
	 * 2015-03-30
	 * Обратите внимание, что хотя Magento CE 1.4.0.1 не передаёт «transport»,
	 * у нас «transport» есть всегда, потому что мы перекрыли класс @see Mage_Core_Block_Abstract
	 * классом @see Mage_1920_Core_Block_Abstract
	 * @var Varien_Object
	 */
	private $_transport;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Удаление $this->_prop(self::P__OBSERVER, self::P__OBSERVER_TYPE)
		 * ускорило загрузку главной страницы с 1.078 сек до 1.067 сек
		 */
		/** @var Varien_Event_Observer $observer */
		$observer = $this[self::$P__OBSERVER];
		$this->_block = $observer['block'];
		$this->_transport = $observer['transport'];
	}

	/** @var string */
	private static $METHOD__APPEND = 'append';
	/** @var string */
	private static $METHOD__PREPEND = 'prepend';
	/** @var string */
	private static $P__OBSERVER = 'observer';

	/**
	 * @used-by Df_Catalog_Observer::core_block_abstract_to_html_after()
	 * @param Varien_Event_Observer $o
	 * @return bool
	 */
	public static function insert(Varien_Event_Observer $o) {
		/** @var Df_Catalog_Model_Category_Content_Inserter $i */
		$i = new self(array(self::$P__OBSERVER => $o));
		return $i->_insert();
	}
}