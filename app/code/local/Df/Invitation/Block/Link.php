<?php
class Df_Invitation_Block_Link extends Df_Core_Block_Template_NoCache {
	/**
	 * Adding link to account links block link params if invitation
	 * is allowed globaly and for current website
	 *
	 * @param string $block
	 * @param string $label
	 * @param string $url
	 * @param string $title
	 * @param bool $prepare [optional]
	 * @param mixed[] $urlParams [optional]
	 * @param $position [optional]
	 * @param $liParams [optional]
	 * @param $aParams [optional]
	 * @param string $beforeText [optional]
	 * @param string $afterText [optional]
	 * @return Df_Invitation_Block_Link
	 */
	public function addAccountLink(
		$block
		,$label
		,$url=''
		,$title=''
		,$prepare=false
		,$urlParams=array()
		,$position=null
		,$liParams=null
		,$aParams=null
		,$beforeText=''
		,$afterText=''
	) {
		if (df_h()->invitation()->config()->isEnabledOnFront()) {
			$blockInstance = $this->getLayout()->getBlock($block);
			if ($blockInstance) {
				/** @noinspection PhpUndefinedMethodInspection */
				$blockInstance->addLink($label, $url, $title, $prepare, $urlParams,$position, $liParams, $aParams, $beforeText, $afterText);
			}
		}
		return $this;
	}

	/**
	 * Adding link to account links block link params if invitation
	 * is allowed globaly and for current website
	 *
	 * @param string $block
	 * @param string $name
	 * @param string $path
	 * @param string $label
	 * @param array $urlParams
	 * @return Df_Invitation_Block_Link
	 */
	public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
	{
		if (df_h()->invitation()->config()->isEnabledOnFront()) {
			$blockInstance = $this->getLayout()->getBlock($block);
			if ($blockInstance) {
				/** @noinspection PhpUndefinedMethodInspection */
				$blockInstance->addLink($name, $path, $label, $urlParams);
			}
		}
		return $this;
	}
}