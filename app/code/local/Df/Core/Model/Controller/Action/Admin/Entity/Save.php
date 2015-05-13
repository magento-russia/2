<?php
abstract class Df_Core_Model_Controller_Action_Admin_Entity_Save
	extends Df_Core_Model_Controller_Action_Admin_Entity {
	/**
	 * @abstract
	 * @return Df_Core_Model_Controller_Action_Admin_Entity_Save
	 */
	abstract protected function entityUpdate();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getFormClass();

	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	protected function generateResponseBody() {
		df_assert(!!$this->getRequestParams());
		try {
			$this->processDependencies();
			$this->entityUpdate();
			$this->getEntity()->save();
			$this->postProcessSuccess();
		}
		catch(Exception $e) {
			if ($this->isDependent()) {
				throw $e;
			}
			else {
				$this->handleException($e);
			}
		}
		if (!$this->isDependent()) {
			$this->redirectToProperPage();
		}
		return '';
	}

	/** @return Df_Core_Model_Form */
	protected function getForm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_model(
					$this->getFormClass()
					,array(Df_Core_Model_Form::P__ZEND_FORM_VALUES => $this->getRequestParams())
				)
			;
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_Form);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getMessageSuccessForExistedEntity() {return 'Объект изменён';}

	/** @return string */
	protected function getMessageSuccessForNewEntity() {return 'Объект добавлен';}

	/** @return string */
	private function getMessageSuccess() {
		return
			$this->isEntityNew()
			? $this->getMessageSuccessForNewEntity()
			: $this->getMessageSuccessForExistedEntity()
		;
	}

	/**
	 * @param Exception $e
	 * @return Df_Core_Model_Controller_Action_Admin_Entity_Save
	 */
	private function handleException(Exception $e) {
		rm_exception_to_session($e);
		df_mage()->adminhtml()->session()->setData(
			$this->getEntity()->getSessionKey(), $this->getRequestParams()
		);
		$this->setNeedRedirectBack(true);
		df_handle_entry_point_exception($e, $rethrow = false);
		return $this;
	}

	/** @return bool */
	private function isDependent() {return $this->cfg(self::P__DEPENDENT, false);}

	/** @return bool */
	private function isNeedRedirectBack() {
		/**
		 * Кэширование результата здесь необходимо,
		 * потому что результат может быть установлен вручную методом
		 * @see setNeedRedirectBack()
		 */
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_bool($this->getRequestParam('back', false));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Controller_Action_Admin_Entity_Save */
	private function postProcessSuccess() {
		if (!$this->isDependent()) {
			df_mage()->adminhtml()->session()
				->addSuccess($this->getMessageSuccess())
				/**
				 * Так как мы необязательно возвращаемся на страницу редактирования сущности,
				 * где сессия сущности извлекается (на случай сбоев редактирования сущности)
				 * и очищается,
				 * а можем вместо этого вернуться на страницу списка товаров,
				 * где сессию сущности никто не использует и не очищает,
				 * то очищаем сессию сущности здесь.
				 */
				->unsetData($this->getEntity()->getSessionKey())
			;
		}
		return $this;
	}

	/** @return Df_Core_Model_Controller_Action_Admin_Entity_Save */
	private function processDependencies() {
		foreach ($this->getEntity()->getDependenciesInfo() as $dependency) {
			/** @var Df_Core_Model_Entity_Dependency $dependency */
			$this->processDependency($dependency);
		}
		return $this;
	}

	/**
	 * @param Df_Core_Model_Entity_Dependency $dependency
	 * @return Df_Core_Model_Controller_Action_Admin_Entity_Save
	 */
	private function processDependency(Df_Core_Model_Entity_Dependency $dependency) {
		/** @var Df_Core_Model_Controller_Action_Admin_Entity_Save $dependencySaveAction */
		$dependencySaveAction =
			df_model(
				$dependency->getActionSaveClassName()
				,array(
					Df_Core_Model_Controller_Action_Admin_Entity_Save::P__CONTROLLER =>
						$this->getController()
					,Df_Core_Model_Controller_Action_Admin_Entity_Save::P__DEPENDENT => true
					,Df_Core_Model_Controller_Action_Admin_Entity_Save::P__REQUEST_PARAMS =>
						df_a(
							$this->getRequestParams()
							,$dependency->getName()
						)
				)
			)
		;
		df_assert(
				$dependencySaveAction
			instanceof
				Df_Core_Model_Controller_Action_Admin_Entity_Save
		);
		$dependencySaveAction->process();
		$this->setData(
			self::P__REQUEST_PARAMS
			,array_merge(
				$this->getRequestParams()
				,array(
					$dependency->getEntityIdFieldName()	=> $dependencySaveAction->getEntity()->getId()
				)
			)
		);
		return $this;
	}

	/** @return Df_Core_Model_Controller_Action_Admin_Entity_Save */
	private function redirectToProperPage() {
		if (!$this->isNeedRedirectBack()) {
			$this->redirect('*/*/');
		}
		else {
			$this->redirect(
				'*/*/edit'
				,array(
					/**
					 * Обратите внимание, что при запросе на редактирование сущности
					 * идентификатор сущности передаётся в адресе запроса
					 * параметром «id» (для красоты адреса),
					 * однако при запросе на сохранение сущности идентификатор сущности
					 * передаётсся в массиве POST параметром, имя которого соответствует
					 * имени идентификатора сущности (getIdFieldName())
					 */
					Df_Core_Controller_Admin_Entity::REQUEST_PARAM__ENTITY_ID =>
						$this->getEntity()->getId()
				)
			);
		}
		return $this;
	}

	/**
	 * @param bool $value
	 * @return Df_Core_Model_Controller_Action_Admin_Entity_Save
	 */
	private function setNeedRedirectBack($value) {
		df_param_boolean($value, 0);
		$this->{__CLASS__ . '::isNeedRedirectBack'} = $value;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__DEPENDENT, self::V_BOOL, false);
	}
	const _CLASS = __CLASS__;
	const P__DEPENDENT = 'dependent';
}