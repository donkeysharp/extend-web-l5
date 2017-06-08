<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\News;
use App\Models\NewsDetail;
use App\Models\Topic;
use App\Models\Media;

class ExportController extends Controller
{
    public function index()
    {
        return view('exports.index');
    }

    public function export(Request $r)
    {
        $data = [];
        if($r->get('clients')) {
            $data['clients'] = Client::all();
        }
        if($r->get('news')) {
            $data['news'] = News::all();
        }
        if($r->get('topics')) {
            $data['topics'] = Topic::all();
        }
        if($r->get('media')) {
            $data['media'] = Media::all();
        }
        if($r->get('news_details')) {
            $data['news_details'] = NewsDetail::all();
        }

        return Excel::create(Carbon::now(), function($excel) use($data) {
            if(isset($data['clients'])) {
                $excel->sheet('Clientes', function($sheet) use($data) {
                    $sheet->fromArray($data['clients']);
                });
            }
            if(isset($data['news'])) {
                $excel->sheet('Noticias', function($sheet) use($data) {
                    $sheet->fromArray($data['news']);
                });
            }
            if(isset($data['topics'])) {
                $excel->sheet('Temas', function($sheet) use($data) {
                    $sheet->fromArray($data['topics']);
                });
            }
            if(isset($data['news_details'])) {
                $excel->sheet('Detalle de Noticias', function($sheet) use($data) {
                    $sheet->fromArray($data['news_details']);
                });
            }
            if(isset($data['media'])) {
                $excel->sheet('Medios', function($sheet) use($data) {
                    $sheet->fromArray($data['media']);
                });
            }

        })->download('xls');
    }
}
