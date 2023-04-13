<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franquicia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class FranquiciaController extends Controller
{
    //

    private function deleteFile($path) {

        File::delete($path);
        
        return  ["Se ha eliminado el archivo"];
    }

    public function getSucursales() {

        $sucursales = DB::select('select id, localidad, nombre, direccion, barrio, telefono, urlIframeMap, urlGoogleMaps from franquicias');

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
            'telefono' => 'required|numeric',
            'urlIframeMap' => 'required|string|max:500',
            'urlGoogleMaps' => 'required|string|max:500'
        ]);

        $sucursal = Franquicia::create([
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


    public function updateSucursales(Request $request, $id) {
        $fields = $request->validate([
            'localidad' => 'required|string|max:50',
            'nombre' => 'required|string|max:50',
            'barrio' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|numeric',
            'urlIframeMap' => 'required|string|max:500',
            'urlGoogleMaps' => 'required|string|max:500'
        ]);

        if($request->imgSucursal) {
            $request->file('imgSucursal')->storeAs('public/sucursales/imagenes/',$id.'.webp');
        }

        $updateSucursal = Franquicia::where('id',$id)->update([
            'nombre' => $fields['nombre'],
            'localidad' => $fields['localidad'],
            'barrio' => $fields['barrio'],
            'direccion' => $fields['direccion'],
            'telefono' => $fields['telefono'],
            'urlIframeMap' => $fields['urlIframeMap'],
            'urlGoogleMaps' => $fields['urlGoogleMaps']
        ]);

        if($updateSucursal == '1') {
            return Response("Sucursal Actualizada", 201);
        } else {
            return Response("No se pudo actualizar la Sucursal", 400);
        }

    }


    public function deleteSucursalesById ($id) {

        $pathImagenFranquicia = storage_path('app/public/sucursales/imagenes/'.$id.'.webp');

        $this->deleteFile($pathImagenFranquicia);

        if (File::exists($pathImagenFranquicia)) {
            return Response(["Archivos no pudieron ser Eliminados "],400);
        }

        $sucursal = DB::delete('delete from franquicias where id = '.$id);

        if($sucursal == '1') {
            return Response("Sucursal Eliminada", 201);
        } else {
            return Response("Id de Sucursal Erronea", 400);
        }

    }
}
