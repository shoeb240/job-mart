<?php
/**
 * Application_Model_PrimaryCategory class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_PrimaryCategory
{
    protected $_primaryCategoryId;
    protected $_categoryTitle;
    protected $_categoryDescription;
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
    
    public function setPrimaryCategoryId($primaryCategoryId)
    {
        $this->_primaryCategoryId = $primaryCategoryId;
    }
    
    public function getPrimaryCategoryId()
    {
        return $this->_primaryCategoryId;
    }
    
    public function setCategoryTitle($categoryTitle)
    {
        $this->_categoryTitle = $categoryTitle;
    }
    
    public function getCategoryTitle()
    {
        return $this->_categoryTitle;
    }
    
    public function setCategoryDescription($categoryDescription)
    {
        $this->_categoryDescription = $categoryDescription;
    }
    
    public function getCategoryDescription()
    {
        return $this->_categoryDescription;
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
    
}