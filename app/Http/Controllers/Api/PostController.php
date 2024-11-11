<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
 public function index()
 {
    $posts = Post::latest()->paginate(5);

    return new PostResource(true, 'List Data Post', $posts);
 }

 public function store(Request $request)
 {
    //define validator rules
    $validator = Validator::make($request->all(), [
        'image' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title' => 'required',
        'content' => 'required',
    ]
    );
    //check jika gagal
    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }
    //upload image
    $image = $request->file('image');
    $image->storeAs('public/posts/', $image->hashName());

    //create post
    $post = Post::create([
        'image' => $image->hashName(),
        'title' => $request->title,
        'content' => $request->content,
    ]);
    return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
 }
 public function show($id){
    $post = Post::find($id);
    return new PostResource(true, 'Detail Data Post!', $post);
 }
 public function update(Request $request, $id)
 {
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'content' => 'required',
    ]);
    //cek jika validasi gagal

    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }
    $post = Post::find($id);

    //cek jika gambar kosong

    if($request->hasFile('image')){
        $image = $request->file('image');
        $image->storeAs('public/posts/', $image->hashName());

        Storage::delete('public/posts/' . basename($post->image));

        //update
        $post->update([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }else{
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }
    return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
 }
 public function destroy($id) {
    $post = Post::find($id);
    //delete image 
    Storage::delete('public/posts/'. basename($post->image));

    //delete post
    $post->delete();
    //return response x
    return new PostResource(true, 'Data Berhasil Dihapus', null);
 }
}
