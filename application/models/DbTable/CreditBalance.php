<?php
/**
 * Application_Model_DbTable_CreditBalance class
 * 
 * @category   Application
 * @package    Application_Model
 * @subpackage DbTable
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_DbTable_CreditBalance extends Zend_Db_Table_Abstract
{
    protected $_name = 'job_credit_balance';
    protected $_primary = 'credit_balance_id';
    
}