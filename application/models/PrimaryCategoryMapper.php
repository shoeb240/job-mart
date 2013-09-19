<?php
/**
 * Application_Model_PrimaryCategoryMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_PrimaryCategoryMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_PrimaryCategory();
        }
        
        return $this->_dbTable;
    }
    
    public function getPrimaryCategories()
    {
        $select = $this->getTable()->select();
        $select->where('status = ?', 1);
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $primaryCategory = new Application_Model_PrimaryCategory();
            $primaryCategory->setPrimaryCategoryId($row->primary_category_id);
            $primaryCategory->setCategoryTitle($row->category_title);
            $info[] = $primaryCategory;
        }
        
        return $info;
    }
    
    public function getPrimaryCategoriyTitle($primaryCategoryId)
    {
        $select = $this->getTable()->select();
        $select->where('primary_category_id = ?', $primaryCategoryId);
        $row = $this->getTable()->fetchRow($select);
        
        return $row->category_title;
    }
    
    


}