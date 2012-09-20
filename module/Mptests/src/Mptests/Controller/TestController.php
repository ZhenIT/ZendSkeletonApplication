<?php

namespace Mptests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Mptests\Model\Transaction;

class TestController extends AbstractActionController {

    protected $transactionTable;
    protected $simulador;

    public function __construct(Adapter $adapter) {
        $this->getTransactionTable();
    }

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
            $this->simulador = new $clname($id);
        }
        return $this->simulador;
    }

    public function indexAction() {
        //Resgitramos la transacción
        $transaction = new Transaction();
        $data = array(
            'dominio' => $this->getRequest()->getServer('HTTP_REFERER'),
            'modulo' => $this->params()->fromRoute('modulo'),
            'sistema' => $this->params()->fromRoute('sistema'),
        );
        $transaction->exchangeArray($data);
        $this->getTransactionTable()->saveTransaction($transaction);
        $id = $this->getTransactionTable()->getLastInsertValue();

        If (!$this->simulador)
            $this->simulador = $this->getSimulador($id, $this->getTransactionTable());
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
            $this->simulador = $this->getSimulador($id, $this->getTransactionTable());
        $this->simulador->notify(true);

        return new ViewModel(array(
                    'url' => $this->simulador->getUrlOk(),
                    'data' => $this->simulador->getReturnData(true),
                    'method' => $this->simulador->getReturnMethod()
                ));
    }

    public function failAction() {
        $id = $this->params()->fromRoute('id');

        If (!$this->simulador)
            $this->simulador = $this->getSimulador($id, $this->getTransactionTable());
        $this->simulador->notify(false);

        return new ViewModel(array(
                    'url' => $this->simulador->getUrlOk(),
                    'data' => $this->simulador->getReturnData(false),
                    'method' => $this->simulador->getReturnMethod(),
                    'response' => $this->_transcation->response2,
                ));
    }

}
