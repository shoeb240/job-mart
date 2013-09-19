<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_appNamespace = 'Application';
    
    protected function _initPlaceholders()
    {
        //Zend_Session::setOptions(array('strict' => true));
        
        //Zend_Registry::set('Zend_Locale', 'bn_BD');
        
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        $view->docType('XHTML1_STRICT');
        $view->headTitle('3PORE')
             ->setSeparator('::');
        $view->headLink()->prependStylesheet('/styles/style_sheet2.css')
                         ->appendStylesheet('/styles/custom_table.css')
                         ->appendStylesheet('/styles/message.css')
                         ->appendStylesheet('/styles/submodal/style.css')
                         ->appendStylesheet('/scripts/jQueryUI/themes/base/ui.all.css')
                         ->appendStylesheet('/styles/submodal/subModal.css')
                         ->appendStylesheet('/styles/submodal/style.css');
        
        $view->headScript()->prependFile('/scripts/jquery-1.7.1.min.js')
                           ->appendFile('/scripts/jquery.placeholder.js')
                           ->appendFile('/scripts/hoverIntent.js')
                           ->appendFile('/scripts/superfish.js')
                           ->appendFile('/scripts/js_sheet.js')
                           ->appendFile('/scripts/country.js')
                           ->appendFile('/scripts/ajaxfileupload.js')
                           ->appendFile('/scripts/jsval.js')
                           ->appendFile('/scripts/integer_check.js')
                           ->appendFile('/scripts/jQueryUI/ui/ui.datepicker.js')
                           ->appendFile('/scripts/job_search.js')
                           ->appendFile('/scripts/member_search.js')
                           ->appendFile('/scripts/submodal/common.js')
                           ->appendFile('/scripts/submodal/subModal.js');
    }
    
    protected function _initCategories()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $info = $db->fetchAll("SELECT * FROM job_primary_category WHERE status = 1 ORDER BY primary_category_id ASC");
        $categories = '';
        if(!empty($info)) {   
            $i = 0;
            foreach($info as $row_category)
            {  
                $categories .= $view->cycle(array('<ul class="left">', '', ''))->next();
                $categories .= '<li><a href="/member/category/' 
                             . $row_category['primary_category_id'] . '">' 
                             . $row_category['category_title'] . '</a></li>';
                $categories .= $view->cycle(array('', '', '</ul>'), 'another')->next();
            }
        }
        $view->placeholder('categories')->set($categories);
        
        $loginNamespace = new Zend_Session_Namespace('login');
        
        if ($loginNamespace->user_logged_in) {
            $info = $db->fetchRow('SELECT COUNT(message_id) as unread_message_number FROM job_message WHERE RECEIVER_user_id = ' . $loginNamespace->user_logged_in . ' AND RECEIVER_read = 0');
            $inbox = '<a class="signup" href="/account/inbox/">INBOX (' . $info['unread_message_number'] . ' MESSAGES)</a>';
            $view->placeholder('inbox')->set($inbox);
        } else $view->placeholder('inbox')->set('');
    }
    
}