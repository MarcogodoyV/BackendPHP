<?php

namespace App\Http\Controllers;

use App\Models\producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ProductoController extends Controller
{

    private function deleteFile($path) {

        File::delete($path);
        
        return  ["Se ha eliminado el archivo"];
    }

    public function getProductos() {

        

        $productos = DB::select('select productos.id, productos.nombre,  productos.descripcion , cat_productos.nombreCatProducto, productos.catProductos_id from productos INNER JOIN cat_productos ON productos.catProductos_id= cat_productos.id ORDER BY id DESC');

        // $ArrayCategorias = array();

        $productosAgrupados = array();

        try {
            
            foreach($productos as $producto)
            { 
                $productosAgrupados[$producto->nombreCatProducto][] = $producto;
            }
                
            // array_push($ArrayCategorias,$productosAgrupados);

            $response = $productosAgrupados;

        }

        catch (Exception $e){
            $response = '$e';
        }

        

        return Response($response,201);
    }

    public function getImagenProducto($name) {

        $formatName = strtolower($name);

        $path = storage_path('app/public/productos/imagenes/'.$formatName.'.webp');

        if (!File::exists($path)) {
            return Response("No se ha encontrado imagen relacionada al producto",400);
        } else{

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }
    }

    public function getImagenInfoNutricionalProducto($name) {

        $formatName = strtolower($name);

        $path = storage_path('app/public/productos/imagennutricional/'.$formatName.'.webp');

        if (!File::exists($path)) {
            return Response("No se ha encontrado imagen relacionada al producto",400);
        } else{

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }
    }

    public function getInfoNutricionalProducto($name) {

        $formatName = strtolower($name);

        $path = storage_path('app/public/productos/infoNutricional/'.$formatName.'.webp');

        if (!File::exists($path)) {
            return Response("No se ha encontrado imagen relacionada al producto",400);
        } else{

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }
    }

    public function setProducto(Request $request) {

        // return $request;

        $fields = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:255',
            'imgProducto' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
            'catProductos_id' => 'required|integer',
            'infoNutricional' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
            'imagenNutricional' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048'
        ]);

        // $pathImgProducto = storage_path('public/productos/'.Str::random(32).'.webp');
        // File::delete($path);

        
        
        //Guardo solo los nombres en la BBDD, luego cuando haga el call obtengo el nombre
        // y hago search para devolver las dos imagenes
        $producto = producto::create([
            'nombre' => $fields['nombre'],
            'descripcion' => $fields['descripcion'],
            'catProductos_id' => $fields['catProductos_id']
        ]);
        
        // $formatName = join("",explode(" ",strtolower($fields['nombre'])));

        $request->file('imgProducto')->storeAs('public/productos/imagenes/',$producto['id'].'.webp');
        $request->file('infoNutricional')->storeAs('public/productos/infoNutricional/',$producto['id'].'.webp');
        $request->file('imagenNutricional')->storeAs('public/productos/imagennutricional/',$producto['id'].'.webp');

        return Response("Se ha creado el producto",201);
    }

    public function updateProducto(Request $request, $id) {


        if($request->imgProducto) {
            $fields = $request->validate(['imgProducto' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048']);
            $request->file('imgProducto')->storeAs('public/productos/imagenes/',$id.'.webp');
        }
        if($request->infoNutricional){
            $fields = $request->validate(['infoNutricional' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048']);
            $request->file('infoNutricional')->storeAs('public/productos/infoNutricional/',$id.'.webp');
        }

        if($request->imagenNutricional){
            $fields = $request->validate(['imagenNutricional' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048']);
            $request->file('imagenNutricional')->storeAs('public/productos/imagenNutricional/',$id.'.webp');
        }

        $fields = $request->validate([
            'nombre' => 'required|string|max:255',
            'catProductos_id' => 'required|integer',
        ]);
    

        $updateProducto = producto::where('id',$id)->update([
            'nombre' => $fields['nombre'],
            'catProductos_id' => $fields['catProductos_id']
        ]);

        if($updateProducto == '1') {
            return Response("Producto Actualizado", 201);
        } else {
            return Response("No se pudo actualizar el Producto", 400);
        }
    }

    public function deleteProductoById($id) {

        $product = DB::delete('delete from productos where id = '.$id);
        $pathImagenProducto = storage_path('app/public/productos/imagenes/'.$id.'.webp');
        $pathInfoNutricionalProducto = storage_path('app/public/productos/infoNutricional/'.$id.'.webp');
        $pathImagenNutricionalProducto = storage_path('app/public/productos/imagenNutricional/'.$id.'.webp');
        
        $this->deleteFile($pathImagenProducto);
        $this->deleteFile($pathInfoNutricionalProducto);
        $this->deleteFile($pathImagenNutricionalProducto);

        if (File::exists($pathImagenProducto)) {
            return Response(["Archivos no pudieron ser Eliminados "],400);
        }
        
        if (File::exists($pathInfoNutricionalProducto)) {
            return Response(["Archivos no pudieron ser Eliminados"],400);
        }
        

        if($product == '1') {
            return Response("Producto Eliminado", 201);
        } else {
            return Response("Id de Producto Erroneo", 400);
        }
    }
}
