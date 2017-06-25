<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsDetail;
use Carbon\Carbon;
use Log;
use DateTime;

class GridEditController extends Controller
{
    public function view()
    {
        return view('news.grid');
    }

    public function index(Request $r)
    {
        // $newsColumns = ['news.date', 'news.press_note', 'news.code', 'news.clasification', 'news_details.*'];
        $newsColumns = ['news_details.*'];

        // Log::info("Grid Edit Search:");
        // Log::info("From Date: $fromDate To Date: $toDate ClientId: $clientId");
        $query = $this->getSearchQuery($r);

        $news = $query->get($newsColumns);

        return response()->json($news);
    }

    private function getSearchQuery(Request $r) {
        $fromDate = $r->get('fromDate', false);
        $toDate = $r->get('toDate', false);
        $searchBy = $r->get('searchBy', false);
        $clientId = $r->get('clientId', false);

        $query = NewsDetail::with([
            'news' => function($q) {
                $q = $q->with('client');
            }
        ]);
        $query->with('media')->with('topic');
        $query->join('news', 'news_details.news_id', '=', 'news.id');

        $dateField = 'date';
        if($searchBy == 'created') {
            $dateField = 'news.created_at';
        }

        // By default search today's news
        $now = Carbon::now();
        $now = $now->year . '-' . $now->month . '-' . $now->day;
        if($fromDate) {
            $fromDate = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
        } else {
            $fromDate = $now;
        }
        if($toDate) {
            $toDate = DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            $toDate = $now;
        }
        $query->where($dateField, '>=', $fromDate);
        $query->where($dateField, '<=', $toDate);

        // Always include coyuntura news
        if ($clientId) {
            $query->where(function($q) use($clientId) {
                $q->where('client_id', '=', 100);
                $q->orWhere('client_id', '=', $clientId);
            });
        }

        return $query;
    }
}
