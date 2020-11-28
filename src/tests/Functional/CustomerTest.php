<?php

namespace Functional;


use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CustomerTest extends \TestCase
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
    public function testGetList()
    {
        $this->json('GET', '/customers', [])
            ->seeJsonContains(["current_page"=>1]);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testGetOne()
    {
        $this->json('GET', '/customers/1', [])
            ->seeJsonStructure(["name", "balance", "created_at", "document", "email", "id", "type", "updated_at"]);
    }


    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testPost()
    {
        $obj = [];
        $email = "emailTest".time()."@gmail.com";
        $obj["name"] = "Diego Pires";
        $obj["email"] = $email;
        $obj["type"] = "1";
        $obj["password"] = "swordfish";
        $obj["document"] = rand(11111111111,99999999999);
        $obj["balance"] = 100;

        $obj2 = $obj;
        unset($obj2["password"]);
        $this->json('POST', '/customers', $obj)
            ->seeJson($obj2);
    }


    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testDeleteError()
    {


        $this->json('DELETE', '/customers/11', [])
            ->seeJsonContains(["data" => ["message"=>"Customer not exists"]]);
    }


    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testDelete()
    {


        $this->json('DELETE', '/customers/10', [])
            ->seeJsonContains(['data' => ['message' => 'Customer deleted']]);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testUpdate()
    {
        $obj = [];
        $email = "emailTest".time()."terceiro@gmail.com";
        $obj["name"] = "Diego Pires";
        $obj["type"] = "1";
        $obj["email"] = $email;
        $obj["document"] = "namao";
        $obj["balance"] = 100;

        $this->json('PUT', '/customers/10', $obj)
            ->seeJson($obj);
    }

    /**
     * Function Test for Required Fields
     *
     * @return void
     */
    public function testUpdateNotFound()
    {
        $obj = [];
        $email = "emailTest".time()."terceiro@gmail.com";
        $obj["name"] = "Diego Pires";
        $obj["type"] = "1";
        $obj["email"] = $email;
        $obj["document"] = "namao";
        $obj["balance"] = 100;

        $obj2 = $obj;
        $this->json('PUT', '/customers/99', $obj)
            ->seeJson(['data' => ["message"=>"Customer not exists"]]);
    }
}














