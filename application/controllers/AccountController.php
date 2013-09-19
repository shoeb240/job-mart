<?php
/**
 * All account management actions
 * 
 * @category   Application
 * @package    Application_Controller
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @uses       Zend_Controller_Action
 * @version    1.0
 */
class AccountController extends Zend_Controller_Action
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
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('bookmark-user', 'html')
                    ->addActionContext('cancel-bookmark', 'html')
                    ->initContext();
    }
    
    /**
     * Account default page action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        echo $this->_messageNamespace->message;
        unset($this->_messageNamespace->message);
    }
    
    /**
     * Profile page action
     *
     * @return void
     */
    public function profileAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if ($this->_messageNamespace->message) {
            $this->view->message = $this->_messageNamespace->message;
            unset($this->_messageNamespace->message);
        }
        if ($this->_messageNamespace->error) {
            $this->view->error = $this->_messageNamespace->error;
            unset($this->_messageNamespace->error);
        }

        $username = $this->getRequest()->getParam('username');
        
        if (empty($username)) {
            $username = $this->_loginNamespace->user_username;
        }
        
        if (empty($username)) {
            $this->_redirector->gotoSimple('index', 'index');
        }
        
        $userMapper = new Application_Model_UserMapper();
        $projectMapper = new Application_Model_ProjectMapper();
        $projectBidMapper = new Application_Model_ProjectBidMapper();
        $feedbackMapper = new Application_Model_FeedbackMapper();
        $portfolioMapper = new Application_Model_PortfolioMapper();
        $bookmarkMapper = new Application_Model_BookmarkMapper();

        $userId = $userMapper->getUserId($username);
        $this->view->userId = $userId;
        $this->view->user = $userMapper->getUserInfo($userId);
        $this->view->projectsOwn = $projectMapper->getProfileProjects($userId);

        $projectIds = array();
        foreach($this->view->projectsOwn as $project) {
            $projectIds[] = $project->getProjectId();
        }
        if ( !empty($projectIds) ) {
            $this->view->assignedProjectsUsers = $projectBidMapper->getAssignedProjectsUsers($projectIds);
        }

        $this->view->testimonials = $feedbackMapper->getTestimonialsByUserId($userId);

        $userTestimonials = array();
        foreach($this->view->testimonials as $testimonial) {
            if($testimonial->getOwnerUserId() == $userId) { 
                $userIdTestimonial = $testimonial->getBidderUserId();
                $userTestimonials[$userIdTestimonial] = $userMapper->getUsername($userIdTestimonial);
            } else if($testimonial->getBidderUserId() == $userId) {  
                $userIdTestimonial = $testimonial->getOwnerUserId();
                $userTestimonials[$userIdTestimonial] = $userMapper->getUsername($userIdTestimonial);
            }
        }
        $this->view->userTestimonials = $userTestimonials;
        
        $this->view->portfolioList = $portfolioMapper->getPortfolioByUserId($userId);
        
        $this->view->getIfBookmarked = $bookmarkMapper->getIfBookmarked($sessionUserId, $userId);
    }
    
    /**
     * Cancel bookmark
     * 
     * Cancel bookmark created by session user
     *
     * @return void
     */
    public function cancelBookmarkAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        $this->_helper->viewRenderer->setNoRender(true);

        $selectedId = $this->getRequest()->getPost('selected_id');

        $bookmarkMapper = new Application_Model_BookmarkMapper();
        $success = $bookmarkMapper->deleteBookmark($sessionUserId, $selectedId);

        if ($success) echo 'yes';
        else echo 'no';
    }
    
    /**
     * Bookmark user
     * 
     * Bookmark user by sesson user
     *
     * @return void
     */
    public function bookmarkUserAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        $this->_helper->viewRenderer->setNoRender(true);

        $selectedId = $this->getRequest()->getPost('selected_id');
        
        $bookmark = new Application_Model_Bookmark();
        $bookmark->setUserId($sessionUserId);
        $bookmark->setSelectedId($selectedId);
        $bookmark->setStatus(1);
        
        $bookmarkMapper = new Application_Model_BookmarkMapper();
        $success = $bookmarkMapper->bookmarkUser($bookmark);
        
        if ($success) echo 'yes';
        else echo 'no';
    }
    
    /**
     * User testimonials
     *
     * @return void
     */
    public function testimonialsAction()
    {
        $this->view->sessionUserId = $this->_loginNamespace->session_user_id;
        
        $username = $this->getRequest()->getParam('username');
        $this->view->username = $username;
        $pageNo = $this->getRequest()->getParam('pageNo');

        $userMapper = new Application_Model_UserMapper();
        $feedbackMapper = new Application_Model_FeedbackMapper();

        $userId = $userMapper->getUserId($username);
        $this->view->userId = $userId;
        
        $perPage = 5;
        $startLimit = ($pageNo) ? $perPage*($pageNo - 1) : 0;
        
        $this->view->testimonials = $feedbackMapper->getTestimonialsByUserId($userId, 'ASC', $startLimit, $perPage);
        
        $userTestimonials = array();
        foreach($this->view->testimonials as $testimonial) {
            if($testimonial->getOwnerUserId() == $userId) { 
                $userIdTestimonial = $testimonial->getBidderUserId();
                $userTestimonials[$userIdTestimonial] = $userMapper->getUsername($userIdTestimonial);
            } else if($testimonial->getBidderUserId() == $userId) {  
                $userIdTestimonial = $testimonial->getOwnerUserId();
                $userTestimonials[$userIdTestimonial] = $userMapper->getUsername($userIdTestimonial);
            }
        }
        $this->view->userTestimonials = $userTestimonials;
    }
    
    /**
     * User portfolio
     *
     * @return void
     */
    public function portfolioAction()
    {
        $this->view->sessionUserId = $this->_loginNamespace->session_user_id;
        
        if ($this->_messageNamespace->message) {
            $this->view->message = $this->_messageNamespace->message;
            unset($this->_messageNamespace->message);
        }
        if ($this->_messageNamespace->error) {
            $this->view->error = $this->_messageNamespace->error;
            unset($this->_messageNamespace->error);
        }

        $username = $this->getRequest()->getParam('username');
        
        if (empty($username)) {
            $username = $this->_loginNamespace->user_username;
        }
        
        if (empty($username)) {
            $this->_redirector->gotoSimple('index', 'index');
        }
        
        $this->view->username = $username;
        $pageNo = $this->getRequest()->getParam('pageNo');

        $userMapper = new Application_Model_UserMapper();
        $portfolioMapper = new Application_Model_PortfolioMapper();

        $userId = $userMapper->getUserId($username);
        $this->view->userId = $userId;

        $this->view->portfolioList = $portfolioMapper->getPortfolioByUserId($userId);
    }
    
    /**
     * User portfolio details
     *
     * @return void
     */
    public function portfolioDetailsAction()
    {
        $portfolioId = $this->getRequest()->getParam('portfolioId');

        $portfolioMapper = new Application_Model_PortfolioMapper();

        $this->view->portfolioDetails = $portfolioMapper->getPortfolioDetails($portfolioId);
    }
    
    /**
     * Delete portfolio
     *
     * @return void
     */
    public function deletePortfolioAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }

        $portfolioId = $this->getRequest()->getParam('portfolioId');
        $userMapper = new Application_Model_UserMapper();
        $username = $userMapper->getUsername($sessionUserId);

        $portfolioMapper = new Application_Model_PortfolioMapper();
        $portfolioDetails = $portfolioMapper->getPortfolioDetails($portfolioId);
        $success = $portfolioMapper->deletePortfolio($portfolioId);

        if ($success) {
            $imageName = $portfolioDetails->getPortfolioImage();
            if ($imageName != '') {
                $imageDestination = $this->view->baseUrl('images/profile_image') . '/' . $imageName;
                if (file_exists($imageDestination)) {
                    unlink($imageDestination);
                }
            }
            $this->_messageNamespace->message = 'Portfolio deleted successfully.';
        } else {
            $this->_messageNamespace->error = 'Portfolio deletion failed.';
        }
        
        $sessionUsername = $this->_loginNamespace->user_username;
        $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'portfolio');
    }
    
    /**
     * Account inbox
     *
     * @return void
     */
    public function inboxAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUsername = $this->_loginNamespace->user_username;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        if ($sessionUserId != '') {
            $messageMapper = new Application_Model_MessageMapper();

            $this->view->unreadMessageCount = $messageMapper->unreadMessageCount($sessionUserId);
            $this->view->messageSearchUser = $messageSearchUser = $this->_getParam('messageSearchUser');

            if ($messageSearchUser != '') {
                $totalRows = $messageMapper->getInboxSearchedCount($sessionUserId, $messageSearchUser);
            } else {
                $totalRows = $messageMapper->getInboxCount($sessionUserId);
            }

            $pageNo = $this->_getParam('page');
            if (empty($pageNo)) $pageNo = 1;
            $perPage = 8;
            $startLimit = $perPage*($pageNo-1);
            
            if ($messageSearchUser != ''){
                $this->view->inboxList = $messageMapper->getInboxSearchedList($sessionUserId, $messageSearchUser, $startLimit, $perPage);
            } else {
                $this->view->inboxList = $messageMapper->getInboxList($sessionUserId, $startLimit, $perPage);
            }
            
            // pagination
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
            $paginator->setCurrentPageNumber($pageNo);
            $paginator->setItemCountPerPage($perPage);
            $this->view->pagination = $paginator;
        }

    }
    
    /**
     * Create My_Form_ReplyMessage form
     *
     * @param string $action Form action name
     * @return My_Form_ReplyMessage
     */
    public function getReplyMessageForm($action = '')
    {
        $form = new My_Form_ReplyMessage($action);
        return $form;
    }
    
    /**
     * Inbox for a particular project
     *
     * @return void
     */
    public function projectInboxAction()
    {
        $userId = $this->view->sessionUserId = 1;
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $projectId = $this->getRequest()->getParam('projectId');
        $senderUserId = $this->getRequest()->getParam('senderUserId');
        $this->view->projectId = $projectId;
        $this->view->senderUserId = $senderUserId;
        
        $action = $this->view->baseUrl('account/project-inbox') . '/' . $projectId . '/' . $senderUserId;
        $form = $this->getReplyMessageForm($action);
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            } else {
                $values = $form->getValues();
                $message = new Application_Model_Message(); 
                $message->setMessage($values['reply_message']);
                $message->setProjectId($projectId);
                $message->setReceiverUserId($senderUserId);
                $message->setSenderUserId($sessionUserId);
                $messageMapper = new Application_Model_MessageMapper();
                $success = $messageMapper->saveMessage($message);
                $form->reset();
            }
        }
        
        $messageMapper = new Application_Model_MessageMapper();
        $this->view->projectMessagesBySender = $messageMapper->getProjectMessagesBySender($projectId, 
                                                                                          $senderUserId, 
                                                                                          $sessionUserId);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectInfo = $projectMapper->getProject($projectId);
    }
    
    /**
     * Delete inbox message
     *
     * @return void
     */
    public function deleteInboxMessageAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }

        $projectId = $this->getRequest()->getParam('projectId');
        $senderUserId = $this->getRequest()->getParam('senderUserId');

        $messageMapper = new Application_Model_MessageMapper();
        
        $success = $messageMapper->deleteInboxMessage($projectId, $senderUserId, $sessionUserId);
        
        $this->_redirector->gotoSimple('inbox', 'account');
    }
    
    /**
     * Account outbox
     *
     * @return void
     */
    public function outboxAction()
    {
        try {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUsername = $this->_loginNamespace->user_username;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        if ($sessionUserId != '') {
            $messageMapper = new Application_Model_MessageMapper();
            
            $this->view->messageSearchUser = $messageSearchUser = $this->getRequest()->getParam('messageSearchUser');

            if ($messageSearchUser != '') {
                $totalRows = $messageMapper->getOutboxSearchedCount($sessionUserId, $messageSearchUser);
            } else {
                $totalRows = $messageMapper->getOutboxCount($sessionUserId);
            }

            $pageNo = $this->_getParam('page');
            if (empty($pageNo)) $pageNo = 1;
            $perPage = 8;
            $startLimit = $perPage*($pageNo-1);
            
            if ($messageSearchUser != ''){
                $this->view->outboxList = $messageMapper->getOutboxSearchedList($sessionUserId, $messageSearchUser, $startLimit, $perPage);
            } else {
                $this->view->outboxList = $messageMapper->getOutboxList($sessionUserId, $startLimit, $perPage);
            }
            
            // pagination
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($totalRows));
            $paginator->setCurrentPageNumber($pageNo);
            $paginator->setItemCountPerPage($perPage);
            $this->view->pagination = $paginator;
        }
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Delete outbox message
     *
     * @return void
     */
    public function deleteOutboxMessageAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }

        $projectId = $this->getRequest()->getParam('projectId');
        $receiverUserId = $this->getRequest()->getParam('receiverUserId');

        $messageMapper = new Application_Model_MessageMapper();
        
        $success = $messageMapper->deleteOutboxMessage($projectId, $receiverUserId, $sessionUserId);
        
        $this->_redirector->gotoSimple('inbox', 'account');
    }
    
    /**
     * Outbox for a particular project
     *
     * @return void
     */
    public function projectOutboxAction()
    {
        $this->view->sessionUserId = $sessionUserId = $this->_loginNamespace->session_user_id;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $projectId = $this->getRequest()->getParam('projectId');
        $receiverUserId = $this->getRequest()->getParam('receiverUserId');
        $this->view->projectId = $projectId;
        $this->view->receiverUserId = $receiverUserId;
        
        $messageMapper = new Application_Model_MessageMapper();
        $this->view->projectMessagesByReceiver = $messageMapper->getProjectMessagesBySender($projectId, 
                                                                                          $receiverUserId, 
                                                                                          $sessionUserId);
        
        $projectMapper = new Application_Model_ProjectMapper();
        $this->view->projectInfo = $projectMapper->getProject($projectId);
    }
    
    /**
     * Create My_Form_AddPortfolio form
     *
     * @return My_Form_AddPortfolio
     */
    public function getAddPortfolioForm()
    {
        $form = new My_Form_AddPortfolio();
        return $form;
    }

    /**
     * Add portfolio
     *
     * @return void
     */
    public function addPortfolioAction()
    {
        try {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUsername = $sessionUsername = $this->_loginNamespace->user_username;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $form = $this->getAddPortfolioForm();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $portfolio = new Application_Model_Portfolio();
                $portfolio->setUserId($sessionUserId);
                $portfolio->setClientName($this->getRequest()->getPost('client_name'));
                $portfolio->setPortfolioTitle($this->getRequest()->getPost('portfolio_title'));
                $portfolio->setProjectUrl($this->getRequest()->getPost('project_url'));
                $portfolio->setProjectDescription($this->getRequest()->getPost('project_description'));
                $portfolio->setPortfolioImage($this->getRequest()->getPost('portfolio_image'));
                $portfolio->setStatus(1);
                $portfolio->setCreatedOn(date('Y-m-d, H:i:s', time()));

                $portfolioMapper = new Application_Model_PortfolioMapper();
                $portfolioId = $portfolioMapper->addPortfolio($portfolio);

                if ($portfolioId > 0) {
                    $this->_messageNamespace->message = 'Portfolio added successfully.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'portfolio');
                } else {
                    $this->_messageNamespace->error = 'Portfolio addition failed.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'portfolio');
                }
            } else {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            }
            
        }        
        
        $this->view->form = $form;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Create My_Form_EditPortfolio form
     *
     * @return My_Form_EditPortfolio
     */
    public function getEditPortfolioForm()
    {
        $form = new My_Form_EditPortfolio();
        return $form;
    }
    
    /**
     * Edit portfolio
     *
     * @return void
     */
    public function editPortfolioAction()
    {
        try {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $this->view->sessionUsername = $sessionUsername = $this->_loginNamespace->user_username;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $form = $this->getEditPortfolioForm();
        
        $portfolioMapper = new Application_Model_PortfolioMapper();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $portfolio = new Application_Model_Portfolio();
                $portfolio->setUserId($sessionUserId);
                $portfolio->setPortfolioId($this->getRequest()->getPost('portfolio_id'));
                $portfolio->setClientName($this->getRequest()->getPost('client_name'));
                $portfolio->setPortfolioTitle($this->getRequest()->getPost('portfolio_title'));
                $portfolio->setProjectUrl($this->getRequest()->getPost('project_url'));
                $portfolio->setProjectDescription($this->getRequest()->getPost('project_description'));
                $portfolio->setPortfolioImage($this->getRequest()->getPost('portfolio_image'));
                $portfolio->setUpdatedOn(date('Y-m-d, H:i:s', time()));
            
                $result = $portfolioMapper->updatePortfolio($portfolio);

                if ($result) {
                    $this->_messageNamespace->message = 'Portfolio updated successfully.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'portfolio');
                } else {
                    $this->_messageNamespace->error = 'Portfolio update failed.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'portfolio');
                }
            } else {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            }
            
        } 
        
        $portfolioId = $this->getRequest()->getParam('portfolioId');
        $portfolioDetails = $portfolioMapper->getPortfolioDetails($portfolioId);
        $data = array(
            'portfolio_id' => $portfolioDetails->getPortfolioId(),
            'portfolio_title' => $portfolioDetails->getPortfolioTitle(),
            'client_name' => $portfolioDetails->getClientName(),
            'project_url' => $portfolioDetails->getProjectUrl(),
            'project_description' => $portfolioDetails->getProjectDescription(),
            'portfolio_image' => $portfolioDetails->getPortfolioImage()
        );
        $form->populate($data);
        $this->view->portfolioImage = $portfolioDetails->getPortfolioImage();
        
        $this->view->form = $form;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Create My_Form_EditProfile form
     *
     * @param  array $params Array contains profileImage and imagePath
     * @return My_Form_EditProfile
     */
    public function getEditProfileForm(array $params = array())
    {
        $form = new My_Form_EditProfile($params);
        return $form;
    }
    
    /**
     * Edit account profile
     *
     * @return void
     */
    public function editProfileAction()
    {
        $sessionUserId = $this->_loginNamespace->session_user_id;
        $sessionUsername = $this->_loginNamespace->user_username;
        
        if (empty($sessionUserId)) {
            $this->_redirector->gotoSimple('login', 'index');
        }
        
        $userMapper = new Application_Model_UserMapper();
        $this->view->userInfo = $userInfo = $userMapper->getUserInfo($sessionUserId);
        
        $params['profileImage'] = $userInfo->getProfileImage();
        $params['imagePath'] = $this->view->baseUrl('images');

        $form = $this->getEditProfileForm($params);
        
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            } else {
                $this->_helper->layout->setLayout('layout-simple');
        
                $user = new Application_Model_User();
                $user->setUserId($sessionUserId);
                $user->setFullName($this->getRequest()->getPost('full_name'));
                $user->setProfileImage($this->getRequest()->getPost('profile_image'));
                $user->setEmail($this->getRequest()->getPost('email'));
                $user->setContactNo($this->getRequest()->getPost('contact_no'));
                $user->setCompany($this->getRequest()->getPost('company'));
                $user->setNricRocNumber($this->getRequest()->getPost('NRIC_ROC_number'));
                
                $userMapper = new Application_Model_UserMapper();
                $updated = $userMapper->updateUser($user);
                
                if ($updated) {
                    $this->_messageNamespace->message = 'Account updated successfully.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'profile');
                } else {
                    $this->_messageNamespace->error = 'Accunt update failed.';
                    $this->_redirector->gotoRoute(array('username' => $sessionUsername), 'profile');

                } 
                
            }
        }
        
        $data = array(
            'full_name' => $userInfo->getFullName(),
            'email' => $userInfo->getEmail(),
            'contact_no' => $userInfo->getContactNo(),
            'company' => $userInfo->getCompany(),
            'NRIC_ROC_number' => $userInfo->getNricRocNumber()
        );
        
        $form->populate($data);
        $this->view->form = $form;
    }

    
}