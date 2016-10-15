<?php
class Df_Cms_Block_Admin_Hierarchy_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/** @return bool */
	public function canDragNodes() {return !$this->isLockedByOther();}

	/** @return string */
	public function getButtonSaveLabel() {return df_h()->cms()->__('Add To Tree');}

	/** @return string */
	public function getButtonUpdateLabel() {return df_h()->cms()->__('Update');}

	/** @return null|int */
	public function getCurrentStore() {return $this->_currentStore;}

	/** @return string */
	public function getCurrentStoreUrlParam() {
		/* @var Df_Core_Model_StoreM $store */
		$store = $this->_currentStore ? df_store($this->_currentStore) : Mage::app()->getAnyStoreView();
		return '?___store=' . $store->getCode();
	}

	/** @return string */
	public function getGridJsObject() {
		return $this->getParentBlock()->getChild('cms_page_grid')->getJsObjectName();
	}

	/**
	 * Return List styles separately for unordered/ordererd list as json
	 * @return string
	 */
	public function getListModesJson() {
		$listModes = Df_Cms_Model_Source_Hierarchy_Menu_Listmode::s()->toOptionArray();
		$result = array();
		foreach ($listModes as $type => $label) {
			if ('' === $type) {
				continue;
			}
			$listType = in_array($type, array('circle', 'disc', 'square')) ? '0' : '1';
			$result[$listType][$type] = $label;
		}
		return df_mage()->coreHelper()->jsonEncode($result);
	}

	/**
	 * Retrieve lock message for js alert
	 * @return string
	 */
	public function getLockAlertMessage() {
		return df_h()->cms()->__('Page lock expires in 60 seconds. Save changes to avoid possible data loss.');
	}

	/** @return int */
	public function getLockLifetime() {return $this->_getLockModel()->getLockLifeTime();}

	/** @return string */
	public function getNodeFieldsetLegend() {return df_h()->cms()->__('Node Properties');}

	/**
	 * Retrieve current nodes Json basing on data loaded from
	 * DB or from model in case we had error in save process.
	 * @return string
	 */
	public function getNodesJson() {
		$nodes = array();
		/* @var $node Df_Cms_Model_Hierarchy_Node */
		$nodeModel = Mage::registry('current_hierarchy_node');
		// restore data is exists
		$data = df_mage()->coreHelper()->jsonDecode($nodeModel->getNodesData());
		if (is_array($data)) {
			foreach ($data as $v) {
				/** @var array(string => mixed) $v */
				$nodes[]= Df_Cms_Helper_Hierarchy::s()->copyMetaData($v, array(
					'node_id' => $v['node_id']
					,'parent_node_id' => $v['parent_node_id']
					,'label' => $v['label']
					,'identifier' => $v['identifier']
					,'page_id' => empty($v['page_id']) ? null : $v['page_id']
				));
			}
		}
		else {
			$collection =
				$nodeModel->getCollection()
					->joinCmsPage()
					->addCmsPageInStoresColumn()
					->joinMetaData()
					->setOrderByLevel()
			;
			foreach ($collection as $item) {
				/* @var Df_Cms_Model_Hierarchy_Node $item */
				$nodes[]= Df_Cms_Helper_Hierarchy::s()->copyMetaData($item->getData(), array(
					'node_id' => $item->getId()
					,'parent_node_id' => $item->getParentNodeId()
					,'label' => $item->getLabel()
					,'identifier' => $item->getIdentifier()
					,'page_id' => $item->getPageId()
					,'assigned_to_store' => $this->isNodeAvailableForStore($item, $this->_currentStore)
				));
			}
		}
		return df_mage()->coreHelper()->jsonEncode($nodes);
	}

	/**
	 * Return legend for Hierarchy page fieldset
	 * @return string
	 */
	public function getPageFieldsetLegend() {return df_h()->cms()->__('Page Properties');}

	/**
	 * Retrieve buttons HTML for Cms Page Grid
	 * @return string
	 */
	public function getPageGridButtonsHtml() {
		return df_admin_button(array(
			'id' => 'add_cms_pages'
			,'label' => df_h()->cms()->__('Add Selected Page(s) to Tree')
			,'onclick' => 'hierarchyNodes.pageGridAddSelected()'
			,'class' => 'add' . (($this->isLockedByOther()) ? ' disabled' : '')
			,'disabled' => $this->isLockedByOther()
		));
	}

	/**
	 * Retrieve Buttons HTML for Page Properties form
	 * @return string
	 */
	public function getPagePropertiesButtons() {
		return implode(' ', array(
			df_admin_button(array(
				'id' => 'delete_node_button'
				,'label' => df_h()->cms()->__('Remove From Tree')
				,'onclick' => 'hierarchyNodes.deleteNodePage()'
				,'class' => 'delete' . (($this->isLockedByOther()) ? ' disabled' : '')
				,'disabled' => $this->isLockedByOther()
			))
			,df_admin_button(array(
				'id' => 'cancel_node_button'
				,'label' => df_h()->cms()->__('Cancel')
				,'onclick' => 'hierarchyNodes.cancelNodePage()'
				,'class' => 'delete' . (($this->isLockedByOther()) ? ' disabled' : '')
				,'disabled'  => $this->isLockedByOther()
			))
			,df_admin_button(array(
				'id' => 'save_node_button'
				,'label' => df_h()->cms()->__('Save')
				,'onclick' => 'hierarchyNodes.saveNodePage()'
				,'class' => 'save' . (($this->isLockedByOther()) ? ' disabled' : '')
				,'disabled' => $this->isLockedByOther()
			))
		));
	}

	/** @return string */
	public function getStoreBaseUrl() {
		/* @var Df_Core_Model_StoreM $store */
		$store = $this->_currentStore ? df_store($this->_currentStore) : Mage::app()->getAnyStoreView();
		return $store->getBaseUrl();
	}

	/**
	 * Retrieve html of store switcher added from layout
	 * @return string
	 */
	public function getStoreSwitcherHtml() {
		return $this->getLayout()->getBlock('store_switcher')->setUseConfirm(false)->toHtml();
	}

	/** @return string */
	public function getTreeButtonsHtml() {
		return df_admin_button(array(
			'id' => 'new_node_button'
			,'label' => df_h()->cms()->__('Add Node...')
			,'onclick' => 'hierarchyNodes.newNodePage()'
			,'class'  => 'add' . (($this->isLockedByOther()) ? ' disabled' : '')
			,'disabled'  => $this->isLockedByOther()
		));
	}

	/** @return bool */
	public function isLockedByMe() {
		if (!$this->hasData('locked_by_me')) {
			$this->setData('locked_by_me', $this->_getLockModel()->isLockedByMe());
		}
		return $this->_getData('locked_by_me');
	}

	/** @return bool */
	public function isLockedByOther() {
		if (!$this->hasData('locked_by_other')) {
			$this->setData('locked_by_other', $this->_getLockModel()->isLockedByOther());
		}
		return $this->_getData('locked_by_other');
	}

	/**
	 * Check if passed node available for store in case this node representation of page.
	 * If node does not represent page then method will return true.
	 * @param Df_Cms_Model_Hierarchy_Node $node
	 * @param null|int $store
	 * @return bool
	 */
	public function isNodeAvailableForStore(Df_Cms_Model_Hierarchy_Node $node, $store) {
		return !$node->getPageId()
			|| !$store
			|| !$node->getPageInStores()
			|| in_array($store, df_csv_parse_int($node->getPageInStores()))
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/cms/hierarchy/edit.phtml');
		$this->_currentStore = $this->getRequest()->getParam('store');
	}

	/**
	 * Retrieve lock model
	 * @return Df_Cms_Model_Hierarchy_Lock
	 */
	protected function _getLockModel() {return Df_Cms_Model_Hierarchy_Lock::s();}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form
	 */
	protected function _prepareForm() {
		/** @var Varien_Data_Form $form */
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form'
			,'action' => $this->getUrl('*/*/save')
			,'method' => 'post'
		));
		$this->addGeneralSettings($form);
		$this->addAsideMenuSettings($form);
		$this->addPaginationSettings($form);
		if ($this->isLockedByOther()) {
			foreach ($form->getElements() as $formElement) {
				/** @var Varien_Data_Form_Element_Abstract $formElement */
				if ('fieldset' === $formElement->getType()) {
					/** @uses Varien_Data_Form_Element_Abstract::setData() */
					df_each($formElement->getElements(), 'setData', 'disabled', true);
				}
			}
		}
		$form->setUseContainer(true);
		$this->setForm($form);
		parent::_prepareForm();
		return $this;
	}

	/**
	 * @param Varien_Data_Form $form
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form
	 */
	private function addAsideMenuSettings(Varien_Data_Form $form) {
		/** @var array $optionsYesNo */
		$optionsYesNo = df_mage()->adminhtml()->yesNo()->toOptionArray();
		/**
		 * Context menu options
		 */
		$menuFieldset = $form->addFieldset('menu_fieldset', array(
			'legend' => df_h()->cms()->__('Navigation Menu Options')
		));
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$menuFieldset->addField('menu_excluded', 'select', array(
			'label' => df_h()->cms()->__('Exclude from Navigation Menu')
			,'name' => 'menu_excluded'
			,'values' => $optionsYesNo
			,'onchange' => "hierarchyNodes.nodeChanged()"
			,'container_id' => 'field_menu_excluded'
			,'tabindex' => '1000'
		));
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupFrontPage */
		$groupFrontPage = Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(array(
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__FIELDSET => $menuFieldset
			,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__ENABLED__LABEL => 'Показывать на главной странице витрины?'
			,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::FRONT
			,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__POSITION__LABEL => 'Место на главной странице витрины'
			,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__TAB_INDEX => 4000
			,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
				::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на главной странице витрины'
		));
		$groupFrontPage->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupCatalogProductListPage */
		$groupCatalogProductListPage =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(array(
				Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__FIELDSET => $menuFieldset
				,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__ENABLED__LABEL => 'Показывать на страницах списка товаров?'
				,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_LIST
				,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__POSITION__LABEL => 'Место на страницах списка товаров'
				,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__TAB_INDEX => 4010
				,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
					::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на страницах списка товаров'
			))
		;
		$groupCatalogProductListPage->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupCatalogProductViewPage */
		$groupCatalogProductViewPage =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(
				array(
					Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__FIELDSET => $menuFieldset
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__ENABLED__LABEL => 'Показывать на страницах товаров?'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_VIEW
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__POSITION__LABEL => 'Место на страницах товаров'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__TAB_INDEX => 4020
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на страницах товаров'
				)
			)
		;
		$groupCatalogProductViewPage->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupAccountPage */
		$groupAccountPage =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(
				array(
					Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__FIELDSET => $menuFieldset
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__ENABLED__LABEL => 'Показывать на страницах личного кабинета?'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::ACCOUNT
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__POSITION__LABEL => 'Место на страницах личного кабинета'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__TAB_INDEX => 4030
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на страницах личного кабинета'
				)
			)
		;
		$groupAccountPage->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupCmsPageOwn */
		$groupCmsPageOwn =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(
				array(
					Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__FIELDSET => $menuFieldset
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__ENABLED__LABEL => 'Показывать на подчинённых текстовых страницах?'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::CMS_OWN
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__POSITION__LABEL => 'Место на подчинённых текстовых страницах'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__TAB_INDEX => 4040
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на подчинённых текстовых страницах'
				)
			)
		;
		$groupCmsPageOwn->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupCmsPageForeign */
		$groupCmsPageForeign =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(
				array(
					Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__FIELDSET => $menuFieldset
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__ENABLED__LABEL => 'Показывать на чужих текстовых страницах?'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::CMS_FOREIGN
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__POSITION__LABEL => 'Место на чужих текстовых страницах'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__TAB_INDEX => 4050
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на чужих текстовых страницах'
				)
			)
		;
		$groupCmsPageForeign->addFields();
		/** @var Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType $groupOtherPage */
		$groupOtherPage =
			Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::i(
				array(
					Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__FIELDSET => $menuFieldset
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__ENABLED__LABEL => 'Показывать на других страницах?'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__PAGE_TYPE_ID => Df_Cms_Model_ContentsMenu_PageType::OTHER
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__POSITION__LABEL => 'Место на других страницах'
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__TAB_INDEX => 4060
					,Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType
						::P__VERTICAL_ORDERING__LABEL => 'Порядковый номер меню на других страницах'
				)
			)
		;
		$groupOtherPage->addFields();
		$menuBriefOptions =
			array(
				array(
					'value' => 1
					,'label' => df_h()->cms()->__('Only Children')
				)
				,array(
					'value' => 0
					,'label' => df_h()->cms()->__('Neighbours and Children')
				)
			)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$menuFieldset
			->addField(
				'menu_brief'
				,'select'
				,array(
					'label' => df_h()->cms()->__('Menu Detalization')
					,'name' => 'menu_brief'
					,'values' => $menuBriefOptions
					,'onchange' => "hierarchyNodes.nodeChanged()"
					,'container_id' => 'field_menu_brief'
					,'tabindex' => '5000'
				)
			)
		;
		$menuFieldset
			->addField(
				'menu_levels_down'
				,'text'
				,array(
					'name' => 'menu_levels_down'
					,'label' => df_h()->cms()->__('Maximal Depth')
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'container_id' => 'field_menu_levels_down'
					,'note' => df_h()->cms()->__('Node Levels to Include')
					,'tabindex' => '6000'
				)
			)
		;
		$menuFieldset
			->addField(
				'menu_ordered'
				,'select'
				,array(
					'label' => df_h()->cms()->__('List Type')
					,'title' => df_h()->cms()->__('List Type')
					,'name' => 'menu_ordered'
					,'values' => Df_Cms_Model_Source_Hierarchy_Menu_Listtype::s()->toOptionArray()
					,'onchange' => 'hierarchyNodes.menuListTypeChanged()'
					,'container_id' => 'field_menu_ordered'
					,'tabindex' => '7000'
				)
			)
		;
		$menuFieldset
			->addField(
				'menu_list_type'
				,'select'
				,array(
					'label' => df_h()->cms()->__('List Style')
					,'title' => df_h()->cms()->__('List Style')
					,'name' => 'menu_list_type'
					,'values' => Df_Cms_Model_Source_Hierarchy_Menu_Listtype::s()->toOptionArray()
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'container_id' => 'field_menu_list_type'
					,'tabindex'  => '8000'
				)
			)
		;
		return $this;
	}

	/**
	 * @param Varien_Data_Form $form
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form
	 */
	private function addGeneralSettings(Varien_Data_Form $form) {
		$fieldset =
			$form
				->addFieldset(
					'node_properties_fieldset'
					,array(
						'legend' => df_h()->cms()->__('Page Properties')
					)
				)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$fieldset
			->addField(
				'nodes_data'
				,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,array(
					'name' => 'nodes_data'
				)
			)
		;
		$fieldset
			->addField(
				'removed_nodes'
				,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,array(
					'name' => 'removed_nodes'
				)
			)
		;
		$fieldset
			->addField(
				'node_id'
				,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,array(
					'name' => 'node_id'
				)
			)
		;
		$fieldset
			->addField(
				'node_page_id'
				,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,array(
					'name' => 'node_page_id'
				)
			)
		;
		$fieldset
			->addField(
				'node_label'
				,'text'
				,array(
					'name' => 'label'
					,'label' => df_h()->cms()->__('Title')
					,'required' => true
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'tabindex' => '10'
				)
			)
		;
		$fieldset
			->addField(
				'node_identifier'
				,'text'
				,array(
					'name' => 'identifier'
					,'label' => df_h()->cms()->__('URL Key')
					,'required' => true
					,'class' => 'rm.validate.urlKey'
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'tabindex' => '20'
				)
			)
		;
		$fieldset
			->addField(
				'node_label_text'
				,'note'
				,array(
					'label' => df_h()->cms()->__('Title')
				)
			)
		;
		$fieldset
			->addField(
				'node_identifier_text'
				,'note'
				,array(
					'label' => df_h()->cms()->__('URL Key')
				)
			)
		;
		$fieldset
			->addField(
				'node_edit'
				,'link'
				,array(
					'href' => '#'
				)
			)
		;
		$fieldset
			->addField(
				'node_preview'
				,'link'
				,array(
					'href' => '#'
					,'value' => df_h()->cms()->__('No preview available')
				)
			)
		;
		return $this;
	}

	/**
	 * @param Varien_Data_Form $form
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form
	 */
	private function addPaginationSettings(Varien_Data_Form $form) {
		/**
		 * Pagination options
		 */
		$pagerFieldset =
			$form->addFieldset(
				'pager_fieldset'
				,array(
					'legend' => df_h()->cms()->__('Pagination Options for Nested Pages')
				)
			)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$pagerFieldset->addField(
			'pager_visibility'
			,'select'
			,array(
				'label' => df_h()->cms()->__('Enable Pagination')
				,'name' => 'pager_visibility'
				,'values' => Df_Cms_Model_Source_Hierarchy_Visibility::s()->toOptionArray()
				,'value' => Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT
				,'onchange' => "hierarchyNodes.metadataChanged('pager_visibility', 'pager_fieldset')"
				,'tabindex' => '70'
			)
		);
		$pagerFieldset
			->addField(
				'pager_frame'
				,'text'
				,array(
					'name' => 'pager_frame'
					,'label' => df_h()->cms()->__('Frame')
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'container_id' => 'field_pager_frame'
					,'note' => df_h()->cms()->__('How many Links to display at once')
					,'tabindex' => '80'
				)
			)
		;
		$pagerFieldset
			->addField(
				'pager_jump'
				,'text'
				,array(
					'name' => 'pager_jump'
					,'label' => df_h()->cms()->__('Frame Skip')
					,'onchange' => 'hierarchyNodes.nodeChanged()'
					,'container_id' => 'field_pager_jump'
					,'note' =>
						df_h()->cms()->__(
							'If the Current Frame Position does not cover Utmost Pages, will render Link to Current Position plus/minus this Value'
						)
					,'tabindex'  => '90'
				)
			)
		;
		return $this;
	}
	/**
	 * Currently selected store in store switcher
	 * @var null|int
	 */
	protected $_currentStore = null;


}