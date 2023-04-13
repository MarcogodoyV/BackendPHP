<?php

namespace App\Http\Controllers;

use App\Models\Franquicias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class SucursalesController extends Controller
{
    //

    public function getSucursales() {

        $sucursales = DB::select('select id, localidad, nombre, direccion, barrio, telefono, urlIframeMap, urlGoogleMaps from sucursales');

        return Response($sucursales,201);   

    }

    public function getImagenSucursal($id) {


        $formatName = strtolower($id);

        $path = storage_path('app/public/sucursales/imagenes/'.$formatName.'.webp');

        if (!File::exists($path)) {
            return Response("No se ha encontrado imagen relacionada a la sucursal",400);
        } else{

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }

    }

    public function setSucursales(Request $request){
        $fields = $request->validate([
            'localidad' => 'required|string|max:50',
            'nombre' => 'required|string|max:50',
            'barrio' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|integer',
            'urlIframeMap' => 'required|string|max:500',
            'urlGoogleMaps' => 'required|string|max:500'
        ]);

        $sucursal = Franquicias::create([
            'nombre' => $fields['nombre'],
            'localidad' => $fields['localidad'],
            'barrio' => $fields['barrio'],
            'direccion' => $fields['direccion'],
            'telefono' => $fields['telefono'],
            'urlIframeMap' => $fields['urlIframeMap'],
            'urlGoogleMaps' => $fields['urlGoogleMaps']
        ]);

        $request->file('imgSucursal')->storeAs('public/sucursales/imagenes/',$sucursal['id'].'.webp');

        return Response('Se ha creado la Sucursal',201);

    }
}
