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
class IndexController extends Zend_Controller_Action
{
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
     * Home page default action
     *
     * @return void
     */    
    public function indexAction()
    {
        if ($this->_messageNamespace->message) {
            $this->view->message = $this->_messageNamespace->message;
            unset($this->_messageNamespace->message);
        }
        if ($this->_messageNamespace->error) {
            $this->view->error = $this->_messageNamespace->error;
            unset($this->_messageNamespace->error);
        }
        
        $userMapper = new Application_Model_UserMapper();
        $this->view->membersPremium = $userMapper->getMembersPremium();
    }
    
    /**
     * Create My_Form_Login form
     *
     * @return My_Form_Login
     */    
    public function getLoginForm()
    {
        $form = new My_Form_Login();
        return $form;
    }
    
    /**
     * User login
     *
     * @return void
     */    
    public function loginAction()
    {
        $form = $this->getLoginForm();
        $this->view->form = $form;
        
        if ($this->_messageNamespace->error) {
            $this->view->error = $this->_messageNamespace->error;
            unset($this->_messageNamespace->error);
        }

        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $errors = $form->getMessages();
                $this->view->error = '';
                foreach ($errors as $field => $fieldErrors) {
                    $this->view->error .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                }
            } else {
                Zend_session::start();
                
                $username = $this->getRequest()->getPost('username');
                $password = $this->getRequest()->getPost('password');
                $keepLoggedin = $this->getRequest()->getPost('keep_loggedin');
                
        
                // using DbTable adapter
                $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'development');
                $dbAdapter = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('job_user')
                            ->setIdentityColumn('username')
                            ->setCredentialColumn('password')
                            ->setCredentialTreatment('MD5(?) AND status = 1');

                $authAdapter->setIdentity($username)
                            ->setCredential($password);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    $loggedinUser = $authAdapter->getResultRowObject();
                    
                    $this->_loginNamespace->user_logged_in = 1;
                    $this->_loginNamespace->session_user_id = $loggedinUser->user_id;
                    $this->_loginNamespace->user_username = $loggedinUser->username;
                    $this->_loginNamespace->user_email_address = $loggedinUser->email;

                    $userMapper = new Application_Model_UserMapper();
                    $userMapper->updateLastLogin($loggedinUser->user_id);
                    
                    if ($keepLoggedin == 'yes') {
                        $loggedInTime = 14*24*3600;
                        $zendAuth = new Zend_Session_Namespace('Zend_Auth');
                        $this->_loginNamespace->setExpirationSeconds($loggedInTime);
                        $zendAuth->setExpirationSeconds($loggedInTime);
                    }
                    
