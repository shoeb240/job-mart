<?php
/**
 * Application_Model_Message class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_Message
{
    protected $_messageId;
    protected $_projectId;
    protected $_senderUserId;
    protected $_receiverUserId;
    protected $_subject;
    protected $_message;
    protected $_status;
    protected $_receiverRead;
    protected $_parentMessageId;
    protected $_createdOn;
    protected $_totalMessage;
    protected $_senderUser;
    protected $_receiverUser;
    
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
    
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
    }
    
    public function getMessageId()
    {
        return $this->_messageId;
    }
    
    public function setProjectId($projectId)
    {
        $this->_projectId = $projectId;
    }
    
    public function getProjectId()
    {
        return $this->_projectId;
    }

    public function setSenderUserId($senderUserId)
    {
        $this->_senderUserId = $senderUserId;
    }
    
    public function getSenderUserId()
    {
        return $this->_senderUserId;
    }
    
    public function setReceiverUserId($receiverUserId)
    {
        $this->_receiverUserId = $receiverUserId;
    }
    
    public function getReceiverUserId()
    {
        return $this->_receiverUserId;
    }
    
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }
    
    public function getSubject()
    {
        return $this->_subject;
    }
    
    public function setMessage($message)
    {
        $this->_message = $message;
    }
    
    public function getMessage()
    {
        return $this->_message;
    }
    
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setReceiverRead($receiverRead)
    {
        $this->_receiverRead = $receiverRead;
    }
    
    public function getReceiverRead()
    {
        return $this->_receiverRead;
    }
    
    public function setParentMessageId($parentMessageId)
    {
        $this->_parentMessageId = $parentMessageId;
    }
    
    public function getParentMessageId()
    {
        return $this->_parentMessageId;
    }
    
    public function setCreatedOn($createdOn)
    {
        $this->_createdOn = $createdOn;
    }
    
    public function getCreatedOn()
    {
        return $this->_createdOn;
    }
    
    public function setTotalMessage($totalMessage)
    {
        $this->_totalMessage = $totalMessage;
    }
    
    public function getTotalMessage()
    {
        return $this->_totalMessage;
    }
    
    public function setSenderUser($senderUser)
    {
        $this->_senderUser = $senderUser;
    }
    
    public function getSenderUser()
    {
        return $this->_senderUser;
    }
    
    public function setReceiverUser($receiverUser)
    {
        $this->_receiverUser = $receiverUser;
    }
    
    public function getReceiverUser()
    {
        return $this->_receiverUser;
    }
    
}