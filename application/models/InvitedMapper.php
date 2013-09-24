<?php
/**
 * Application_Model_InvitedMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_InvitedMapper
{
    /**
     * @var Application_Model_DbTable_Invited
     */
    private $_dbTable = null;
    
    /**
     * Create Zend_Db_Adapter_Abstract object
     *
     * @return Application_Model_DbTable_Invited
     */
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Invited();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Get invited member count
     *
     * @param  int $projectId
     * @return int
     */
    public function getInvitedMemberCount($projectId)
    {
        $select = $this->getTable()->select();
        $select->from('job_invited', array('count' => 'COUNT(*)'))
                ->where('project_id = ?', $projectId);
        $row = $this->getTable()->fetchRow($select);
        
        return $row->count;
    }
    
    /**
     * Save member invitation
     *
     * @param  Application_Model_Invited $invited
     * @return int
     */
    public function invitedSave(Application_Model_Invited $invited)
    {
        $data = array(
            'project_id' => $invited->getProjectId(),
            'user_id' => $invited->getUserId(),
            'status' => $invited->getStatus()
        );
        return $this->getTable()->insert($data);
    }
    
    /**
     * Delete member invitation
     *
     * @param  int $userId
     * @param  int $projectId
     * @return int              
     */
    public function deleteInvited($userId, $projectId)
    {
        $where[] = $this->getTable()->getAdapter()->quoteInto('user_id = ?', $userId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('project_id = ?', $projectId, 'INTEGER');

        return $this->getTable()->delete($where);
    }


}