<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::all();
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'description' => 'string',
            'image' => 'string',
        ]);
        $create = Menu::create($request->only([
            'name',
            'price',
            'description',
            'image',
        ]));
        return response()->json([
            'success' => true,
            'data' => $create
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'string',
            'price' => 'integer',
            'description' => 'string',
            'image' => 'string',
        ]);

        $menu->update($request->only([
            'name',
            'price',
            'description',
            'image',
        ]));
        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }
}
