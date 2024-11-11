<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

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
    $image->storeAs('public/post/', $image->hashName());

    //create post
    $post = Post::create([
        'image' => $image->hashName(),
        'title' => $request->title,
        'content' => $request->content,
    ]);
    return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
 }
}
