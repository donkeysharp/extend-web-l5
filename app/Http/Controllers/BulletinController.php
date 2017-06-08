<?php namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\Bulletin;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Subtitle;


class BulletinController extends Controller
{
    public function index(Request $r)
    {
        $limit = 30; $page = $r->get('page', 1);

        $bulletins = Bulletin::orderBy('created_at', 'desc')
            ->with('client')
            ->with('details')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($bulletins->all(), Bulletin::count(), $limit);
        return view('bulletins.index')
            ->with('bulletins', $paginator);
    }

    public function newsOrder($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        // $subtitles = DB::table('news_details')->distinct()->get(['subtitle']);
        $details = $bulletin->details()
            ->with(['news' => function($q) {
                $q->with('client');
                $q->with('urls')->with('uploads');
        },])->with('media')
            ->orderBy('news_order')
            ->get();

        $clientId = $bulletin->client_id;
        $client = Client::findOrFail($clientId);
        $subtitles = $client->customSubtitles()->orderBy('custom_subtitles.id')->get();
        if (count($subtitles) === 0) {
            $subtitles = Subtitle::orderBy('id')->get();
        }

        return view('bulletins.order')
            ->with('bulletinId', $bulletin->id)
            ->with('date', Carbon::now())
            ->with('details', $details)
            ->with('subtitles', $subtitles)
            ->with('client', $client);
    }

    public function saveNewsOrder(Request $r, $id)
    {

        $bulletin = Bulletin::findOrFail($id);
        $details = $bulletin->details()->get();

        $inputDetails = $r->get('details', []);
        $inputDetails = $this->parseDetails($inputDetails, $details);
        $bulletin->details()->sync($inputDetails);

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function sendToClients($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        // $subtitles = DB::table('news_details')->distinct()->get(['subtitle']);
        $details = $bulletin->details()->with(['news' => function($q) {
            $q->with('client');
            $q->with('urls')->with('uploads');
        }])->with('media')
            ->orderBy('news_order')
            ->get();
        $clientId = $bulletin->client_id;
        $client = Client::findOrFail($clientId);

        $subtitles = $client->customSubtitles()->orderBy('custom_subtitles.id')->get();
        if (count($subtitles) === 0) {
            $subtitles = Subtitle::orderBy('id')->get();
        }
        $info = [
            'date' => Carbon::now(),
            'details' => $details,
            'subtitles'=>$subtitles,
            'client' => $client
        ];

        $contacts = Contact::where('client_id', '=', $clientId)->get();
        // TODO: new
        Mail::send('bulletins.templates.mosaic', $info, function($message) use($contacts) {
            foreach($contacts as $contact) {
                $message = $message->to($contact->email, $contact->name);
            }
            $message->subject('Boletín Extend');
        });

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function sendToTestClient($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        // $subtitles = DB::table('news_details')->distinct()->get(['subtitle']);
        $details = $bulletin->details()->with(['news' => function($q) {
            $q->with('client');
            $q->with('urls')->with('uploads');
        }])->with('media')
            ->orderBy('news_order')
            ->get();
        $clientId = $bulletin->client_id;
        $client = Client::findOrFail($clientId);
        $subtitles = $client->customSubtitles()->orderBy('custom_subtitles.id')->get();
        if (count($subtitles) === 0) {
            $subtitles = Subtitle::orderBy('id')->get();
        }
        $info = [
            'date' => Carbon::now(),
            'details' => $details,
            'subtitles'=>$subtitles,
            'client' => $client
        ];

        $clientId = 100;
        $contacts = Contact::where('client_id', '=', $clientId)->get();
        if(count($contacts) === 0) {
            return response()->json([
                'status' => 'The client does not have contacts'
            ], 400);
        }
        // TODO: new
        Mail::send('bulletins.templates.mosaic', $info, function($message) use($contacts) {
            foreach($contacts as $contact) {
                $message = $message->to($contact->email, $contact->name);
            }
            $message->subject('Boletín Extend');
        });

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function publicDisplay($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        // $subtitles = DB::table('news_details')->distinct()->get(['subtitle']);
        $details = $bulletin->details()
            ->with(['news' => function($q) {
                $q->with('client');
                $q->with('urls')->with('uploads');
            },])->with('media')
                ->orderBy('news_order', 'asc')
                ->get();

        $clientId = $bulletin->client_id;
        $client = Client::findOrFail($clientId);
        $subtitles = $client->customSubtitles()->orderBy('custom_subtitles.id')->get();
        if (count($subtitles) === 0) {
            $subtitles = Subtitle::orderBy('id')->get();
        }

        return view('bulletins.templates.mosaic')
            ->with('date', Carbon::now())
            ->with('details', $details)
            ->with('subtitles', $subtitles)
            ->with('client', $client);
    }

    public function store(Request $r)
    {
        $hasSelected = false;
        $data = $r->all();
        $details = [];
        foreach($data as $key => $value) {
            if (strcmp(substr($key, 0, 15), 'news_detail_id_') !== 0) { continue; }
            $hasSelected = true;
            $details[] = $value;
        }
        if(!$hasSelected) {
            return redirect('/dashboard/news')
                ->with('error', 'Se debe elegir al menos una noticia.');
        }
        if(!$r->get('client_id', false)) {
            return redirect('/dashboard/news')
                ->with('error', 'Cliente no especificado.');
        }

        $bulletin = null;
        DB::beginTransaction();
        try {
            $bulletin = new Bulletin();
            $bulletin->client_id = $r->get('client_id');
            $bulletin->save();

            if($hasSelected) {
                $bulletin->details()->attach($details);
            }

        } catch(Exception $e) {
            DB::rollback();
            return $details;
        }
        DB::commit();

        return redirect('dashboard/bulletins/' . $bulletin->id . '/order')
            ->with('message', 'Boletín creado exitosamente');
    }

    public function destroy($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        $bulletin->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    private function parseDetails(array $inputDetails, $details)
    {
        $result = [];
        foreach ($inputDetails as $inputDetail) {
            foreach ($details as $detail) {
                if ($inputDetail['news_id'] == $detail->id) {
                    $result[$inputDetail['news_id']] = [
                        'news_order' => $inputDetail['news_order']
                    ];
                    break;
                }
            }
        }

        return $result;
    }
}
