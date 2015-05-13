<?php
class Df_PageCache_Model_Container_Sidebar_Poll extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * Current Poll id
	 */
	protected $_activePollId = null;


	/**
	 * Get identifier from cookies
	 *
	 * @return string
	 */
	protected function _getIdentifier()
	{
		$visitor = $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
		if (!$visitor) {
			$visitor = $_SERVER['REMOTE_ADDR'];
		}
		return $visitor;
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		if ($this->_getPollToShow() === null) {
			return false;
		}

		return 'CONTAINER_POLL'
		   . '_' . md5($this->_placeholder->getAttribute('cache_id')
		   . '_' . $this->_getIdentifier()
		   . '_' . $this->_getPollToShow());
	}

	/**
	 * Returns cache identifier for informational data about customer banners
	 *
	 * @return string
	 */
	protected function _getInfoCacheId()
	{
		return 'POLL_INFORMATION_'
			. '_' . md5($this->_placeholder->getAttribute('cache_id')
			. '_' . $this->_getIdentifier());
	}

	/**
	 * Saves informational cache, containing parameters used to show poll.
	 *
	 * @param array $renderedParams
	 * @return Df_PageCache_Model_Container_Sidebar_Poll
	 */
	protected function _saveInfoCache($renderedParams)
	{
		$data = serialize($renderedParams);
		$id = $this->_getInfoCacheId();
		$tags = array(Df_PageCache_Model_Processor::CACHE_TAG);
		Df_PageCache_Model_Cache::getCacheInstance()->save($data, $id, $tags);
		return $this;
	}

	/**
	 * Loads informational cache, containing parameters used to show poll
	 *
	 * @return false|array
	 */
	protected function _loadInfoCache()
	{
		$infoCacheId = $this->_getInfoCacheId();
		$data = $this->_loadCache($infoCacheId);
		if ($data === false) {
			return false;
		}
		return unserialize($data);
	}

	/**
	 * Get poll id to show
	 *
	 * @return int|null|bool
	 */
	protected function _getPollToShow()
	{
		if ($this->_activePollId === null) {
			$renderedParams = $this->_loadInfoCache();
			if (!$renderedParams) {
				return null;
			}

			//filter voted
			$voted = $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_POLL, '');
			if ($voted
				&& in_array($voted, $renderedParams['active_ids'])
				&& !in_array($voted, $renderedParams['voted_ids'])
			) {
				return null;
			}

			$activeIds = array_diff($renderedParams['active_ids'], $renderedParams['voted_ids']);
			$randomKey = array_rand($activeIds);
			$this->_activePollId = isset($activeIds[$randomKey]) ? $activeIds[$randomKey] : false;
		}

		return $this->_activePollId;
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$renderedParams = $this->_loadInfoCache();

		$templates = unserialize($this->_placeholder->getAttribute('templates'));
		$block = $this->_getPlaceHolderBlock();

		if ($templates) {
			foreach ($templates as $type=>$template) {
				$block->setPollTemplate($template, $type);
			}
		}

		if ($renderedParams) {
			if($this->_getPollToShow()) {
				$block->setPollId($this->_getPollToShow());
			} else {
				$voted = $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_POLL, '');
				if ($voted && in_array($voted, $renderedParams['active_ids'])) {
					$renderedParams = array(
						'active_ids' => $block->getActivePollsIds(),
						'voted_ids' => $block->getVotedPollsIds(),
					);
					$this->_saveInfoCache($renderedParams);
				}
			}
		} else {
			$renderedParams = array(
				'active_ids' => $block->getActivePollsIds(),
				'voted_ids' => $block->getVotedPollsIds(),
			);
			$this->_saveInfoCache($renderedParams);
		}

		$content = $block->toHtml();

		if (is_null($this->_activePollId)) {
			$this->_activePollId = $block->getPollToShow();
		}

		return $content;
	}

	/**
	 * Generate placeholder content before application was initialized and apply to page content if possible
	 *
	 * @param string $content
	 * @return bool
	 */
	public function applyWithoutApp(&$content)
	{
		$cacheId = $this->_getCacheId();
		if ($cacheId !== false) {
			$block = $this->_loadCache($cacheId);
			if ($block !== false) {
				$this->_applyToContent($content, $block);
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}
}
