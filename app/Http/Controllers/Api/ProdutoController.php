<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Http\Requests\StoreProdutoRequest;

class ProdutoController extends Controller
{
    public function index(){
        return ProdutoResource::collection(Produto::all());
    }

    public function store(StoreProdutoRequest $request){
        Produto::create($request->validated());
        return response()->json('Produtos criados');
    }

    public function update(StoreProdutoRequest $request, Produto $produto){
        $produto->update($request->validated());
        return response()->json("Produto atualizado");
    }

    public function destroy(Produto $produto){
        $produto->delete();
        return response()->json("Produto deletado");
    }
}
