<?php
/**
 * Application_Model_MembershipMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_MembershipMapper
{
    /**
     * @var Application_Model_DbTable_Membership
     */
    private $_dbTable = null;
    
    /**
     * Create Zend_Db_Adapter_Abstract object
     *
     * @return Application_Model_DbTable_Membership
     */
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Membership();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Get premiummembership list
     *
     * @return array $info Array of Application_Model_Membership
     */
    public function getPremiumMembershipList()
    {
        $select = $this->getTable()->select();
        $select->where('status = ?', 1)
               ->where('is_premium = ?', 1);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $membership = new Application_Model_Membership();
            $membership->setMembershipId($row->membership_id);
            $membership->setMembership($row->membership);
            $membership->setSubscriptionInterval($row->subscription_interval);
            $membership->setStatus($row->status);
            $membership->setCreatedOn($row->created_on);
            $membership->setIsPremium($row->is_premium);
            $membership->setIsDefault($row->is_default);
            $membership->setMembershipCost($row->membership_cost);
            $info[] = $membership;
        }
        
        return $info;
    }
    
    /**
     * Save new membership
     *
     * @param  Application_Model_Membership $membership
     * @return int
     */
    public function saveMembership(Application_Model_Membership $membership)
    {
        $data['project_id']       = $membership->getProjectId();
        $data['SENDER_user_id']   = $membership->getSenderUserId();
        $data['RECEIVER_user_id'] = $membership->getReceiverUserId();
        $data['membership']          = $membership->getMembership();
        return $this->getTable()->insert($data);
    }
    
    /**
     * Update membership status as deleted
     *
     * @param  int $projectId
     * @param  int $senderUserId
     * @param  int $userId      
     * @return int                  The number of rows updated.
     */
    public function deleteMembership($projectId, $senderUserId, $userId)
    {
        $data = array(
            'status' => 3
        );

        $where[] = $this->getTable()->getAdapter()->quoteInto('project_id = ?', $projectId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('SENDER_user_id = ?', $senderUserId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('RECEIVER_user_id = ?', $userId, 'INTEGER');

        return $this->getTable()->update($data, $where);
    }
    

   
}