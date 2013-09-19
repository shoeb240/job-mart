<?php
/**
 * Application_Model_Feedback class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_Feedback
{
    protected $_feedbackId;
    protected $_projectId;
    protected $_ownerUserId;
    protected $_ownerFeedbackRate;
    protected $_ownerComment;
    protected $_ownerPostDate;
    protected $_bidderUserId;
    protected $_bidderFeedbackRate;
    protected $_bidderComment;
    protected $_bidderPostDate;
    protected $_status;
    
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
    
    public function setFeedbackId($feedbackId)
    {
        $this->_feedbackId = $feedbackId;
    }
    
    public function getFeedbackId()
    {
        return $this->_feedbackId;
    }
    
    public function setProjectId($projectId)
    {
        $this->_projectId = $projectId;
    }
    
    public function getProjectId()
    {
        return $this->_projectId;
    }
    
    public function setOwnerUserId($ownerUserId)
    {
        $this->_ownerUserId = $ownerUserId;
    }
    
    public function getOwnerUserId()
    {
        return $this->_ownerUserId;
    }
    
    public function setOwnerFeedbackRate($ownerFeedbackRate)
    {
        $this->_ownerFeedbackRate = $ownerFeedbackRate;
    }
    
    public function getOwnerFeedbackRate()
    {
        return $this->_ownerFeedbackRate;
    }
    
    public function setOwnerComment($ownerComment)
    {
        $this->_ownerComment = $ownerComment;
    }
    
    public function getOwnerComment()
    {
        return $this->_ownerComment;
    }
    
    public function setOwnerPostDate($ownerPostDate)
    {
        $this->_ownerPostDate = $ownerPostDate;
    }
    
    public function getOwnerPostDate()
    {
        return $this->_ownerPostDate;
    }
    
    public function setBidderUserId($bidderUserId)
    {
        $this->_bidderUserId = $bidderUserId;
    }
    
    public function getBidderUserId()
    {
        return $this->_bidderUserId;
    }
    
    public function setBidderFeedbackRate($bidderFeedbackRate)
    {
        $this->_bidderFeedbackRate = $bidderFeedbackRate;
    }
    
    public function getBidderFeedbackRate()
    {
        return $this->_bidderFeedbackRate;
    }
    
    public function setBidderComment($bidderComment)
    {
        $this->_bidderComment = $bidderComment;
    }
    
    public function getBidderComment()
    {
        return $this->_bidderComment;
    }
    
    public function setBidderPostDate($bidderPostDate)
    {
        $this->_bidderPostDate = $bidderPostDate;
    }
    
    public function getBidderPostDate()
    {
        return $this->_bidderPostDate;
    }
    
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setProject(Application_Model_Project $project)
    {
        $this->_project = $project;
    }
    
    public function getProject()
    {
        return $this->_project;
    }
    
}