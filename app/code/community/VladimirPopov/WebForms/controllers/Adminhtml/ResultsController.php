<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Adminhtml_ResultsController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('webforms/webforms');
        if ((float)substr(Mage::getVersion(), 0, 3) > 1.3)
            $this->_title($this->__('Web-forms'))->_title($this->__('Results'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        if (!Mage::registry('webform_data')) {
            $webform = Mage::getModel('webforms/webforms')
                ->setStoreId($this->getRequest()->getParam('store'))
                ->load($this->getRequest()->getParam('webform_id'));
            Mage::register('webform_data', $webform);
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('webforms/adminhtml_results_grid')->toHtml()
        );
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $result = Mage::getModel('webforms/results')->load($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Result was successfully deleted'));
                $this->_redirect('*/*/', array('webform_id' => $result->getWebformId()));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        if (!Mage::registry('webform_data')) {
            $webform = Mage::getModel('webforms/webforms')->load($this->getRequest()->getParam('webform_id'));
            Mage::register('webform_data', $webform);
        }
        $fileName = 'results.csv';
        $content = $this->getLayout()->createBlock('webforms/adminhtml_results_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        if (!Mage::registry('webform_data')) {
            $webform = Mage::getModel('webforms/webforms')->load($this->getRequest()->getParam('webform_id'));
            Mage::register('webform_data', $webform);
        }
        $fileName = 'results.xml';
        $content = $this->getLayout()->createBlock('webforms/adminhtml_results_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massEmailAction()
    {
        $Ids = (array)$this->getRequest()->getParam('id');
        try {
            $k = 0;
            foreach ($Ids as $id) {
                $result = Mage::getModel('webforms/results')->load($id);
                $success = $result->sendEmail();
                if ($success) $k++;
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d result(s) have been emailed.', count($k))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred during operation.'));
        }

        $this->_redirect('*/*/', array('webform_id' => $this->getRequest()->getParam('webform_id')));

    }

    public function massDeleteAction()
    {
        $Ids = (array)$this->getRequest()->getParam('id');

        try {
            foreach ($Ids as $id) {
                $result = Mage::getModel('webforms/results')->load($id);
                $result->delete();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been deleted.', count($Ids))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
        }

        $this->_redirect('*/*/', array('webform_id' => $this->getRequest()->getParam('webform_id')));

    }

    public function massApproveAction($approveStatus = 1)
    {
        $Ids = (array)$this->getRequest()->getParam('id');
        try {
            foreach ($Ids as $id) {
                $result = Mage::getModel('webforms/results')->load($id);
                $result->setApproved(intval($approveStatus));
                $result->save();
                Mage::dispatchEvent('webforms_result_approve', array('result' => $result));
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d result(s) have been updated.', count($Ids))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred during operation.'));
        }

        $this->_redirect('*/*/', array('webform_id' => $this->getRequest()->getParam('webform_id')));

    }

    public function massDisapproveAction()
    {
        $this->massApproveAction(0);
    }

}
