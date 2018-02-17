<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

require_once('netpay_class.php');
require_once('netpay_atol.php');

class plgVmPaymentNetPay extends vmPSPlugin {
    // instance of class
    public static $_this = false;
    
    function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        
        $this->_loggable   = true;
        $this->tableFields = array_keys($this->getTableSQLFields());
        $varsToPush        = array(
            'payment_logos' => array(
                '',
                'char'
            ),
            'payment_currency' => array(
                0,
                'int'
            ),
            'api_key' => array(
                '',
                'string'
            ),
            'auth_sign' => array(
                '',
                'string'
            ),
            'mode' => array(
                '',
                'string'
            ),
            'email' => array(
                '',
                'string'
            ),
            'email_success_text' => array(
                '',
                'string'
            ),
            'email_failure_text' => array(
                '',
                'string'
            ),
            'status_paid' => array(
                '',
                'string'
            ),
            'status_capture' => array(
                '',
                'string'
            ),
            'test' => array(
                '',
                'int'
            ), 
            'atol' => array(
                '',
                'int'
            ),            
            'inn' => array(
                '',
                'string'
            ),            
            'tax' => array(
                'none',
                'string'
            ),
        );
        
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
    }
    
    
    protected function getVmPluginCreateTableSQL() {
        return $this->createTableSQL('Payment NetPay Table');
    }
    
    function getTableSQLFields() {
        $SQLfields = array(
            'id' => 'tinyint(1) unsigned NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED DEFAULT NULL',
            'order_number' => 'char(32) DEFAULT NULL',
            'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED DEFAULT NULL',
            'payment_name' => 'char(255) NOT NULL DEFAULT \'\' ',
            'payment_order_total' => 'decimal(15,2) NOT NULL DEFAULT \'0.00\' ',
            'payment_currency' => 'char(3) '
        );
        
        return $SQLfields;
    }
    
    const SUCCESSFUL_ORDER_TEXT = "Номер заказа %s.";
    
    function plgVmConfirmedOrder($cart, $order) {
        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        
        $lang     = JFactory::getLanguage();
        $filename = 'com_virtuemart';
        $lang->load($filename, JPATH_ADMINISTRATOR);
        $vendorId = 0;
        
        $session        = JFactory::getSession();
        $return_context = $session->getId();
        $this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');
        
        $html = "";
        
        if (!class_exists('VirtueMartModelOrders'))
            require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
        if (!$method->payment_currency)
            $this->getPaymentCurrency($method);
        // END printing out HTML Form code (Payment Extra Info)
        $q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
        $db =& JFactory::getDBO();
        $db->setQuery($q);
        
        $currency = strtoupper($db->loadResult());
        if ($currency == 'RUR')
          $currency = 'RUB';

        $paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
		$totalInPaymentCurrency = round(
			$paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 
			2
			);
        
        if ($method->test == '1'){
			$url_net2pay_pay = 'https://demo.net2pay.ru/billingService/paypage/';
            $Api_key='js4cucpn4kkc6jl1p95np054g2';
            $AuthSign='1';
		}
		else {
			$url_net2pay_pay = 'https://my.net2pay.ru/billingService/paypage/';
            $Api_key=$method->api_key;
            $AuthSign=$method->auth_sign;
		}
        
        $md5_Api_key = base64_encode(md5($Api_key, true));	
		$order_date = $order['details']['BT']->modified_on;
	
		$dateClass = new DateTime($order_date);
		$dateClass->modify('+1 day');
		$order_date = $dateClass->format('Y-m-dVH:i:s');
		$cryptoKey = substr(base64_encode(md5($md5_Api_key.$order_date, true)),0,16);
	
        $orderId = $order['details']['BT']->order_number;
        $phone = isset($order['details']['BT']->phone_2) ? $order['details']['BT']->phone_2 : $order['details']['BT']->phone_1;
        
		$params = array();
		$params['description'] = 'ORDER '.$orderId;;
		$params['amount'] = $order['details']['BT']->order_total;
		$params['currency'] = 'RUB';
		$params['orderID'] = $orderId;
		$params['cardHolderCity'] = "";
		$params['cardHolderCountry'] = "";
		$params['cardHolderPostal'] = "";
		$params['cardHolderRegion'] = "";
		$params['successUrl'] = 'http://'.$_SERVER['HTTP_HOST'];
		$params['failUrl'] = 'http://'.$_SERVER['HTTP_HOST'];
        $params['phone'] = $phone;
        $params['email'] = $order['details']['BT']->email;
        
        if ($method->test == '1') {
			$params['param1'] = 'host';
			$params['value1'] = $_SERVER['HTTP_HOST'].'_'.substr(md5($method->auth_sign.$_SERVER['HTTP_HOST']), 0, 5);
		}
        
        // HOLD
        $params['isHold'] = 'true';
        
        // ATOL include 
        if ($method->atol) {
        	$is_test = ($method->test == '1');
        	$netpayAtol = new NetpayAtol(
				$is_test ? '7717586110':$method->inn, 
				$params['email'], 
				$params['amount'],
				$is_test ? 'test2.atol.ru' : '', // host
				$params['phone']
				);
			foreach ($order['items'] as $item) {
				$netpayAtol->addItem($item->order_item_name, $item->product_item_price, 
					$item->product_quantity, $item->product_final_price * $item->product_quantity, $method->tax
					);	
			}
			if (!class_exists('VirtueMartModelShipmentmethod'))
                require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'shipmentmethod.php');
            $shipment = new VirtueMartModelShipmentmethod;
            $shipment->setId($order['details']['BT']->virtuemart_shipmentmethod_id);
            $shipment_info = $shipment->getShipment();
			$netpayAtol->addShipping($shipment_info->shipment_name, $order['details']['BT']->order_shipment);
					
			$params['cashbox_data'] = $netpayAtol->getJSON();
		}
		// ATOL -------
				
		$netpay = new Netpay();
	
		$params_crypted = array();
		foreach ($params as $key => $param) {            
			$cripter = $netpay->_encrypt($key.'='.$param, $cryptoKey);
			$params_crypted[] = $cripter;
		}
	
		$params_crypted_str = implode('&', $params_crypted);
        
        if ($method->mode === 'email') {
            $link = "http://".$_SERVER['HTTP_HOST']."/netpay/?"
            	."data=".urlencode($params_crypted_str)
            	."&auth=".$AuthSign
            	."&expire=".urlencode($order_date);
            $link = str_replace("%", "%25", $link);        	
            $html = $method->textinsteadbutton;
            $headers = "From: ".JFactory::getApplication()->getCfg('mailfrom')."\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n";
            $headers .= 'Content-type: text/plain; charset="utf-8"'."\n\r";
            $text = "Номер заказа: ".$orderId."\n\n";
            $text .= "Сумма: ".$order['details']['BT']->order_total." RUB\n\n";
            $text .= "E-Mail: ".$order['details']['BT']->email."\n\n";
            $text .= "Телефон: "
            	.(isset($order['details']['BT']->phone_2) ? $order['details']['BT']->phone_2 : $order['details']['BT']->phone_1)."\n\n";
            $text .= "Ссылка на оплату: \n\n".$link;
            if (mail($method->email, "Order #$orderId from ".$_SERVER['HTTP_HOST'], $text, $headers))
                $html = "<p>".strtr(self::SUCCESSFUL_ORDER_TEXT, array('%s' => $orderId))
                            ."</p>"."<p>$method->email_success_text</p>";
            else
                $html = "<p>".strtr(self::SUCCESSFUL_ORDER_TEXT, array('%s' => $orderId))."</p>"
                            ."<p>$method->email_failure_text</p>";
		}
		else {
            $html = '<form method="post" action="'.$url_net2pay_pay.'" name="vm_netpay_form">';
            $html .= '<input type="hidden" name="data" value="'.urlencode($params_crypted_str).'">';
            $html .= '<input type="hidden" name="auth" value="'.$AuthSign.'">';
            $html .= '<input type="hidden" name="expire" value="'.urlencode($order_date).'">';
            $html .= '</form>';
            $html .= '<script type="text/javascript">';
            $html .= 'document.forms.vm_netpay_form.submit();';
            $html .= '</script>';
        }       
       
        return $this->processConfirmedOrderPaymentResponse(true, $cart, $order, $html, $this->renderPluginName($method, $order), 'P');
    }
    
    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id) {
        if (!$this->selectedThisByMethodId($virtuemart_payment_id)) {
            return null; // Another method was selected, do nothing
        }
        
        $db = JFactory::getDBO();
        $q  = 'SELECT * FROM `' . $this->_tablename . '` ' . 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
        $db->setQuery($q);
        if (!($paymentTable = $db->loadObject())) {
            vmWarn(500, $q . " " . $db->getErrorMsg());
            return '';
        }
        $this->getPaymentCurrency($paymentTable);
        
        $html = '<table class="adminlist">' . "\n";
        $html .= $this->getHtmlHeaderBE();
        $html .= $this->getHtmlRowBE('STANDARD_PAYMENT_NAME', $paymentTable->payment_name);
        $html .= $this->getHtmlRowBE('STANDARD_PAYMENT_TOTAL_CURRENCY', $paymentTable->payment_order_total . ' ' . $paymentTable->payment_currency);
        $html .= '</table>' . "\n";
        return $html;
    }
    
    function getCosts(VirtueMartCart $cart, $method, $cart_prices) {
        return 0;
    }
    
    protected function checkConditions($cart, $method, $cart_prices) {
        return true;
    }
    
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
        return $this->onStoreInstallPluginTable($jplugin_id);
    }
    
    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
        return $this->OnSelectCheck($cart);
    }
    
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
        return $this->displayListFE($cart, $selected, $htmlIn);
    }
    
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
    
    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
        return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }
    
    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }
    
    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
        return $this->onShowOrderPrint($order_number, $method_id);
    }
    
    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
        return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmDeclarePluginParamsPaymentVM3(&$data) {
		return $this->declarePluginParams('payment', $data);
    }
    
    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
        return $this->setOnTablePluginParams($name, $id, $table);
    }
    
    protected function displayLogos($logo_list) {
        $img = "";
        
        if (!(empty($logo_list))) {
            $url = JURI::root() . str_replace('\\', '/', str_replace(JPATH_ROOT, '', dirname(__FILE__))) . '/';
            if (!is_array($logo_list))
                $logo_list = (array) $logo_list;
            foreach ($logo_list as $logo) {
                $alt_text = substr($logo, 0, strpos($logo, '.'));
                $img .= '<img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /> ';
            }
        }
        return $img;
    }
    
    function plgVmOnPaymentNotification() {
        if (!class_exists('VirtueMartModelOrders')) {
                require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }
        
        if (vRequest::getString('pm', '') !== 'netpay') {
            return NULL;
        }
        
        $this->debugLog('OK','plgVmOnPaymentNotification', 'debug');
        
        parse_str($_SERVER['QUERY_STRING'], $query_arr);
		$getData = $query_arr;
		foreach ($query_arr as $k => $v) { //cleaning bad keys
			if ($k == 'orderID') {
				break;
			}
			unset($getData[$k]);
		}			
		$preToken = '';
		foreach ($getData as $k => $v) {
		    if ($k !== 'token') $preToken .= $v.';';
		}			
        
		$vm_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($getData['orderID']);
		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($vm_order_id);
		$method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id);
		
        if ($method->test == '1'){
            $api_key='js4cucpn4kkc6jl1p95np054g2';
            $auth_sign='1';
		}
		else {
            $api_key=$method->api_key;
            $auth_sign = $method->auth_sign;
		}        
        
		$token = md5($preToken.base64_encode(md5($api_key, true)).';');
		
		if ($getData['auth'] == $auth_sign) {
		    if ($token === urldecode($getData['token'])) {
		        if (in_array($getData['error'], array('000', '0'))) { 
		        	$mOrder = VmModel::getModel('orders');
		        	$mOrderId = VirtueMartModelOrders::getOrderIdByOrderNumber($getData['orderID']);
					if ($getData['status'] === 'APPROVED') { 
						$mOrder->updateStatusForOneOrder($mOrderId, array('order_status' => $method->status_paid));
						if ($getData['transactionType'] == 'Capture') {							 
		                    $mOrder->_updateOrderHist($mOrderId, $method->status_paid, 1, 
		                    	'Произведена оплата заказа на сумму '.$getData['amount']);
						}
						else { // transactionType=Sale 
		                    $mOrder->_updateOrderHist($mOrderId, $method->status_paid, 1, 'Заказ успешно оплачен');
						}
					}
					elseif (($getData['transactionType'] == 'Authorize') && ($getData['status'] == 'WAITING')) {
			            $mOrder->updateStatusForOneOrder($mOrderId, array('order_status' => $method->status_capture)); 
	                    $mOrder->_updateOrderHist($mOrderId, $method->status_capture, 1, 
	                    	'Сумма '.$getData['amount'].' для оплаты заказа была заморожена');
					}
					else {
						$getData['status'] = '0';
					}
					$getData['status'] = '1';
                    $getData['error'] = '';
		        }
		        else {
		            $getData['status'] = '0';
		        }
		    }
		    else {
		        $getData['status'] = '0';
		        $getData['error'] = 'Error: token does not match';
		    }
		}
		else {
		    $getData['status'] = 0;
		    $getData['error'] = 'Wrong auth value';
		}
		
		echo '<notification>
	                <orderId>' . $getData['orderID'] . '</orderId>
	                <transactionType>' . $getData['transactionType'] . '</transactionType>
	                <status>' . $getData['status'] . '</status>
	                <error>' . $getData['error'] . '</error>
	        </notification>';
        die();              
    }
}
