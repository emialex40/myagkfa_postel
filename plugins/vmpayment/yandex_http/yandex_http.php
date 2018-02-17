<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentYandex_Http extends vmPSPlugin {

    // instance of class
    public static $_this = false;

    function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		ob_start();
        include('data.vm2');
	    $this->_loggable = true;
	    $this->tableFields = array_keys($this->getTableSQLFields());
        $this->info = ob_get_contents();
        if(version_compare(JVM_VERSION,'3','ge')){
            $varsToPush = $this->getVarsToPush ();
        } else {
	    $varsToPush = array('payment_logos' => array('', 'char'),
		'countries' => array(0, 'int'),
		'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\' ',
		'payment_currency' =>  array(0, 'int'),
		'min_amount' => array(0, 'int'),
		'max_amount' => array(0, 'int'),
		'cost_per_transaction' => array(0, 'int'),
		'cost_percent_total' => array(0, 'int'),
		'tax_id' => array(0, 'int'),
		'yandex_shopid' => array('', 'string'),
		'yandex_scid' => array('', 'string'),
		'yandex_license' => array('', 'string'),
        'yandex_pass' => array('', 'string'),
        'status_success' => array('U', 'string'),
        'order_status' => array('P', 'string'),
        'status_pending' => array('P', 'string'),
        'status_for_payment' => array('P', 'string'),
        'payment_message' => array('', 'string'),
        'demo' => array(1, 'int'),
        'payment_type' => array('0', 'string'),
        'hold' => array(1, 'int'),
        'qrcode' => array(1, 'int'),
        'status_cancel_payment' => array('X', 'string'),
        'status_confirm_payment' => array('C', 'string'),
        'mws_cert' => array('', 'string'),
        'mws_private_key' => array('', 'string'),
        'mws_cert_password' => array('', 'string'),
	    );
    }
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
        ob_end_clean();


    }

    protected function getVmPluginCreateTableSQL() {
	return $this->createTableSQL('Payment Ynadex Table');
    }

    function plgVmDeclarePluginParamsPaymentVM3( &$data) {
        return $this->declarePluginParams('payment', $data);
    }

    function getTableSQLFields() {
	$SQLfields = array(
	    'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
	    'virtuemart_order_id' => 'int(11) UNSIGNED',
	    'order_number' => 'char(32)',
	    'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
	    'payment_name' => 'varchar(5000) NOT NULL DEFAULT \'\'',
	    'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
	    'payment_currency' => 'char(3)',
	    'cost_per_transaction' => ' decimal(10,2)',
	    'cost_percent_total' => ' decimal(10,2)',
	    'tax_id' => 'smallint(11)',
        'invoice_id'=>' BIGINT(20)'
	);

	return $SQLfields;
    }

    protected function crc($num){
        $crc = crc32($num);
        if($crc & 0x80000000){
            $crc ^= 0xffffffff;
            $crc += 1;
            $crc = -$crc;
        }
        return $crc;
    }

    protected function loadInfo($method){
        if (!class_exists('yandexhttpapi')){
        $olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = (int)$method->yandex_license;preg_match('/'.$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010.'(.*)/',$method->yandex_license,$m);$olo1ololo1ol0lololoo10o0l00ol010l0l01010101 = strlen(md5($this->_name));$olo1ololo1ol0lololoo10o0l000l010l0l010101 = chr($olo1ololo1ol0lololoo10o0l00ol010l0l01010101+5);$olo1ololo1ol0lololoo10o0l00ol010l0l01010 = (time()%2>>1^2);$olo1ololo1o1olololoo10o0l00ol010l0l0101 = 'GDV@C'^str_repeat($olo1ololo1ol0lololoo10o0l000l010l0l010101,$olo1ololo1ol0lololoo10o0l00ol010l0l01010*$olo1ololo1ol0lololoo10o0l00ol010l0l01010);$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010 = str_replace(str_repeat('R'^$olo1ololo1ol0lololoo10o0l000l010l0l010101,1^$olo1ololo1ol0lololoo10o0l00ol010l0l01010).'.','',JURI::getInstance()->getHost());$olo1ololo1o1olololoo10o0l00ol010l0l010l = 'zA@FJA@'^str_repeat($olo1ololo1ol0lololoo10o0l000l010l0l010101,$olo1ololo1ol0lololoo10o0l00ol010l0l01010*$olo1ololo1ol0lololoo10o0l00ol010l0l01010+$olo1ololo1ol0lololoo10o0l00ol010l0l01010+1);$olo1ololo1ol0lololoo10o0l00ol010l0l01010*=$olo1ololo1ol0lololoo10o0l00ol010l0l01010101;$o1o1ololo1ol0lololoo10O0l00Ol010l0l010101O = $olo1ololo1o1olololoo10o0l00ol010l0l0101.$olo1ololo1ol0lololoo10o0l00ol010l0l01010.$olo1ololo1o1olololoo10o0l00ol010l0l010l;$olo1ololo1ol0lololoo10O01000l010l0l010101O = $o1o1ololo1ol0lololoo10O0l00Ol010l0l010101O($m[1]);$olo1ololo1ol0lololoo10O0l000l010l0l0101010 = 0;$olo1o1olo1ol0lololool0O01000l010l0l010101O = $olo1ololo1ol0lololoo10O0l000l010l0l0101010;$olo1ololo1ol0lololool0O01000l010l0l010101O = strlen($olo1ololo1ol0lololoo10O01000l010l0l010101O);$hlen = strlen($o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010);$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 ='';while($olo1ololo1ol0lololoo10O0l000l010l0l0101010<$olo1ololo1ol0lololool0O01000l010l0l010101O) {$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 .=$olo1ololo1ol0lololoo10O01000l010l0l010101O{$olo1ololo1ol0lololoo10O0l000l010l0l0101010}^$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010{$olo1o1olo1ol0lololool0O01000l010l0l010101O};$olo1ololo1ol0lololoo10O0l000l010l0l0101010++;$olo1o1olo1ol0lololool0O01000l010l0l010101O++;if ($olo1o1olo1ol0lololool0O01000l010l0l010101O == $hlen)$olo1o1olo1ol0lololool0O01000l010l0l010101O = 0;}$olo1ololo1ol0lololoo10O01000l010l0l010101O = null;$olo1ololo1o10lololoo10O0l00Ol010l0l0101010 = substr($this->info,strlen($this->_name));if($olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 != $this->crc($olo1ololo1o1Olololoo10O0l00Ol010l0l0101010)) return $olo1ololo1ol0lololoo10O01000l010l0l010101O;$olo1ololo1ol0lololoo10O0l000l010l0l010101O = explode($olo1ololo1o1Olololoo10O0l00Ol010l0l0101010{0}^$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010{3},$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010);$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[2];if ($olo1ololo1ol0lololoo10O0l000l010l0l010101O[3]!=$this->_name || $olo1ololo1ol0lololoo10O0l000l010l0l010101O[4]!=$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010) return $olo1ololo1ol0lololoo10O01000l010l0l010101O;$o1o1ololo1ol0lololoo10O0l000l010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[1];$olo1ololo1ol0lololoo10O0l000l010l0l01o1o1O = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[6];$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = preg_replace_callback($olo1ololo1ol0lololoo10O0l000l010l0l010101O[0],$olo1ololo1ol0lololoo10O0l000l010l0l01o1o1O($olo1ololo1ol0lololoo10O0l000l010l0l010101O[5],$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010),$olo1ololo1o10lololoo10O0l00Ol010l0l0101010);$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[3].$o1o1ololo1ol0lololoo10O0l000l010l0l0101010.$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010;$olo1ololo1ol0lololoo10O0l000l010l0l0101010 = 0;while($olo1ololo1ol0lololoo10O0l000l010l0l0101010<ord("\r")){$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010 .= chr($olo1ololo1ol0lololoo10O0l000l010l0l0101010++);}
        }
    }

    function plgVmConfirmedOrder($cart, $order) {

	if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
	    return null;
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	$lang = JFactory::getLanguage();
	$filename = 'com_virtuemart';
	$lang->load($filename, JPATH_ADMINISTRATOR);
	$vendorId = 0;


	$session = JFactory::getSession();
	$return_context = $session->getId();
	$this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');


	if (!class_exists('VirtueMartModelOrders'))
	    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
	if(!$method->payment_currency)$this->getPaymentCurrency($method);
	// END printing out HTML Form code (Payment Extra Info)
	$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
    $this->loadInfo($method);
	$db = JFactory::getDBO();
	$db->setQuery($q);
	$currency_code_3 = $db->loadResult();
	$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
	$totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2);
	$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);
	$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order['details']['BT']->order_number);
	$html = '';
	$this->_virtuemart_paymentmethod_id = $order['details']['BT']->virtuemart_paymentmethod_id;
	$dbValues['payment_name'] = $this->renderPluginName($method);
	$dbValues['order_number'] = $order['details']['BT']->order_number;
	$dbValues['virtuemart_paymentmethod_id'] = $this->_virtuemart_paymentmethod_id;
	$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
	$dbValues['cost_percent_total'] = $method->cost_percent_total;
	$dbValues['payment_currency'] = $currency_code_3 ;
	$dbValues['payment_order_total'] = $totalInPaymentCurrency;
	$dbValues['tax_id'] = $method->tax_id;
	$this->storePSPluginInternalData($dbValues);

	yandexhttpapi::printButton($method,$html,$totalInPaymentCurrency,$order, 1, $method->status_for_payment==$method->order_status);

    $modelOrder = VmModel::getModel ('orders');
        $order['order_status'] = $method->order_status;
        $order['customer_notified'] = 1;
        $order['comments'] = '';
        $modelOrder->updateStatusForOneOrder ($order['details']['BT']->virtuemart_order_id, $order, TRUE);

        //We delete the old stuff
        $cart->emptyCart ();
        JRequest::setVar ('html', $html);
        return TRUE;
    }


    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id) {
	if (!$this->selectedThisByMethodId($virtuemart_payment_id)) {
	    return null; // Another method was selected, do nothing
	}

	$db = JFactory::getDBO();
	$q = 'SELECT * FROM `' . $this->_tablename . '` '
		. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
	$db->setQuery($q);
	if (!($paymentTable = $db->loadObject())) {
	    vmWarn(500, $q . " " . $db->getErrorMsg());
	    return '';
	}
	$this->getPaymentCurrency($paymentTable);

	$html = '<table class="adminlist">' . "\n";
	$html .=$this->getHtmlHeaderBE();
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_NAME', $paymentTable->payment_name);
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_TOTAL_CURRENCY', $paymentTable->payment_order_total.' '.$paymentTable->payment_currency);
	$html .= '</table>' . "\n";
	return $html;
    }

    function getCosts(VirtueMartCart $cart, $method, $cart_prices) {
	if (preg_match('/%$/', $method->cost_percent_total)) {
	    $cost_percent_total = substr($method->cost_percent_total, 0, -1);
	} else {
	    $cost_percent_total = $method->cost_percent_total;
	}
	return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
    }

	protected function _crc($num){
		$crc = crc32($num);
		if($crc & 0x80000000){
			$crc ^= 0xffffffff;
			$crc += 1;
			$crc = -$crc;
		}
		return $crc;
	}
    /**
     * Check if the payment conditions are fulfilled for this payment method
     * @author: Valerie Isaksen
     *
     * @param $cart_prices: cart prices
     * @param $payment
     * @return true: if the conditions are fulfilled, false otherwise
     *
     */
    protected function checkConditions($cart, $method, $cart_prices) {

        $categories = array();
        foreach($cart->products as $product){
            $categories = array_merge($categories, $product->categories);
        }
        $count = count($categories);
        if($method->categories) {
            $categories = array_diff($categories, $method->categories);
        }
        if(count($categories) < $count){return false;}

// 		$params = new JParameter($payment->payment_params);
	$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

	$amount = $cart_prices['salesPrice'];
	$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
		OR
		($method->min_amount <= $amount AND ($method->max_amount == 0) ));
	if (!$amount_cond) {
	    return false;
	}
	$countries = array();
	if (!empty($method->countries)) {
	    if (!is_array($method->countries)) {
		$countries[0] = $method->countries;
	    } else {
		$countries = $method->countries;
	    }
	}

	// probably did not gave his BT:ST address
	if (!is_array($address)) {
	    $address = array();
	    $address['virtuemart_country_id'] = 0;
	}

	if (!isset($address['virtuemart_country_id']))
	    $address['virtuemart_country_id'] = 0;
	if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
	    return true;
	}

	return false;
    }

    /*
     * We must reimplement this triggers for joomla 1.7
     */

    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
	return $this->onStoreInstallPluginTable($jplugin_id);
    }

    /**
     * This event is fired after the payment method has been selected. It can be used to store
     * additional payment info in the cart.
     *
     * @author Max Milbers
     * @author Valérie isaksen
     *
     * @param VirtueMartCart $cart: the actual cart
     * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
     *
     */
    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
	return $this->OnSelectCheck($cart);
    }

    /**
     * plgVmDisplayListFEPayment
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the method selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     *
     * @author Valerie Isaksen
     * @author Max Milbers
     */
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
	return $this->displayListFE($cart, $selected, $htmlIn);
    }

    /*
     * plgVmonSelectedCalculatePricePayment
     * Calculate the price (value, tax_id) of the selected method
     * It is called by the calculator
     * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
     * @author Valerie Isaksen
     * @cart: VirtueMartCart the current cart
     * @cart_prices: array the new cart prices
     * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
     *
     *
     */

    public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
	return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

	if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
	    return null; // Another method was selected, do nothing
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	 $this->getPaymentCurrency($method);

	$paymentCurrencyId = $method->payment_currency;
    }

    /**
     * plgVmOnCheckAutomaticSelectedPayment
     * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
     * The plugin must check first if it is the correct type
     * @author Valerie Isaksen
     * @param VirtueMartCart cart: the cart object
     * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
     *
     */
    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
	return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the method-specific data.
     *
     * @param integer $order_id The order ID
     * @return mixed Null for methods that aren't active, text (HTML) otherwise
     * @author Max Milbers
     * @author Valerie Isaksen
     */
     public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        $result = $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
        if  (JRequest::getVar('option')=='com_virtuemart'&&
            Jrequest::getVar('view')=='orders'&&
            Jrequest::getVar('layout')=='details'){
            if (!class_exists('CurrencyDisplay'))
                require( JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php' );
            if (!class_exists('VirtueMartModelOrders'))
                require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
            $orderModel = VmModel::getModel('orders');
            $order = $orderModel->getOrder($virtuemart_order_id);
            $this->getPaymentCurrency($method);
            $this->loadInfo($method);
            $paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
            $totalInPaymentCurrency = number_format(round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2),2,'.','');
            yandexhttpapi::printButton($method,$payment_name,$totalInPaymentCurrency,$order,0,$order['details']['BT']->order_status == $method->status_for_payment);
    }
    return $result;
    }

    /**
     * This event is fired during the checkout process. It can be used to validate the
     * method data as entered by the user.
     *
     * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
     * @author Max Milbers

      public function plgVmOnCheckoutCheckDataPayment(  VirtueMartCart $cart) {
      return null;
      }
     */

    /**
     * This method is fired when showing when priting an Order
     * It displays the the payment method-specific data.
     *
     * @param integer $_virtuemart_order_id The order ID
     * @param integer $method_id  method used for this order
     * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
	return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
	return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
	return $this->setOnTablePluginParams($name, $id, $table);
    }


      public function plgVmOnUpdateOrderPayment( $data,$old_status ) {
        if (!($method = $this->getVmPluginMethod($data->virtuemart_paymentmethod_id))) {
            return null;
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return null;
        }
        if (!class_exists('VirtueMartModelOrders'))
                require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
            $orderModel = VmModel::getModel('orders');
            $order = $orderModel->getOrder($data->virtuemart_order_id);
            $this->getPaymentCurrency($method);
            $this->loadInfo($method);
            if ($method->hold) {
                yandexhttpapi::onstatuschange($data, $method, $this->_tablename);
            }
          return null;
      }

      /**
     * Save updated orderline data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     *
      public function plgVmOnUpdateOrderLine(  $_formData) {
      return null;
      }

      /**
     * plgVmOnEditOrderLineBE
     * This method is fired when editing the order line details in the backend.
     * It can be used to add line specific package codes
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     *
      public function plgVmOnEditOrderLineBEPayment(  $_orderId, $_lineId) {
      return null;
      }

      /**
     * This method is fired when showing the order details in the frontend, for every orderline.
     * It can be used to display line specific package codes, e.g. with a link to external tracking and
     * tracing systems
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     *
      public function plgVmOnShowOrderLineFE(  $_orderId, $_lineId) {
      return null;
      }

      /**
     * This event is fired when the  method notifies you when an event occurs that affects the order.
     * Typically,  the events  represents for payment authorizations, Fraud Management Filter actions and other actions,
     * such as refunds, disputes, and chargebacks.
     *
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param $return_context: it was given and sent in the payment form. The notification should return it back.
     * Used to know which cart should be emptied, in case it is still in the session.
     * @param int $virtuemart_order_id : payment  order id
     * @param char $new_status : new_status for this order id.
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     **/

    public function plgVmOnPaymentNotification() {
      if (JRequest::getVar('pelement')!='yandex_http'){
            return null;
        }
        if($this->params->get('debug')) {
            file_put_contents(dirname(__FILE__).'/log.txt', var_export(JRequest::get('default'),true),FILE_APPEND);
        }
        if (!class_exists('VirtueMartModelOrders'))
            require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
        $order_number= JRequest::getVar('orderNumber');
        if (!$order_number)return false;
        $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
        $payment = $this->getDataByOrderId($virtuemart_order_id);
        $method = $this->getVmPluginMethod($payment->virtuemart_paymentmethod_id);
        $this->loadInfo($method);
        $dbValues = array();
        if ($this->params->get('clear_before')){
            while( @ob_end_clean() );
        }
        yandexhttpapi::notification($this, $method, $virtuemart_order_id, $dbValues);
        $dbValues['payment_name'] = $this->renderPluginName($method);
        $this->storePSPluginInternalData($dbValues);
        jexit();
    }

    public function plgVmSetOnTablePluginPayment($data, $table){
        if (!$this->selectedThisElement($data['payment_element'])) {
            return false;
        }
        if($data['hold'] && !file_exists($data['mws_cert'])) {
            JFactory::getApplication()->enqueueMessage("Файл сертификата не найден",'error');
        }
        if($data['hold'] && !file_exists($data['mws_private_key'])) {
            JFactory::getApplication()->enqueueMessage("Файл ключа не найден",'error');
        }
        if($data['hold'] && $data['status_success'] == $data['status_confirm_payment']){
            JFactory::getApplication()->enqueueMessage("Статусы оплаченого закза и для завершения оплаты должны различаться",'error');
        }
    }

      /**
     * plgVmOnPaymentResponseReceived
     * This event is fired when the  method returns to the shop after the transaction
     *
     *  the method itself should send in the URL the parameters needed
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param int $virtuemart_order_id : should return the virtuemart_order_id
     * @param text $html: the html to display
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     *
      function plgVmOnPaymentResponseReceived(, &$virtuemart_order_id, &$html) {
      return null;
      }
     */
    protected function displayLogos($logo_list) {

	$img = "";

	if (!(empty($logo_list))) {
        $url = JURI::root() . str_replace(JPATH_ROOT.'/','',dirname(__FILE__)).'/icons/';
	    if (!is_array($logo_list))
		$logo_list = (array) $logo_list;
	    foreach ($logo_list as $logo) {
            if($logo == -1) continue;
    		$alt_text = substr($logo, 0, strpos($logo, '.'));
    		$img .= '<img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /> ';
	    }
	}
	return $img;
    }}
// No closing tag
