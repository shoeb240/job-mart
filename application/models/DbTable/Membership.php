<?php
/**
 * Application_Model_DbTable_Membership class
 * 
 * @category   Application
 * @package    Application_Model
 * @subpackage DbTable
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_DbTable_Membership extends Zend_Db_Table_Abstract
{
    protected $_name = 'job_membership';
    protected $_primary = 'membership_id';
    
}