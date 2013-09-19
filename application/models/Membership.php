<?php
/**
 * Application_Model_Membership class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_Membership
{
    protected $_membershipId;
    protected $_membership;
    protected $_subscriptionInterval;
    protected $_isPremium;
    protected $_isDefault;
    protected $_membershipCost;
    protected $_status;
    protected $_createdOn;
    
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
    
    public function setMembershipId($membershipId)
    {
        $this->_membershipId = $membershipId;
    }
    
    public function getMembershipId()
    {
        return $this->_membershipId;
    }
    
    public function setMembership($membership)
    {
        $this->_membership = $membership;
    }
    
    public function getMembership()
    {
        return $this->_membership;
    }

    public function setSubscriptionInterval($subscriptionInterval)
    {
        $this->_subscriptionInterval = $subscriptionInterval;
    }
    
    public function getSubscriptionInterval()
    {
        return $this->_subscriptionInterval;
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
    
    public function setIsPremium($isPremium)
    {
        $this->_isPremium = $isPremium;
    }
    
    public function getIsPremium()
    {
        return $this->_isPremium;
    }
    
    public function setIsDefault($isDefault)
    {
        $this->_isDefault = $isDefault;
    }
    
    public function getIsDefault()
    {
        return $this->_isDefault;
    }
    
    public function setMembershipCost($membershipCost)
    {
        $this->_membershipCost = $membershipCost;
    }
    
    public function getMembershipCost()
    {
        return $this->_membershipCost;
    }

    
}