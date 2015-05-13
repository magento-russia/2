<?php
abstract class Df_Core_Model_Controller_Action_Admin_Entity
	extends Df_Core_Model_Controller_Action_Admin {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getEntityClass();

	/**
	 * @override
	 * @return Df_Core_Controller_Admin_Entity
	 */
	protected function getController() {return parent::getController();}

	/** @return Df_Core_Model_Entity */
	protected function getEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Entity $result */
			$result = df_model($this->getEntityClass());
			df_assert($result instanceof Df_Core_Model_Entity);
			/** @var int $entityId */
			$entityId = rm_nat0($this->getRequestParam($result->getIdFieldName()));
			$this->_entityNew = (0 >= $entityId);
			if (!$this->_entityNew) {
				$result->load($entityId);
				rm_nat($result->getId());
			}
			/** @var array|null $data */
			$data = df_mage()->adminhtml()->session()->getData(get_class($this), $clear = true);
			if ($data) {
				$result->addData($data);
			}
			Mage::register(get_class($result), $result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getRequestParams() {return $this->cfg(self::P__REQUEST_PARAMS);}

	/**
	 * @param string $name
	 * @param string|array|null $defaultValue[optional]
	 * @return string|array|null
	 */
	protected function getRequestParam($name, $defaultValue = null) {
		return df_a($this->getRequestParams(), $name, $defaultValue);
	}

	/** @return bool */
	protected function isEntityNew() {
		if (!isset($this->_entityNew)) {
			$this->getEntity();
			df_result_boolean($this->_entityNew);
		}
		return $this->_entityNew;
	}
	/** @var bool */
	private $_entityNew;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__REQUEST_PARAMS, self::V_ARRAY)
			->_prop(self::P__CONTROLLER, Df_Core_Controller_Admin_Entity::_CLASS);
		;
	}
	const _CLASS = __CLASS__;
	const P__REQUEST_PARAMS = 'request_params';
}