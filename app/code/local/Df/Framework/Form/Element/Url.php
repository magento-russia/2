<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element;
// 2016-05-30
abstract class Url extends Element {
	/**
	 * 2016-05-31
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	abstract protected function messageForThirdPartyLocalhost();
	/**
	 * 2016-05-30
	 * 2016-06-07
	 * 'id' => $this->getId() нужно для совместимости с 2.0.6,
	 * иначе там сбой в выражении inputs = $(idTo).up(this._config.levels_up)
	 * https://mail.google.com/mail/u/0/#search/maged%40wrapco.com.au/15510135c446afdb
	 * @override
	 * @see \Varien_Data_Form_Element_Abstract::getElementHtml()
	 * @return string
	 */
	public function getElementHtml() {return
		df_tag('div', ['class' => 'df-url', 'id' => $this->getId()],
			$this->thirdPartyLocalhost()
			? $this->messageForThirdPartyLocalhost()
			: $this->messageForOthers()
		)
	;}

	/**
	 * 2016-05-30
	 * @override
	 * @see \Df\Framework\Form\Element::getComment()
	 * https://github.com/magento/magento2/blob/a5fa3af3/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
		if ((string)$element->getComment()) {
			$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
		}
	 */
	public function getComment() {return $this->thirdPartyLocalhost() ? null : parent::getComment();}

	/**
	 * 2016-05-31
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	protected function messageForOthers() {return
		!$this->requireHttps() || df_check_https($this->url())
			? $this->url()
			: 'Опция <a href="http://magento-forum.ru/topic/5492/" target="_blank">'
			 . '«<b>Общие</b>» → «<b>Интернет</b>» → «<b>HTTPS</b>'
			 . ' → «<b>Общая часть веб-адресов системы</b>»</a>'
			 . ' Вашего магазина настроена неправильно: её значение должно начинаться с «<b>https</b>»).'

	;}

	/**
	 * 2016-05-30
	 * @return string|null
	 */
	protected function routePath() {return dfc($this, function() {return
		df_fe_fc($this, 'dfWebhook_routePath')
	;});}

	/**
	 * 2016-05-30
	 * 2016-05-31
	 * https://mage2.pro/tags/secure-url
	 * https://github.com/magento/magento2/blob/a5fa3af3/lib/internal/Magento/Framework/Url.php#L437-L439
		if (isset($params['_secure'])) {
			$this->getRouteParamsResolver()->setSecure($params['_secure']);
		}
	 * @return string
	 */
	protected function url() {return dfc($this, function() {return
		df_my_local() ? $this->urlForMyLocalPc() : $this->urlForOthers()
	;});}

	/**
	 * 2016-05-31
	 * @return string
	 */
	protected function urlForMyLocalPc() {return
		df_cc_path_t('https://mage2.pro/sandbox2', $this->routePath())
	;}

	/**
	 * 2016-05-31
	 * @return string
	 */
	protected function urlForOthers() {return
		df_url_frontend($this->routePath(), ['_secure' => $this->requireHttps() ? true : null])
	;}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	protected function requireHttps() {return dfc($this, function() {return
		!df_is_localhost() && df_fe_fc_b($this, 'dfWebhook_requireHTTPS')
	;});}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	protected function thirdPartyLocalhost() {return dfc($this, function() {return
		df_is_localhost() && !df_my()
	;});}
}