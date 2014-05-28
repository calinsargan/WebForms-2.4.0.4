<?php
/**
 * @author        Vladimir Popov
 * @copyright    Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Results
    extends VladimirPopov_WebForms_Block_Webforms
    implements Mage_Widget_Block_Interface
{
    protected $_resultsCollection;

    protected function _construct()
    {
        parent::_construct();
        $this->setData('results', 1);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = Mage::app()->getLayout()->createBlock('page/html_pager')) {
            $toolbar->setCollection($this->getResultsCollection());
            $this->setChild('toolbar', $toolbar);
        }
        $data = $this->getFormData();
        if ($rating = Mage::app()->getLayout()->createBlock('webforms/rating')) {
            $rating->setData('webform_id', $data["webform_id"]);
            $rating->setTemplate('webforms/results/rating.phtml');
            $this->setChild('rating', $rating);
        }

        return $this;
    }

    public function getResultsCollection()
    {
        if (null === $this->_resultsCollection) {
            $data = $this->getFormData();
            $this->_resultsCollection = Mage::getModel('webforms/results')->getCollection()
                ->addFilter('store_id', Mage::app()->getStore()->getId())
                ->addFilter('webform_id', $data["webform_id"])
                ->addFilter('approved', 1);
            $this->_resultsCollection->getSelect()->order('created_time desc');
        }
        return $this->_resultsCollection;
    }

}
