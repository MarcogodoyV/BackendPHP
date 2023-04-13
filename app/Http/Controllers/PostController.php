<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function getPosts() {

        $posts = DB::select('select id, imageURL img, title title, description description, URL URL  from posts ORDER BY id DESC');

        $response = $posts;

        return Response($response,201);
    }

    public function getCantPosts($cant) {

        $posts = DB::select('select id, imageURL img, title title, description description, URL URL  from posts ORDER BY id DESC LIMIT '.$cant);

        $response = $posts;

        return Response($response,201);
    }

    public function setPost(Request $request) {
       
        $fields =$request->validate([
            'imageURL' => 'required|string',
            'title' => 'required|string|max:120',
            'description' => 'required|string|max:500',
            'URL' => 'required|string',
        ]);

        $post = Post::create([
            'imageURL' => $fields['imageURL'],
            'title' => $fields['title'],
            'description' => $fields['description'],
            'URL' => $fields['URL'],
        ]);


        // $response = [
        //     'post' => $post
        // ];

        return Response(["Se ha cargado el Post"],201);
        
    }

    public function updatePost(Request $request) {

        $fields =$request->validate([
            'id' => 'required|integer',
            'imageURL' => 'required|string',
            'title' => 'required|string|max:120',
            'description' => 'required|string|max:500',
            'URL' => 'required|string',
        ]);

        $updatePost = Post::where('id',$fields['id'])->update($request->all());

        if($updatePost == '1') {
            return Response("Post Actualizado", 200);
        } else {
            return Response("No se pudo actualizar el post", 400);
        }

        // $post = DB::update('update from post where id ='.$fields["id"]);



    }

    public function deletePostById($id) {

        $post = DB::delete('delete from posts where id = '.$id);

        if($post == '1') {
            return Response("Post Borrado", 201);
        } else {
            return Response("Id de Post Erroneo", 400);
        }

        // $response = ["Post ".$id.' deleted:' => $post];

        // return Response($response,201);
    }
}