                    $this->_messageNamespace->message = 'You are successfully logged in.';
                    $this->_redirector->gotoSimple('index', 'account');
                } else {
                    $errors = $form->getMessages();
                    $errorSt = '';
                    foreach ($errors as $field => $fieldErrors) {
                        $errorSt .= $field . ': ' . implode(' ', $fieldErrors) . '<br />';
                    }
                    $this->_messageNamespace->error = $errorSt;
                    $this->_redirector->gotoSimple('login', 'index');
                }
            }
        }
        
    }
    
    /**
     * User logout
     *
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        
        Zend_Session::namespaceUnset('login');
        
        $this->_redirector->gotoSimple('login', 'index');
    }
    
    /**
     * Create My_Form_Signup form
     *
     * @param  array $params Array contains primaryCategories and membershipList
     * @return My_Form_Signup
     */
    public function getSignupForm(array $params = array())
    {
        $form = new My_Form_Signup($params);
        return $form;
    }
    
    /**
     * Create My_Form_PaypalPayment form
     *
     * @return My_Form_PaypalPayment
     */
    public function getPaypalPaymentForm()
    {
        $form = new My_Form_PaypalPayment();
        return $form;
    }
    
    /**
     * User signup
     *
     * @return void
     */
    public function signupAction()
    {
        $primaryCategoryMapper = new Application_Model_PrimaryCategoryMapper();
        $primaryCategories = $primaryCategoryMapper->getPrimaryCategories();
        
        $membershipMapper = new Application_Model_MembershipMapper();
        $membershipList = $membershipMapper->getMembershipList();
        
        $params['primaryCategories'] = $primaryCategories;
        $params['membershipList'] = $membershipList;

        $form = $this->getSignupForm($params);
        $this->view->form = $form;

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
                $user->setUsername($this->getRequest()->getPost('username'));
                $user->setFullName($this->getRequest()->getPost('full_name'));
                $user->setProfileImage($this->getRequest()->getPost('profile_image'));
                $user->setEmail($this->getRequest()->getPost('email'));
                $user->setCountry($this->getRequest()->getPost('country'));
                $user->setState($this->getRequest()->getPost('state'));
                $user->setCity($this->getRequest()->getPost('city'));
                $user->setPassword($this->getRequest()->getPost('password'));
                $user->setContactNo($this->getRequest()->getPost('contact_no'));
                $user->setCompany($this->getRequest()->getPost('company'));
                $user->setNricRocNumber($this->getRequest()->getPost('NRIC_ROC_number'));
                $user->setPrimaryCategoryId($this->getRequest()->getPost('primary_category_id'));
                $user->setMembershipId($this->getRequest()->getPost('membership_id'));
                
                $userMapper = new Application_Model_UserMapper();
                $userId = $userMapper->saveUser($user);
                
                if (!$userId) {
                     $this->_messageNamespace->error = 'User sign up failed.';
                     $this->_redirector->gotoSimple('index', 'index');
                }
                    
                if ($this->getRequest()->getPost('membership_id') == 0) {
                    // email work
                    
                    $this->_messageNamespace->message = 'You have signed up successfully.';
                    $this->_redirector->gotoSimple('index', 'index');

                } else {
                    
                    $this->_redirector->gotoRoute(array('userId' => $userId), 'paypalPayment');
                }
                
            }
        }
        
//        if ($this->_messageNamespace->error) {
//            $this->view->error = $this->_messageNamespace->error;
//            unset($this->_messageNamespace->error);
//        }
        
        
    }
    
    /**
     * Paypal payment form
     * 
     * Generate paypal payment form and auto submit to paypal for payment processing
     *
     * @return void
     */
    public function paypalPaymentAction()
    {
        $this->_helper->layout->setLayout('layout-simple');
        
        $userId = $this->getRequest()->getParam('userId');
        
        $userMapper = new Application_Model_UserMapper();
        $userMembership = $userMapper->getUserMembership($userId);
        
        $host = $this->getRequest()->getHttpHost();

        $data['notify_url']        = $this->view->baseUrl($host . '/ipn');
        $data['currency_code']     = Zend_Registry::get('ADMIN_CURRENCY');
        $data['business']          = Zend_Registry::get('BUSINESS_EMAIL');
        $data['payer_id']          = $userMembership->getUserId();
        $data['item_name']         = $userMembership->getUserId() . '__' . $userMembership->getMembership()->getMembership();
        $data['item_id']           = $userMembership->getUserId();
        $data['test_ipn']          = 1;
        $data['return']            = $this->view->baseUrl($host . '/ipn/postpaid/successful.php');
        $data['cancel_return']     = $this->view->baseUrl($host . '/ipn/postpaid/un-successful.php');
        $data['a3']                = Zend_Registry::get('PAYPAL_a3'); 
        $data['payer_email']       = $userMembership->getEmail();
        
        $form = $this->getPaypalPaymentForm();
        $form->populate($data);
        $this->view->form = $form;
    }
    
    /**
     * Payment success page action
     * 
     * After successful payment processing user redirected to this action
     *
     * @return void
     */
    public function premiumSuccessfulAction()
    {
        $this->_messageNamespace->message = 'You have successfully registered as a premium member.';
        $this->_redirector->gotoSimple('index', 'index');
    }
    
    /**
     * Payment failure page action
     * 
     * Failure of paypal payment processing redirects user to this action
     *
     * @return void
     */
    public function premiumUnsuccessfulAction()
    {
        $this->_messageNamespace->error = 'Your registration for premium membership is not fully completed. Please try again!';
        $this->_redirector->gotoSimple('index', 'index');
    }
    
}