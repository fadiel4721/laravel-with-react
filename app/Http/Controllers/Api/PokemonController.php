<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PokemonController extends Controller
{
    public function index()
    {
        $pokemons = Pokemon::latest()->paginate(5);

        return new PokemonResource(true, 'List Data Pokemon', $pokemons);
    }

    public function store(Request $request)
    {
        //define validator rules
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'desc' => 'required',
                'ability' => 'required',
                'image' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]
        );
        //check jika gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/pokemons/', $image->hashName());

        //create pokemon
        $pokemon = Pokemon::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'desc' => $request->desc,
            'ability' => $request->ability,
        ]);
        return new PokemonResource(true, 'Data Pokemon Berhasil Ditambahkan!', $pokemon);
    }
    public function show($id)
    {
        $pokemon = Pokemon::find($id);
        return new PokemonResource(true, 'Detail Data Pokemon!', $pokemon);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'desc' => 'required',
            'ability' => 'required',
        ]);
        //cek jika validasi gagal

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $pokemon = Pokemon::find($id);

        //cek jika gambar kosong

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/pokemons/', $image->hashName());

            Storage::delete('public/pokemons/' . basename($pokemon->image));

            //update
            $pokemon->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'desc' => $request->desc,
                'ability' => $request->ability,
            ]);
        } else {
            $pokemon->update([
                'name' => $request->name,
                'desc' => $request->desc,
                'ability' => $request->ability,
            ]);
        }
        return new PokemonResource(true, 'Data Pokemon Berhasil Diubah!', $pokemon);
    }
    public function destroy($id)
    {
        $pokemon = Pokemon::find($id);
        //delete image 
        Storage::delete('public/pokemons/' . basename($pokemon->image));

        //delete pokemon
        $pokemon->delete();
        //return response x
        return new PokemonResource(true, 'Data Berhasil Dihapus', null);
    }
}
