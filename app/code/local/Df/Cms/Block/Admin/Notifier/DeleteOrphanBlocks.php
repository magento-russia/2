<?php
class Df_Cms_Block_Admin_Notifier_DeleteOrphanBlocks extends Df_Core_Block_Admin {
	/**
	 * @param Df_Cms_Model_Block $block
	 * @return string
	 */
	public function getBlockTitle(Df_Cms_Model_Block $block) {
		return sprintf('«%s» (%s)', $block->getTitle(), $block->getIdentifier());
	}

	/** @return string */
	protected function enumerateBlocks() {
		if (!isset($this->{__METHOD__})) {
			/** @uses getBlockTitle() */
			$this->{__METHOD__} = df_csv_pretty($this->getBlocks()->walk(array($this, 'getBlockTitle')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getLink() {return Df_Cms_Model_Admin_Action_DeleteOrphanBlocks::s()->getLink();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/cms/notifier/delete_orphan_blocks.phtml';}

	/** @return Df_Cms_Model_Resource_Block_Collection */
	private function getBlocks() {return $this->cfg(self::$P__BLOCKS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__BLOCKS, Df_Cms_Model_Resource_Block_Collection::class);
	}
	/** @var string */
	private static $P__BLOCKS = 'blocks';
	/**
	 * @param Df_Cms_Model_Resource_Block_Collection $blocks
	 * @return string
	 */
	public static function render(Df_Cms_Model_Resource_Block_Collection $blocks) {
		return df_render(__CLASS__, array(self::$P__BLOCKS => $blocks));
	}
}