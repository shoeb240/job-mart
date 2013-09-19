<?php
/**
 * Application_Model_CreditBalance class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_CreditBalance
{
    protected $_creditBalanceId;
    protected $_userId;
    protected $_transactionForUserId;
    protected $_status;
    protected $_createdOn;
    protected $_type;
    protected $_balance;
    
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
    
    public function setCreditBalanceId($creditBalanceId)
    {
        $this->_creditBalanceId = $creditBalanceId;
    }
    
    public function getCreditBalanceId()
    {
        return $this->_creditBalanceId;
    }
    
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function setTransactionForUserId($transactionForUserId)
    {
        $this->_transactionForUserId = $transactionForUserId;
    }
    
    public function getTransactionForUserId()
    {
        return $this->_transactionForUserId;
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
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function setBalance($balance)
    {
        $this->_balance = $balance;
    }
    
    public function getBalance()
    {
        return $this->_balance;
    }
    
}