<?php
/**
 * Application_Model_PaymentRecordMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_PaymentRecordMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_PaymentRecord();
        }
        
        return $this->_dbTable;
    }
    
    public function checkPayment($userId)
    {
        $select = $this->getTable()->select();
        $select->where('user_id = ?', $userId)
               ->where('status = ?', 1);
        $row = $this->getTable()->fetchRow($select);
        
        if ( !empty($row) ) return true;
        else return false;
    }
    
   
}