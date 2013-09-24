<?php
/**
 * Application_Model_TemporaryProjectMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_TemporaryProjectMapper
{
    /**
     * @var Application_Model_DbTable_TemporaryProject
     */
    private $_dbTable = null;
    
    /**
     * Create Zend_Db_Adapter_Abstract object
     *
     * @return Application_Model_DbTable_TemporaryProject
     */
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_TemporaryProject();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Save project
     *
     * @param  Application_Model_Project $project
     * @return int
     */
    public function saveProject(Application_Model_Project $project)
    {
        $data = array(
            'user_id' => $project->getUserId(),
            'project_category_id' => $project->getProjectCategoryId(),
            'project_title' => $project->getProjectTitle(),
            'project_description' => $project->getProjectDescription(),
            'cost' => $project->getCost(),
            'CurrencyCode' => $project->getCurrencyCode(),
            'additional_remarks' => $project->getAdditionalRemarks(),
            'meet_up_required' => $project->getMeetUpRequired(),
            'milestone_payments' => $project->getMilestonePayments(),
            'require_portfolio' => $project->getRequirePortfolio(),
            'status' => $project->getStatus(),
            'created_on' => $project->getCreatedOn(),
            'bid_ending_date' => $project->getBidEndingDate()
        );
        
        return $this->getTable()->insert($data);
    }
    
    /**
     * Get project
     *
     * @param  int $projectId
     * @return Application_Model_Project
     */
    public function getProject($projectId)
    {
        $select = $this->getTable()->select();
        $select->where('project_id = ?', $projectId);
        $row = $this->getTable()->fetchRow($select);
        
        $project = new Application_Model_Project();
        $project->setUserId($row->user_id);
        $project->setCurrencyCode($row->CurrencyCode); 
        $project->setProjectTitle($row->project_title);  
        $project->setProjectDescription($row->project_description);
        $project->setProjectCategoryId($row->project_category_id);
        $project->setCost($row->cost); 
        $project->setBidEndingDate($row->bid_ending_date);  
        $project->setAdditionalRemarks($row->additional_remarks); 
        $project->setMeetUpRequired($row->meet_up_required);
        $project->setMilestonePayments($row->milestone_payments);
        $project->setRequirePortfolio($row->require_portfolio);
        $project->setStatus($row->status); 
        $project->setCreatedOn($row->created_on); 
        
        return $project;
    }
    


}