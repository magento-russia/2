<?php
/**
 * 2016-11-10
 * Надо обязательно стать наследником @see Df_Core_Exception_Client,
 * потому что иначе метод @see Df_Shipping_Model_Collector::createRateResultError()
 * создаст ненужный нам диагностический отчёт: http://magento-forum.ru/topic/5495/
 */
class Df_Shipping_Exception extends Df_Core_Exception_Client {}