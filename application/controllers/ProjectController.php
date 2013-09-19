<?php
/**
 * Project management actions
 * 
 * @category   Application
 * @package    Application_Controller
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @uses       Zend_Controller_Action
 * @version    1.0
 */
class ProjectController extends Zend_Controller_Action
{
    /**
     * @var Zend_Controller_Action_Helper_Redirector Action helper Redirector
     */
    protected $_redirector = null;
    
    /**
     * @var Zend_Session_Namespace Session namespace 'login'
     */
    private $_loginNamespace;
    
    /**
     * @var Zend_Session_Namespace Session namespace 'message'
     */
    private $_messageNamespace;
    
    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */    
    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        
        $this->_loginNamespace = new Zend_Session_Namespace('login');
        $this->_messageNamespace = new Zend_Session_Namespace('message');
    }

    /**
     * Account default page action
     *
     * @return void
     */    
    public function indexAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        $searchType = $this->getRequest()->getParam('searchType');
        $searchType = $searchType ? $searchType : 'latest';
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
                
        $projectMapper = new Application_Model_ProjectMapper();
        $userMapper = new Application_Model_UserMapper();
        $primaryCategoryMapper = new Application_Model_PrimaryCategoryMapper();
        
        $projectListPremium = $projectMapper->getProjectsPremium();
        $totalRows = $projectMapper->getProjectsDefaultCount($searchType);
        $projectListDefault = $projectMapper->getProjectsDefault($searchType, $startLimit, $perPage);
        
        // fetching assigned users list by projectIds
        $projectIds = array();
        foreach($projectListPremium as $project) {
            $projectIds[] = $project->getProjectId();
        }
        foreach($projectListDefault as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $projectBidMapper = new Application_Model_ProjectBidMapper();
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }

        $this->view->projectListPremium = $projectListPremium;
        $this->view->projectListDefault = $projectListDefault;

        if ($sessionUserId) {
            $userPrimaryCategoryId = $userMapper->getPrimaryCategoryByUser($sessionUserId);
        } else $userPrimaryCategoryId = 1;
        
        $this->view->categoryName = $primaryCategoryMapper->getPrimaryCategoriyTitle($userPrimaryCategoryId);
        $this->view->projectsByCategory = $projectMapper->getProjectsByCategory($userPrimaryCategoryId);
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }

    /**
     * Active projects
     *
     * @return void
     */    
    public function activeProjectsAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $searchType = $searchType ? $searchType : 'latest';
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $totalRows = $projectMapper->getActiveProjectsCount($sessionUserId);
        $activeProjects = $projectMapper->getActiveProjects($sessionUserId, $searchType, $startLimit, $perPage);
        $this->view->activeProjects = $activeProjects;
        
        // fetching assigned users list by projectIds
        $projectIds = array();
        foreach($activeProjects as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $projectBidMapper = new Application_Model_ProjectBidMapper();
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }

    /**
     * Projects bidded by loggedon user
     *
     * @return void
     */    
    public function biddedProjectsAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $searchType = $searchType ? $searchType : 'latest';
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 3;
        $startLimit = $perPage*($pageNo-1);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $totalRows = $projectMapper->getBiddedProjectsCount($sessionUserId);
        $biddedProjects = $projectMapper->getBiddedProjects($sessionUserId, $searchType, $startLimit, $perPage);
        $this->view->biddedProjects = $biddedProjects;
        
        // fetching assigned users list by projectIds
        $projectIds = array();
        foreach($biddedProjects as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $projectBidMapper = new Application_Model_ProjectBidMapper();
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Archived projects
     *
     * @return void
     */    
    public function archiveProjectsAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $searchType = $searchType ? $searchType : 'latest';
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $totalRows = $projectMapper->getArchiveProjectsCount($sessionUserId);
        $archiveProjects = $projectMapper->getArchiveProjects($sessionUserId, $searchType, $startLimit, $perPage);
        $this->view->archiveProjects = $archiveProjects;
        
        // fetching assigned users list by projectIds
        $projectIds = array();
        foreach($archiveProjects as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $projectBidMapper = new Application_Model_ProjectBidMapper();
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Archived projects bidded by loggedon user
     *
     * @return void
     */    
    public function archiveBiddedProjectsAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $totalRows = $projectMapper->getArchiveBiddedProjectsCount($sessionUserId);
        $archiveBiddedProjects = $projectMapper->getArchiveBiddedProjects($sessionUserId, $searchType, $startLimit, $perPage);
        $this->view->archiveBiddedProjects = $archiveBiddedProjects;
        
        // fetching assigned users list by projectIds
        $projectIds = array();
        foreach($archiveBiddedProjects as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $projectBidMapper = new Application_Model_ProjectBidMapper();
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Project details
     *
     * @return void
     */    
    public function projectDetailsAction()
    {
        $projectId = $this->getRequest()->getParam('projectId');
        
        if (empty($projectId)) {
            $this->_redirector->gotoSimple('index', 'project');
        }
        
        $this->view->message = $this->_messageNamespace->message;
        unset($this->_messageNamespace->message);
        
        $sessionUserId = $this->_loginNamespace->session_user_id;

        // Fetching project details as Project object
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectDetails = $projectMapper->getProjectDetails($projectId);

        // Detecting if logged in user is the owner of the project
        $this->view->isProjectOwner = $this->view->projectDetails->getUserId() == $sessionUserId ? true : false;

        // Fetching project attachments as ProjectAttachment object
        $projectAttachmentMapper = new Application_Model_ProjectAttachmentMapper();
        $this->view->projectAttachments = $projectAttachmentMapper->getProjectAttachments($projectId);
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        
        // Fetcing assigned bid ProjectBid objects
        $this->view->assignedBid = $assignedBid = $projectBidMapper->getAssignedBid($projectId);
        
        // Fetcing all bids of the project as ProjectBid objects
        $this->view->projectBids = $projectBidMapper->getProjectBids($projectId);

        if ($sessionUserId) {
            // User bid count for last one month
            $this->view->bidNumberCount = $projectBidMapper->getBidNumberCount($sessionUserId);
        }
        $assignedBidderUserId = $assignedBid->getBidderUserId();
        // Detecting if the project is assignd to any bidder
        $this->view->isProjectAssigned = !empty($assignedBidderUserId) ? true : false;
        
        // Detecting if the project accepted or declined
        $this->view->afterProjectAccept = $this->view->assignedBid->getAcceptDecline() ? true : false; // need query

        // Detecting if the logged in user is a bidder of hte project
        $this->view->isCurrentUserBidder = false;
        if ($sessionUserId) {
            foreach($this->view->projectBids as $bid) {
                 if ($bid->getBidderUserId() == $sessionUserId) {
                     $this->view->isCurrentUserBidder = true;
                     break;
                 }
            }
            
            // Checking if the logged in user is a paid member
            $paymentRecordMapper = new Application_Model_PaymentRecordMapper();
            $this->view->paymentCheck = $paymentRecordMapper->checkPayment($sessionUserId);
        }
        
        // Per onth bid quota
        $this->view->bidNumberPerMonth = Zend_Registry::get('bidNumberPerMonth');
        
    }

    /**
     * Assign project
     * 
     * Project owner (loggedin user) assigns project to chosen bidder
     *
     * @return void
     */    
    public function assignProjectAction()
    {
        $projectOwnerUserId = $this->_loginNamespace->session_user_id;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $bidderUserId = $this->getRequest()->getUserParam('bidderUserId');
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $projectMapper = new Application_Model_ProjectMapper();
        $messageMapper = new Application_Model_MessageMapper();
        
        $message = new Application_Model_Message();
        $message->setProjectId($projectId);
        $message->setSenderUserId($projectOwnerUserId);
        $message->setReceiverUserId($bidderUserId);
        $message->setMessage('A project has been assigned to you. Please '
                           . '<a href="' . $this->view->baseUrl('project/project-details')
                           . '/' . $projectId.'/">click here</a> to see details.');
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $bootstrap->bootstrap('db');
        $db = $bootstrap->getResource('db');
        $db->beginTransaction();
        
        try {
            $projectBidMapper->updateBidAssigned($projectId, $bidderUserId);
            $projectMapper->updateProjectFrozen($projectId);
            $messageMapper->saveMessage($message);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
        
        // Need to send email       
        $projectUserBid = $projectBidMapper->getProjectUserBid($projectId, $bidderUserId);
        
        $this->_redirector->gotoRoute(array('projectId' => $projectId), 'projectDetails');
    }
    
    /**
     * Accept won project
     *
     * Bidder (loggedin user) accepts won project
     * 
     * @return void
     */    
    public function acceptProjectAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $sessionUserName = $this->_loginNamespace->user_username;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $bidderUserId = $this->getRequest()->getUserParam('bidderUserId');
        
        if ( $sessionUserId != $bidderUserId ) die('Hacking attempt');
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $projectMapper = new Application_Model_ProjectMapper();
        $messageMapper = new Application_Model_MessageMapper();
        $creditBalanceMapper = new Application_Model_CreditBalanceMapper();
        
        $project = $projectMapper->getProject($projectId);
        
        $projectUserBidAmount = $projectBidMapper->getProjectUserBidAmount($projectId, $bidderUserId);
        
        $creditBalanceOwner = new Application_Model_CreditBalance();
        $creditBalanceOwner->setUserId($bidderUserId);
        $creditBalanceOwner->setTransactionForUserId($project->getProjectOwner()->getUserId());
        $creditBalanceOwner->setCreatedOn(date('Y-m-d H:i:s', time()));
        $creditBalanceOwner->setType('earned');
        $creditBalanceOwner->setBalance($projectUserBidAmount->getBidAmount());
        $creditBalanceOwner->setStatus(1);
        
        $creditBalanceBidder = new Application_Model_CreditBalance();
        $creditBalanceBidder->setUserId($project->getProjectOwner()->getUserId());
        $creditBalanceBidder->setTransactionForUserId($bidderUserId);
        $creditBalanceBidder->setCreatedOn(date('Y-m-d H:i:s', time()));
        $creditBalanceBidder->setType('spend');
        $creditBalanceBidder->setBalance($projectUserBidAmount->getBidAmount());
        $creditBalanceBidder->setStatus(1);
        
        $message = new Application_Model_Message();
        $message->setProjectId($projectId);
        $message->setSenderUserId($bidderUserId);
        $message->setReceiverUserId($project->getProjectOwner()->getUserId());
        $message->setMessage('Your assign project has accepted by ' 
                           . $sessionUserName . '.To show details please ' 
                           . '<a href="' . $this->view->baseUrl('project/project-details') . '/' 
                           . $projectId . '/">click here</a>');
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $bootstrap->bootstrap('db');
        $db = $bootstrap->getResource('db');
        $db->beginTransaction();
        
        try {
            $projectBidMapper->setBidAcceptDecline($projectId, $bidderUserId);
            $projectMapper->updateProjectClose($projectId, $bidderUserId);
            $creditBalanceMapper->saveCreditBalance($creditBalanceOwner);
            $creditBalanceMapper->saveCreditBalance($creditBalanceBidder);
            $messageMapper->saveMessage($message);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
        
        $this->_redirector->gotoRoute(array('projectId' => $projectId), 'projectDetails');
    }
    
    /**
     * Decline won project
     *
     * Bidder (loggedin user) declines won project
     *
     * @return void
     */    
    public function declineProjectAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $sessionUserName = $this->_loginNamespace->user_username;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $bidderUserId = $this->getRequest()->getUserParam('bidderUserId');
        
        if ( $sessionUserId != $bidderUserId ) die('Hacking attempt');
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $projectMapper = new Application_Model_ProjectMapper();
        $messageMapper = new Application_Model_MessageMapper();
        $creditBalanceMapper = new Application_Model_CreditBalanceMapper();
        
        $project = $projectMapper->getProject($projectId);
        
        $projectUserBidAmount = $projectBidMapper->getProjectUserBidAmount($projectId, $bidderUserId);
        
        $message = new Application_Model_Message();
        $message->setProjectId($projectId);
        $message->setSenderUserId($bidderUserId);
        $message->setReceiverUserId($project->getProjectOwner()->getUserId());
        $message->setMessage('A project has been decline by'
                           . $sessionUserName.'. To show details please ' 
                           . '<a href="' . $this->view->baseUrl('project/project-details') . '/' 
                           . $projectId.'/">click here</a>');
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $bootstrap->bootstrap('db');
        $db = $bootstrap->getResource('db');
        $db->beginTransaction();
        
        try {
            $projectBidMapper->unsetBidAcceptDecline($projectId, $bidderUserId);
            $projectMapper->updateProjectOpen($projectId);
            $messageMapper->saveMessage($message);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
        
        $this->_redirector->gotoRoute(array('projectId' => $projectId), 'projectDetails');
    }
    
    /**
     * Cancel project
     *
     * Project owner (loggedin user) cancels own project
     *
     * @return void
     */    
    public function cancelProjectAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        
        $projectMapper = new Application_Model_ProjectMapper();
        $projectMapper->updateProjectCancel($projectId);
        
        $this->_redirector->gotoSimple('index', 'project');
    }

    /**
     * Delete bid
     *
     * Bidder (loggedin user) deletes bid on a project
     *
     * @return void
     */    
    public function deleteBidAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $projectBidMapper->setBidDeleted($projectId, $sessionUserId);
        
        $this->_redirector->gotoRoute(array('projectId' => $projectId), 'projectDetails');
    }

    /**
     * Account default page action
     *
     * @return void
     * @todo: work when projectBidAction is done
     */    
    public function projectBidPayment()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $paymentCheck = $this->getRequest()->getUserParam('paymentCheck');
        
        if($paymentCheck == 'add') {   
            redirect(BASEURL.'project/project-bid/'.$project_id);
            $this->_redirector->gotoSimple('project-bid', 
                                              'project', 
                                              null, 
                                              array('projectId' => $projectId));
        } else {

        }
        
    }

    /**
     * Feedback/rating page for project owner
     *
     * @return void
     */    
    public function ownerRatingAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $this->view->sessionUsername = $this->_loginNamespace->user_username;
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $this->view->projectId = $projectId;
        $this->view->bidderUserId = $this->getRequest()->getUserParam('bidderUserId');
        
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectAssignedUser = $projectMapper->getProjectAssignedUser($projectId);
    }

    /**
     * Feedback/rating page for bidder
     *
     * @return void
     */    
    public function bidderRatingAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $this->view->sessionUsername = $this->_loginNamespace->user_username;
        
        $projectId = $this->getRequest()->getUserParam('projectId');
        $this->view->projectId = $projectId;
        $this->view->ownerUserId = $this->getRequest()->getUserParam('ownerUserId');
        
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectAssignedUser = $projectMapper->getProject($projectId);
    }

    /**
     * Save feedback/rating by project owner
     *
     * @return void
     */    
    public function saveOwnerRatingAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getPost('project_id');
        $bidderUserId = $this->getRequest()->getPost('BIDDER_user_id');
        $rating = $this->getRequest()->getPost('rating');
        $comment = $this->getRequest()->getPost('comment');
        $postDate = date('Y-m-d H:i:s',time());
        
        $feedback = new Application_Model_Feedback();
        $feedback->setProjectId($projectId);
        $feedback->setOwnerUserId($sessionUserId);
        $feedback->setBidderUserId($bidderUserId);
        $feedback->setOwnerFeedbackRate($rating);
        $feedback->setOwnerComment($comment);
        $feedback->setOwnerPostDate($postDate);
        $feedback->setStatus(2);
        
        $feedbackMapper = new Application_Model_FeedbackMapper();
        $success = $feedbackMapper->updateOwnerFeedback($feedback);
        if ($success) {
            $projectMapper = new Application_Model_ProjectMapper();
            $projectMapper->updateProjectArchive($projectId);
        } else {
            $feedbackMapper->saveOwnerFeedback($feedback);
        }
        
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $projectUserBid = $projectBidMapper->getProjectUserBid($projectId, $bidderUserId);
        
        $subject = "3lance / " . $sessionUserId . " has written a testimonial on the project " . $projectUserBid->getProject()->getProjectTitle() . "";
        
        // TODO: email work
        
        $message = new Application_Model_Message();
        $message->setProjectId($projectId);
        $message->setSenderUserId($sessionUserId);
        $message->setReceiverUserId($bidderUserId);
        $message->setSubject($subject);
        $message->setMessage("3lance / " . $sessionUserId . " has written a testimonial on the project " . $projectUserBid->getProject()->getProjectTitle() . "");
        
        $messageMapper = new Application_Model_MessageMapper();
        $messageMapper->saveMessage($message);
        
        $this->_redirector->gotoRoute(array('searchType' => 'latest'), 'activeProjects');
    }

    /**
     * Save feedback/rating by bidder
     *
     * @return void
     */    
    public function saveBidderRatingAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        $projectId = $this->getRequest()->getPost('project_id');
        $ownerUserId = $this->getRequest()->getPost('owner_user_id');
        $rating = $this->getRequest()->getPost('rating');
        $comment = $this->getRequest()->getPost('comment');
        $postDate = date('Y-m-d H:i:s',time());
        
        $feedback = new Application_Model_Feedback();
        $feedback->setProjectId($projectId);
        $feedback->setOwnerUserId($ownerUserId);
        $feedback->setBidderUserId($sessionUserId);
        $feedback->setBidderFeedbackRate($rating);
        $feedback->setBidderComment($comment);
        $feedback->setBidderPostDate($postDate);
        $feedback->setStatus(2);
        
        $feedbackMapper = new Application_Model_FeedbackMapper();
        $success = $feedbackMapper->updateBidderFeedback($feedback);
        if ($success) {
            $projectMapper = new Application_Model_ProjectMapper();
            $projectMapper->updateProjectArchive($projectId);
        } else {
            $feedbackMapper->saveBidderFeedback($feedback);
        }
        
        $projectMapper = new Application_Model_ProjectMapper();
        $projectUser = $projectMapper->getProject($projectId);
        
        $subject = "3lance / " . $sessionUserId . " has written a testimonial on the project " . $projectUser->getProjectTitle() . "";
        
        // TODO: email work
        
        $message = new Application_Model_Message();
        $message->setProjectId($projectId);
        $message->setSenderUserId($sessionUserId);
        $message->setReceiverUserId($ownerUserId);
        $message->setSubject($subject);
        $message->setMessage("3lance / " . $sessionUserId . " has written a testimonial on the project " . $projectUser->getProjectTitle() . "");
        
        $messageMapper = new Application_Model_MessageMapper();
        $messageMapper->saveMessage($message);
        
        $this->_redirector->gotoRoute(array('searchType' => 'latest'), 'biddedProjects');
    }

    /**
     * Feedbacks given by loggedin user as a project owenr or bidder
     *
     * @return void
     */    
    public function feedbacksByMeAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUserId = $sessionUserId;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
        
        $userMapper = new Application_Model_UserMapper();
        
        $feedbackMapper = new Application_Model_FeedbackMapper();
        $totalRows = $feedbackMapper->getFeedbacksByMeCount($sessionUserId);
        $this->view->givenFeedbacks = $feedbackMapper->getFeedbacksByMe($sessionUserId, $searchType, $startLimit, $perPage);
        
        $userFeedbacks = array();
        foreach($this->view->givenFeedbacks as $testimonial) {
            if($testimonial->getOwnerUserId() == $sessionUserId) { 
                $userIdFeedback = $testimonial->getBidderUserId();
                $userFeedbacks[$userIdFeedback] = $userMapper->getUsername($userIdFeedback);
            } else if($testimonial->getBidderUserId() == $sessionUserId) {  
                $userIdFeedback = $testimonial->getOwnerUserId();
                $userFeedbacks[$userIdFeedback] = $userMapper->getUsername($userIdFeedback);
            }
        }
        $this->view->userFeedbacks = $userFeedbacks;
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Feedbacks given to loggedin user as a project owenr or bidder
     *
     * @return void
     */    
    public function feedbacksForMeAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUserId = $sessionUserId;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 8;
        $startLimit = $perPage*($pageNo-1);
        
        $userMapper = new Application_Model_UserMapper();
        
        $feedbackMapper = new Application_Model_FeedbackMapper();
        $totalRows = $feedbackMapper->getFeedbacksForMeCount($sessionUserId);
        $this->view->givenFeedbacks = $feedbackMapper->getFeedbacksForMe($sessionUserId, $searchType, $startLimit, $perPage);
        
        $userFeedbacks = array();
        foreach($this->view->givenFeedbacks as $testimonial) {
            if($testimonial->getOwnerUserId() == $sessionUserId) { 
                $userIdFeedback = $testimonial->getBidderUserId();
                $userFeedbacks[$userIdFeedback] = $userMapper->getUsername($userIdFeedback);
            } else if($testimonial->getBidderUserId() == $sessionUserId) {  
                $userIdFeedback = $testimonial->getOwnerUserId();
                $userFeedbacks[$userIdFeedback] = $userMapper->getUsername($userIdFeedback);
            }
        }
        $this->view->userFeedbacks = $userFeedbacks;
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }

    /**
     * Create My_Form_JobPost form
     *
     * @param  array $params Array contains primaryCategories
     * @return My_Form_JobPost
     */    
    public function getJobPostForm(array $params = array())
    {
        $form = new My_Form_JobPost($params);
        return $form;
    }

    /**
     * Project post form page
     * 
     * Form to post project; after submiting form forwards to preview page
     *
     * @see projectSubmitPreviewAction
     * @return void
     */    
    public function projectSubmitAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $primaryCategoryMapper = new Application_Model_PrimaryCategoryMapper();
        $primaryCategories = $primaryCategoryMapper->getPrimaryCategories();
        
        $params['primaryCategories'] = $primaryCategories;
        
        $form = $this->getJobPostForm($params);
        
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $errors = $form->getMessages();
                $this->view->error = implode(' ', $errors);
            } else $this->_forward('project-submit-preview');
        }        
        
        $this->view->form = $form;
    }

    /**
     * Create My_Form_JobConfirm form
     *
     * @return My_Form_JobConfirm
     */   
    public function getJobConfirmForm()
    {
        $form = new My_Form_JobConfirm();
        return $form;
    }
    
    /**
     * Submitted project preview
     *
     * @see projectSubmitAction
     * @return void
     */    
    public function projectSubmitPreviewAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        if (!$this->getRequest()->isPost()) {
            $this->_redirector->gotoSimple('project-submit', 'project');
        }
        
        $additionalInfo = $this->getRequest()->getPost('additional_info');
        $meetUpRequired = in_array('Meet up required', $additionalInfo) ? 1 : 0;
        $milestonePayments = in_array('Milestone payments', $additionalInfo) ? 1 : 0;
        $requirePortfolio = in_array('Require portfolio', $additionalInfo) ? 1 : 0;
        $bidEndingDate = date('Y-m-d, H:i:s',strtotime($this->getRequest()->getPost('bid_ending_date')));

        $project = new Application_Model_Project();
        $project->setUserId($sessionUserId);
        $project->setCurrencyCode($this->getRequest()->getPost('CurrencyCode')); 
        $project->setProjectTitle($this->getRequest()->getPost('project_title'));  
        $project->setProjectDescription($this->getRequest()->getPost('project_description'));
        $project->setProjectCategoryId($this->getRequest()->getPost('primary_category_id'));
        $project->setCost($this->getRequest()->getPost('cost')); 
        $project->setBidEndingDate($bidEndingDate);  
        $project->setAdditionalRemarks($this->getRequest()->getPost('additional_remarks')); 
        $project->setMeetUpRequired($meetUpRequired);
        $project->setMilestonePayments($milestonePayments);
        $project->setRequirePortfolio($requirePortfolio);
        $project->setStatus(1); 
        $project->setCreatedOn(date('Y-m-d, H:i:s',time())); 
        
        $temporaryProjectMapper = new Application_Model_TemporaryProjectMapper();
        $temporaryProjectId = $temporaryProjectMapper->saveProject($project);
        
        $projectAttachment = new Application_Model_ProjectAttachment();
        $projectAttachment->setProjectId($temporaryProjectId);
        $projectAttachment->setAttachment($this->getRequest()->getPost('attach1'));
        
        $temporaryProjectAttachmentMapper = new Application_Model_TemporaryProjectAttachmentMapper();
        $temporaryProjectAttachmentMapper->saveProjectAttachment($projectAttachment);
        
        $this->view->project = $project;
        $this->view->projectAttachment = $projectAttachment;
        
        $form = $this->getJobConfirmForm();
        $this->view->form = $form;
        $data = array();
        $data['project_id'] = $temporaryProjectId;
        $form->populate($data);
        
        $primaryCategoryMapper = new Application_Model_PrimaryCategoryMapper();
        $this->view->primaryCategoriyTitle = $primaryCategoryMapper->getPrimaryCategoriyTitle($project->getProjectCategoryId());
        
    }
    
    /**
     * Save submitted project
     *
     * @return void
     */    
    public function projectSubmitConfirmAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        if (!$this->getRequest()->isPost()) {
            $this->_redirector->gotoSimple('project-submit', 'project');
        }
        
        $temporaryProjectId = $this->getRequest()->getPost('project_id');

        $temporaryProjectMapper = new Application_Model_TemporaryProjectMapper();
        $project = $temporaryProjectMapper->getProject($temporaryProjectId);
        $projectMapper = new Application_Model_ProjectMapper();
        $projectId = $projectMapper->saveProject($project);
        
        $temporaryProjectAttachmentMapper = new Application_Model_TemporaryProjectAttachmentMapper();
        $projectAttachments = $temporaryProjectAttachmentMapper->getProjectAttachments($temporaryProjectId);
        $projectAttachmentMapper = new Application_Model_ProjectAttachmentMapper();
        foreach($projectAttachments AS $projectAttachment) {
            $projectAttachment->setProjectId($projectId);
            $projectAttachmentMapper->saveProjectAttachment($projectAttachment);
        }
        
        $this->_forward('invite-member', null, null, array('projectId' => $projectId));
        
    }
    
    /**
     * Create My_Form_ProjectBid form
     *
     * @param  array $params Array contains primaryCategories
     * @return My_Form_ProjectBid
     */    
    public function getProjectBidForm(array $params = array())
    {
        $form = new My_Form_ProjectBid($params);
        return $form;
    }
    
    /**
     * Project bid post form page
     * 
     * Form to post bid
     *
     * @return void
     */    
    public function projectBidAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $projectId = $this->getRequest()->getParam('projectId');
        if (empty($projectId)) {
            $this->_redirector->gotoSimple('index', 'project');
        }
        
        //$project = new Zend_Session_Namespace('project');
        //$project->bidProjectId = $project;
        
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectInfo = $projectMapper->getProject($projectId);
        
        $params['projectId'] = $projectId;
        
        $form = $this->getProjectBidForm($params);
        
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            } else {
                $projectId = $this->getRequest()->getPost('projectId');
                
                $projectBid = new Application_Model_ProjectBid();
                $projectBid->setProjectId($projectId);
                $projectBid->setBidderUserId($sessionUserId); 
                $projectBid->setBidAmount($this->getRequest()->getPost('bid_amount'));  
                $projectBid->setTimePeriod($this->getRequest()->getPost('time_period'));
                $projectBid->setStatus(1);
                $projectBid->setCreatedOn(date('Y-m-d, H:i:s',time())); 

                $projectBidMapper = new Application_Model_ProjectBidMapper();
                $projectBidId = $projectBidMapper->saveProjectBid($projectBid);
                
                $projectBidAttach = new Application_Model_ProjectBidAttach();
                $projectBidAttach->setProjectId($projectId);
                $projectBidAttach->setBidderUserId($sessionUserId); 
                $projectBidAttach->setAttachment($this->getRequest()->getPost('attach1'));
                $projectBidAttach->setStatus(1);
                $projectBidAttach->setCreatedOn(date('Y-m-d, H:i:s',time())); 
                
                $projectBidAttachMapper = new Application_Model_ProjectBidAttachMapper();
                $projectBidAttachMapper->saveProjectBidAttach($projectBidAttach);
                
                $this->_messageNamespace->message = 'Your bid has been submitted successfully.';
                $this->_redirector->gotoRoute(array('projectId' => $projectId), 'projectDetails');
            }
        }        
        
        $this->view->form = $form;
    }
    
    /**
     * Invite members to bid on created project
     *
     * @return void
     */    
    public function inviteMemberAction()
    {
        try {
        $this->view->projectId = $projectId = $this->getRequest()->getParam('projectId');
        $this->view->message = $this->_messageNamespace->message;
        unset($this->_messageNamespace->message);
        
        $invitedMapper = new Application_Model_InvitedMapper();
        $this->view->invitedMemberCount = $invitedMapper->getInvitedMemberCount($projectId)+1;
        
        $userId = $this->getRequest()->getParam('userId');  

        if (!empty($projectId) && !empty($userId) 
            && $this->view->invitedMemberCount <= Zend_Registry::get('invitedMemberLimit')) {
                $invited = new Application_Model_Invited();
                $invited->setProjectId($projectId);
                $invited->setUserId($userId);
                $invited->setStatus(1);
                $result = $invitedMapper->invitedSave($invited); 
                $this->_messageNamespace->message = 'You have successfully invited this person!';
                $this->_redirector->gotoRoute(array('projectId' => $projectId), 'inviteMember');
        }
        
        $userMapper = new Application_Model_UserMapper();
        $this->view->suggestedInviteMembers = $userMapper->getMembersToInvite($this->view->projectId);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
}