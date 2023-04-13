<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Http\Controllers\AuthController;

use function PHPSTORM_META\map;

class BannerController extends Controller
{
    
    private function saveId ($id) {
        Storage::put('public/ids.txt',$id);
        return Storage::get('public/ids.txt');
    }

    private function deleteFile($path) {

        File::delete($path);
        
        return  ["message" => "Se ha eliminado el carousel"];
    }

    private function saveBanner ($request){

        

        $userRole = auth()->user()->getRoleNames()->first();

        if($userRole == 'Admin'){

            $fields =$request->validate([
                'type' => 'required|string',
                'banner' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
            ]);

            if($fields['type'] == 'modal') {

                $path = storage_path('public/banner/'.$fields['type'].'.webp');
                $this->deleteFile($path);
                $request->file('banner')->storeAs('public/banner/',$fields['type'].'.webp');

            } elseif ($fields['type'] == 'carousel') {
                $fields =$request->validate([ 
                    'number' => 'required|integer',
                    'bannerMovil' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
                ]);
                // $path = storage_path('app/public/banner/'.Str::random(8).'.webp');
                // $request->file('banner')->storeAs('public/banner/',count(Storage::files('public/banner/')).Str::random(3).'.webp');
                // $request->file('banner')->storeAs('public/banner/','carousel'.count(Storage::files('public/banner/')).'.webp');
                $request->file('banner')->storeAs('public/banner/','carousel'.$fields['number'].'.webp');
                $request->file('bannerMovil')->storeAs('public/banner/','movil'.$fields['number'].'.webp');

            } else {
                return Response(["Especifique un tipo válido", 200]);
            }

            
            
            // $request->file('banner')->storeAs('app/public/banner/'.$path.'.webp');

            return Response(["Se ha guardado con exito"],200);

        }else {
            return Response(["No tiene permisos para realizar esta operación"],400);
        }
    } 

    public function getBanner ($name) {

        // $path;

        // if($name == 'modal') {

        //     $path = storage_path('app/public/'.$name.'/' .''.$name.'.webp');

        // } elseif($name == 'carousel') {

        //     $path = storage_path('app/public/'.$name.'/' .''.$name.''.$id.'.webp');

        // }else {
        //     return ["message" => "Tipo no especificado ".$name];
        // }

        $path = storage_path('app/public/banner/'.$name.'.webp');

        if (!File::exists($path)) {
            return Response("No se ha encontrado ningun banner",400);
        } else{

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }

    }

    public function getBannerModal () {
        return ($this->getBanner('modal'));
    }

    public function getBannersCarousel () {

        $originalFiles = Storage::files('public/banner/');

        $carouselFiles = array_values(array_filter($originalFiles,function ($elementArr)
        {
            if(str_contains($elementArr,'carousel')){ 
                return $elementArr;
            }
        },0));

        return Response(array_map(function ($element) {
            return substr($element,14,-5);
        },$carouselFiles),200);
    }

    public function getBannersCarouselMovil () {

        $originalFiles = Storage::files('public/banner/');

        $carouselFiles = array_values(array_filter($originalFiles,function ($elementArr)
        {
            if(str_contains($elementArr,'movil')){ 
                return $elementArr;
            }
        },0));

        return Response(array_map(function ($element) {
            return substr($element,14,-5);
        },$carouselFiles),200);
    }

    public function getBannerCarouselByName($name) {
        return ($this->getBanner($name));
    }

    public function setBanner (Request $request) {
        // return [substr(Storage::files('public/carousel/')[0],24,-5)];
        //TODO si solo esta el banner 2 creado y no existe archivo Ids, asignar Id 1
        // $id = count(Storage::files('public/carousel/')) + 1;
        // if(substr(Storage::files('public/carousel/')[0],24,-5) == $id) {
        //     $id += 1;
        // }
        // if(File::exists(storage_path('app/public/ids.txt'))){
        //     $avaliableIds =  explode(",",Storage::get('public/ids.txt'));
        //     rsort($avaliableIds);
        //     if(count($avaliableIds) > 1) {
        //         $id = array_pop($avaliableIds);
        //         Storage::put('public/ids.txt',implode(",",$avaliableIds));
        //     }
        // }

        return ($this->saveBanner($request));

    }

    public function updateBanner (Request $request) {
        return ($this->saveBanner($request));
    }

    // public function updateBannerById(Request $request,$id) {

    //     $nBanners = count(Storage::files('public/carousel/'));

    //     if($id <= $nBanners && $id !== null && $request['type'] != 'modal') {
    //         return ($this->saveBanner($request,$id));
    //     }
    //         return ["message" => "Banner carousel no existe (o intenta especificar id en modal), solo existen ".$nBanners." banners, especificar banner a modificar en URL(/bannerCarousel/{id})"];
    // }

    public function deleteBanner(Request $request) {
        $fields =$request->validate([
            'type' => 'required|string',
        ]);

        $path = storage_path('app/public/'.$fields['type'].'/'.$fields['type'].'.webp');
        $this->deleteFile($path);

        return  ["message" => "Se ha eliminado el ".$fields['type']];

    }

    public function deleteBannerCarouselById ($id) {

        // $avaliableIds = array();

        // $fields =$request->validate([
        //     'type' => 'required|string',
        //     'id' => 'required|integer',
        // ]);

        // if(File::exists(storage_path('app/public/ids.txt'))){
        //     $avaliableIds =  explode(",",Storage::get('public/ids.txt'));
        //     array_push($avaliableIds,$id);
        //     Storage::put('public/ids.txt',implode(",",$avaliableIds));
        // } else {
        //     array_push($avaliableIds,$id);
        //     Storage::put('public/ids.txt',implode(",",$avaliableIds));
        // }
        
        
        $pathDesktop = storage_path('app/public/banner/carousel'.$id.'.webp');
        if (!File::exists($pathDesktop)) {
            return Response(["Archivos no pudieron ser borrados"],400);
        }
        
        $pathMovil = storage_path('app/public/banner/movil'.$id.'.webp');
        if (!File::exists($pathMovil)) {
            return Response(["Archivos no pudieron ser borrados"],400);
        }
        $this->deleteFile($pathDesktop);
        $this->deleteFile($pathMovil);
        
        return Response(["Archivos borrados"],200);

        // if($fields['type'] == 'carousel') {
            
        //     $path = storage_path('app/public/banner/carousel'.$fields['id'].'.webp');
        //     return [$this->deleteFile($path)];

        // } else {
        //     return ["No es posible eliminar un modal especificando el id, por favor usar la URL sin Id."];
        // }

    }
    
}
