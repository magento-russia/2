<?php
abstract class Df_Core_Controller_Admin_Entity extends Df_Core_Controller_Admin {
	/** @return string */
	abstract protected function getActionSaveClass();
	/** @return string */
	abstract protected function getActiveMenuPath();
	/** @return string */
	abstract protected function getEntityClass();
	/** @return string */
	abstract protected function getEntityTitle();
	/** @return string */
	abstract protected function getEntityTitleNew();
	/** @return string */
	abstract protected function getMessageDeleteSuccess();
	/** @return string[] */
	abstract protected function getTitleParts();

	/** @return void */
	public function deleteAction() {
		Df_Core_Model_Controller_Action_Admin_Entity_Delete::i(
			array(
				Df_Core_Model_Controller_Action_Admin_Entity_Delete::P__CONTROLLER => $this
				,Df_Core_Model_Controller_Action_Admin_Entity_Delete
					::P__ENTITY_CLASS => $this->getEntityClass()
				,Df_Core_Model_Controller_Action_Admin_Entity_Delete
					::P__MESSAGE_SUCCESS => $this->getMessageDeleteSuccess()
				,Df_Core_Model_Controller_Action_Admin_Entity_Delete
					::P__REQUEST_PARAMS => $this->getRequest()->getParams()
			)
		)->process();
	}

	/** @return void */
	public function editAction() {
		$this->setTitle();
		$this->title(
			$this->getEntity()->getId() ? $this->getEntityTitle() : $this->getEntityTitleNew()
		);
		$this->loadAndRenderLayout();
	}

	/** @return void */
	public function indexAction() {
		$this->setTitle();
		$this->loadAndRenderLayout();
	}

	/** @return void */
	public function newAction() {$this->_forward('edit');}

	/** @return void */
	public function saveAction() {
		/** @var Df_Core_Model_Controller_Action_Admin_Entity_Save $action */
		$action =
			df_model(
				$this->getActionSaveClass()
				,array(
					Df_Core_Model_Controller_Action_Admin_Entity_Save::P__CONTROLLER => $this
					,Df_Core_Model_Controller_Action_Admin_Entity_Save
						::P__REQUEST_PARAMS => $this->getRequest()->getParams()
				)
			)
		;
		df_assert($action instanceof Df_Core_Model_Controller_Action_Admin_Entity_Save);
		$action->process();
	}

	/** @return Df_Core_Model_Entity */
	protected function getEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Entity $result */
			$result = df_model($this->getEntityClass());
			df_assert($result instanceof Df_Core_Model_Entity);
			/** @var int $entityId */
			$entityId =
				rm_nat0(
					$this->getRequest()->getParam(
						/**
						 * Обратите внимание, что при запросе на редактирование сущности
						 * идентификатор сущности передаётся в адресе запроса
						 * параметром «id» (для красоты адреса),
						 * однако при запросе на сохранение сущности идентификатор сущности
						 * передаётсся в массиве POST параметром, имя которого соответствует
						 * имени идентификатора сущности (getIdFieldName())
						 */
						self::REQUEST_PARAM__ENTITY_ID
					)
				)
			;
			if (0 < $entityId) {
				$result->load($entityId);
				rm_nat($result->getId());
			}
			/** @var array|null $data */
			$data =
				df_mage()->adminhtml()->session()->getData($result->getSessionKey(), $clear = true)
			;
			if ($data) {
				$result->addData($data);
			}
			Mage::register($result->getSessionKey(), $result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Controller_Admin_Entity */
	private function loadAndRenderLayout() {
		$this->loadLayout();
		$this->setActiveMenu($this->getActiveMenuPath());
		$this->addBreadcrumb(
			rm_last($this->getTitleParts()), rm_last($this->getTitleParts())
		);
		$this->renderLayout();
		return $this;
	}

	/** @return Df_Core_Controller_Admin_Entity */
	private function setTitle() {
		foreach ($this->getTitleParts() as $titlePart) {
			/** @var string|int|bool|null $titlePart */
			$this->title($this->__($titlePart));
		}
		return $this;
	}

	const _CLASS = __CLASS__;
	/**
	 * Обратите внимание, что при запросе на редактирование сущности
	 * идентификатор сущности передаётся в адресе запроса
	 * параметром «id» (для красоты адреса),
	 * однако при запросе на сохранение сущности идентификатор сущности
	 * передаётсся в массиве POST параметром, имя которого соответствует
	 * имени идентификатора сущности (getIdFieldName())
	 */
	const REQUEST_PARAM__ENTITY_ID = 'id';
}