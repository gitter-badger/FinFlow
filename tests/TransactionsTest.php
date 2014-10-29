<?php
/**
 * FinFlow 1.0 - Transactions Test Case
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

include_once 'init-tests.php';
include_once 'UnitTestBase.php';

class TransactionsTest extends UnitTestBase{

    public function __construct(){
        $this->requires_db = true;
        $this->dbtype         = 'notrans';
    }

    public function testAddTransaction(){

        $defaultCC = fn_Currency::get_default();

        $transaction = new stdClass();

        $transaction->optype = FN_OP_IN;
        $transaction->value   = strval(986.54);
        $transaction->currency_id = $defaultCC->currency_id;
        $transaction->comments   = ( 'Transaction added from UnitTest  ' . __CLASS__ . '::' . __FUNCTION__ );
        $transaction->sdate          = date('Y-m-d H:i:s', strtotime('-7 days'));
        $transaction->mdate         = null;
        $transaction->account_id= 0;
        $transaction->contact_id = 0;

        $trans_id = fn_OP::add($transaction->optype, $transaction->value, $transaction->currency_id, $transaction->comments, $transaction->sdate);

        $this->assertTrue($trans_id>0, "Failed to insert transaction");

        $dbtransaction = fn_OP::get($trans_id);

        if( $trans_id ) {
            $transaction->trans_id = $trans_id; $this->assertEquals($transaction, $dbtransaction, "Failed to insert transaction");
        }

    }

    /**
     * @depends testAddTransaction
     */
    public function testAssociateLabels(){

        $transaction = $this->getLastTransaction();

        $labels = array(1, 2, 3); foreach($labels as $label_id){
            fn_OP::associate_label($transaction->trans_id, $label_id);
        }

        $dblabels = fn_OP::get_labels($transaction->trans_id); foreach($dblabels as $label){
            $this->assertTrue(in_array($label->label_id, $labels), "Failed to associate label with transaction.");
        }

    }

    /**
     * @depends testAssociateLabels
     */
    public function testAssignAccount(){

        $transaction = $this->getLastTransaction();

        $account_id = 1;

        fn_Accounts::add_trans($account_id, $transaction->trans_id);

        $account = fn_OP::get_account($transaction->trans_id);

        $this->assertEquals($account_id, $account->account_id, "Failed to assign account to transaction");

    }

    /**
     * @depends testAssignAccount
     */
    public function testRemoveTransaction(){
        $transaction = $this->getLastTransaction(); fn_OP::remove( $transaction->trans_id ); $removed = fn_OP::get($transaction->trans_id); $this->assertEmpty($removed, "Failed to remove transaction");
    }

    public function testBalance(){
        //TODO ... test if the balance matches the value expected

        //--- create a balance ---//
        //--- create a balance ---//
    }

    public function testFilterByLabel(){
        //TODO ...
    }

    public function testFilterByDateSpan(){
        //TODO ...
    }


    public function testFilterByCurrency(){
        //TODO...
    }

    public function testFilterByAccount(){
        //TODO ...
    }

    public function testFilterByMulti(){
        //TODO...
    }
}