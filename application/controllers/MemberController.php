<?php
/**
 * Member management actions
 * 
 * @category   Application
 * @package    Application_Controller
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @uses       Zend_Controller_Action
 * @version    1.0
 */
class MemberController extends Zend_Controller_Action
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
     * Member default page action
     *
     * @return void
     */    
    public function indexAction()
    {
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $premiumLimit = 2;
        $defaultPerPage = 6;
        $startLimit = $defaultPerPage*($pageNo-1);
        
        $userMapper = new Application_Model_UserMapper();
        $this->view->membersPremium = $userMapper->getMembersPremium($premiumLimit);
        $totalRows = $userMapper->getMembersDefaultCount();
        $this->view->membersDefault = $userMapper->getMembersDefault($searchType, $startLimit, $defaultPerPage);

        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($defaultPerPage);
        $this->view->pagination = $paginator;
        
        
        $primaryCategory = new Application_Model_PrimaryCategoryMapper();
        $this->view->primaryCategories = $primaryCategory->getPrimaryCategories();
    }
    
    /**
     * Project categories
     *
     * @return void
     */    
    public function categoryAction()
    {
        $categoryId = $this->getRequest()->getParam('categoryId');
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        $this->view->categoryId = $categoryId;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 9;
        $startLimit = $perPage*($pageNo-1);
        
        $userMapper = new Application_Model_UserMapper();
        $this->view->membersCategoryAll = $userMapper->getMembersCategoryAll($categoryId, $searchType, $startLimit, $perPage);
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null(20));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
        
        $primaryCategory = new Application_Model_PrimaryCategoryMapper();
        $this->view->primaryCategories = $primaryCategory->getPrimaryCategories();
    }
    
    /**
     * Featured members
     *
     * @return void
     */    
    public function featuredMembersAction()
    {
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 9;
        $startLimit = $perPage*($pageNo-1);

        $userMapper = new Application_Model_UserMapper();
        $totalRows = $userMapper->getMembersFeaturedCount($searchType);
        $this->view->featuredMembers = $userMapper->getMembersFeatured($searchType, 
                                                                       $startLimit, 
                                                                       $perPage);
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Current hired members of loggedin user
     *
     * @return void
     */    
    public function currentHiredMembersAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 1;
        $startLimit = $perPage*($pageNo-1);

        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $totalRows = $projectBidMapper->getCurrentHiredMembersCount($sessionUserId);
        $this->view->currentHiredMembers = $projectBidMapper->getCurrentHiredMembers($sessionUserId, $searchType, 
                                                                                     $startLimit, $perPage);
        
        // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Bookmarks of loggedin user
     *
     * @return void
     */    
    public function bookmarksAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $searchType = $this->getRequest()->getParam('searchType');
        $this->view->searchType = $searchType;
        
        $pageNo = $this->_getParam('page');
        if (empty($pageNo)) $pageNo = 1;
        $perPage = 9;
        $startLimit = $perPage*($pageNo-1);

        $userMapper = new Application_Model_UserMapper();
        $bookmarkMapper = new Application_Model_BookmarkMapper();
        $totalRows = $bookmarkMapper->getBookmarkedMembersCount($sessionUserId);
        $this->view->bookmarkedMembers = $userMapper->getBookmarkedMembers($sessionUserId, 
                                                                           $searchType, 
                                                                           $startLimit, 
                                                                           $perPage);
        
         // pagination
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
        $paginator->setCurrentPageNumber($pageNo);
        $paginator->setItemCountPerPage($perPage);
        $this->view->pagination = $paginator;
    }
    
    /**
     * Member search
     *
     * @return void
     */    
    public function memberSearchAction()
    {
        $username = $this->getRequest()->getPost('member_search');

        $userMapper = new Application_Model_UserMapper();
        $this->view->searchedMembers = $userMapper->getSearchedMembers($username);
    }
    
    /**
     * Creative member search
     *
     * @return void
     */    
    public function creativeSearchAction()
    {
        $username = $this->getRequest()->getPost('creative_search');

        $userMapper = new Application_Model_UserMapper();
        $this->view->searchedCreatives = $userMapper->getSearchedCreatives($username);
    }
    
}