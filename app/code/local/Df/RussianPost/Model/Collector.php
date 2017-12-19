<?php
class Df_RussianPost_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Shipping_Model_Method[]
	 * @throws Exception
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Method[] $result */
			$result = array();
			if (
					$this->getRateRequest()->getOriginCountry()->isRussia()
				&&
					$this->getRateRequest()->getDestinationCountry()->isRussia()
				&&
					(31.5 >= $this->getRateRequest()->getWeightInKilogrammes())
				&&
					$this->getRateRequest()->getOriginPostalCode()
				&&
					$this->getRateRequest()->getDestinationPostalCode()
			) {
				/**
				 * 2017-12-19
				 * 1) "Модуль «Почта России» поломался,
				 * потому что используемый им для внутрероссийских отправлений сервис russianpostcalc.ru
				 * ввёл CAPTCHA (стал требовать «проверочный код»)": https://github.com/magento-russia/2/issues/9
				 * 2) «API. Метод calc. Расчет стоимости отправки Почтового отправления»:
				 * http://russianpostcalc.ru/user/myaddr/api/#calc
				 * 3) «PHP скрипт - пример, вызова метода calc»: http://russianpostcalc.ru/user/rp_calc.zip
				 */
				/** @var Zend_Http_Client $c */
				$c = new Zend_Http_Client('http://russianpostcalc.ru/api_v1.php');
				$c->setMethod(Zend_Http_Client::POST);
				/**
				 * 2017-12-19
				 * Параметры должны идти именно в такой последовательности не только для расчёта `hash`,
				 * но и в самом запросе HTTP к API, иначе API ответит:
				 * «10006 auth Ошибка доступа (не верная подпись)!».
				 * https://df.tips/t/268
				 */
				$p = array(
					// 2017-12-19 «API ключ Вашего аккаунта»
					'apikey' => '21ae2a1cd5dd3493a99a841658ea8bc8'
					// 2017-12-19 «Вызываемый метод»
					,'method' => 'calc'
					// 2017-12-19 «Почтовый индекс отправителя»
					,'from_index' => $this->getRateRequest()->getOriginPostalCode()
					// 2017-12-19 «Почтовый индекс получателя»
					,'to_index' => $this->getRateRequest()->getDestinationPostalCode()
					,'weight' => dff_2($this->getRateRequest()->getWeightInKilogrammes())
					// 2017-12-19 «Объявленная ценность отправления, руб»
					,'ob_cennost_rub' => rm_currency()->convertFromBaseToRoubles($this->declaredValueBase())
				); /** @var array(string => string) $p */
				// 2017-12-19 «Обязательный аргумент, если аутентификация по методу API ключ + API пароль»
				$c->setParameterPost($p + ['hash' => md5(implode('|', array_merge($p, array('braKecU3'))))]);
				$res = df_json_decode($c->request()->getBody()); /** @var array(string => mixed) $res */
				if ('done' !== df_a_deep($res, 'msg/type')) {
					df_error(df_a_deep($res, 'msg/text'));
				}
				foreach ($res['calc'] as $rate) { /** @var array(string => string|float) $rate */
					$result[]= $this->createMethod(new Df_RussianPost_Model_RussianPostCalc_Method($rate));
				}
			}
			try {
				/** @var Df_RussianPost_Model_Official_Method_International $methodInternational */
				$methodInternational =
					$this->createMethod(
						$class = Df_RussianPost_Model_Official_Method_International::_CLASS
						,$title = 'Ценная посылка'
					)
				;
				if (
						$methodInternational->isApplicable()
					&&
						(0 < $methodInternational->getCost())
				) {
					$result[]= $methodInternational;
				}
			}
			catch(Exception $e) {
				if (!($e instanceof Df_Core_Exception_Client)) {
					df_notify_exception($e);
				}
				if (!$result && $this->getRmConfig()->frontend()->needDisplayDiagnosticMessages()) {
					throw $e;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}