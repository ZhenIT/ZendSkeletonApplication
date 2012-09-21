<?php

namespace Mptests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Mptests\Model\Transaction;

class SimuladorController extends AbstractActionController {

    protected $transactionTable;
    protected $simulador;

    public function getTransactionTable() {
        if (!$this->transactionTable) {
            $sm = $this->getServiceLocator();
            $this->transactionTable = $sm->get('Mptests\Model\TransactionTable');
        }
        return $this->transactionTable;
    }

    private function getSimulador($id) {
        if (!$this->simulador) {
            $clname = 'Mptests\\Simuladores\\' . ucfirst($this->params()->fromRoute('sistema'));
            $this->simulador = new $clname($id, $this->getTransactionTable());
        }
        return $this->simulador;
    }

    public function indexAction() {
        //Resgitramos la transacción
        $transaction = new Transaction();
        $transaction->exchangeArray(
                array(
                    'dominio' => $this->getRequest()->getServer('HTTP_REFERER', 'http://127.0.0.1/'),
                    'modulo' => $this->params()->fromRoute('modulo'),
                    'sistema' => $this->params()->fromRoute('sistema'),
                )
        );
        $this->getTransactionTable()->saveTransaction($transaction);
        $id = $this->getTransactionTable()->getLastInsertValue();

        If (!$this->simulador)
            $this->simulador = $this->getSimulador($id);
        $this->simulador->safeReferencia($this->getRequest());

        return new ViewModel(array(
                    'id' => $id,
                    'lang' => $this->simulador->getLang($this->getRequest()),
                    'amount' => $this->simulador->getAmount($this->getRequest()),
                    'sistema' => $this->params()->fromRoute('sistema'),
                    'modulo' => $this->params()->fromRoute('modulo'),
                ));
    }

    public function okAction() {
        $id = $this->params()->fromRoute('id');
        If (!$this->simulador)
            $this->simulador = $this->getSimulador($id);
        $this->simulador->notify(true);

        return new ViewModel(array(
                    'url' => $this->simulador->getUrlOk(),
                    'data' => $this->simulador->getReturnData(true),
                    'method' => $this->simulador->getReturnMethod(),
                    'response' => $this->transcation->response2,
                    'sistema' => get_class($this->simulador)            
                ));
    }

    public function failAction() {
        $id = $this->params()->fromRoute('id');
        If (!$this->simulador)
            $this->simulador = $this->getSimulador($id, $this->getTransactionTable());
        $this->simulador->notify(false);
        
        return new ViewModel(array(
                    'url' => $this->simulador->getUrlFail(),
                    'data' => $this->simulador->getReturnData(false),
                    'method' => $this->simulador->getReturnMethod(),
                    'response' => $this->transcation->response2,
                    'sistema' => get_class($this->simulador)
                ));
    }

}
