<?php
/**
 * Application_Model_ProjectAttachmentMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_ProjectAttachmentMapper
{
    /**
     * @var Application_Model_DbTable_ProjectAttachment
     */
    private $_dbTable = null;
    
    /**
     * Create Zend_Db_Adapter_Abstract object
     *
     * @return Application_Model_DbTable_ProjectAttachment
     */
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_ProjectAttachment();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Get project attachments
     *
     * @param  int   $projectId
     * @return array $info      Array of Application_Model_ProjectAttachment
     */
    public function getProjectAttachments($projectId)
    {
        $select = $this->getTable()->select();
        $select->where('project_id = ?', $projectId);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $projectAttachment = new Application_Model_ProjectAttachment();
            $projectAttachment->setProjectAttachmentId($row->project_attachment_id);
            $projectAttachment->setProjectId($row->project_id);
            $projectAttachment->setAttachment($row->attachment);
            $projectAttachment->setStatus($row->status);
            $projectAttachment->setCreatedOn($row->created_on);
            $info[] = $projectAttachment;
        }
        
        return $info;
    }
    
    /**
     * Save project attachment
     *
     * @param  Application_Model_ProjectAttachment $projectAttachment
     * @return int
     */
    public function saveProjectAttachment(Application_Model_ProjectAttachment $projectAttachment)
    {
        $data = array(
            'project_id' => $projectAttachment->getProjectId(),
            'attachment' => $projectAttachment->getAttachment()
        );
        
        return $this->getTable()->insert($data);
    }

}