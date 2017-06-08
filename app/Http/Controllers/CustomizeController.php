<?php namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Subtitle;

class CustomizeController extends Controller
{
    public function subtitles()
    {
        $clients = Client::all()->pluck('name', 'id');
        $clients[''] = 'Seleccionar un cliente';

        return view('customization.subtitles')
            ->with('clients', $clients);
    }

    public function getSubtitlesByClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        $subtitles = Subtitle::orderBy('id')->get();
        $customSubtitles = $client->customSubtitles()->orderBy('custom_subtitles.id')->get();

        if (count($customSubtitles) === 0) {
            $customSubtitles = $subtitles;
        } else if(count($subtitles) != count($customSubtitles)) {
            $subtitleDiff = [];
            foreach ($subtitles as $sub1) {
                $subtitleExists = false;
                foreach ($customSubtitles as $sub2) {
                    if (strcmp($sub1->subtitle, $sub2->subtitle) === 0) {
                        $subtitleExists = true;
                        break;
                    }
                }
                if (!$subtitleExists) {
                    $subtitleDiff[] = $sub1;
                }
            }
            $result = [];
            for ($i = 0; $i < count($customSubtitles) - 1; $i++) {
                $result[] = $customSubtitles[$i];
            }
            for ($i = 0; $i < count($subtitleDiff); $i++) {
                $result[] = $subtitleDiff[$i];
            }
            $result[] = $customSubtitles[count($customSubtitles) - 1];
            $ids = array_pluck($result, 'id');

            DB::table('custom_subtitles')->where('client_id', '=', $clientId)->delete();
            $client->customSubtitles()->attach($ids);

            return response()->json($result, 200);
        }

        return response()->json($customSubtitles, 200);
    }

    public function saveSubtitles(Request $r, $clientId)
    {
        $ids = $r->get('subtitles');
        $client = Client::findOrFail($clientId);
        DB::table('custom_subtitles')->where('client_id', '=', $clientId)->delete();
        $client->customSubtitles()->attach($ids);

        return response()->json([
            'status' => 'ok'
        ], 200);
    }
}
