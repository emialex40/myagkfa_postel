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

if (!class_exists('ZtonepageHelperAjax'))
{

    /**
     * Ajax execute class
     */
    class ZtonepageHelperAjax
    {
        protected $_responses = array();

        /**
         * Execute Joomla login
         * @todo Remember me
         */
        public static function userLogin()
        {
            $input = JFactory::getApplication()->input;
            $ajax = ZtonepageHelperAjax::getInstance();
            $username = $input->get('username');
            $password = $input->get('password');
            $options = array();
            $options['remember'] = JFactory::getApplication()->input->getBool('remember', false);
            if (ZtonepageHelperJoomlaUser::login($username, $password, '', '', $options) || JFactory::getUser()->guest == false)
            {
                // Login success than we need reload this html
                $ajax->addExecute('zt.joomla.updateToken("' . JSession::getFormToken() . '")');
                $ajax->addExecute('zt.onepagecheckout._settings.message="Login successful !";zt.onepagecheckout._settings.error=false;zt.onepagecheckout.display();');
            } else
            {
                $ajax->addMessage('Wrong username / password');
                $ajax->addExecute('zt.onepagecheckout._settings.message="Wrong username / password !!";zt.onepagecheckout._settings.error=true;zt.onepagecheckout.display();');
            }
            $ajax->response();
        }

        public static function guestCheckout()
        {
            $input = JFactory::getApplication()->input;
            $ajax = ZtonepageHelperAjax::getInstance();

            if (ZtonepageHelperJoomlaUser::quickLogin($input->getString('email')))
            {
                $ajax->addExecute('zt.onepagecheckout.display();');
            } else
            {
                $ajax->addMessage('Something wrong');
            }
            $ajax->response();
        }

        /**
         * Display whole form view
         */
        public static function display()
        {
            // Get view class
            $view = new VirtueMartViewCart();
            /**
             * @todo What am i dong here ? !!!
             */
            ob_start();
            $html = $view->display();
            $html = ob_get_contents();
            ob_end_clean();
            $ajax = ZtonepageHelperAjax::getInstance();
            /**
             * @todo We need replaceHtml function
             */
            $ajax->addHtml($html, '#zt-opc-plugin');
            $ajax->response();
        }

        public static function updateBillTo()
        {
            $model = ZtonepageModelVirtuemart::getInstance();
            $model->updateAddress();
            /**
             * @todo Actually we no need to refresh page. Just show message as respond
             */
            $ajax = ZtonepageHelperAjax::getInstance();
            $ajax->addMessage(ZtonepageHelperText::_('UPDATE_SUCCESSED'));
            $ajax->response();
        }

        public static function updateShipTo()
        {
            self::updateBillTo();
        }

        public static function updateCouponCode()
        {
            $model = ZtonepageModelVirtuemart::getInstance();
            $model->updateCoupon(JFactory::getApplication()->input->get('coupon_code'));
        }

        /**
         * 
         */
        public static function updateCartQuantity()
        {
            $input = JFactory::getApplication()->input;
            // Simulator VM request
            $quantity[$input->getInt('pKey')] = $input->get('quantity');
            $input->set('quantity', $quantity);
            $model = ZtonepageModelVirtuemart::getInstance();
            $model->updateCart();
            $ajax = ZtonepageHelperAjax::getInstance();
            $view = new VirtueMartViewCart();
            /**
             * @todo What am i dong here ? !!!
             */
            ob_start();
            $view->setLayout('cart_summary');
            $html = $view->display();
            $html = ob_get_contents();
            ob_end_clean();

            $ajax->addHtml($html, '#zt-opc-shoppingcart .inner-wrap');
            $ajax->response();
        }

        public static function updateShipment()
        {
            $model = ZtonepageModelVirtuemart::getInstance();
            $model->updateShipment();
            $ajax = ZtonepageHelperAjax::getInstance();
            $ajax->addExecute('zt.onepagecheckout.display();');
            $ajax->response();
        }

        public static function updatePurchaseConfirm()
        {
            $model = ZtonepageModelVirtuemart::getInstance();
            $ajax = ZtonepageHelperAjax::getInstance();
            $confirmed = $model->confirm();
            $view = new VirtueMartViewCart();
            if ($confirmed)
            {
                /**
                 * @todo What am i dong here ? !!!
                 */
                ob_start();
                $view->setLayout('order_done');
                $html = $view->display();
                $html = ob_get_contents();
                ob_end_clean();
                $session = JFactory::getSession();
                $session->restart();
                // Stronger forcing
                VirtueMartCart::getCart(true)->emptyCart();
                VirtueMartCart::getCart(true)->deleteCart();
                $ajax->addHtml($html, '#zt-opc-plugin');
                $ajax->addExecute('zt.onepagecheckout._rebind();');
            } else
            {
                $ajax->addMessage(ZtonepageHelperText::_('INVALID_DATA'));
            }
            $ajax->response();
        }

        /**
         * Register new user
         */
        public static function registerUser()
        {
            /**
             * @todo Use JInput instead
             */
            $return = ZtonepageHelperJoomlaUser::registerUser($_REQUEST);
            $ajax = ZtonepageHelperAjax::getInstance();

            if ($return === false)
            {
                $ajax->addMessage('Failed');
            } else
            {
                $message = implode(PHP_EOL, $return['message']);
                $ajax->addMessage($message);
            }
            $ajax->response();
        }

        /**
         * 
         */
        public static function updateCart()
        {
            $ajax = ZtonepageHelperAjax::getInstance();
            $cart = VirtueMartCart::getCart(true);
            $cart->_fromCart = true;
            $cart->_redirected = false;

            if (vRequest::get('cancel', 0))
            {
                $cart->_inConfirm = false;
            }
            if ($cart->getInCheckOut())
            {
                vRequest::setVar('checkout', true);
            }

            $cart->saveCartFieldsInCart();

            if ($cart->updateProductCart())
            {
                vmInfo('COM_VIRTUEMART_PRODUCT_UPDATED_SUCCESSFULLY');
            }

            $STsameAsBT = vRequest::getInt('STsameAsBT', vRequest::getInt('STsameAsBTjs', false));

            if ($STsameAsBT)
            {
                $cart->STsameAsBT = $STsameAsBT;
            }

            $currentUser = JFactory::getUser();

            if (!$currentUser->guest)
            {
                $cart->selected_shipto = vRequest::getVar('shipto', $cart->selected_shipto);
                if (!empty($cart->selected_shipto))
                {
                    $userModel = VmModel::getModel('user');
                    $stData = $userModel->getUserAddressList($currentUser->id, 'ST', $cart->selected_shipto);

                    if (isset($stData[0]) and is_object($stData[0]))
                    {
                        $stData = get_object_vars($stData[0]);
                        $cart->ST = $stData;
                        $cart->STsameAsBT = 0;
                    } else
                    {
                        $cart->selected_shipto = 0;
                    }
                }
                if (empty($cart->selected_shipto))
                {
                    $cart->STsameAsBT = 1;
                    $cart->selected_shipto = 0;
                    //$cart->ST = $cart->BT;
                }
            } else
            {
                $cart->selected_shipto = 0;
                if (!empty($cart->STsameAsBT))
                {
                    //$cart->ST = $cart->BT;
                }
            }

            if (!isset($force))
                $force = VmConfig::get('oncheckout_opc', true);
            $cart->setShipmentMethod($force, false);
            $cart->setPaymentMethod($force, false);

            $cart->prepareCartData();

            $coupon_code = trim(vRequest::getString('coupon_code', ''));
            if (!empty($coupon_code))
            {
                $msg = $cart->setCouponCode($coupon_code);
                if ($msg)
                    vmInfo($msg);
            }
            $ajax = ZtonepageHelperAjax::getInstance();
            $view = new VirtueMartViewCart();
            /**
             * @todo What am i dong here ? !!!
             */
            ob_start();
            $view->setLayout('cart_summary');
            $html = $view->display();
            $html = ob_get_contents();
            ob_end_clean();

            $ajax->addHtml($html, '#zt-opc-shoppingcart .inner-wrap');
            $ajax->response();
        }

        public static function updateCheckout()
        {
            $ajax = ZtonepageHelperAjax::getInstance();
            $cart = VirtueMartCart::getCart(true);
            $cart->_fromCart = true;
            $cart->_redirected = false;
            if (vRequest::get('cancel', 0))
            {
                $cart->_inConfirm = false;
            }
            if ($cart->getInCheckOut())
            {
                vRequest::setVar('checkout', true);
            }
            $cart->saveCartFieldsInCart();

            if ($cart->updateProductCart())
            {
                vmInfo('COM_VIRTUEMART_PRODUCT_UPDATED_SUCCESSFULLY');
            }

            $STsameAsBT = vRequest::getInt('STsameAsBT', vRequest::getInt('STsameAsBTjs', false));
            if ($STsameAsBT)
            {
                $cart->STsameAsBT = $STsameAsBT;
            }

            $currentUser = JFactory::getUser();
            if (!$currentUser->guest)
            {
                $cart->selected_shipto = vRequest::getVar('shipto', $cart->selected_shipto);
                if (!empty($cart->selected_shipto))
                {
                    $userModel = VmModel::getModel('user');
                    $stData = $userModel->getUserAddressList($currentUser->id, 'ST', $cart->selected_shipto);

                    if (isset($stData[0]) and is_object($stData[0]))
                    {
                        $stData = get_object_vars($stData[0]);
                        $cart->ST = $stData;
                        $cart->STsameAsBT = 0;
                    } else
                    {
                        $cart->selected_shipto = 0;
                    }
                }
                if (empty($cart->selected_shipto))
                {
                    $cart->STsameAsBT = 1;
                    $cart->selected_shipto = 0;
                    //$cart->ST = $cart->BT;
                }
            } else
            {
                $cart->selected_shipto = 0;
                if (!empty($cart->STsameAsBT))
                {
                    //$cart->ST = $cart->BT;
                }
            }

            if (!isset($force))
                $force = VmConfig::get('oncheckout_opc', true);

            $cart->setShipmentMethod(true, false);
            $cart->setPaymentMethod(true, false);

            $cart->prepareCartData();

            $coupon_code = trim(vRequest::getString('coupon_code', ''));
            if (!empty($coupon_code))
            {
                $msg = $cart->setCouponCode($coupon_code);
                if ($msg)
                    vmInfo($msg);
            }

            $request = vRequest::getRequest();
            $task = vRequest::getCmd('task');
            if (($task == 'confirm' or isset($request['confirm'])) and ! $cart->getInCheckOut())
            {

                $cart->confirmDone();

                $cart->_fromCart = false;
            } else
            {
                //$cart->_inCheckOut = false;
                $redirect = (isset($request['checkout']) or $task == 'checkout');
                $cart->_inConfirm = false;
                $cart->checkoutData($redirect);
            }

            if (!$cart->_fromCart)
            {
                $view = new VirtueMartViewCart();
                /**
                 * @todo What am i dong here ? !!!
                 */
                ob_start();
                $view->setLayout('order_done');
                $html = $view->display();
                $html = ob_get_contents();
                ob_end_clean();

                $ajax->addHtml($html, '#zt-opc-plugin');
                $ajax->addExecute('zt.onepagecheckout._rebind();');
            } else
            {
                $ajax->addMessage(ZtonepageHelperText::_('INVALID_DATA'));
            }
            $ajax->response();
        }

        /**
         *
         * @staticvar ZtonepageHelperAjax $instance
         * @return \ZtonepageHelperAjax
         */
        public static function getInstance()
        {
            static $instance;
            if (!isset($instance))
            {
                $instance = new ZtonepageHelperAjax();
            }
            return $instance;
        }

        /**
         * Add response data
         * @param mixed $data
         * @param string $key
         * @return \ZtonepageHelperAjax
         */
        public function add($data, $key = 'global')
        {
            $object = new stdClass();
            $object->type = $key;
            $object->data = $data;
            $this->_responses[] = $object;
            return $this;
        }

        /**
         * Add HTML to client side
         * @param type $html
         * @param type $target
         * @return \ZtonepageHelperAjax
         */
        public function addHtml($html, $target = '')
        {
            $data = new stdClass();
            $data->html = $html;
            $data->target = $target;
            return $this->add($data, 'html');
        }

        /**
         * Append HTML to client side
         * @param type $html
         * @param type $target
         * @return \ZtonepageHelperAjax
         */
        public function appendHtml($html, $target = '')
        {
            $data = new stdClass();
            $data->html = $html;
            $data->target = $target;
            return $this->add($data, 'appendHtml');
        }

        /**
         * Add notice message
         * @param type $message
         * @param type $type
         * @return \ZtonepageHelperAjax
         */
        public function addMessage($message, $header = '', $type = 'info')
        {
            switch ($type)
            {
                case 'error':
                case 'danger':
                case 'alert':
                    $messageType = 'error';
                    break;
                case 'warning':
                    $messageType = 'warning';
                    break;
                case 'success':
                case 'message':
                case '':
                    $messageType = 'success';
                    break;
                default:
                    $messageType = 'info';
                    break;
            }
            $html = new ZtonepageHtml();
            $html->set('header', $header);
            $html->set('message', $message);
            $html->set('type', $messageType);
            $html->set('originalType', $type);
            $data = new stdClass();
            $data->message = $html->fetch('Ztonepage://assets/message.php');
            $this->add($data, 'message');
            return $this;
        }

        /**
         * Add execute script
         * @param string $script
         * @return \ZtonepageHelperAjax
         */
        public function addExecute($script)
        {
            return $this->add($script, 'execute');
        }

        /**
         * Do response json data
         */
        public function response()
        {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($this->_responses);
            exit();
        }

        /**
         * Response empty object and exit
         */
        public function breakResponse()
        {
            echo CrexHelperEncode::jsonEncode(array());
            exit();
        }

        /*
         * return response withou json
         */
        public function responseMessage(){
            return $this->_responses;
        }
    }

}

