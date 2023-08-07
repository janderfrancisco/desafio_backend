<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Requests\UpdateUserFormRequest;

class UserController extends Controller
{
    /**
     * Lista todos os usuários 
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response()->json($users, 200);
    }


    /**
     * Exibe um usuário
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->with('transactions')->first();
        if(!$user)
            return response()->json(['message' => 'User not found'], 404);

        return response()->json($user, 200);
    }

    
    /**
     * Cria  um usuário
     *
     * @param  \Illuminate\Http\StoreUserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserFormRequest $request)
    {
        $data = $request->validated();
        
        $user = User::create($data);

        return response()->json($user, 201);
    }


    /**
     * Atualiza  um usuário
     *
     * @param  \Illuminate\Http\UpdateUserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserFormRequest $request, $id)
    {
        $data = $request->validated();
        $user = User::where('id', $id)->first();

        if(!$user)
            return response()->json(['message' => 'User not found'], 404);
        $user->update($data);

        return response()->json($user, 200);
    }


    /**
     * Deleta  um usuário
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();

        if(!$user)
            return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }


}
