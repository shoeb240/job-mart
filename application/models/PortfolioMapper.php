<?php
/**
 * Application_Model_PortfolioMapper class
 * 
 * @category   Application
 * @package    Application_Model
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2013, Shoeb Abdullah
 * @version    1.0
 */
class Application_Model_PortfolioMapper
{
    private $_dbTable = null;
    
    public function getTable()
    {
        if (null == $this->_dbTable) {
            $this->_dbTable = new Application_Model_DbTable_Portfolio();
        }
        
        return $this->_dbTable;
    }
    
    public function getPortfolioByUserId($userId)
    {
        $select = $this->getTable()->select();
        $select->where('user_id = ?', $userId)
               ->order('portfolio_id DESC');
        $rowSets = $this->getTable()->fetchAll($select);
        
        $info = array();
        foreach($rowSets as $k => $row) {
            $portfolio = new Application_Model_Portfolio();
            $portfolio->setPortfolioId($row->portfolio_id);
            $portfolio->setUserId($row->user_id);
            $portfolio->setClientName($row->client_name);
            $portfolio->setPortfolioTitle($row->portfolio_title);
            $portfolio->setProjectUrl($row->project_url);
            $portfolio->setProjectDescription($row->project_description);
            $portfolio->setPortfolioImage($row->portfolio_image);
            $portfolio->setStatus($row->status);
            $portfolio->setCreatedOn($row->created_on);
            $portfolio->setUpdatedOn($row->updated_on);
            $info[] = $portfolio;
        }
        
        return $info;
    }
    
    
    public function getPortfolioDetails($portfolioId)
    {
        $select = $this->getTable()->select();
        $select->where('portfolio_id = ?', $portfolioId)
               ->order('portfolio_id DESC');
        $row = $this->getTable()->fetchRow($select);
        
        $portfolio = new Application_Model_Portfolio();
        $portfolio->setPortfolioId($row->portfolio_id);
        $portfolio->setUserId($row->user_id);
        $portfolio->setClientName($row->client_name);
        $portfolio->setPortfolioTitle($row->portfolio_title);
        $portfolio->setProjectUrl($row->project_url);
        $portfolio->setProjectDescription($row->project_description);
        $portfolio->setPortfolioImage($row->portfolio_image);
        $portfolio->setStatus($row->status);
        $portfolio->setCreatedOn($row->created_on);
        $portfolio->setUpdatedOn($row->updated_on);
        
        return $portfolio;
    }
    
    
    public function deletePortfolio($portfolioId)
    {
        $where = $this->getTable()->getAdapter()->quoteInto('portfolio_id = ?', $portfolioId, 'INTEGER');

        return $this->getTable()->delete($where);
    }
    
    public function addPortfolio(Application_Model_Portfolio $portfolio)
    {
        $data = array(
            'user_id' => $portfolio->getUserId(),
            'client_name' => $portfolio->getClientName(),
            'portfolio_title' => $portfolio->getPortfolioTitle(),
            'project_url' => $portfolio->getProjectUrl(),
            'project_description' => $portfolio->getProjectDescription(),
            'portfolio_image' => $portfolio->getPortfolioImage(),
            'status' => $portfolio->getStatus(),
            'created_on' => $portfolio->getCreatedOn()
        );
        return $this->getTable()->insert($data);
    }
    
    public function updatePortfolio(Application_Model_Portfolio $portfolio)
    {
        $data = array(
            'portfolio_title' => $portfolio->getPortfolioTitle(),
            'client_name' => $portfolio->getClientName(),
            'project_url' => $portfolio->getProjectUrl(),
            'project_description' => $portfolio->getProjectDescription(),
            'portfolio_image' => $portfolio->getPortfolioImage(),
            'updated_on' => $portfolio->getUpdatedOn()
        );
        
        $where = $this->getTable()->getAdapter()->quoteInto('portfolio_id = ?', $portfolio->getPortfolioId(), 'INTEGER');
        
        return $this->getTable()->update($data, $where);
    }
    


}