<?php
/**
 * Application_Model_BookmarkMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_BookmarkMapper
{
    /**
     * @var Application_Model_DbTable_Bookmark
     */
    private $_dbTable = null;
    
    /**
     * Create Zend_Db_Adapter_Abstract object
     *
     * @return Application_Model_DbTable_Bookmark
     */
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Bookmark();
        }
        
        return $this->_dbTable;
    }
    
    /**
     * Check if user bookmarked another selected user
     *
     * @param  int $userId
     * @param  int $selectedId
     * @return int 
     */
    public function getIfBookmarked($userId, $selectedId)
    {
        $select = $this->getTable()->select();
        $select->from('job_bookmark', array('count' => 'COUNT(*)'))
                ->where('user_id = ?', $userId)
                ->where('selected_id = ?', $selectedId);
        $row = $this->getTable()->fetchRow($select);
        
        return $row->count;
    }
    
    /**
     * Bookmark a selected user
     *
     * @param  Application_Model_Bookmark $bookmark
     * @return int 
     */
    public function bookmarkUser(Application_Model_Bookmark $bookmark)
    {
        $data = array(
            'user_id' => $bookmark->getUserId(),
            'selected_id' => $bookmark->getSelectedId(),
            'status' => $bookmark->getStatus()
        );
        return $this->getTable()->insert($data);
    }
    
    /**
     * Delete selected user from bookmark list
     *
     * @param  int $userId
     * @param  int $selectedId
     * @return int              The number of rows deleted.
     */
    public function deleteBookmark($userId, $selectedId)
    {
        $where[] = $this->getTable()->getAdapter()->quoteInto('user_id = ?', $userId, 'INTEGER');
        $where[] = $this->getTable()->getAdapter()->quoteInto('selected_id = ?', $selectedId, 'INTEGER');

        return $this->getTable()->delete($where);
    }
    
    /**
     * Count number of bookmarked users
     *
     * @param  int $userId
     * @return int          Number of bokmarked user
     */
    public function getBookmarkedMembersCount($userId)
    {
        $select = $this->getTable()->select();
        $select->from(array('b' => 'job_bookmark'), array('total_rows' => 'COUNT(*)'))
               ->where('b.user_id = ?', $userId);
        $row = $this->getTable()->fetchRow($select);
        
        return $row['total_rows'];
    }

}