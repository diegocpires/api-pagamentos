<?php

namespace Functional;

use App\Exceptions\InsufficientFundsException;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TransactionTest extends \TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    public function tearDown():void
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testRequiredFields()
    {
        $this->json('POST', '/transaction', [])
            ->seeJson(
                [
                    "payee" => ["The payee field is required."],
                    "payer" => ["The payer field is required."],
                    "value" => ["The value field is required."]
                ]
            );
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testTransactionOk()
    {
        $this->json('POST', '/transaction', ["payee" => 3, "payer" => 5, "value" => 1.00])
            ->seeJson(
                [
                    "data" => ["message" => "Transaction done!"]
                ]
            );
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testStorePayer()
    {
        $obj = [];
        $email = "emailTest".time()."@gmail.com";
        $obj["name"] = "Diego Pires";
        $obj["email"] = $email;
        $obj["type"] = "2";
        $obj["document"] = rand(11111111111,99999999999);
        $obj["password"] = "swordfish";
        $obj["balance"] = 100;

        $obj2 = $obj;
        unset($obj2["password"]);
        $this->json('POST', '/customers', $obj)
            ->seeJson($obj2);


        $this->json('POST', '/transaction', ["payee" => 3, "payer" => 11, "value" => 1.00])
            ->seeJsonContains(
            [
                "exception" => "Exception"
            ]
        )->seeJsonContains(["message"=>"Payer must not be a Store"]);

    }

}














