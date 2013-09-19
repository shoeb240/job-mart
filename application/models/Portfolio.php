<?php
/**
 * Application_Model_Portfolio class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_Portfolio
{
    protected $_portfolioId;
    protected $_userId;
    protected $_clientName;
    protected $_portfolioTitle;
    protected $_projectUrl;
    protected $_projectDescription;
    protected $_portfolioImage;
    protected $_status;
    protected $_createdOn;
    protected $_updatedOn;
    
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
    
    public function setPortfolioId($portfolioId)
    {
        $this->_portfolioId = $portfolioId;
    }
    
    public function getPortfolioId()
    {
        return $this->_portfolioId;
    }
    
    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function setClientName($clientName)
    {
        $this->_clientName = $clientName;
    }
    
    public function getClientName()
    {
        return $this->_clientName;
    }
    
    public function setPortfolioTitle($portfolioTitle)
    {
        $this->_portfolioTitle = $portfolioTitle;
    }
    
    public function getPortfolioTitle()
    {
        return $this->_portfolioTitle;
    }
    
    public function setProjectUrl($projectUrl)
    {
        $this->_projectUrl = $projectUrl;
    }
    
    public function getProjectUrl()
    {
        return $this->_projectUrl;
    }
    
    public function setProjectDescription($projectDescription)
    {
        $this->_projectDescription = $projectDescription;
    }
    
    public function getProjectDescription()
    {
        return $this->_projectDescription;
    }
    
    public function setPortfolioImage($portfolioImage)
    {
        $this->_portfolioImage = $portfolioImage;
    }
    
    public function getPortfolioImage()
    {
        return $this->_portfolioImage;
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
    
    public function setUpdatedOn($updatedOn)
    {
        $this->_updatedOn = $updatedOn;
    }
    
    public function getUpdatedOn()
    {
        return $this->_updatedOn;
    }
    
    
}