<?php
/**
 * Paypal IPN
 * 
 * Paypal calls this class method as Instant Payment Notification to acknowledge payment processing
 * 
 * @category   Application
 * @package    Application_Controller
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @uses       Zend_Controller_Action
 * @version    1.0
 */
class IpnController extends Zend_Controller_Action
{
    /**
     * Update subscription
     *
     * @return void
     */    
    public function indexAction()
    {
        $subscrId = $this->getRequest()->getPost('subscr_id');
        $txnType = $this->getRequest()->getPost('txn_type');

        $userMapper = new Application_Model_UserMapper();
        
        if ( $txnType == 'subscr_cancel' || $txnType == 'subscr_failed' || $txnType == 'subscr_eot' ) {
            $userMapper->cancelSubscription($subscrId);
        } else {
            $paymentStatus = $this->getRequest()->getPost('payment_status');
            $itemName = $this->getRequest()->getPost('item_name');
            $itemNameArr = explode('__', $itemName);
            $userId = $itemNameArr[0];            
            
            if($paymentStatus == 'Pending' || $paymentStatus == 'Completed' || $txnType == 'subscr_payment') {
                $userMapper->updateSubscription($userId, $subscrId);
            }
        }
    }
    
   
}