<?php
namespace Mptests\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class TransactionTable extends AbstractTableGateway
{
    protected $table ='tbl_transaction';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Transaction());
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getTransaction($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array(
            'id' => $id,
            )
                );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveTransaction(Transaction $transaction)
    {
        $data = array(
            'dominio'   => $transaction->dominio,
            'modulo'    => $transaction->modulo,
            'sistema'   => $transaction->sistema,
            'referencia'=> $transaction->referencia,
            'response1'  => $transaction->response1,
            'response2' => $transaction->response2
        );
        $id = (int)$transaction->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getTransaction($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteTransaction($id)
    {
        $this->delete(array('id' => $id));
    }
}