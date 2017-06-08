<?php namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\Subtitle;

class SubtitleController extends Controller
{
    public function index()
    {
        $subtitles = Subtitle::all();

        return response()->json($subtitles, 200);
    }

    public function store(Request $r)
    {
        // TODO: new
        $rules = ['subtitle'=>'required'];
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Subtitulo requerido'
            ], 400);
        }

        $subtitle = new Subtitle();
        $subtitle->subtitle = $r->get('subtitle');
        $subtitle->save();

        return response()->json($subtitle, 200);
    }
}
