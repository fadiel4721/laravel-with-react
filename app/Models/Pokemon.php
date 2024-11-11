<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
   use HasFactory;

   protected $fillable = [
    'name',
    'desc',
    'ability',
    'image',
   ];


protected function image(): Attribute
{
    return Attribute::make(
        get: fn($image) => url('/storage/public/pokemons/' . $image),
    );
}
}