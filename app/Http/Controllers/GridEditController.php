<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;


class GridEditController extends Controller
{
    public function view()
    {
        return view('news.grid');
    }

    public function index(Request $r)
    {
        return response()->json([
            'foo' => 'bar'
        ]);
    }
}
