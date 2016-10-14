<?php
/**
 * @used-by Df/Cms/etc/widget.xml
 * @used-by Df_Cms_Adminhtml_Cms_Hierarchy_WidgetController::chooserAction()
 */
class Df_Cms_Block_Admin_Hierarchy_Widget_Chooser extends Df_Core_Block_Admin {
	/** @return array[] */
	public function getNodes() {
		/** @var array[] $result */
		$result = array();
		$collection = Df_Cms_Model_Hierarchy_Node::c();
		$collection
			->joinCmsPage()
			->setTreeOrder()
		;
		foreach ($collection as $item) {
			/* @var Df_Cms_Model_Hierarchy_Node $item */
			$result[]= array(
				'node_id' => $item->getId()
				,'parent_node_id' => $item->getParentNodeId()
				,'label' => $item->getLabel()
				,'page_exists' => !!$item->getPageExists()
				,'page_id' => $item->getPageId()
			);
		}
		return $result;
	}

	/**
	 * Retrieve Hierarchy JSON string
	 * @return string
	 */
	public function getNodesJson() {
		return df_mage()->coreHelper()->jsonEncode($this->getNodes());
	}

	/**
	 * @used-by Df_Cms_Adminhtml_Cms_Hierarchy_WidgetController::chooserAction()
	 * @return string
	 */
	public function getTreeHtml()
	{
		$chooserJsObject = $this->getId();
		$html = '
			<div id="tree'.$this->getId().'" class="cms-tree tree x-tree"></div>
			<script type="text/javascript">

			function clickNode(node) {
				$("tree-container").insert({before: node.text});
				$("'.$this->getId().'").value = node.id;
				treeRoot.collapse();
			}

			var nodes = '.$this->getNodesJson().';
			if (nodes.length > 0) {
				tree'.$this->getId().' = new Ext.tree.TreePanel("tree'.$this->getId().'", {
					animate: false,enableDD: false,containerScroll: true,rootVisible: false,lines: true
				});
				treeRoot'.$this->getId().' = new Ext.tree.AsyncTreeNode({
					text: "'. $this->__("Root") .'",id: "root",allowDrop: true,allowDrag: false,expanded: true,cls: "cms_node_root",});
				tree'.$this->getId().'.setRootNode(treeRoot'.$this->getId().');
				for(var i = 0; i < nodes.length; i++) {
					var cls = nodes[i].page_id ? "cms_page" : "cms_node";
					var node = new Ext.tree.TreeNode({
						id: nodes[i].node_id,text: nodes[i].label,cls: cls,expanded: nodes[i].page_exists,allowDrop: false,allowDrag: false,page_id: nodes[i].page_id,});
					if (parentNode = tree'.$this->getId().'.getNodeById(nodes[i].parent_node_id)) {
						parentNode.appendChild(node);
					} else {
						treeRoot'.$this->getId().'.appendChild(node);
					}
				}

				tree'.$this->getId().'.addListener("click", function (node, event) {
					'.$chooserJsObject.'.setElementValue(node.id);
					'.$chooserJsObject.'.setElementLabel(node.text);
					'.$chooserJsObject.'.close();
				});
				tree'.$this->getId().'.render();
				treeRoot'.$this->getId().'.expand();
			}
			else {
				$("tree'.$this->getId().'").innerHTML = "'.$this->__('No Nodes available').'";
			}
			</script>
		';
		return $html;
	}

	/**
	 * Prepare chooser element HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element Form Element
	 * @return Varien_Data_Form_Element_Abstract
	 */
	public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$uniqId = df_mage()->coreHelper()->uniqHash($element->getId());
		$sourceUrl = $this->getUrl('*/cms_hierarchy_widget/chooser', array('uniq_id' => $uniqId));
		$params = new Varien_Object;;
		$params
			->setElement($element)
			->setTranslationHelper($this->getTranslationHelper())
			->setConfig($this->getConfig())
			->setFieldsetId($this->getFieldsetId())
			->setSourceUrl($sourceUrl)
			->setUniqId($uniqId);
		if ($element->getValue()) {
			$node = Df_Cms_Model_Hierarchy_Node::i()->load($element->getValue());
			if ($node->getId()) {
				$params->setLabel($node->getLabel());
			}
		}
		/**
		 * 2015-04-01
		 * Надо использовать именно @uses df_render_l(),
		 * потому что блок @uses Mage_Widget_Block_Adminhtml_Widget_Chooser
		 * использует метод @see Mage_Widget_Block_Adminhtml_Widget_Chooser::getLayout()
		 */
		$element['after_element_html'] = df_render_l(
			'Mage_Widget_Block_Adminhtml_Widget_Chooser', $params->getData()
		);
		return $element;
	}

	/**
	 * @used-by Df_Cms_Adminhtml_Cms_Hierarchy_WidgetController::chooserAction()
	 * @param string $id
	 * @return Df_Cms_Block_Admin_Hierarchy_Widget_Chooser
	 */
	public static function i($id) {return new self(array('id' => $id));}
}