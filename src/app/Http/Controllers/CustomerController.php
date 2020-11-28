<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /** @var \App\Models\Customer */
    private $customer;

    /**
     * CustomerController constructor.
     *
     * @param \App\Models\Customer $customer
     */
    public function __construct(
        Customer $customer
    ) {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->customer->paginate(10);
    }

    /**
     * @param int $customerId
     * @return mixed
     */
    public function get(int $customerId)
    {
        return $this->customer->find($customerId);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:customers|max:100',
            'type' => 'required|max:1',
            'document' => 'required|unique:customers|max:20'
        ]);

        try {
            $customer = $this->customer->create($request->all());
        } catch (\Throwable $exception) {
            /** @phpstan-ignore-next-line */
            return response(['data' => ["message" => "Customer cannot be created"]], 400);
        }
        return $customer;
    }

    /**
     * @param int                      $customerId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(int $customerId, Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
            'type' => 'required|max:1',
            'document' => 'required|max:20'
        ]);

        try {
            $customer = $this->customer->findOrFail($customerId);
        } catch (\Throwable $exception) {
            /** @phpstan-ignore-next-line */
            return response(['data' => ["message" => "Customer not exists"]], 404);
        }

        try {
            $customer->update($request->all());
        } catch (\Throwable $exception) {
            /** @phpstan-ignore-next-line */
            return response(['data' => ["message" => "Customer cannot be updated."]], 400);
        }

        return $customer;
    }

    /**
     * @param int $customerId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete(int $customerId)
    {
        try {
            $customer = $this->customer->findOrFail($customerId);
        } catch (\Throwable $exception) {
            /** @phpstan-ignore-next-line */
            return response(['data' => ["message" => "Customer not exists"]], 404);
        }

        try {
            $customer->delete();
        } catch (\Throwable $exception) {
            /** @phpstan-ignore-next-line */
            return response(['data' => ["message" => "Customer cannot be deleted."]], 400);
        }
        /** @phpstan-ignore-next-line */
        return response()->json(['data' => ['message' => 'Customer deleted']]);
    }
}
