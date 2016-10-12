<?php
/**
 * Объект данного класса способен добавлять новые блоки на страницу товарного раздела
 * без каких-либо правок, перекрытий и наследований системных файлов
 */
class Df_Catalog_Model_Category_Content_Inserter extends Df_Core_Model {
	/** @return bool */
	public function insert() {
		/** @var bool $result */
		$result = $this->getTransport() && $this->getMethodOfUpdate();
		if ($result) {
			call_user_func(array($this, $this->getMethodOfUpdate()), $this->getNewContent());
		}
		return $result;
	}
	
	/** @return string|null */
	private function getMethodOfUpdate() {
		if (!isset($this->{__METHOD__})) {
			/** @var (string|null)[] $map */
			$map = array();
			if ($this->isItProductListBlock()) {
				$map = array(
					Df_Catalog_Model_System_Config_Source_Category_Content_Position
						::DF_AFTER_PRODUCTS => self::DF_METHOD_APPEND
					,Df_Catalog_Model_System_Config_Source_Category_Content_Position
						::DF_BEFORE_PRODUCTS => self::DF_METHOD_PREPEND
					,Df_Catalog_Model_System_Config_Source_Category_Content_Position
						::DF_BEFORE_STATIC_BLOCK => null
					,Df_Catalog_Model_System_Config_Source_Category_Content_Position
						::DF_BEFORE_AND_AFTER_PRODUCTS => self::DF_METHOD_APPEND_AND_PREPEND
				);
			}
			else if ($this->isItCategoryStaticBlock()) {
				$map =
					array(
						Df_Catalog_Model_System_Config_Source_Category_Content_Position
							::DF_AFTER_PRODUCTS => null
						,Df_Catalog_Model_System_Config_Source_Category_Content_Position
							::DF_BEFORE_PRODUCTS => self::DF_METHOD_APPEND
						,Df_Catalog_Model_System_Config_Source_Category_Content_Position
							::DF_BEFORE_STATIC_BLOCK => self::DF_METHOD_PREPEND
						,Df_Catalog_Model_System_Config_Source_Category_Content_Position
							::DF_BEFORE_AND_AFTER_PRODUCTS => null
					)
				;
			}
			$this->{__METHOD__} = rm_n_set(df_a($map, df_cfg()->catalog()->navigation()->getPosition()));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return bool */
	private function isItProductListBlock() {
		return 'product_list' === $this->getBlock()->getNameInLayout();
	}

	/** @return bool */
	private function isItCategoryStaticBlock() {
		return
				($this->getBlock() instanceof Mage_Cms_Block_Block)
			&&
				rm_state()->hasCategory()
			&&
				$this->getBlockId()
			&&
				($this->getBlockId() === $this->getLandingPageId())
		;
	}

	/**
	 * @param string $content
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	public function prependContent($content) {
		$this->setContentByArray(array($content, $this->getContent()));
		return $this;
	}

	/**
	 * @param string $content
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	public function appendAndPrependContent($content) {
		$this->setContentByArray(array($content, $this->getContent(), $content));
		return $this;
	}

	/**
	 * @param string $content
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	public function appendContent($content) {
		$this->setContentByArray(array($this->getContent(), $content));
		return $this;
	}

	/**
	 * @param array $content
	 * @return void
	 */
	private function setContentByArray(array $content) {$this->setContent(implode($content));}

	/**
	 * @param string $content
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	private function setContent($content) {
		$this->getTransport()->setData(self::DF_CONTENT_KEY, $content);
		return $this;
	}

	/** @return string */
	private function getContent() {return $this->getTransport()->getData(self::DF_CONTENT_KEY);}

	/** @return int|null */
	private function getLandingPageId() {
		return
			!rm_state()->hasCategory()
			? null
			: rm_state()->getCurrentCategory()->getData('landing_page')
		;
	}

	/** @return int|null */
	private function getBlockId() {return $this->getBlock()->getData('block_id');}

	/** @return Mage_Core_Block_Abstract */
	private function getBlock() {return $this->_block;}
	/** @var Mage_Core_Block_Abstract */
	private $_block;

	/** @return Varien_Object */
	private function getTransport() {return $this->_transport;}
	/** @var Varien_Object */
	private $_transport;

	/**
	 * Возвращает HTML нового блока
	 * @return string
	 */
	private function getNewContent() {
		if (!isset($this->{__METHOD__})) {
			// Mage_Core_Block_Abstract::$_transportObject — глобальный объект.
			// Его содержимое перетирается в методе Mage_Core_Block_Abstract::toHtml
			// Поэтому перед вызовом toHtml
			// мы сохраняем состояние объекта Mage_Core_Block_Abstract::$_transportObject
			//$currentBlockContent = $this->getContent();
			$transportObjectState = $this->getTransport()->getData();
			$this->{__METHOD__} = $this->getBlockToInsert()->toHtml();
			$this->getTransport()->setData($transportObjectState);
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Block_Abstract */
	private function getBlockToInsert() {return $this->cfg(self::P__BLOCK_TO_INSERT);}

	/** @return Varien_Event_Observer */
	private function getObserver() {return $this->cfg(self::P__OBSERVER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			/**
			 * Удаление $this->_prop(self::P__OBSERVER, self::P__OBSERVER_TYPE)
			 * ускорило загрузку главной страницы с 1.078 сек до 1.067 сек
			 */
			->_prop(self::P__BLOCK_TO_INSERT, self::P__BLOCK_TO_INSERT_TYPE)
		;
		$this->_block = $this->getObserver()->getData('block');
		$this->_transport = $this->getObserver()->getData('transport');
	}
	const _CLASS = __CLASS__;
	const DF_CONTENT_KEY = 'html';
	const DF_METHOD_PREPEND = 'prependContent';
	const DF_METHOD_APPEND = 'appendContent';
	const DF_METHOD_APPEND_AND_PREPEND = 'appendAndPrependContent';
	const P__BLOCK_TO_INSERT = 'blockToInsert';
	const P__BLOCK_TO_INSERT_TYPE = 'Mage_Core_Block_Abstract';
	const P__OBSERVER = 'observer';
	const P__OBSERVER_TYPE = 'Varien_Event_Observer';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}