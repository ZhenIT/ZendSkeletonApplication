<?php

namespace Mptests\Simuladores;

use Zend\Http\PhpEnvironment\Request as HttpRequest;

abstract class Simulador {

    const ISO_TYPE_LANG = 1;
    const ISO_TYPE_CURR = 2;
    const RETURN_METHOD = 'POST';

    public $transaction;
    public $transactionTable;

    public function __construct($id, $transactionTable) {
        $this->transactionTable = $transactionTable;
        $this->transaction = $this->transactionTable->getTransaction($id);
    }

    function __destruct() {
        $this->transactionTable->saveTransaction($this->transaction);
    }

    public function getReturnMethod() {
        return self::RETURN_METHOD;
    }

    abstract public function getReturnData($result);

    abstract public function getAmount(HttpRequest $request);

    abstract public function getLang(HttpRequest $request);

    abstract public function notify($result);

    abstract public function getUrlOk();

    abstract public function getUrlFail();

    abstract public function safeReferencia(HttpRequest $request);
}