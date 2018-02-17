<?php

/**
 * One Page Checkout For VirtueMart
 *
 * @version     1.0.2
 * @link        http://www.zootemplate.com
 * @author      ZooTemplate
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('ZtonepageModelVirtuemart'))
{

    class ZtonepageModelVirtuemart
    {

        public $cart;

        public function __construct()
        {
            if (!class_exists('VmConfig'))
                require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');

            if (!class_exists('VirtueMartCart'))
                require(VMPATH_SITE . '/helpers/cart.php');

            VmConfig::loadConfig();
            $this->cart = VirtueMartCart::getCart();
        }

        /**
         *
         * @staticvar ZtonepageModelVirtuemart $instance
         * @return \ZtonepageModelVirtuemart
         */
        public static function &getInstance()
        {
            static $instance;
            if (!isset($instance))
            {
                $instance = new ZtonepageModelVirtuemart();
            }
            return $instance;
        }

        /**
         * @return Array of product in cart
         */
        public function getCart()
        {
            $this->cart->prepareAjaxData();
            return $this->cart->products;
        }

        /**
         * @return Array of bill to address
         */
        public function getBillTo()
        {
            $this->cart->_fromCart = true;
            $this->cart->prepareAjaxData();
            return isset($this->cart->BTaddress) ? $this->cart->BTaddress : $this->_getBilltoAddressDefault();
        }

        /**
         * @return Array of ship to address
         */
        public function getShipto()
        {
            $this->cart->_fromCart = true;
            $this->cart->setCartIntoSession();
            return isset($this->cart->STaddress) ? $this->cart->STaddress : $this->_getShiptoAddressDefault();
        }

        /**
         * @return Array shipment objects
         */
        public function getShipments()
        {
            $shipmentModel = VmModel::getModel('shipmentmethod');
            return $shipmentModel->getShipments();
        }

        /**
         * @return Array payment objects
         */
        public function getPayments()
        {
            $paymentModel = VmModel::getModel('paymentmethod');
            return $paymentModel->getPayments();
        }

        /**
         * @return Array Coupon objects
         */
        public function getCoupons()
        {
            $couponModel = VmModel::getModel('coupon');
            return $couponModel->getCoupons();
        }

        /**
         * Update address can use for both BT & ST
         */
        public function updateAddress()
        {

            $cart = VirtueMartCart::getCart(true);
            $this->_updateAddress($cart);
        }

        public function updateCoupon($coupon_code)
        {
            $cart = VirtueMartCart::getCart(true);
            $cart->setCouponCode($coupon_code);
        }

        public function updateCart()
        {
            $cart = VirtueMartCart::getCart(true);
            return $cart->updateProductCart();
        }

        public function updateShipment()
        {
            $cart = VirtueMartCart::getCart(true);
            return $cart->setShipmentMethod(true);
        }

        /**
         * @todo Replace with Joomla! standard way
         * @todo Code clean up
         * @param type $cartObj
         * @return type
         */
        private function _updateAddress($cartObj)
        {
            $mainframe = JFactory::getApplication();

            $msg = '';
            $data = vRequest::getPost(FILTER_SANITIZE_STRING);
            $register = isset($_REQUEST['register']);

            $userModel = VmModel::getModel('user');
            $currentUser = JFactory::getUser();

            if (empty($data['address_type']))
            {
                $data['address_type'] = vRequest::getCmd('addrtype', 'BT');
            }

            if ($cartObj)
            {
                if ($cartObj->_fromCart or $cartObj->getInCheckOut())
                {

                    $cart = VirtueMartCart::getCart(true);
                    $prefix = '';
                    if ($data['address_type'] == 'STaddress' || $data['address_type'] == 'ST')
                    {
                        $prefix = 'shipto_';
                        vmdebug('Storing user ST prefix ' . $prefix);
                    }
                    $cart->saveAddressInCart($data, $data['address_type'], true, $prefix);
                }
            }

            if (isset($data['vendor_accepted_currencies']))
            {
                // Store multiple selectlist entries as a ; separated string
                if (array_key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies']))
                {
                    $data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
                }

                $data['vendor_store_name'] = vRequest::getHtml('vendor_store_name');
                $data['vendor_store_desc'] = vRequest::getHtml('vendor_store_desc');
                $data['vendor_terms_of_service'] = vRequest::getHtml('vendor_terms_of_service');
                $data['vendor_letter_css'] = vRequest::getHtml('vendor_letter_css');
                $data['vendor_letter_header_html'] = vRequest::getHtml('vendor_letter_header_html');
                $data['vendor_letter_footer_html'] = vRequest::getHtml('vendor_letter_footer_html');
            }

            if ($data['address_type'] == 'ST' and ! $currentUser->guest)
            {
                $ret = $userModel->storeAddress($data);
                if ($cartObj and ! empty($ret))
                {
                    $cartObj->selected_shipto = $ret;
                    $cartObj->setCartIntoSession();
                }
            } else
            {

                if ($currentUser->guest == 1 and ( $register or ! $cartObj ))
                {
                    if ($this->checkCaptcha('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT') == FALSE)
                    {
                        $msg = vmText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL');
                        if ($cartObj and $cartObj->_fromCart)
                        {
                            //$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT'), $msg);
                        } else if ($cartObj and $cartObj->getInCheckOut())
                        {
                            //$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT'), $msg);
                        } else
                        {
                            //$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=edit&addrtype=BT'), $msg);
                        }
                        return $msg;
                    }
                }

                if ($currentUser->guest != 1 or ! $cartObj or ( $currentUser->guest == 1 and $register))
                {

                    $switch = false;
                    if ($currentUser->guest == 1 and $register)
                    {
                        $userModel->setId(0);
                        $adminID = JFactory::getSession()->get('vmAdminID', false);
                        if ($adminID)
                        {
                            if (!class_exists('vmCrypt'))
                                require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmcrypt.php');
                            $adminID = vmCrypt::decrypt($adminID);
                            $adminIdUser = JFactory::getUser($adminID);
                            if ($adminIdUser->authorise('core.admin', 'com_virtuemart') or $adminIdUser->authorise('vm.user', 'com_virtuemart'))
                            {
                                $superUser = VmConfig::isSuperVendor($adminID);
                                if ($superUser > 1)
                                {
                                    $data['vendorId'] = $superUser;
                                }
                                $switch = true;
                            }
                        }
                    }

                    if (!class_exists('VirtueMartCart'))
                        require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
                    $cart = VirtueMartCart::getCart(true);
                    if (!empty($cart->vendorId) and $cart->vendorId != 1)
                    {
                        $data['vendorId'] = $cart->vendorId;
                    }
                    $ret = $userModel->store($data);

                    if ($switch)
                    { //and VmConfig::get ('oncheckout_change_shopper')){
                        //update session
                        $current = JFactory::getUser($ret['newId']);
                        $session = JFactory::getSession();
                        $session->set('user', $current);
                    }
                }


                if ($currentUser->guest == 1 and ( $register or ! $cartObj ))
                {
                    $msg = (is_array($ret)) ? $ret['message'] : $ret;
                    $usersConfig = JComponentHelper::getParams('com_users');
                    $useractivation = $usersConfig->get('useractivation');

                    if (is_array($ret) and $ret['success'] and ! $useractivation)
                    {
                        // Username and password must be passed in an array
                        $credentials = array('username' => $ret['user']->username,
                            'password' => $ret['user']->password_clear
                        );
                        $return = $mainframe->login($credentials);
                    } else if (VmConfig::get('oncheckout_only_registered', 0))
                    {
                        $layout = vRequest::getCmd('layout', 'edit');
                        //$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&layout=' . $layout, FALSE), $msg);
                    }
                }
            }

            return $msg;
        }

        public function getShoppingCart()
        {
            
        }

        public function getConfirm()
        {
            
        }

        protected function _getShiptoAddressDefault()
        {
            return json_decode('{"fields":{"address_type_name":{"name":"shipto_address_type_name","value":"Shipment","title":"Address Nickname","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_address_type_name_field\" name=\"shipto_address_type_name\" size=\"30\" value=\"Shipment\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"company":{"name":"shipto_company","value":null,"title":"Company Name","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_company_field\" name=\"shipto_company\" size=\"30\" value=\"\"  maxlength=\"64\" \/> ","description":""},"first_name":{"name":"shipto_first_name","value":null,"title":"First Name","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_first_name_field\" name=\"shipto_first_name\" size=\"30\" value=\"\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"middle_name":{"name":"shipto_middle_name","value":null,"title":"Middle Name","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_middle_name_field\" name=\"shipto_middle_name\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""},"last_name":{"name":"shipto_last_name","value":null,"title":"Last Name","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_last_name_field\" name=\"shipto_last_name\" size=\"30\" value=\"\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"address_1":{"name":"shipto_address_1","value":null,"title":"Address 1","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_address_1_field\" name=\"shipto_address_1\" size=\"30\" value=\"\"  class=\"required\" maxlength=\"64\" \/> ","description":""},"address_2":{"name":"shipto_address_2","value":null,"title":"Address 2","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_address_2_field\" name=\"shipto_address_2\" size=\"30\" value=\"\"  maxlength=\"64\" \/> ","description":""},"zip":{"name":"shipto_zip","value":null,"title":"Zip \/ Postal Code","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_zip_field\" name=\"shipto_zip\" size=\"30\" value=\"\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"city":{"name":"shipto_city","value":null,"title":"City","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_city_field\" name=\"shipto_city\" size=\"30\" value=\"\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"virtuemart_country_id":{"name":"shipto_virtuemart_country_id","value":"","title":"Country","type":"select","required":"1","hidden":false,"formcode":"<select id=\"shipto_virtuemart_country_id\" name=\"shipto_virtuemart_country_id\" class=\"vm-chzn-select required\" style=\"width: 210px\">\n\t<option value=\"\" selected=\"selected\">-- Select --<\/option>\n\t<option value=\"1\">Afghanistan<\/option>\n\t<option value=\"2\">Albania<\/option>\n\t<option value=\"3\">Algeria<\/option>\n\t<option value=\"4\">American Samoa<\/option>\n\t<option value=\"5\">Andorra<\/option>\n\t<option value=\"6\">Angola<\/option>\n\t<option value=\"7\">Anguilla<\/option>\n\t<option value=\"8\">Antarctica<\/option>\n\t<option value=\"9\">Antigua and Barbuda<\/option>\n\t<option value=\"10\">Argentina<\/option>\n\t<option value=\"11\">Armenia<\/option>\n\t<option value=\"12\">Aruba<\/option>\n\t<option value=\"13\">Australia<\/option>\n\t<option value=\"14\">Austria<\/option>\n\t<option value=\"15\">Azerbaijan<\/option>\n\t<option value=\"16\">Bahamas<\/option>\n\t<option value=\"17\">Bahrain<\/option>\n\t<option value=\"18\">Bangladesh<\/option>\n\t<option value=\"19\">Barbados<\/option>\n\t<option value=\"20\">Belarus<\/option>\n\t<option value=\"21\">Belgium<\/option>\n\t<option value=\"22\">Belize<\/option>\n\t<option value=\"23\">Benin<\/option>\n\t<option value=\"24\">Bermuda<\/option>\n\t<option value=\"25\">Bhutan<\/option>\n\t<option value=\"26\">Bolivia<\/option>\n\t<option value=\"27\">Bosnia and Herzegovina<\/option>\n\t<option value=\"28\">Botswana<\/option>\n\t<option value=\"29\">Bouvet Island<\/option>\n\t<option value=\"30\">Brazil<\/option>\n\t<option value=\"31\">British Indian Ocean Territory<\/option>\n\t<option value=\"32\">Brunei Darussalam<\/option>\n\t<option value=\"33\">Bulgaria<\/option>\n\t<option value=\"34\">Burkina Faso<\/option>\n\t<option value=\"35\">Burundi<\/option>\n\t<option value=\"36\">Cambodia<\/option>\n\t<option value=\"37\">Cameroon<\/option>\n\t<option value=\"38\">Canada<\/option>\n\t<option value=\"244\">Canary Islands<\/option>\n\t<option value=\"39\">Cape Verde<\/option>\n\t<option value=\"40\">Cayman Islands<\/option>\n\t<option value=\"41\">Central African Republic<\/option>\n\t<option value=\"42\">Chad<\/option>\n\t<option value=\"43\">Chile<\/option>\n\t<option value=\"44\">China<\/option>\n\t<option value=\"45\">Christmas Island<\/option>\n\t<option value=\"46\">Cocos (Keeling) Islands<\/option>\n\t<option value=\"47\">Colombia<\/option>\n\t<option value=\"48\">Comoros<\/option>\n\t<option value=\"49\">Congo<\/option>\n\t<option value=\"50\">Cook Islands<\/option>\n\t<option value=\"51\">Costa Rica<\/option>\n\t<option value=\"53\">Croatia<\/option>\n\t<option value=\"54\">Cuba<\/option>\n\t<option value=\"55\">Cyprus<\/option>\n\t<option value=\"56\">Czech Republic<\/option>\n\t<option value=\"52\">C&ocirc;te d\'Ivoire<\/option>\n\t<option value=\"57\">Denmark<\/option>\n\t<option value=\"58\">Djibouti<\/option>\n\t<option value=\"59\">Dominica<\/option>\n\t<option value=\"60\">Dominican Republic<\/option>\n\t<option value=\"240\">East Timor<\/option>\n\t<option value=\"61\">East Timor<\/option>\n\t<option value=\"62\">Ecuador<\/option>\n\t<option value=\"63\">Egypt<\/option>\n\t<option value=\"64\">El Salvador<\/option>\n\t<option value=\"65\">Equatorial Guinea<\/option>\n\t<option value=\"66\">Eritrea<\/option>\n\t<option value=\"67\">Estonia<\/option>\n\t<option value=\"68\">Ethiopia<\/option>\n\t<option value=\"69\">Falkland Islands (Malvinas)<\/option>\n\t<option value=\"70\">Faroe Islands<\/option>\n\t<option value=\"71\">Fiji<\/option>\n\t<option value=\"72\">Finland<\/option>\n\t<option value=\"73\">France<\/option>\n\t<option value=\"75\">French Guiana<\/option>\n\t<option value=\"76\">French Polynesia<\/option>\n\t<option value=\"77\">French Southern Territories<\/option>\n\t<option value=\"78\">Gabon<\/option>\n\t<option value=\"79\">Gambia<\/option>\n\t<option value=\"80\">Georgia<\/option>\n\t<option value=\"81\">Germany<\/option>\n\t<option value=\"82\">Ghana<\/option>\n\t<option value=\"83\">Gibraltar<\/option>\n\t<option value=\"84\">Greece<\/option>\n\t<option value=\"85\">Greenland<\/option>\n\t<option value=\"86\">Grenada<\/option>\n\t<option value=\"87\">Guadeloupe<\/option>\n\t<option value=\"88\">Guam<\/option>\n\t<option value=\"89\">Guatemala<\/option>\n\t<option value=\"90\">Guinea<\/option>\n\t<option value=\"91\">Guinea-Bissau<\/option>\n\t<option value=\"92\">Guyana<\/option>\n\t<option value=\"93\">Haiti<\/option>\n\t<option value=\"94\">Heard and McDonald Islands<\/option>\n\t<option value=\"95\">Honduras<\/option>\n\t<option value=\"96\">Hong Kong<\/option>\n\t<option value=\"97\">Hungary<\/option>\n\t<option value=\"98\">Iceland<\/option>\n\t<option value=\"99\">India<\/option>\n\t<option value=\"100\">Indonesia<\/option>\n\t<option value=\"101\">Iran, Islamic Republic of<\/option>\n\t<option value=\"102\">Iraq<\/option>\n\t<option value=\"103\">Ireland<\/option>\n\t<option value=\"104\">Israel<\/option>\n\t<option value=\"105\">Italy<\/option>\n\t<option value=\"106\">Jamaica<\/option>\n\t<option value=\"107\">Japan<\/option>\n\t<option value=\"241\">Jersey<\/option>\n\t<option value=\"108\">Jordan<\/option>\n\t<option value=\"109\">Kazakhstan<\/option>\n\t<option value=\"110\">Kenya<\/option>\n\t<option value=\"111\">Kiribati<\/option>\n\t<option value=\"112\">Korea, Democratic People\'s Republic of<\/option>\n\t<option value=\"113\">Korea, Republic of<\/option>\n\t<option value=\"114\">Kuwait<\/option>\n\t<option value=\"115\">Kyrgyzstan<\/option>\n\t<option value=\"116\">Lao People\'s Democratic Republic<\/option>\n\t<option value=\"117\">Latvia<\/option>\n\t<option value=\"118\">Lebanon<\/option>\n\t<option value=\"119\">Lesotho<\/option>\n\t<option value=\"120\">Liberia<\/option>\n\t<option value=\"121\">Libya<\/option>\n\t<option value=\"122\">Liechtenstein<\/option>\n\t<option value=\"123\">Lithuania<\/option>\n\t<option value=\"124\">Luxembourg<\/option>\n\t<option value=\"125\">Macau<\/option>\n\t<option value=\"126\">Macedonia, the former Yugoslav Republic of<\/option>\n\t<option value=\"127\">Madagascar<\/option>\n\t<option value=\"128\">Malawi<\/option>\n\t<option value=\"129\">Malaysia<\/option>\n\t<option value=\"130\">Maldives<\/option>\n\t<option value=\"131\">Mali<\/option>\n\t<option value=\"132\">Malta<\/option>\n\t<option value=\"133\">Marshall Islands<\/option>\n\t<option value=\"134\">Martinique<\/option>\n\t<option value=\"135\">Mauritania<\/option>\n\t<option value=\"136\">Mauritius<\/option>\n\t<option value=\"137\">Mayotte<\/option>\n\t<option value=\"138\">Mexico<\/option>\n\t<option value=\"139\">Micronesia, Federated States of<\/option>\n\t<option value=\"140\">Moldova, Republic of<\/option>\n\t<option value=\"141\">Monaco<\/option>\n\t<option value=\"142\">Mongolia<\/option>\n\t<option value=\"143\">Montserrat<\/option>\n\t<option value=\"144\">Morocco<\/option>\n\t<option value=\"145\">Mozambique<\/option>\n\t<option value=\"146\">Myanmar<\/option>\n\t<option value=\"147\">Namibia<\/option>\n\t<option value=\"148\">Nauru<\/option>\n\t<option value=\"149\">Nepal<\/option>\n\t<option value=\"150\">Netherlands<\/option>\n\t<option value=\"151\">Netherlands Antilles<\/option>\n\t<option value=\"152\">New Caledonia<\/option>\n\t<option value=\"153\">New Zealand<\/option>\n\t<option value=\"154\">Nicaragua<\/option>\n\t<option value=\"155\">Niger<\/option>\n\t<option value=\"156\">Nigeria<\/option>\n\t<option value=\"157\">Niue<\/option>\n\t<option value=\"158\">Norfolk Island<\/option>\n\t<option value=\"159\">Northern Mariana Islands<\/option>\n\t<option value=\"160\">Norway<\/option>\n\t<option value=\"161\">Oman<\/option>\n\t<option value=\"162\">Pakistan<\/option>\n\t<option value=\"163\">Palau<\/option>\n\t<option value=\"248\">Palestinian Territory, Occupied<\/option>\n\t<option value=\"164\">Panama<\/option>\n\t<option value=\"165\">Papua New Guinea<\/option>\n\t<option value=\"166\">Paraguay<\/option>\n\t<option value=\"167\">Peru<\/option>\n\t<option value=\"168\">Philippines<\/option>\n\t<option value=\"169\">Pitcairn<\/option>\n\t<option value=\"170\">Poland<\/option>\n\t<option value=\"171\">Portugal<\/option>\n\t<option value=\"172\">Puerto Rico<\/option>\n\t<option value=\"173\">Qatar<\/option>\n\t<option value=\"175\">Romania<\/option>\n\t<option value=\"176\">Russian Federation<\/option>\n\t<option value=\"177\">Rwanda<\/option>\n\t<option value=\"174\">R&eacute;union<\/option>\n\t<option value=\"197\">Saint Helena<\/option>\n\t<option value=\"178\">Saint Kitts and Nevis<\/option>\n\t<option value=\"179\">Saint Lucia<\/option>\n\t<option value=\"246\">Saint Martin (French part)<\/option>\n\t<option value=\"198\">Saint Pierre and Miquelon<\/option>\n\t<option value=\"180\">Saint Vincent and the Grenadines<\/option>\n\t<option value=\"181\">Samoa<\/option>\n\t<option value=\"182\">San Marino<\/option>\n\t<option value=\"183\">Sao Tome And Principe<\/option>\n\t<option value=\"184\">Saudi Arabia<\/option>\n\t<option value=\"185\">Senegal<\/option>\n\t<option value=\"245\">Serbia<\/option>\n\t<option value=\"186\">Seychelles<\/option>\n\t<option value=\"187\">Sierra Leone<\/option>\n\t<option value=\"188\">Singapore<\/option>\n\t<option value=\"247\">Sint Maarten (Dutch part)<\/option>\n\t<option value=\"189\">Slovakia<\/option>\n\t<option value=\"190\">Slovenia<\/option>\n\t<option value=\"191\">Solomon Islands<\/option>\n\t<option value=\"192\">Somalia<\/option>\n\t<option value=\"193\">South Africa<\/option>\n\t<option value=\"194\">South Georgia and the South Sandwich Islands<\/option>\n\t<option value=\"195\">Spain<\/option>\n\t<option value=\"196\">Sri Lanka<\/option>\n\t<option value=\"242\">St. Barthelemy<\/option>\n\t<option value=\"243\">St. Eustatius<\/option>\n\t<option value=\"199\">Sudan<\/option>\n\t<option value=\"200\">Suriname<\/option>\n\t<option value=\"201\">Svalbard and Jan Mayen<\/option>\n\t<option value=\"202\">Swaziland<\/option>\n\t<option value=\"203\">Sweden<\/option>\n\t<option value=\"204\">Switzerland<\/option>\n\t<option value=\"205\">Syrian Arab Republic<\/option>\n\t<option value=\"206\">Taiwan<\/option>\n\t<option value=\"207\">Tajikistan<\/option>\n\t<option value=\"208\">Tanzania, United Republic of<\/option>\n\t<option value=\"209\">Thailand<\/option>\n\t<option value=\"237\">The Democratic Republic of Congo<\/option>\n\t<option value=\"210\">Togo<\/option>\n\t<option value=\"211\">Tokelau<\/option>\n\t<option value=\"212\">Tonga<\/option>\n\t<option value=\"213\">Trinidad and Tobago<\/option>\n\t<option value=\"214\">Tunisia<\/option>\n\t<option value=\"215\">Turkey<\/option>\n\t<option value=\"216\">Turkmenistan<\/option>\n\t<option value=\"217\">Turks and Caicos Islands<\/option>\n\t<option value=\"218\">Tuvalu<\/option>\n\t<option value=\"219\">Uganda<\/option>\n\t<option value=\"220\">Ukraine<\/option>\n\t<option value=\"221\">United Arab Emirates<\/option>\n\t<option value=\"222\">United Kingdom<\/option>\n\t<option value=\"223\">United States<\/option>\n\t<option value=\"224\">United States Minor Outlying Islands<\/option>\n\t<option value=\"225\">Uruguay<\/option>\n\t<option value=\"226\">Uzbekistan<\/option>\n\t<option value=\"227\">Vanuatu<\/option>\n\t<option value=\"228\">Vatican City State (Holy See)<\/option>\n\t<option value=\"229\">Venezuela<\/option>\n\t<option value=\"230\">Viet Nam<\/option>\n\t<option value=\"231\">Virgin Islands, British<\/option>\n\t<option value=\"232\">Virgin Islands, U.S.<\/option>\n\t<option value=\"233\">Wallis and Futuna<\/option>\n\t<option value=\"234\">Western Sahara<\/option>\n\t<option value=\"235\">Yemen<\/option>\n\t<option value=\"238\">Zambia<\/option>\n\t<option value=\"239\">Zimbabwe<\/option>\n<\/select>\n","description":"","country_2_code":"","country_3_code":""},"virtuemart_state_id":{"name":"shipto_virtuemart_state_id","value":"","title":"State \/ Province \/ Region","type":"select","required":"1","hidden":false,"formcode":"<select  id=\"shipto_virtuemart_state_id\" class=\"vm-chzn-select\" name=\"shipto_virtuemart_state_id\" style=\"width: 210px\">\n\t\t\t\t\t\t<option value=\"\">-- Select --<\/option>\n\t\t\t\t\t\t<\/select>","description":"","state_2_code":"","state_3_code":""},"phone_1":{"name":"shipto_phone_1","value":null,"title":"Phone","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_phone_1_field\" name=\"shipto_phone_1\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""},"phone_2":{"name":"shipto_phone_2","value":null,"title":"Mobile phone","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_phone_2_field\" name=\"shipto_phone_2\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""},"fax":{"name":"shipto_fax","value":null,"title":"Fax","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"shipto_fax_field\" name=\"shipto_fax\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""}},"functions":[],"scripts":[],"links":[]}');
        }

        protected function _getBilltoAddressDefault()
        {
            return json_decode('{"fields":{"email":{"name":"email","value":"khangvm530@gmail.com","title":"E-Mail","type":"emailaddress","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"email_field\" name=\"email\" size=\"30\" value=\"khangvm530@gmail.com\"  class=\"required\" maxlength=\"100\" \/> ","description":""},"delimiter_billto":{"name":"delimiter_billto","value":null,"title":"Bill To","type":"delimiter","required":"0","hidden":false,"formcode":"","description":""},"company":{"name":"company","value":"dsfgs","title":"Company Name","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"company_field\" name=\"company\" size=\"30\" value=\"dsfgs\"  maxlength=\"64\" \/> ","description":""},"title":{"name":"title","value":"Mr","title":"Title","type":"select","required":"0","hidden":false,"formcode":"<select id=\"title\" name=\"title\" class=\"vm-chzn-select\" style=\"width: 210px\">\n\t<option value=\"\">-- Select --<\/option>\n\t<option value=\"Mr\" selected=\"selected\">Mr<\/option>\n\t<option value=\"Mrs\">Mrs<\/option>\n<\/select>\n","description":""},"first_name":{"name":"first_name","value":"gsds","title":"First Name","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"first_name_field\" name=\"first_name\" size=\"30\" value=\"gsds\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"middle_name":{"name":"middle_name","value":"dsfsd","title":"Middle Name","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"middle_name_field\" name=\"middle_name\" size=\"30\" value=\"dsfsd\"  maxlength=\"32\" \/> ","description":""},"last_name":{"name":"last_name","value":"dfsdf","title":"Last Name","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"last_name_field\" name=\"last_name\" size=\"30\" value=\"dfsdf\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"address_1":{"name":"address_1","value":"df\u00e1df \u00e1df \u00e1","title":"Address 1","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"address_1_field\" name=\"address_1\" size=\"30\" value=\"df\u00e1df \u00e1df \u00e1\"  class=\"required\" maxlength=\"64\" \/> ","description":""},"address_2":{"name":"address_2","value":"","title":"Address 2","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"address_2_field\" name=\"address_2\" size=\"30\" value=\"\"  maxlength=\"64\" \/> ","description":""},"zip":{"name":"zip","value":"10000","title":"Zip \/ Postal Code","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"zip_field\" name=\"zip\" size=\"30\" value=\"10000\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"city":{"name":"city","value":"fdf","title":"City","type":"text","required":"1","hidden":false,"formcode":"<input type=\"text\" id=\"city_field\" name=\"city\" size=\"30\" value=\"fdf\"  class=\"required\" maxlength=\"32\" \/> ","description":""},"virtuemart_country_id":{"name":"virtuemart_country_id","value":"United States","title":"Country","type":"select","required":"1","hidden":false,"formcode":"<select id=\"virtuemart_country_id\" name=\"virtuemart_country_id\" class=\"vm-chzn-select required\" style=\"width: 210px\">\n\t<option value=\"\">-- Select --<\/option>\n\t<option value=\"1\">Afghanistan<\/option>\n\t<option value=\"2\">Albania<\/option>\n\t<option value=\"3\">Algeria<\/option>\n\t<option value=\"4\">American Samoa<\/option>\n\t<option value=\"5\">Andorra<\/option>\n\t<option value=\"6\">Angola<\/option>\n\t<option value=\"7\">Anguilla<\/option>\n\t<option value=\"8\">Antarctica<\/option>\n\t<option value=\"9\">Antigua and Barbuda<\/option>\n\t<option value=\"10\">Argentina<\/option>\n\t<option value=\"11\">Armenia<\/option>\n\t<option value=\"12\">Aruba<\/option>\n\t<option value=\"13\">Australia<\/option>\n\t<option value=\"14\">Austria<\/option>\n\t<option value=\"15\">Azerbaijan<\/option>\n\t<option value=\"16\">Bahamas<\/option>\n\t<option value=\"17\">Bahrain<\/option>\n\t<option value=\"18\">Bangladesh<\/option>\n\t<option value=\"19\">Barbados<\/option>\n\t<option value=\"20\">Belarus<\/option>\n\t<option value=\"21\">Belgium<\/option>\n\t<option value=\"22\">Belize<\/option>\n\t<option value=\"23\">Benin<\/option>\n\t<option value=\"24\">Bermuda<\/option>\n\t<option value=\"25\">Bhutan<\/option>\n\t<option value=\"26\">Bolivia<\/option>\n\t<option value=\"27\">Bosnia and Herzegovina<\/option>\n\t<option value=\"28\">Botswana<\/option>\n\t<option value=\"29\">Bouvet Island<\/option>\n\t<option value=\"30\">Brazil<\/option>\n\t<option value=\"31\">British Indian Ocean Territory<\/option>\n\t<option value=\"32\">Brunei Darussalam<\/option>\n\t<option value=\"33\">Bulgaria<\/option>\n\t<option value=\"34\">Burkina Faso<\/option>\n\t<option value=\"35\">Burundi<\/option>\n\t<option value=\"36\">Cambodia<\/option>\n\t<option value=\"37\">Cameroon<\/option>\n\t<option value=\"38\">Canada<\/option>\n\t<option value=\"244\">Canary Islands<\/option>\n\t<option value=\"39\">Cape Verde<\/option>\n\t<option value=\"40\">Cayman Islands<\/option>\n\t<option value=\"41\">Central African Republic<\/option>\n\t<option value=\"42\">Chad<\/option>\n\t<option value=\"43\">Chile<\/option>\n\t<option value=\"44\">China<\/option>\n\t<option value=\"45\">Christmas Island<\/option>\n\t<option value=\"46\">Cocos (Keeling) Islands<\/option>\n\t<option value=\"47\">Colombia<\/option>\n\t<option value=\"48\">Comoros<\/option>\n\t<option value=\"49\">Congo<\/option>\n\t<option value=\"50\">Cook Islands<\/option>\n\t<option value=\"51\">Costa Rica<\/option>\n\t<option value=\"53\">Croatia<\/option>\n\t<option value=\"54\">Cuba<\/option>\n\t<option value=\"55\">Cyprus<\/option>\n\t<option value=\"56\">Czech Republic<\/option>\n\t<option value=\"52\">C&ocirc;te d\'Ivoire<\/option>\n\t<option value=\"57\">Denmark<\/option>\n\t<option value=\"58\">Djibouti<\/option>\n\t<option value=\"59\">Dominica<\/option>\n\t<option value=\"60\">Dominican Republic<\/option>\n\t<option value=\"240\">East Timor<\/option>\n\t<option value=\"61\">East Timor<\/option>\n\t<option value=\"62\">Ecuador<\/option>\n\t<option value=\"63\">Egypt<\/option>\n\t<option value=\"64\">El Salvador<\/option>\n\t<option value=\"65\">Equatorial Guinea<\/option>\n\t<option value=\"66\">Eritrea<\/option>\n\t<option value=\"67\">Estonia<\/option>\n\t<option value=\"68\">Ethiopia<\/option>\n\t<option value=\"69\">Falkland Islands (Malvinas)<\/option>\n\t<option value=\"70\">Faroe Islands<\/option>\n\t<option value=\"71\">Fiji<\/option>\n\t<option value=\"72\">Finland<\/option>\n\t<option value=\"73\">France<\/option>\n\t<option value=\"75\">French Guiana<\/option>\n\t<option value=\"76\">French Polynesia<\/option>\n\t<option value=\"77\">French Southern Territories<\/option>\n\t<option value=\"78\">Gabon<\/option>\n\t<option value=\"79\">Gambia<\/option>\n\t<option value=\"80\">Georgia<\/option>\n\t<option value=\"81\">Germany<\/option>\n\t<option value=\"82\">Ghana<\/option>\n\t<option value=\"83\">Gibraltar<\/option>\n\t<option value=\"84\">Greece<\/option>\n\t<option value=\"85\">Greenland<\/option>\n\t<option value=\"86\">Grenada<\/option>\n\t<option value=\"87\">Guadeloupe<\/option>\n\t<option value=\"88\">Guam<\/option>\n\t<option value=\"89\">Guatemala<\/option>\n\t<option value=\"90\">Guinea<\/option>\n\t<option value=\"91\">Guinea-Bissau<\/option>\n\t<option value=\"92\">Guyana<\/option>\n\t<option value=\"93\">Haiti<\/option>\n\t<option value=\"94\">Heard and McDonald Islands<\/option>\n\t<option value=\"95\">Honduras<\/option>\n\t<option value=\"96\">Hong Kong<\/option>\n\t<option value=\"97\">Hungary<\/option>\n\t<option value=\"98\">Iceland<\/option>\n\t<option value=\"99\">India<\/option>\n\t<option value=\"100\">Indonesia<\/option>\n\t<option value=\"101\">Iran, Islamic Republic of<\/option>\n\t<option value=\"102\">Iraq<\/option>\n\t<option value=\"103\">Ireland<\/option>\n\t<option value=\"104\">Israel<\/option>\n\t<option value=\"105\">Italy<\/option>\n\t<option value=\"106\">Jamaica<\/option>\n\t<option value=\"107\">Japan<\/option>\n\t<option value=\"241\">Jersey<\/option>\n\t<option value=\"108\">Jordan<\/option>\n\t<option value=\"109\">Kazakhstan<\/option>\n\t<option value=\"110\">Kenya<\/option>\n\t<option value=\"111\">Kiribati<\/option>\n\t<option value=\"112\">Korea, Democratic People\'s Republic of<\/option>\n\t<option value=\"113\">Korea, Republic of<\/option>\n\t<option value=\"114\">Kuwait<\/option>\n\t<option value=\"115\">Kyrgyzstan<\/option>\n\t<option value=\"116\">Lao People\'s Democratic Republic<\/option>\n\t<option value=\"117\">Latvia<\/option>\n\t<option value=\"118\">Lebanon<\/option>\n\t<option value=\"119\">Lesotho<\/option>\n\t<option value=\"120\">Liberia<\/option>\n\t<option value=\"121\">Libya<\/option>\n\t<option value=\"122\">Liechtenstein<\/option>\n\t<option value=\"123\">Lithuania<\/option>\n\t<option value=\"124\">Luxembourg<\/option>\n\t<option value=\"125\">Macau<\/option>\n\t<option value=\"126\">Macedonia, the former Yugoslav Republic of<\/option>\n\t<option value=\"127\">Madagascar<\/option>\n\t<option value=\"128\">Malawi<\/option>\n\t<option value=\"129\">Malaysia<\/option>\n\t<option value=\"130\">Maldives<\/option>\n\t<option value=\"131\">Mali<\/option>\n\t<option value=\"132\">Malta<\/option>\n\t<option value=\"133\">Marshall Islands<\/option>\n\t<option value=\"134\">Martinique<\/option>\n\t<option value=\"135\">Mauritania<\/option>\n\t<option value=\"136\">Mauritius<\/option>\n\t<option value=\"137\">Mayotte<\/option>\n\t<option value=\"138\">Mexico<\/option>\n\t<option value=\"139\">Micronesia, Federated States of<\/option>\n\t<option value=\"140\">Moldova, Republic of<\/option>\n\t<option value=\"141\">Monaco<\/option>\n\t<option value=\"142\">Mongolia<\/option>\n\t<option value=\"143\">Montserrat<\/option>\n\t<option value=\"144\">Morocco<\/option>\n\t<option value=\"145\">Mozambique<\/option>\n\t<option value=\"146\">Myanmar<\/option>\n\t<option value=\"147\">Namibia<\/option>\n\t<option value=\"148\">Nauru<\/option>\n\t<option value=\"149\">Nepal<\/option>\n\t<option value=\"150\">Netherlands<\/option>\n\t<option value=\"151\">Netherlands Antilles<\/option>\n\t<option value=\"152\">New Caledonia<\/option>\n\t<option value=\"153\">New Zealand<\/option>\n\t<option value=\"154\">Nicaragua<\/option>\n\t<option value=\"155\">Niger<\/option>\n\t<option value=\"156\">Nigeria<\/option>\n\t<option value=\"157\">Niue<\/option>\n\t<option value=\"158\">Norfolk Island<\/option>\n\t<option value=\"159\">Northern Mariana Islands<\/option>\n\t<option value=\"160\">Norway<\/option>\n\t<option value=\"161\">Oman<\/option>\n\t<option value=\"162\">Pakistan<\/option>\n\t<option value=\"163\">Palau<\/option>\n\t<option value=\"248\">Palestinian Territory, Occupied<\/option>\n\t<option value=\"164\">Panama<\/option>\n\t<option value=\"165\">Papua New Guinea<\/option>\n\t<option value=\"166\">Paraguay<\/option>\n\t<option value=\"167\">Peru<\/option>\n\t<option value=\"168\">Philippines<\/option>\n\t<option value=\"169\">Pitcairn<\/option>\n\t<option value=\"170\">Poland<\/option>\n\t<option value=\"171\">Portugal<\/option>\n\t<option value=\"172\">Puerto Rico<\/option>\n\t<option value=\"173\">Qatar<\/option>\n\t<option value=\"175\">Romania<\/option>\n\t<option value=\"176\">Russian Federation<\/option>\n\t<option value=\"177\">Rwanda<\/option>\n\t<option value=\"174\">R&eacute;union<\/option>\n\t<option value=\"197\">Saint Helena<\/option>\n\t<option value=\"178\">Saint Kitts and Nevis<\/option>\n\t<option value=\"179\">Saint Lucia<\/option>\n\t<option value=\"246\">Saint Martin (French part)<\/option>\n\t<option value=\"198\">Saint Pierre and Miquelon<\/option>\n\t<option value=\"180\">Saint Vincent and the Grenadines<\/option>\n\t<option value=\"181\">Samoa<\/option>\n\t<option value=\"182\">San Marino<\/option>\n\t<option value=\"183\">Sao Tome And Principe<\/option>\n\t<option value=\"184\">Saudi Arabia<\/option>\n\t<option value=\"185\">Senegal<\/option>\n\t<option value=\"245\">Serbia<\/option>\n\t<option value=\"186\">Seychelles<\/option>\n\t<option value=\"187\">Sierra Leone<\/option>\n\t<option value=\"188\">Singapore<\/option>\n\t<option value=\"247\">Sint Maarten (Dutch part)<\/option>\n\t<option value=\"189\">Slovakia<\/option>\n\t<option value=\"190\">Slovenia<\/option>\n\t<option value=\"191\">Solomon Islands<\/option>\n\t<option value=\"192\">Somalia<\/option>\n\t<option value=\"193\">South Africa<\/option>\n\t<option value=\"194\">South Georgia and the South Sandwich Islands<\/option>\n\t<option value=\"195\">Spain<\/option>\n\t<option value=\"196\">Sri Lanka<\/option>\n\t<option value=\"242\">St. Barthelemy<\/option>\n\t<option value=\"243\">St. Eustatius<\/option>\n\t<option value=\"199\">Sudan<\/option>\n\t<option value=\"200\">Suriname<\/option>\n\t<option value=\"201\">Svalbard and Jan Mayen<\/option>\n\t<option value=\"202\">Swaziland<\/option>\n\t<option value=\"203\">Sweden<\/option>\n\t<option value=\"204\">Switzerland<\/option>\n\t<option value=\"205\">Syrian Arab Republic<\/option>\n\t<option value=\"206\">Taiwan<\/option>\n\t<option value=\"207\">Tajikistan<\/option>\n\t<option value=\"208\">Tanzania, United Republic of<\/option>\n\t<option value=\"209\">Thailand<\/option>\n\t<option value=\"237\">The Democratic Republic of Congo<\/option>\n\t<option value=\"210\">Togo<\/option>\n\t<option value=\"211\">Tokelau<\/option>\n\t<option value=\"212\">Tonga<\/option>\n\t<option value=\"213\">Trinidad and Tobago<\/option>\n\t<option value=\"214\">Tunisia<\/option>\n\t<option value=\"215\">Turkey<\/option>\n\t<option value=\"216\">Turkmenistan<\/option>\n\t<option value=\"217\">Turks and Caicos Islands<\/option>\n\t<option value=\"218\">Tuvalu<\/option>\n\t<option value=\"219\">Uganda<\/option>\n\t<option value=\"220\">Ukraine<\/option>\n\t<option value=\"221\">United Arab Emirates<\/option>\n\t<option value=\"222\">United Kingdom<\/option>\n\t<option value=\"223\" selected=\"selected\">United States<\/option>\n\t<option value=\"224\">United States Minor Outlying Islands<\/option>\n\t<option value=\"225\">Uruguay<\/option>\n\t<option value=\"226\">Uzbekistan<\/option>\n\t<option value=\"227\">Vanuatu<\/option>\n\t<option value=\"228\">Vatican City State (Holy See)<\/option>\n\t<option value=\"229\">Venezuela<\/option>\n\t<option value=\"230\">Viet Nam<\/option>\n\t<option value=\"231\">Virgin Islands, British<\/option>\n\t<option value=\"232\">Virgin Islands, U.S.<\/option>\n\t<option value=\"233\">Wallis and Futuna<\/option>\n\t<option value=\"234\">Western Sahara<\/option>\n\t<option value=\"235\">Yemen<\/option>\n\t<option value=\"238\">Zambia<\/option>\n\t<option value=\"239\">Zimbabwe<\/option>\n<\/select>\n","description":"","virtuemart_country_id":223,"country_2_code":"US","country_3_code":"USA"},"virtuemart_state_id":{"name":"virtuemart_state_id","value":"Hawaii","title":"State \/ Province \/ Region","type":"select","required":"1","hidden":false,"formcode":"<select  id=\"virtuemart_state_id\" class=\"vm-chzn-select\" name=\"virtuemart_state_id\" style=\"width: 210px\">\n\t\t\t\t\t\t<option value=\"\">-- Select --<\/option>\n\t\t\t\t\t\t<\/select>","description":"","virtuemart_state_id":12,"state_2_code":"HI","state_3_code":"HWI"},"phone_1":{"name":"phone_1","value":"","title":"Phone","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"phone_1_field\" name=\"phone_1\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""},"phone_2":{"name":"phone_2","value":"","title":"Mobile phone","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"phone_2_field\" name=\"phone_2\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""},"fax":{"name":"fax","value":"","title":"Fax","type":"text","required":"0","hidden":false,"formcode":"<input type=\"text\" id=\"fax_field\" name=\"fax\" size=\"30\" value=\"\"  maxlength=\"32\" \/> ","description":""}},"functions":[],"scripts":[],"links":[]}');
        }

        public function confirm()
        {

            $cart = VirtueMartCart::getCart(true);
            return $cart->confirmDone();
        }

        public function getMedia($pid)
        {
            $db = JFactory::getDbo();
            $query = ' SELECT pm.virtuemart_media_id FROM #__virtuemart_product_medias AS pm '
                    . ' WHERE pm.virtuemart_product_id = ' . $pid;
            $db->setQuery($query);
            $mid = $db->loadResult();
            if ($mid)
            {
                $query = ' SELECT * FROM #__virtuemart_medias WHERE virtuemart_media_id = ' . $mid;
                $db->setQuery($query);
                $media = $db->loadObject();
                if ($media)
                {
                    $media->imageUrl = rtrim(JUri::root(), '/') . '/' . $media->file_url;
                    $media->thumbUrl = rtrim(JUri::root(), '/') . '/' . $media->file_url_thumb;
                    return $media;
                }
            }
            return false;
        }

    }

}
