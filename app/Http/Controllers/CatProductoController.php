<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\catProducto;

class CatProductoController extends Controller
{
    public function getCatProductos() {

        $catProductos = DB::select('select id, nombreCatProducto from cat_productos ORDER BY id DESC');

        $response = $catProductos;

        return Response($response,201);
    }

    public function setCatProducto(Request $request) {

        $fields =$request->validate([
            'nombreCatProducto' => 'required|string'
        ]);

        $response = catProducto::create([
            'nombreCatProducto' => $fields['nombreCatProducto']
        ]);

        return Response('Se ha creado la categoria',201);
    }

    public function updateCatProducto($id, Request $request) {

        $request->validate([
            'nombreCatProducto' => 'required'
        ]);

        $catProducto = catProducto::find($id);

        $catProducto->nombreCatProducto = $request['nombreCatProducto'];

        $catProducto->save();


        return Response("Se ha actualizado la categoria",201);
    }

    public function deleteCatProductoByCat($id) {

        catProducto::find($id)->delete();

        return Response("Categoria Eliminada",201);
    }
}
