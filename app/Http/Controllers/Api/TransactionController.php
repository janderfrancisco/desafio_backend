<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\ExternalServices\Authorizer;
use App\ExternalServices\Notifier;

class TransactionController extends Controller
{

    private $authorizer;
    private $notifier;

    public function __construct(Authorizer $authorizer, Notifier $notifier)
    {
        $this->authorizer = $authorizer;
        $this->notifier = $notifier;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function transfer(StoreTransactionRequest $request)
    {
        $data = $request->validated();




    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function reverse(UpdateTransactionRequest $request)
    {
        //
    }

}
