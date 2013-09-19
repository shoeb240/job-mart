<?php
/**
 * Application_Model_PaymentRecord class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_PaymentRecord
{
    protected $_paymentRecordId;
    protected $_userId;
    protected $_membershipId;
    protected $_cost;
    protected $_status;
    protected $_createdOn;
    protected $_txnId;
    
    public function __construct($options = null)
    {
        if (is_array($options)) $this->setOptions($options);
    }
    
    public function setOptions($options)
    {
        $methods = get_class_methods($this);
        foreach($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function setPaymentRecordId($paymentRecordId)
    {
        $this->_paymentRecordId = $paymentRecordId;
    }
    
    public function getPaymentRecordId()
    {
        return $this->_paymentRecordId;
    }
    
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function setMembershipId($membershipId)
    {
        $this->_membershipId = $membershipId;
    }
    
    public function getMembershipId()
    {
        return $this->_membershipId;
    }
    
    public function setCost($cost)
    {
        $this->_cost = $cost;
    }
    
    public function getCost()
    {
        return $this->_type;
    }
    
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setCreatedOn($createdOn)
    {
        $this->_createdOn = $createdOn;
    }
    
    public function getCreatedOn()
    {
        return $this->_createdOn;
    }
    
    public function setTxnId($txnId)
    {
        $this->_txnId = $txnId;
    }
    
    public function getTxnId()
    {
        return $this->_txnId;
    }
    
}