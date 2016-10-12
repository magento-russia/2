<?php
class Df_Cms_Model_Observer extends Df_Core_Model {
	/**
	 * Clean up private versions after user deleted.
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function adminUserDeleteAfter(Varien_Event_Observer $observer) {
		if (df_enabled(Df_Core_Feature::CMS_2)) {
			/** @var Df_Cms_Model_Page_Version $version */
			$version = Df_Cms_Model_Page_Version::i();
			$collection = $version->getCollection();
			$collection
				->addAccessLevelFilter(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PRIVATE)
				->addUserIdFilter()
			;
			Mage::getSingleton('core/resource_iterator')->walk(
				$collection->getSelect()
				,array(array($this, 'removeVersionCallback'))
				,array('version'=> $version)
			);
		}
		return $this;
	}

	/**
	 * Validate and render Cms hierarchy page
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function cmsControllerRouterMatchBefore(Varien_Event_Observer $observer) {
		if (
				df_cfg()->cms()->hierarchy()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::CMS_2)
		) {
			$condition = $observer->getEvent()->getCondition();
			/**
			 * Validate Request and modify router match condition
			 */
			/* @var Df_Cms_Model_Hierarchy_Node $node */
			$node = Df_Cms_Model_Hierarchy_Node::i();
			$requestUrl = $condition->getIdentifier();
			$node->loadByRequestUrl($requestUrl);
			if ($node->checkIdentifier($requestUrl, Mage::app()->getStore())) {
				$condition->setContinue(false);
			}
			if (!$node->getId()) {
				return $this;
			}
			if (!$node->getPageId()) {
				/* @var $child Df_Cms_Model_Hierarchy_Node */
				$child = Df_Cms_Model_Hierarchy_Node::i();
				$child->loadFirstChildByParent($node->getId());
				if (!$child->getId()) {
					return $this;
				}
				$url = Mage::getUrl('', array('_direct' => $child->getRequestUrl()));
				$condition->setRedirectUrl($url);
			} else {
				if (!$node->getPageIsActive()) {
					return $this;
				}

				// register hierarchy and node
				Mage::register('current_cms_hierarchy_node', $node);
				$condition->setContinue(true);
				$condition->setIdentifier($node->getPageIdentifier());
			}
		}
		return $this;
	}

	/**
	 * Removing unneeded data from increment table for removed page.
	 * @param $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function cmsPageDeleteAfter(Varien_Event_Observer $observer) {
		if (df_enabled(Df_Core_Feature::CMS_2)) {
			/* @var $page Mage_Cms_Model_Page */
			$page = $observer->getEvent()->getObject();
			Df_Cms_Model_Resource_Increment::s()->cleanIncrementRecord(
				Df_Cms_Model_Increment::TYPE_PAGE
				, $page->getId()
				, Df_Cms_Model_Increment::LEVEL_VERSION
			);
		}
		return $this;
	}

	/**
	 * Processing extra data after cms page saved
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function cmsPageSaveAfter(Varien_Event_Observer $observer) {
		if (df_enabled(Df_Core_Feature::CMS_2)) {
			/* @var $page Mage_Cms_Model_Page */
			$page = $observer->getEvent()->getObject();
			if (df_cfg()->cms()->versioning()->isEnabled()) {
				// Create new initial version & revision if it
				// is a new page or version control was turned on for this page.
				if (
						$page->getIsNewPage()
					||
						(
								$page->getUnderVersionControl()
							&&
								$page->dataHasChangedFor('under_version_control')
						)
				) {
					/** @var Df_Cms_Model_Page_Version $version */
					$version = Df_Cms_Model_Page_Version::i();
					$revisionInitialData = $page->getData();
					$revisionInitialData['copied_from_original'] = true;
					$version
						->setLabel($page->getTitle())
						->setAccessLevel(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
						->setPageId($page->getId())
						->setUserId(df_mage()->admin()->session()->getUser()->getId())
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
		return $this;
	}

	/**
	 * Preparing cms page object before it will be saved
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function cmsPageSaveBefore(Varien_Event_Observer $observer) {
		if (df_enabled(Df_Core_Feature::CMS_2)) {
			/* @var $page Mage_Cms_Model_Page */
			$page = $observer->getEvent()->getObject();
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
							if (rm_bool(df_a($row, 'current_page'))) {
								$currentPageIsOrphan = false;
								/** @var int $sortOrder */
								$sortOrder = rm_int(df_a($row, 'sort_order'));
								/** @var int $parentNodeId */
								$parentNodeId = rm_nat0(df_a($row, 'parent_node_id'));
								$currentPageIsRoot = (0 === $parentNodeId);
								if ($currentPageIsRoot) {
									/**
									 * Привязываем текущую страницу к разделу "Корень"
									 */
									if ($page->getId()) {
										/**
										 * Видимо, здесь надо создать корневой узел иерархии для текущей страницы, * если его ещё не существует
										 */


										/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $currentPageRootNodes */
										$currentPageRootNodes = Df_Cms_Model_Hierarchy_Node::c();
										$currentPageRootNodes
											->addPageFilter($page->getId())
											->addRootNodeFilter()
										;
										if (0 === $currentPageRootNodes->count()) {
											/** @var Df_Cms_Model_Hierarchy_Node $node */
											$node =
												Df_Cms_Model_Hierarchy_Node::i(
													array(
														'page_id' => $page->getId()
														,'level' => 1
														,'sort_order' => $sortOrder
														,'request_url' => $page->getIdentifier()
													)
												)
											;
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
							$parentNodeId = rm_nat0(df_a($row, 'parent_node_id'));
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
							$nodeId = df_string(df_a($row, 'node_id'));
							/**
							 * Странно названным флагом «page_exists»
							 * обозначаются те разделы, где администратор поставил флажок
							 * (то есть, обозначил их как родительские разделы текущей страницы)
							 *
							 * @var bool $markedAsParent
							 */
							$markedAsParent =
								rm_bool(
									df_a($row, 'page_exists')
								)
							;
							/** @var int $sortOrder */
							$sortOrder = rm_int(df_a($row, 'sort_order'));
							if ($markedAsParent) {
								$appendToNodes[$nodeId] = 0;
							}

							if (isset($appendToNodes[$parentNodeId])) {
								if (rm_contains($nodeId, '_')) {
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
		return $this;
	}

	/**
	 * Modify status's label from 'Enabled' to 'Published'.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function modifyPageStatuses(Varien_Event_Observer $observer) {
		if (
				df_enabled(Df_Core_Feature::CMS_2)
			&&
				df_cfg()->cms()->versioning()->isEnabled()
		) {
			$statuses = $observer->getEvent()->getStatuses();
			$statuses->setData(Mage_Cms_Model_Page::STATUS_ENABLED, df_h()->cms()->__('Published'));
			$statuses->setData(Mage_Cms_Model_Page::STATUS_DISABLED, df_h()->cms()->__('Unpublished'));
		}
		return $this;
	}

	/**
	 * Making changes to main tab regarding to custom logic
	 * @param Varien_Event_Observer $observer
	 * @return Df_Cms_Model_Observer
	 */
	public function onMainTabPrepareForm($observer) {
		if (df_enabled(Df_Core_Feature::CMS_2)) {
			$form = $observer->getEvent()->getForm();
			/* @var $baseFieldset Varien_Data_Form_Element_Fieldset */
			$baseFieldset = $form->getElement('base_fieldset');
			/* @var $baseFieldset Varien_Data_Form_Element_Fieldset */
			/* @var $page Mage_Cms_Model_Page */
			$page = Mage::registry('cms_page');
			if ($page) {
				$this->replaceUrlKeyValidatorToAllowCyrillic($baseFieldset);
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
					$this->replaceUrlKeyValidatorToAllowCyrillic($baseFieldset);
					/**
					 * Обратите внимание,
					 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
					 * потому что addField() возвращает не $fieldset, а созданное поле.
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
						$userId = df_mage()->admin()->session()->getUser()->getId();
						$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
						/** @var Df_Cms_Model_Page_Revision $revision */
						$revision = Df_Cms_Model_Page_Revision::i();
						$revision
							->loadWithRestrictions(
								$accessLevel, $userId, $page->getPublishedRevisionId()
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
										,'href' =>
											rm_url_admin('*/cms_page_revision/edit', array(
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
				if ($revisionAvailable && !df_mage()->admin()->session()->isAllowed('cms/page/save_revision')) {
					foreach ($baseFieldset->getElements() as $element) {
						$element->setDisabled(true);
					}
				}
				/*
				 * User does not have access to revision or revision is no longer available
				 */
				if (!$revisionAvailable && $page->getId() && $page->getUnderVersionControl()) {
					$baseFieldset
						->addField(
							'published_revision_status'
							,'label'
							,array('bold' => true)
						)
					;
					$page
						->setPublishedRevisionStatus(
							df_h()->cms()->__('Published Revision Unavailable')
						)
					;
				}
			}
		}
		return $this;
	}

	/**
	 * Handler for cms hierarchy view
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchCmsHierachyView($config, $eventModel) {
		return
			df_enabled(Df_Core_Feature::CMS_2) && df_cfg()->cms()->hierarchy()->isEnabled()
			? $eventModel->setInfo(df_h()->cms()->__('Tree Viewed'))
			: false
		;
	}

	/**
	 * Handler for cms revision preview
	 * @param Varien_Simplexml_Element $config
	 * @param Df_Logging_Model_Event $eventModel
	 * @return Df_Logging_Model_Event|bool
	 */
	public function postDispatchCmsRevisionPreview($config, $eventModel) {
		return
			df_enabled(Df_Core_Feature::CMS_2) && df_cfg()->cms()->versioning()->isEnabled()
			? $eventModel->setInfo(Mage::app()->getRequest()->getParam('revision_id'))
			: false
		;
	}

	/**
	 * Этот метод должен быть публичным,
	 * потому что используется как callable
	 * за пределами своего класса:
	 * @see Df_Cms_Model_Observer::adminUserDeleteAfter()
	 *
	 * Callback function to remove version or change access
	 * level to protected if we can't remove it.
	 * @param array $args
	 */
	public function removeVersionCallback($args) {
		if (
				df_enabled(Df_Core_Feature::CMS_2)
			&&
				df_cfg()->cms()->versioning()->isEnabled()
		) {
			$version = $args['version'];
			$version->setData($args['row']);
			try {
				$version->delete();
			} catch (Mage_Core_Exception $e) {
				// If we have situation when revision from
				// orphaned private version published we should
				// change its access level to protected so publisher
				// will have chance to see it and assign to some user
				$version->setAccessLevel(Df_Cms_Model_Page_Version::ACCESS_LEVEL_PROTECTED);
				$version->save();
			}
		}
	}

	/**
	 * @param Varien_Data_Form_Element_Fieldset $fieldset
	 * @return Df_Cms_Model_Observer
	 */
	private function replaceUrlKeyValidatorToAllowCyrillic(
		Varien_Data_Form_Element_Fieldset $fieldset
	) {
		/** @var Varien_Data_Form_Element_Abstract $fieldUrlKey */
		$fieldUrlKey = $fieldset->getElements()->searchById('identifier');
		df_assert($fieldUrlKey instanceof Varien_Data_Form_Element_Abstract);
		$fieldUrlKey->setData(
			'class'
			,str_replace('validate-identifier', 'rm.validate.urlKey', $fieldUrlKey->getData('class'))
		);
		return $this;
	}
}