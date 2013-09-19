<?php
/**
 * Application_Model_CreditBalanceMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_CreditBalanceMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_CreditBalance();
        }
        
        return $this->_dbTable;
    }
    
    public function saveCreditBalance(Application_Model_CreditBalance $creditBalance)
    {
        $data['user_id'] = $creditBalance->getUserId();
        $data['TRANSACTION_FOR_user_id'] = $creditBalance->getTransactionForUserId();
        $data['created_on'] = $creditBalance->getCreatedOn();
        $data['type'] = $creditBalance->getType();
        $data['balance'] = $creditBalance->getBalance();
        $data['status'] = $creditBalance->getStatus();

        return $this->getTable()->insert($data);
    }
    
   
}