<?php
/**
 * Application_Model_ProjectBidAttachMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_ProjectBidAttachMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_ProjectBidAttach();
        }
        
        return $this->_dbTable;
    }
    
    public function saveProjectBidAttach(Application_Model_ProjectBidAttach $projectBidAttach)
    {
        $data = array(
            'project_id' => $projectBidAttach->getProjectId(),
            'BIDDER_user_id' => $projectBidAttach->getBidderUserId(),
            'attachment' => $projectBidAttach->getAttachment(),
            'status' => $projectBidAttach->getStatus(),
            'created_on' => $projectBidAttach->getCreatedOn()
        );
        
        return $this->getTable()->insert($data);
    }
    

}