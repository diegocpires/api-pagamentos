<?php

namespace Unit\Http\Controllers;

use App\Http\Controllers\CustomerController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Mockery;

class CustomerControllerTest extends \TestCase
{
    /**
     * @var \Mockery\Mock
     */
    private $customerModel;
    private $customerController;

    public function setUp(): void
    {
        parent::setUp();

        $this->customerModel = Mockery::mock(Customer::class)->makePartial();

        $this->customerController = Mockery::mock(
            CustomerController::class,
            [
                $this->customerModel
            ]
        )->makePartial();
    }

    public function testGetOne()
    {
        $this->customerModel->shouldReceive("FIND")->andReturn(new \StdClass());
        $this->assertInstanceOf(\StdClass::class, $this->customerController->get(1));
    }

    public function testDeleteFail()
    {
        $customerMock = Mockery::mock(Customer::class)->makePartial();
        $customerMock->shouldReceive("delete")->andThrow(\Exception::class);
        $this->customerModel->shouldReceive("findOrFail")->andReturn($customerMock);
        $response = $this->customerController->delete(1);
        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        $this->assertEquals('{"data":{"message":"Customer cannot be deleted."}}', $content);
        $this->assertEquals('400', $statusCode);
    }

    public function testUpdateFail()
    {
        $request = Mockery::mock(Request::class)->makePartial();

        $customerMock = Mockery::mock(Customer::class)->makePartial();
        $customerMock->shouldReceive("update")->andThrow(\Exception::class);
        $this->customerModel->shouldReceive("findOrFail")->andReturn($customerMock);

        $this->customerController->shouldReceive("validate")->andReturn(true);
        $response = $this->customerController->update(10, $request);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        $this->assertEquals('{"data":{"message":"Customer cannot be updated."}}', $content);
        $this->assertEquals('400', $statusCode);
        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
    }

    public function testInsertFail()
    {
        $request = Mockery::mock(Request::class)->makePartial();

        $this->customerModel->shouldReceive("create")->andThrow(\Exception::class);

        $this->customerController->shouldReceive("validate")->andReturn(true);
        $response = $this->customerController->insert($request);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        $this->assertEquals('{"data":{"message":"Customer cannot be created"}}', $content);
        $this->assertEquals('400', $statusCode);
        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
    }

}
