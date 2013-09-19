<?php
/**
 * Application_Model_DbTable_Bookmark class
 * 
 * @category   Application
 * @package    Application_Model
 * @subpackage DbTable
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_DbTable_Bookmark extends Zend_Db_Table_Abstract
{
    protected $_name = 'job_bookmark';
    protected $_primary = 'bookmark_id';
    
}