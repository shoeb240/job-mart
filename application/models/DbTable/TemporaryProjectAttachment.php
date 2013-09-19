<?php
/**
 * Application_Model_DbTable_TemporaryProjectAttachment class
 * 
 * @category   Application
 * @package    Application_Model
 * @subpackage DbTable
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_DbTable_TemporaryProjectAttachment extends Zend_Db_Table_Abstract
{
    protected $_name = 'job_temporary_project_attachment';
    protected $_primary = 'project_attachment_id';
    
}