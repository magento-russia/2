<?php
class Df_PageCache_Model_Container_Messages extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * Message store factory names
	 *
	 * @var array
	 */
	protected $_messageStoreTypes = array(
		'core/session',
		'customer/session',
		'catalog/session',
		'checkout/session',
		'tag/session'
	);

	/**
	 * Check for new messages. New message flag will be reseted if needed.
	 *
	 * @return bool
	 */
	protected function _isNewMessageRecived()
	{
		return $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_MESSAGE)
			|| array_key_exists(Df_PageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM, $_GET);
	}

	/**
	 * Redirect to content processing on new message
	 *
	 * @param string $content
	 * @return bool
	 */
	public function applyWithoutApp(&$content)
	{
		if ($this->_isNewMessageRecived()) {
			return false;
		}
		return parent::applyWithoutApp($content);
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		Mage::getSingleton('core/cookie')->delete(Df_PageCache_Model_Cookie::COOKIE_MESSAGE);

		$block = $this->_getPlaceHolderBlock();

		$types = unserialize($this->_placeholder->getAttribute('storage_types'));
		foreach ($types as $type) {
			$this->_addMessagesToBlock($type, $block);
		}
		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

		return $block->toHtml();
	}

	/**
	 * Add messages from storage to message block
	 *
	 * @param string $messagesStorage
	 * @param Mage_Core_Block_Messages $block
	 */
	protected function _addMessagesToBlock($messagesStorage, Mage_Core_Block_Messages $block)
	{
		if ($storage = Mage::getSingleton($messagesStorage)) {
			$block->addMessages($storage->getMessages(true));
			$block->setEscapeMessageFlag($storage->getEscapeMessages(true));
		}
	}
}
