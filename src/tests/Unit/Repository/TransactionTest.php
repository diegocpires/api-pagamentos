<?php

namespace Unit\Repository;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\PossibleFraudException;
use App\Models\Customer;
use App\Repository\Transaction;
use App\Service\AuthorizePayment;
use App\Service\Cashback;
use App\Service\FraudDetection;
use App\Service\SendNotification;
use Mockery;

class TransactionTest extends \TestCase
{

    /**
     * @var \Mockery\Mock
     */
    private $fraudDetection;
    /**
     * @var \Mockery\Mock
     */
    private $authorizePayment;
    /**
     * @var \Mockery\Mock
     */
    private $cashback;
    /**
     * @var \Mockery\Mock
     */
    private $sendNotification;
    /**
     * @var \Mockery\Mock
     */
    private $payer;
    /**
     * @var \Mockery\Mock
     */
    private $payee;
    /**
     * @var \Mockery\Mock
     */
    private $transaction;
    /**
     * @var \Mockery\Mock
     */
    private $transactionModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->fraudDetection = \Mockery::mock(FraudDetection::class)->makePartial();
        $this->authorizePayment = \Mockery::mock(AuthorizePayment::class)->makePartial();
        $this->cashback = \Mockery::mock(Cashback::class)->makePartial();
        $this->sendNotification = \Mockery::mock(SendNotification::class)->makePartial();
        $this->payer = \Mockery::mock(Customer::class)->makePartial();
        $this->payee = \Mockery::mock(Customer::class)->makePartial();
        $this->transactionModel = \Mockery::mock(\App\Models\Transaction::class)->makePartial();

        $this->transaction = Mockery::mock(
            Transaction::class,
            [
                $this->fraudDetection,
                $this->authorizePayment,
                $this->cashback,
                $this->sendNotification,
                $this->transactionModel
            ]
        )
            ->makePartial();
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testRequiredFields()
    {
        $this->transactionModel->shouldReceive("verifyOpenTransaction")->andReturn(null);
        $this->assertTrue($this->transaction->validate($this->payer, $this->payee));
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testFailStorePayer()
    {
        $this->expectException(\Exception::class);

        $this->payer->shouldReceive('getAttribute')
            ->withArgs(["type"])
            ->once()
            ->andReturn(2);

        $this->transaction->validate($this->payer, $this->payee);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testFailFraud()
    {
        $this->expectException(PossibleFraudException::class);

        $this->transactionModel->shouldReceive("verifyOpenTransaction")->andReturn(null);
        $executeReturn = new \StdClass();
        $executeReturn->score = 2;
        $this->fraudDetection->shouldReceive('execute')
            ->andReturn($executeReturn);

        $this->transaction->validate($this->payer, $this->payee);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testFailFunds()
    {
        $this->expectException(InsufficientFundsException::class);

        $this->transactionModel->shouldReceive("verifyOpenTransaction")->andReturn(null);
        $executeReturn = new \StdClass();
        $executeReturn->message = "error";
        $this->authorizePayment->shouldReceive('execute')
            ->andReturn($executeReturn);

        $this->transaction->validate($this->payer, $this->payee);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testNotificationFailed()
    {
        $this->expectsJobs(\App\Jobs\NotifyJob::class);
        $executeReturn = new \StdClass();
        $executeReturn->message = "error";
        $this->sendNotification->shouldReceive('execute')
            ->andReturn($executeReturn);
        $this->transactionModel->shouldReceive("saveOrFail")->andReturns([true, true]);
        $this->transactionModel->shouldReceive("verifyOpenTransaction")->andReturn(null);
        $this->payer->shouldReceive("saveOrFail")->andReturn(true);
        $this->payee->shouldReceive("saveOrFail")->andReturn(true);

        $this->transaction->execute($this->payer, $this->payee, 1);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testWithoutCashback()
    {
        $executeReturn = new \StdClass();
        $executeReturn->is_eligible = false;
        $this->cashback->shouldReceive('execute')
            ->andReturn($executeReturn);
        $this->transactionModel->shouldReceive("saveOrFail")->andReturns([true, true]);
        $this->payer->shouldReceive("saveOrFail")->andReturn(true);
        $this->payee->shouldReceive("saveOrFail")->andReturn(true);

        $this->transaction->execute($this->payer, $this->payee, 1);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testTransactionRollback()
    {
        $this->expectException(\App\Exceptions\BadTransactionException::class);
        $executeReturn = new \StdClass();
        $executeReturn->is_eligible = false;
        $this->cashback->shouldReceive('execute')
            ->andReturn($executeReturn);
        $this->transactionModel->shouldReceive("saveOrFail")->andReturns([true, false]);
        $this->payer->shouldReceive("saveOrFail")->andReturn(true);
        $this->payee->shouldReceive("saveOrFail")->andThrowExceptions([new \Exception("Error")]);

        $this->transaction->execute($this->payer, $this->payee, 1);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testOpenTransaction()
    {
        $this->expectException(\App\Exceptions\OpenTransactionException::class);
        $this->transactionModel->shouldReceive("verifyOpenTransaction")->andReturn(true);
        $this->transaction->validate($this->payer, $this->payee);
    }

}














