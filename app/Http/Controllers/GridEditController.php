<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsDetail;
use App\Models\News;
use Carbon\Carbon;
use Log;
use DateTime;
use DB;

class GridEditController extends Controller
{
    public function view()
    {
        return view('news.grid');
    }

    public function index(Request $r)
    {
        $newsColumns = ['news_details.*', 'clients.name as client_name', 'media.name as media_name', 'topics.name as topic_name', 'news.client_id'];

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

        $query = NewsDetail::with('news');
            // 'news' => function($q) {
            //     $q = $q->with('client');
            // }
        // ]);
        // $query->with('media')->with('topic');
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
        $query->leftJoin('clients', 'clients.id', '=', 'client_id');
        $query->leftJoin('media', 'media.id', '=', 'media_id');
        $query->leftJoin('topics', 'topics.id', '=', 'topic_id');
        $query->where($dateField, '>=', $fromDate);
        $query->where($dateField, '<=', $toDate);

        // Always include coyuntura news
        if ($clientId) {
            $query->where(function($q) use($clientId) {
                $q->where('client_id', '=', 100);
                $q->orWhere('client_id', '=', $clientId);
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function update(Request $r)
    {
        DB::beginTransaction();

        $result = 'failed';
        try {
            $data = $r->all();
            $newsData = [];
            foreach($data as $item) {
                if (!isset($item['news_id'])) {
                    $newsData[$item['news_id']] = [];
                }
                $newsData[$item['news_id']][] = $item;
            }

            foreach ($newsData as $key => $newsDetails) {
                print_r($newsDetails);
                $news = News::findOrFail($key);
                $news->code = $newsDetails['news']['code'];
                $news->clasification = $newsDetails['news']['clasification'];
                $news->client_id = $newsDetails['news']['client_id'];
                // $news->save();

                foreach ($newsDetails as $item) {
                    $newsDetail = NewsDetail::findOrFail($item['id']);
                    $newsDetail->media_id = $item['media_id'];
                    $newsDetail->title = $item['title'];
                    $newsDetail->measure = $item['measure'];
                    $newsDetail->cost = $item['cost'];
                    $newsDetail->topic_id = $item['topic_id'];
                    $newsDetail->tendency = $item['tendency'];
                    $newsDetail->type = $item['type'];
                    $newsDetail->section = $item['section'];
                    $newsDetail->page = $item['page'];
                    $newsDetail->source = $item['source'];
                    $newsDetail->alias = $item['alias'];
                    $newsDetail->gender = $item['gender'];
                    $newsDetail->subtitle = $item['subtitle'];
                    // $newsDetail->save();
                }
            }
            $result = 'success';
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
        }

        return response()->json([
            'status' => $result
        ]);
    }
}
