<?php
/**
 * Application_Model_ProjectAttachment class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_ProjectAttachment
{
    protected $_projectAttachmentId;
    protected $_projectId;
    protected $_attachment;
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
    
    public function setProjectAttachmentId($projectAttachmentId)
    {
        $this->_projectAttachmentId = $projectAttachmentId;
    }
    
    public function getProjectAttachmentId()
    {
        return $this->_projectAttachmentId;
    }
    
    public function setProjectId($projectId)
    {
        $this->_projectId = $projectId;
    }
    
    public function getProjectId()
    {
        return $this->_projectId;
    }
    
    public function setAttachment($attachment)
    {
        $this->_attachment = $attachment;
    }
    
    public function getAttachment()
    {
        return $this->_attachment;
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