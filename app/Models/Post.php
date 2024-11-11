<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
   use HasFactory;

   protected $fillable = [
    'title',
    'content',
    'image',
   ];


protected function image(): Attribute
{
    return Attribute::make(
        get: fn($image) => url('/storage/public/posts/' . $image),
    );
}
}