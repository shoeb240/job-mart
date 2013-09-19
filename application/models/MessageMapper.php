<?php
/**
 * Application_Model_MessageMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_MessageMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Message();
        }
        
        return $this->_dbTable;
    }
    
    public function getInboxList($userId, $startLimit, $limit)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.*', 'total_message' => 'COUNT(m.message_id)'))
               ->joinLeft(array('p' => 'job_project'), 
                          'p.project_id = m.project_id', 
                          array('p.user_id', 'subject' => 'p.project_title'))
               ->joinLeft(array('u' => 'job_user'), 
                          'm.SENDER_user_id = u.user_id', 
                          array('sender' => 'u.username'))
               ->where('m.RECEIVER_user_id = ?', $userId)
               ->where('m.status != ?', 3)
               ->group(array('m.SENDER_user_id', 'm.project_id'))
               ->order('m.created_on DESC')
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $message = new Application_Model_Message();
            $message->setMessageId($row->message_id);
            $message->setProjectId($row->project_id);
            $message->setSenderUserId($row->SENDER_user_id);
            $message->setReceiverUserId($row->RECEIVER_user_id);
            $message->setSubject($row->subject);
            $message->setReceiverRead($row->RECEIVER_read);
            $message->setCreatedOn($row->created_on);
            $sender = new Application_Model_User();
            $sender->setUsername($row->sender);
            $message->setSenderUser($sender);
            $message->setTotalMessage($row->total_message);
            $info[] = $message;
        }
        
        return $info;
    }
    
    public function getInboxCount($userId)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('job_message'), array('message_id'))
               ->where('RECEIVER_user_id = ?', $userId)
               ->where('status != ?', 3)
               ->group(array('SENDER_user_id', 'project_id'));
        $rowSets = $this->getTable()->fetchAll($select);
        
        return count($rowSets);
    }
    
    public function getInboxSearchedList($userId, $messageSearchUser, $startLimit, $limit)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.*', 'total_message' => 'COUNT(m.message_id)'))
               ->joinLeft(array('p' => 'job_project'), 
                          'p.project_id = m.project_id', 
                          array('p.user_id', 'subject' => 'p.project_title'))
               ->joinLeft(array('u' => 'job_user'), 
                          'm.SENDER_user_id = u.user_id', 
                          array('sender' => 'u.username'))
               ->where('m.RECEIVER_user_id = ?', $userId)
               ->where("u.username like '%$messageSearchUser%'")
               ->where('m.status != ?', 3)
               ->group(array('m.SENDER_user_id', 'm.project_id'))
               ->order('m.created_on DESC')
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $message = new Application_Model_Message();
            $message->setMessageId($row->message_id);
            $message->setProjectId($row->project_id);
            $message->setSenderUserId($row->SENDER_user_id);
            $message->setReceiverUserId($row->RECEIVER_user_id);
            $message->setSubject($row->subject);
            $message->setReceiverRead($row->RECEIVER_read);
            $message->setCreatedOn($row->created_on);
            $sender = new Application_Model_User();
            $sender->setUsername($row->sender);
            $message->setSenderUser($sender);
            $message->setTotalMessage($row->total_message);
            $info[] = $message;
        }
        
        return $info;
    }
    
    public function getInboxSearchedCount($userId, $messageSearchUser)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.message_id'))
               ->join(array('u' => 'job_user'), 
                          'm.SENDER_user_id = u.user_id', 
                          array())
               ->where('m.RECEIVER_user_id = ?', $userId)
               ->where("u.username like '%$messageSearchUser%'")
               ->where('m.status != ?', 3)
               ->group(array('m.SENDER_user_id', 'm.project_id'));
        $rowSets = $this->getTable()->fetchAll($select);
        return count($rowSets);
    }
    
    public function getProjectMessagesBySender($projectId, $senderUserId, $sessionUserId)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.*'))
               ->join(array('u' => 'job_user'), 
                          'm.SENDER_user_id = u.user_id', 
                          array('sender' => 'u.username', 'u.profile_image'))
               ->where('m.project_id = ' . $projectId . ' AND SENDER_user_id = ' . $senderUserId . ' AND RECEIVER_user_id = ' . $sessionUserId)
               ->orWhere('m.project_id = ' . $projectId . ' AND SENDER_user_id = ' . $sessionUserId . ' AND RECEIVER_user_id = ' . $senderUserId)
               ->order('m.message_id DESC');
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $message = new Application_Model_Message();
            $message->setMessageId($row->message_id);
            $message->setProjectId($row->project_id);
            $message->setSenderUserId($row->SENDER_user_id);
            $message->setReceiverUserId($row->RECEIVER_user_id);
            $message->setMessage($row->message);
            $message->setReceiverRead($row->RECEIVER_read);
            $message->setCreatedOn($row->created_on);
            $sender = new Application_Model_User();
            $sender->setUsername($row->sender);
            $sender->setProfileImage($row->profile_image);
            $message->setSenderUser($sender);
            $info[] = $message;
        }
        
        return $info;
    }    
    
    public function saveMessage(Application_Model_Message $message)
    {
        $data['project_id']       = $message->getProjectId();
        $data['SENDER_user_id']   = $message->getSenderUserId();
        $data['RECEIVER_user_id'] = $message->getReceiverUserId();
        $data['message']          = $message->getMessage();
        return $this->getTable()->insert($data);
    }
    
    public function deleteInboxMessage($projectId, $senderUserId, $userId)
    {
        $data = array(
            'status' => 3
        );

        $where[] = $this->getTable()->getAdapter()->quoteInto('project_id = ?', $projectId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('SENDER_user_id = ?', $senderUserId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('RECEIVER_user_id = ?', $userId, 'INTEGER');

        $this->getTable()->update($data, $where);
    }
    
    public function getOutboxList($userId, $startLimit, $limit)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.*', 'total_message' => 'COUNT(m.message_id)'))
               ->joinLeft(array('p' => 'job_project'), 
                          'p.project_id = m.project_id', 
                          array('p.user_id', 'subject' => 'p.project_title'))
               ->joinLeft(array('u' => 'job_user'), 
                          'm.RECEIVER_user_id = u.user_id', 
                          array('receiver' => 'u.username'))
               ->where('m.SENDER_user_id = ?', $userId)
               ->where('m.status != ?', 4)
               ->group(array('m.RECEIVER_user_id', 'm.project_id'))
               ->order('m.created_on DESC')
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $message = new Application_Model_Message();
            $message->setMessageId($row->message_id);
            $message->setProjectId($row->project_id);
            $message->setSenderUserId($row->SENDER_user_id);
            $message->setReceiverUserId($row->RECEIVER_user_id);
            $message->setSubject($row->subject);
            $message->setReceiverRead($row->RECEIVER_read);
            $message->setCreatedOn($row->created_on);
            $sender = new Application_Model_User();
            $sender->setUsername($row->receiver);
            $message->setReceiverUser($sender);
            $message->setTotalMessage($row->total_message);
            $info[] = $message;
        }
        
        return $info;
    }
    
    public function getOutboxCount($userId)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from('job_message', array('message_id'))
               ->where('SENDER_user_id = ?', $userId)
               ->where('status != ?', 4)
               ->group(array('RECEIVER_user_id', 'project_id'));
        $rowSets = $this->getTable()->fetchAll($select);
        
        return count($rowSets);
    }
    
    public function getOutboxSearchedList($userId, $messageSearchUser, $startLimit, $limit)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.*', 'total_message' => 'COUNT(m.message_id)'))
               ->joinLeft(array('p' => 'job_project'), 
                          'p.project_id = m.project_id', 
                          array('p.user_id', 'subject' => 'p.project_title'))
               ->joinLeft(array('u' => 'job_user'), 
                          'm.RECEIVER_user_id = u.user_id', 
                          array('receiver' => 'u.username'))
               ->where('m.SENDER_user_id = ?', $userId)
               ->where('m.status != ?', 4)
               ->where("u.username like '%$messageSearchUser%'")
               ->group(array('m.RECEIVER_user_id', 'm.project_id'))
               ->order('m.created_on DESC')
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $message = new Application_Model_Message();
            $message->setMessageId($row->message_id);
            $message->setProjectId($row->project_id);
            $message->setSenderUserId($row->SENDER_user_id);
            $message->setReceiverUserId($row->RECEIVER_user_id);
            $message->setSubject($row->subject);
            $message->setReceiverRead($row->RECEIVER_read);
            $message->setCreatedOn($row->created_on);
            $sender = new Application_Model_User();
            $sender->setUsername($row->receiver);
            $message->setReceiverUser($sender);
            $message->setTotalMessage($row->total_message);
            $info[] = $message;
        }
        
        return $info;
    }
    
    public function getOutboxSearchedCount($userId, $messageSearchUser)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('m' => 'job_message'), array('m.message_id'))
               ->joinLeft(array('u' => 'job_user'), 
                          'm.RECEIVER_user_id = u.user_id', 
                          array())
               ->where('m.SENDER_user_id = ?', $userId)
               ->where('m.status != ?', 4)
               ->where("u.username like '%$messageSearchUser%'")
               ->group(array('m.RECEIVER_user_id', 'm.project_id'));
        $rowSets = $this->getTable()->fetchAll($select);
        
        return count($rowSets);
    }
    
    public function deleteOutboxMessage($projectId, $receiverUserId, $userId)
    {
        $data = array(
            'status' => 4
        );

        $where[] = $this->getTable()->getAdapter()->quoteInto('project_id = ?', $projectId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('SENDER_user_id = ?', $userId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('RECEIVER_user_id = ?', $receiverUserId, 'INTEGER');

        $this->getTable()->update($data, $where);
    }
    
    public function unreadMessageCount($userId)
    {
        $select = $this->getTable()->select();
        $select->from('job_message', array('unread_message_number' => 'COUNT(message_id)'))
               ->where('RECEIVER_user_id = ?', $userId)
               ->where('RECEIVER_read = ?', 0);
        $row = $this->getTable()->fetchRow($select);
        
        return $row['unread_message_number'];
    }
    

   
}