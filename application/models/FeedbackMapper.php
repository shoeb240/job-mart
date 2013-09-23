<?php
/**
 * Application_Model_FeedbackMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_FeedbackMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Feedback();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Feedbacks given as project owner or assigned bidder
     *
     * @return array
     */    
    public function getTestimonialsByUserId($userId, $orderType = 'DESC' , $startLimit = 0, $limit = 5)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
                
               ->from(array('f' => 'job_feedback'), array('f.owner_user_id',
                                                          'f.owner_feedback_rate',
                                                          'f.owner_comment',
                                                          'f.owner_post_date',
                                                          'f.bidder_user_id',
                                                          'f.bidder_feedback_rate',
                                                          'f.bidder_comment',     
                                                          'f.bidder_post_date'))
               ->join(array('p'=>'job_project'), 
                          'f.project_id = p.project_id', 
                          array('p.project_title', 'p.project_id', 
                                'p.assigned_user_id', 'p.cost'))
               ->where("f.owner_user_id = $userId && f.bidder_feedback_rate != ''")
               ->orWhere("f.bidder_user_id = $userId && f.owner_feedback_rate != ''")
               ->order('f.feedback_id ' . $orderType)
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $feedback = new Application_Model_Feedback();
            $feedback->setProjectId($row->project_id);
            $feedback->setOwnerUserId($row->owner_user_id);
            $feedback->setOwnerFeedbackRate($row->owner_feedback_rate);
            $feedback->setOwnerComment($row->owner_comment);
            $feedback->setOwnerPostDate($row->owner_post_date);
            $feedback->setBidderUserId($row->bidder_user_id);
            $feedback->setBidderFeedbackRate($row->bidder_feedback_rate);
            $feedback->setBidderComment($row->bidder_comment);
            $feedback->setBidderPostDate($row->bidder_post_date);
            $project = new Application_Model_Project();
            $project->setProjectTitle($row->project_title);
            $project->setProjectId($row->project_id);
            $project->setAssignedUserId($row->assigned_user_id);
            $project->setCost($row->cost);
            $feedback->setProject($project);
            $info[] = $feedback;
        }
        
        return $info;
    }
    
    public function getFeedbacksByMe($userId, $searchType = 'latest', $startLimit = 0, $limit = 10)
    {
        if($searchType == 'latest') {    
          $searchType = "f.feedback_id DESC";
        } else if ($searchType == 'rating') {
           $searchType = "f.owner_feedback_rate DESC";
        } else if ($searchType == 'alphabetical') {
           $searchType = "p.project_title ASC";
        } else {    
          $searchType = "f.feedback_id DESC";
        }
       
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
                
               ->from(array('f' => 'job_feedback'), array('f.owner_user_id',
                                                          'f.owner_feedback_rate',
                                                          'f.owner_comment',
                                                          'f.owner_post_date',
                                                          'f.bidder_user_id',
                                                          'f.bidder_feedback_rate',
                                                          'f.bidder_comment',     
                                                          'f.bidder_post_date'))
               ->join(array('p'=>'job_project'), 
                          'f.project_id = p.project_id', 
                          array('p.project_title', 'p.project_id', 
                                'p.assigned_user_id', 'p.cost'))
               ->where("f.owner_user_id = $userId AND f.owner_feedback_rate != ''")
               ->orWhere("f.bidder_user_id = $userId AND f.bidder_feedback_rate != ''")
               ->order($searchType)
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $feedback = new Application_Model_Feedback();
            $feedback->setProjectId($row->project_id);
            $feedback->setOwnerUserId($row->owner_user_id);
            $feedback->setOwnerFeedbackRate($row->owner_feedback_rate);
            $feedback->setOwnerComment($row->owner_comment);
            $feedback->setOwnerPostDate($row->owner_post_date);
            $feedback->setBidderUserId($row->bidder_user_id);
            $feedback->setBidderFeedbackRate($row->bidder_feedback_rate);
            $feedback->setBidderComment($row->bidder_comment);
            $feedback->setBidderPostDate($row->bidder_post_date);
            $project = new Application_Model_Project();
            $project->setProjectTitle($row->project_title);
            $project->setProjectId($row->project_id);
            $project->setAssignedUserId($row->assigned_user_id);
            $project->setCost($row->cost);
            $feedback->setProject($project);
            $info[] = $feedback;
        }
        
        return $info;
    }
    
    public function getFeedbacksByMeCount($userId)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('f' => 'job_feedback'), array('total_rows' => 'COUNT(f.owner_user_id)'))
               ->where("f.owner_user_id = $userId AND f.owner_feedback_rate != ''")
               ->orWhere("f.bidder_user_id = $userId AND f.bidder_feedback_rate != ''");
        $row = $this->getTable()->fetchRow($select);
        
        return $row['total_rows'];
    }
    
    public function getFeedbacksForMe($userId, $searchType = 'latest', $startLimit = 0, $limit = 10)
    {
        if($searchType == 'latest'){    
          $searchType = "f.feedback_id DESC";
        } else if ($searchType == 'rating') {
           $searchType = "f.owner_feedback_rate DESC";
        } else if ($searchType == 'alphabetical') {
           $searchType = "p.project_title ASC";
        } else {    
          $searchType = "f.feedback_id DESC";
        }
       
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
                
               ->from(array('f' => 'job_feedback'), array('f.owner_user_id',
                                                          'f.owner_feedback_rate',
                                                          'f.owner_comment',
                                                          'f.owner_post_date',
                                                          'f.bidder_user_id',
                                                          'f.bidder_feedback_rate',
                                                          'f.bidder_comment',     
                                                          'f.bidder_post_date'))
               ->join(array('p'=>'job_project'), 
                          'f.project_id = p.project_id', 
                          array('p.project_title', 'p.project_id', 
                                'p.assigned_user_id', 'p.cost'))
               ->where("f.owner_user_id = $userId AND f.bidder_feedback_rate != ''")
               ->orWhere("f.bidder_user_id = $userId AND f.owner_feedback_rate != ''")
               ->order($searchType)
               ->limit($limit, $startLimit);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $feedback = new Application_Model_Feedback();
            $feedback->setProjectId($row->project_id);
            $feedback->setOwnerUserId($row->owner_user_id);
            $feedback->setOwnerFeedbackRate($row->owner_feedback_rate);
            $feedback->setOwnerComment($row->owner_comment);
            $feedback->setOwnerPostDate($row->owner_post_date);
            $feedback->setBidderUserId($row->bidder_user_id);
            $feedback->setBidderFeedbackRate($row->bidder_feedback_rate);
            $feedback->setBidderComment($row->bidder_comment);
            $feedback->setBidderPostDate($row->bidder_post_date);
            $project = new Application_Model_Project();
            $project->setProjectTitle($row->project_title);
            $project->setProjectId($row->project_id);
            $project->setAssignedUserId($row->assigned_user_id);
            $project->setCost($row->cost);
            $feedback->setProject($project);
            $info[] = $feedback;
        }
        
        return $info;
    }
    
    public function getFeedbacksForMeCount($userId)
    {
        $select = $this->getTable()->select();
        $select->setIntegrityCheck(false)
               ->from(array('f' => 'job_feedback'), array('total_rows' => 'f.owner_user_id'))
               ->where("f.owner_user_id = $userId AND f.bidder_feedback_rate != ''")
               ->orWhere("f.bidder_user_id = $userId AND f.owner_feedback_rate != ''");
        $row = $this->getTable()->fetchRow($select);
        
        return $row['total_rows'];
    }
    
    public function updateOwnerFeedback(Application_Model_Feedback $feedback)
    {
        $select = $this->getTable()->select();
        $select->where('project_id = ?', $feedback->getProjectId());
        
        $row = $this->getTable()->fetchRow($select);
        
        if (!$row) return false;
        
        $row->owner_user_id = $feedback->getOwnerUserId();
        $row->owner_feedback_rate = $feedback->getOwnerFeedbackRate();
        $row->owner_comment = $feedback->getOwnerComment();
        $row->owner_post_date = $feedback->getOwnerPostDate();
        $row->status = $feedback->getStatus();
        
        return $row->save();
    }
    
    public function updateBidderFeedback(Application_Model_Feedback $feedback)
    {
        $select = $this->getTable()->select();
        $select->where('project_id = ?', $feedback->getProjectId());
        
        $row = $this->getTable()->fetchRow($select);
        
        if (!$row) return false;
        
        $row->bidder_user_id = $feedback->getBidderUserId();
        $row->bidder_feedback_rate = $feedback->getBidderFeedbackRate();
        $row->bidder_comment = $feedback->getBidderComment();
        $row->bidder_post_date = $feedback->getBidderPostDate();
        $row->status = $feedback->getStatus();
        
        return $row->save();
    }
    
    public function saveOwnerFeedback(Application_Model_Feedback $feedback)
    {
        $data = array(
            'project_id' => $feedback->getProjectId(),
            'owner_user_id' => $feedback->getOwnerUserId(),
            'BIDDER_user_id' => $feedback->getBidderUserId(),
            'owner_feedback_rate' => $feedback->getOwnerFeedbackRate(),
            'owner_comment' => $feedback->getOwnerComment()
        );
        return $this->getTable()->insert($data);
    }
    
    public function saveBidderFeedback(Application_Model_Feedback $feedback)
    {
        $data = array(
            'project_id' => $feedback->getProjectId(),
            'owner_user_id' => $feedback->getOwnerUserId(),
            'BIDDER_user_id' => $feedback->getBidderUserId(),
            'bidder_feedback_rate' => $feedback->getBidderFeedbackRate(),
            'bidder_comment' => $feedback->getBidderComment()
        );
        return $this->getTable()->insert($data);
    }
    


}