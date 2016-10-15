<?php
class Df_Cms_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function admin_user_delete_after() {
		if (df_cfg()->cms()->versioning()->isEnabled()) {
			/** @var Df_Cms_Model_Page_Version $version */
			$version = Df_Cms_Model_Page_Version::i();
			$collection = $version->getCollection();
			$collection
				->addAccessLevelFilter(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PRIVATE)
				->addUserIdFilter()
			;
			/** @var Mage_Core_Model_Resource_Iterator $iterator */
			$iterator = Mage::getSingleton('core/resource_iterator');
			$iterator->walk(
				$collection->getSelect()
				/** @uses Df_Cms_Observer::removeVersionCallback() */
				,array(array($this, 'removeVersionCallback'))
				,array('version'=> $version)
			);
		}
	}

	/**
	 * @used-by Df_Logging_Model_Processor::logAction()
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function adminhtml_cms_hierarchy_index(
		Varien_Simplexml_Element $config, Df_Logging_Model_Event $eventModel
	) {
		return
			df_cfg()->cms()->hierarchy()->isEnabled()
			? $eventModel->setInfo(df_h()->cms()->__('Tree Viewed'))
			: false
		;
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main::_prepareForm()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function adminhtml_cms_page_edit_tab_main_prepare_form(Varien_Event_Observer $o) {
		/** @var Varien_Data_Form $form */
		$form = $o['form'];
		/* @var $baseFieldset Varien_Data_Form_Element_Fieldset */
		$baseFieldset = $form->getElement('base_fieldset');
		/* @var $baseFieldset Varien_Data_Form_Element_Fieldset */
		/* @var $page Mage_Cms_Model_Page */
		$page = Mage::registry('cms_page');
		if ($page) {
			$this->replaceValidatorByCyrillic($baseFieldset);
		}
		if (df_cfg()->cms()->versioning()->isEnabled()) {
			$isActiveElement = $form->getElement('is_active');
			if ($isActiveElement) {
				// Making is_active as disabled if user does not have publish permission
				if (!Df_Cms_Model_Config::s()->canCurrentUserPublishRevision()) {
					$isActiveElement->setDisabled(true);
				}
			}
			/*
			 * Adding link to current published revision
			 */
			$revisionAvailable = false;
			if ($page) {
				$this->replaceValidatorByCyrillic($baseFieldset);
				/**
				 * Обратите внимание,
				 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
				 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
				 * возвращает не $fieldset, а созданное поле.
				 */
				$baseFieldset
					->addField(
						'under_version_control'
						,'select'
						,array(
							'label'	=> df_h()->cms()->__('Under Version Control')
							,'title' => df_h()->cms()->__('Under Version Control')
							,'name' => 'under_version_control'
							,'values' => df_mage()->adminhtml()->yesNo()->toOptionArray()
						)
					)
				;
				if ($page->getPublishedRevisionId() && $page->getUnderVersionControl()) {
					$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
					/** @var Df_Cms_Model_Page_Revision $revision */
					$revision = Df_Cms_Model_Page_Revision::i();
					$revision
						->loadWithRestrictions(
							$accessLevel, rm_admin_id(), $page->getPublishedRevisionId()
						)
					;
					if ($revision->getId()) {
						$revisionNumber = $revision->getRevisionNumber();
						$versionNumber = $revision->getVersionNumber();
						$versionLabel = $revision->getLabel();
						$page->setPublishedRevisionLink(
							df_h()->cms()->__('%s; rev #%s', $versionLabel, $revisionNumber));
						$baseFieldset
							->addField(
								'published_revision_link'
								,'link'
								,array(
									'label' => df_h()->cms()->__('Currently Published Revision')
									,'href' => rm_url_admin('*/cms_page_revision/edit', array(
										'page_id' => $page->getId()
										,'revision_id' => $page->getPublishedRevisionId()
									))
								)
							)
						;
						$revisionAvailable = true;
					}
				}
			}
			if ($revisionAvailable && !rm_admin_allowed('cms/page/save_revision')) {
				/** @uses Varien_Data_Form_Element_Abstract::setData() */
				df_each($baseFieldset->getElements(), 'setData', 'disabled', true);
			}
			/*
			 * User does not have access to revision or revision is no longer available
			 */
			if (!$revisionAvailable && $page->getId() && $page->getUnderVersionControl()) {
				$baseFieldset->addField('published_revision_status', 'label', array('bold' => true));
				$page->setPublishedRevisionStatus(df_h()->cms()->__('Published Revision Unavailable'));
			}
		}
	}

	/**
	 * @used-by Df_Logging_Model_Processor::logAction()
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function adminhtml_cms_page_revision_preview(
		Varien_Simplexml_Element $config, Df_Logging_Model_Event $eventModel
	) {
		return
			df_cfg()->cms()->versioning()->isEnabled()
			? $eventModel->setInfo(Mage::app()->getRequest()->getParam('revision_id'))
			: false
		;
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Df_Cms_Controller_Router::match()
	 * @see Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main::match()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function cms_controller_router_match_before(Varien_Event_Observer $o) {
		if (df_cfg()->cms()->hierarchy()->isEnabled()) {
			/**
			 * @var Varien_Object $condition
			 * @see Df_Cms_Controller_Router::match()
			 * @see Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main::match()
				$condition = new Varien_Object(array('identifier' => $identifier, 'continue' => true));
			 */
			$condition = $o['condition'];
			/* @var Df_Cms_Model_Hierarchy_Node $node */
			$node = Df_Cms_Model_Hierarchy_Node::i();
			/** @var string $requestUrl */
			$requestUrl = $condition['identifier'];
			$node->loadByRequestUrl($requestUrl);
			if ($node->checkIdentifier($requestUrl, df_store())) {
				$condition['continue'] = false;
			}
			if (!$node->getId()) {
				return;
			}
			if (!$node->getPageId()) {
				/* @var $child Df_Cms_Model_Hierarchy_Node */
				$child = Df_Cms_Model_Hierarchy_Node::i();
				$child->loadFirstChildByParent($node->getId());
				if (!$child->getId()) {
					return;
				}
				$url = Mage::getUrl('', array('_direct' => $child->getRequestUrl()));
				/**
				 * @see Df_Cms_Controller_Router::match()
				 * @see Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main::match()
				 * if ($condition->getRedirectUrl()) {
				 */
				$condition['redirect_url'] = $url;
			} else {
				if (!$node->getPageIsActive()) {
					return;
				}
				// register hierarchy and node
				Mage::register('current_cms_hierarchy_node', $node);
				$condition['continue'] = true;
				$condition['identifier'] = $node->getPageIdentifier();
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function cms_page_delete_after(Varien_Event_Observer $o) {
		/* @var Mage_Cms_Model_Page $page */
		$page = $o['object'];
		Df_Cms_Model_Resource_Increment::s()->cleanIncrementRecord(
			Df_Cms_Model_Increment::TYPE_PAGE
			, $page->getId()
			, Df_Cms_Model_Increment::LEVEL_VERSION
		);
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function cms_page_save_after(Varien_Event_Observer $o) {
		/* @var Mage_Cms_Model_Page|Df_Cms_Model_Page $page */
		$page = $o['object'];
		if (df_cfg()->cms()->versioning()->isEnabled()) {
			// Create new initial version & revision if it
			// is a new page or version control was turned on for this page.
			if (
					$page->getIsNewPage()
				||
						$page->getUnderVersionControl()
					&&
						$page->dataHasChangedFor('under_version_control')
			) {
				/** @var Df_Cms_Model_Page_Version $version */
				$version = Df_Cms_Model_Page_Version::i();
				$revisionInitialData = $page->getData();
				$revisionInitialData['copied_from_original'] = true;
				$version
					->setLabel($page->getTitle())
					->setAccessLevel(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
					->setPageId($page->getId())
					->setUserId(rm_admin_id())
					->setInitialRevisionData($revisionInitialData)
					->save()
				;
				if ($page->getUnderVersionControl()) {
					$revision = $version->getLastRevision();
					if ($revision instanceof Df_Cms_Model_Page_Revision) {
						$revision->publish();
					}
				}
			}
		}
		if (df_cfg()->cms()->hierarchy()->isEnabled()) {
			// rebuild URL rewrites if page has changed for identifier
			if ($page->dataHasChangedFor('identifier')) {
				Df_Cms_Model_Hierarchy_Node::s()->updateRewriteUrls($page);
			}
			/*
			 * Appending page to selected nodes it will remove pages from other nodes
			 * which are not specified in array. So should be called even array is empty!
			 * Returns array of new ids for page nodes array( oldId => newId ).
			 */
			Df_Cms_Model_Hierarchy_Node::s()->appendPageToNodes($page, $page->getAppendToNodes());
			/*
			 * Updating sort order for nodes in parent nodes which have current page as child
			 */
			foreach ($page->getNodesSortOrder() as $nodeId => $value) {
				Df_Cms_Model_Resource_Hierarchy_Node::s()->updateSortOrder($nodeId, $value);
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function cms_page_save_before(Varien_Event_Observer $o) {
		/* @var Mage_Cms_Model_Page|Df_Cms_Model_Page $page */
		$page = $o['object'];
		if (df_cfg()->cms()->versioning()->isEnabled()) {
			/*
			 * All new pages created by user without permission to publish
			 * should be disabled from the beginning.
			 */
			if (!$page->getId()) {
				$page->setIsNewPage(true);
				if (!Df_Cms_Model_Config::s()->canCurrentUserPublishRevision()) {
					$page->setIsActive(false);
				}
				// newly created page should be auto assigned to website root
				//$page->setWebsiteRoot(true);
			} else if (!$page->getUnderVersionControl()) {
				$page->setPublishedRevisionId(null);
			}
		}
		if (df_cfg()->cms()->hierarchy()->isEnabled()) {
			/*
			 * Checking if node's data was passed and if yes. Saving new sort order for nodes.
			 */
			$nodesData = $page->getNodesData();
			$appendToNodes = array();
			$sortOrders = array();
			if ($nodesData) {
				$nodesData = df_mage()->coreHelper()->jsonDecode($page->getNodesData());
				if (!empty($nodesData)) {
					/**
					 * Нам надо различать случаи:
					 *
					 * 1)	когда текущая страница не привязана ни к одному разделу
					 * 		(не включаем её в оглавление вовсе)
					 *
					 * 2)	когда текущая страница привязана к разделу "Корень"
					 * 		(назначаем текущую страницу одним из корневых разделов)
					 */
					/** @var bool $currentPageIsOrphan */
					$currentPageIsOrphan = true;
					/** @var bool $currentPageIsRoot */
					$currentPageIsRoot = false;
					foreach ($nodesData as $row) {
						if (df_bool(dfa($row, 'current_page'))) {
							$currentPageIsOrphan = false;
							/** @var int $sortOrder */
							$sortOrder = df_int(dfa($row, 'sort_order'));
							/** @var int $parentNodeId */
							$parentNodeId = df_nat0(dfa($row, 'parent_node_id'));
							$currentPageIsRoot = (0 === $parentNodeId);
							if ($currentPageIsRoot) {
								/**
								 * Привязываем текущую страницу к разделу "Корень"
								 */
								if ($page->getId()) {
									/**
									 * Видимо, здесь надо создать корневой узел иерархии для текущей страницы,
									 * если его ещё не существует
									 */
									/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $currentPageRootNodes */
									$currentPageRootNodes = Df_Cms_Model_Hierarchy_Node::c();
									$currentPageRootNodes
										->addPageFilter($page->getId())
										->addRootNodeFilter()
									;
									if (!$currentPageRootNodes->count()) {
										/** @var Df_Cms_Model_Hierarchy_Node $node */
										$node = Df_Cms_Model_Hierarchy_Node::i(array(
											'page_id' => $page->getId()
											,'level' => 1
											,'sort_order' => $sortOrder
											,'request_url' => $page->getIdentifier()
										));
										$node->setDataChanges(true);
										$node->save();
									}
								}
							}
							/**
							 * Не прерываем цикл посредством break,
							 * потому что одна страница может быть привязана к разным разделам
							 */
						}
					}
					if ($currentPageIsOrphan && $page->getId()) {
						/**
						 * Надо удалить все узлы иерархии, привязанные к текущей странице
						 */
						Df_Cms_Model_Resource_Hierarchy_Node::s()->deleteNodesByPageId($page->getId());
					}
					if (!$currentPageIsRoot) {
						Df_Cms_Model_Resource_Hierarchy_Node::s()->deleteRootNodesByPageId($page->getId());
					}
					foreach ($nodesData as $row) {
						/** @var int $parentNodeId */
						$parentNodeId = df_nat0(dfa($row, 'parent_node_id'));
						/**
						 * В качестве типа данных используем строку,
						 * потому что некоторые идентификаторы
						 * начинаются с символа подчёркивания.
						 *
						 * Начальным символом подчёркивания обозначаются новые узлы дерева,
						 * которые образуются при перемещении администратором страницы
						 * в иерархии (при этом сама страница может быть не новой)
						 *
						 * @var string $nodeId
						 */
						$nodeId = df_string(dfa($row, 'node_id'));
						/**
						 * Странно названным флагом «page_exists»
						 * обозначаются те разделы, где администратор поставил флажок
						 * (то есть, обозначил их как родительские разделы текущей страницы)
						 *
						 * @var bool $markedAsParent
						 */
						$markedAsParent =
							df_bool(
								dfa($row, 'page_exists')
							)
						;
						/** @var int $sortOrder */
						$sortOrder = df_int(dfa($row, 'sort_order'));
						if ($markedAsParent) {
							$appendToNodes[$nodeId] = 0;
						}

						if (isset($appendToNodes[$parentNodeId])) {
							if (df_contains($nodeId, '_')) {
								/**
								 * Начальным символом подчёркивания обозначаются новые узлы дерева, * которые образуются при перемещении администратором страницы
								 * в иерархии (при этом сама страница может быть не новой)
								 */
								$appendToNodes[$parentNodeId] = $sortOrder;
							} else {
								/**
								 * Запоминаем порядок текущей статьи и её статьей-братьев
								 */
								$sortOrders[$nodeId] = $sortOrder;
							}
						}
					}
				}
			}
			$page->setNodesSortOrder($sortOrders);
			$page->setAppendToNodes($appendToNodes);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Cms_Model_Page::getAvailableStatuses()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function cms_page_get_available_statuses(Varien_Event_Observer $o) {
		if (df_cfg()->cms()->versioning()->isEnabled()) {
			/** @var Varien_Object $statuses */
			$statuses = $o['statuses'];
			$statuses->setData(Mage_Cms_Model_Page::STATUS_ENABLED, df_h()->cms()->__('Published'));
			$statuses->setData(Mage_Cms_Model_Page::STATUS_DISABLED, df_h()->cms()->__('Unpublished'));
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $o) {
		try {
			if (df_cfg()->cms()->hierarchy()->isEnabled()) {
				df_handle_event(
					Df_Cms_Model_Handler_ContentsMenu_Insert::_C
					,Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_C
					,$o
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Df_Cms_Observer::admin_user_delete_after()
	 * @used-by Mage_Core_Model_Resource_Iterator::walk()
	 * @param array(string => mixed) $args
	 */
	public function removeVersionCallback($args) {
		/**
		 * 2015-02-01
		 * Проверки на лицензирование и включенность модуля намеренно убрал,
		 * потому что данный метод использзуется только как callback из метода
		 * @used-by Df_Cms_Observer::admin_user_delete_after(),
		 * где такие проверки уже осуществлены.
		 */
		/** @var Df_Cms_Model_Page_Version $version */
		$version = $args['version'];
		$version->setData($args['row']);
		try {
			$version->delete();
		}
		catch (Mage_Core_Exception $e) {
			// If we have situation when revision from
			// orphaned private version published we should
			// change its access level to protected so publisher
			// will have chance to see it and assign to some user
			$version->setAccessLevel(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PROTECTED);
			$version->save();
		}
	}

	/**
	 * @param Varien_Data_Form_Element_Fieldset $fieldset
	 * @return void
	 */
	private function replaceValidatorByCyrillic(Varien_Data_Form_Element_Fieldset $fieldset) {
		/** @var Varien_Data_Form_Element_Abstract $fieldUrlKey */
		$fieldUrlKey = $fieldset->getElements()->searchById('identifier');
		df_assert($fieldUrlKey instanceof Varien_Data_Form_Element_Abstract);
		$fieldUrlKey['class'] = str_replace(
			'validate-identifier', 'rm.validate.urlKey', $fieldUrlKey['class']
		);
	}
}