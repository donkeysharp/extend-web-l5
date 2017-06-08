<?php namespace App\Http\Controllers;

use DB;
use DateTime;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\News;
use App\Models\NewsDetail;
use App\Models\Media;
use App\Models\Topic;
use App\Models\Subtitle;
use App\Models\Source;
use App\Models\NewsUpload;
use App\Models\NewsUrl;


class NewsController extends Controller
{
    public function index(Request $r)
    {
        $page = $r->get('page', 1);
        $limit = 300;
        $query = $this->search($r);

        $newsColumns = ['news.date', 'news.press_note', 'news.code', 'news.clasification', 'news_details.*'];
        if ($r->get('export', false)) {
            $news = $query->get(['news_details.*']);
            return Excel::create(Carbon::now(), function($excel) use($r, $news) {
                $excel->sheet('Noticias', function($sheet) use($r, $news) {
                    $clientId = $r->get('client_id', false);
                    $client = Client::where('id', '=', $clientId)->get()->first();
                    $sheet->setAutoSize(false);
                    $sheet->getStyle('A2:S2' . $sheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                    $sheet->setHeight(2, 46);
                    $sheet->row(2, function($row) {
                        $row->setFont(['bold' => true]);
                    });
                    $sheet->cells('A2:N1', function($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('A2:N2', function($cells) {
                        $cells->setValignment('middle');
                    });
                    $sheet->mergeCells('A1:N1');

                    $sheet->row(1, ['']);
                    if ($client) {
                        $sheet->row(1, [$client->name]);
                    }

                    $sheet->setWidth('A', 5);
                    $sheet->setWidth('B', 10);
                    $sheet->setWidth('C', 17.44);
                    $sheet->setWidth('D', 5);
                    $sheet->setWidth('E', 16.11);
                    $sheet->setWidth('F', 28.34);
                    $sheet->setWidth('G', 8.58);
                    $sheet->setWidth('H', 13);
                    $sheet->setWidth('I', 20.25);
                    $sheet->setWidth('J', 10.5);
                    $sheet->setWidth('K', 9.91);
                    $sheet->setWidth('L', 12.14);
                    $sheet->setWidth('M', 12.14);
                    $sheet->setWidth('N', 12.14);
                    $sheet->setWidth('S', 12.14);
                    $sheet->row(2, [
                        'N°', 'FECHA', 'CLIENTE', 'OJO', 'MEDIO', 'TÍTULO ARTÍCULO',
                        'Pixeles/ CM. COL', 'Equivalencia Publicitaria en dólares',
                        'TEMA', 'TENDENCIA', 'TIPO', 'SECCIÓN', 'PÁG', 'CÓDIGO',
                        'FUENTE', 'TENDENCIA', 'ALIAS', 'GÉNERO', 'SUBTÍTULO'
                    ]);
                    $row = 3;
                    foreach ($news as $item) {
                        $data = [];
                        $data[] = $row-1;
                        $data[] = $item->news->date;
                        if ($item->news->client) {
                            $data[] = $item->news->client->name;
                        } else {
                            $data[] = '';
                        }
                        $data[] = $item->news->clasification;
                        if ($item->media) {
                            $data[] = $item->media->name;
                        } else {
                            $data[] = '';
                        }
                        $data[] = $item->title;
                        $data[] = $item->measure;
                        $data[] = $item->cost;
                        if ($item->topic) {
                            $data[] = $item->topic->name;
                        } else {
                            $data[] = '';
                        }
                        $tendency = '';
                        if ($item->tendency == '1') { $tendency = 'Positivo'; }
                        else if($item->tendency == '2') { $tendency = 'Negativo'; }
                        else if($item->tendency == '3') {$tendency = 'Neutro'; }
                        $data[] = $tendency;
                        $type = '';
                        if ($item->type == '1') {$type = 'Impreso'; }
                        else if ($item->type == '2') { $type = 'Digital'; }
                        else if ($item->type == '3') { $type = 'Radio'; }
                        else if ($item->type == '4') { $type = 'Televisión'; }
                        $data[] = $type;
                        $data[] = $item->section;
                        $data[] = $item->page;
                        $data[] = $item->code;
                        $data[] = $item->source;
                        $tendency = '';
                        if ($item->sourceTendency == '1') { $tendency = 'Positivo'; }
                        else if($item->sourceTendency == '2') { $tendency = 'Negativo'; }
                        else if($item->sourceTendency == '3') {$tendency = 'Neutro'; }
                        $data[] = $tendency;
                        $data[] = $item->alias;
                        $data[] = $item->gender;
                        $data[] = $item->subtitle;
                        $sheet->row($row++, $data);
                    }
                });
            })->export('xls');
        }
        if (!$r->get('q', false)) {
            $query->skip($limit * ($page - 1))
                        ->take($limit);
        }
        $news = $query->get($newsColumns);

        // TODO: new
        $paginator = new Paginator($news->all(), NewsDetail::count(), $limit);
        $clients = Client::where('id', '<>', 100)->get()->pluck('name', 'id');
        $media = Media::all()->pluck('name', 'id');
        $media[''] = '--- Seleccione un medio ---';
        $clients[''] = '--- Seleccione un cliente ---';


        return view('news.index')
            ->with('news', $paginator)
            ->with('model', new SearchQuery($r->all()))
            ->with('clients', $clients)
            ->with('media', $media);
    }

    private function search(Request $r)
    {
        $detailsData = [];
        $detailsData['mediaType'] = $r->get('mediaType', false);
        $detailsData['mediaId'] = $r->get('media_id', false);
        $detailsData['title'] = $r->get('title', false);
        $detailsData['tendency'] = $r->get('tendency', false);
        $detailsData['source'] = $r->get('source', false);
        $detailsData['gender'] = $r->get('gender', false);
        $detailsData['show'] = $r->get('show', false);
        $detailsData['description'] = $r->get('description', false);

        $fromDate = $r->get('fromDate', false);
        $toDate = $r->get('toDate', false);
        $searchBy = $r->get('searchBy', false);
        $clientId = $r->get('client_id', false);

        $query = NewsDetail::with([
            'news' => function($q) {
                $q = $q->with('client');
            }
        ]);
        $query->with('media')->with('topic');
        if($detailsData['mediaType']) {
            $query->where('type', '=', $detailsData['mediaType']);
        }
        if($detailsData['mediaId']) {
            $query->where('media_id', '=', $detailsData['mediaId']);
        }
        if($detailsData['title']) {
            $query->where('title', 'like', '%' . $detailsData['title'] .'%');
        }
        if($detailsData['tendency']) {
            $query->where('tendency', '=', $detailsData['tendency']);
        }
        if($detailsData['source']) {
            $query->where('source', 'like', '%'.$detailsData['source'].'%');
        }
        if($detailsData['gender']) {
            $query->where('gender', 'like', '%'.$detailsData['gender'].'%');
        }
        if($detailsData['show']) {
            $query->where('show', 'like', '%'.$detailsData['show'].'%');
        }
        if($detailsData['description']) {
            $query->where('description', 'like', '%'.$detailsData['description'].'%');
        }
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

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $news = News::with('details')->findOrFail($id);

        return response()->json($news, 200);
    }

    public function view($id)
    {
        $news = News::with([
            'details' => function($q) {
                $q->with('media')->with('topic');
            },
            'client'
        ])->findOrFail($id);
        // return $news;
        return view('news.view')
            ->with('news', $news);
    }

    public function extra(Request $r)
    {
        $clients = [];
        $topics = [];
        $media = [];
        $subtitles = [];
        $sources = [];
        if ($r->get('clients')) {
            $clients = Client::orderBy('name')->get()->all();
        }
        if ($r->get('topics')) {
            $topics = Topic::orderBy('name')->get()->all();
        }
        if ($r->get('media')) {
            $media = Media::orderBy('name')->get()->all();
        }
        if ($r->get('subtitles')) {
            $subtitles = Subtitle::orderBy('subtitle')->get()->all();
        }
        if ($r->get('sources')) {
            $sources = Source::orderBy('source')->get()->all();
        }

        return response()->json([
            'clients' => $clients,
            'topics' => $topics,
            'media' => $media,
            'subtitles' => $subtitles,
            'sources' => $sources,
        ], 200);
    }

    public function create()
    {
        return view('news.edit')
            ->with('id', null);
    }

    public function store(Request $r)
    {
        $data = $r->all();
        $news = new News();
        $news->client_id = $data['client_id'];
        $news->date = DateTime::createFromFormat('d/m/Y', $data['date']);
        $news->press_note = $data['press_note'];
        $news->clasification = $data['clasification'];
        $news->code = $data['code'];
        $news->save();

        return response()->json($news, 200);
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);

        return view('news.edit')
            ->with('id', $news->id);
    }

    public function update(Request $r, $id)
    {
        DB::beginTransaction();
        try {
            $data = $r->all();
            $news = News::findOrFail($id);
            $news->client_id = $data['client_id'];
            $news->date = DateTime::createFromFormat('d/m/Y', $data['date']);
            $news->press_note = $data['press_note'];
            $news->clasification = $data['clasification'];
            $news->code = $data['code'];
            $news->save();

            $detailsIds = $this->getDetailsIds($data);
            $data = $this->getNewsDetailInstances($data, $id);

            $newsDetails = NewsDetail::whereIn('id', $detailsIds)->get();
            foreach($data as $item2) {
                if (!isset($item2['id'])) {
                    $newsDetail = new NewsDetail($item2);
                    $newsDetail->news_id = $id;
                    $newsDetail->save();
                    continue;
                }

                foreach($newsDetails as $item) {
                    if ($item->id == $item2['id']) {
                        $item->fill($item2);
                        $item->save();
                        break;
                    }
                }
            }
        } catch(Exception $e) {
            DB::rollback();
            throw new Exception($e);
        }
        DB::commit();

        $news->details = $newsDetails;
        return response()->json($news, 200);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function copyNews($id, $clientId)
    {
        // It gives an extra 'date2' column to avoid
        // date parsing as News model accessor changes date format
        $news = News::with('details')
            ->with('urls')
            ->with('uploads')
            ->select(['news.*', 'date as date2'])
            ->findOrFail($id);

        $copiedObject = new News();
        DB::beginTransaction();
        try {
            $copiedObject->press_note = $news->press_note;
            $copiedObject->code = $news->code;
            $copiedObject->clasification = $news->clasification;
            $copiedObject->date = $news->date2;
            $copiedObject->client_id = $clientId;
            $copiedObject->save();

            foreach ($news->details as $detail) {
                $copiedDetail = new NewsDetail();
                $copiedDetail->type = $detail->type;
                $copiedDetail->title = $detail->title;
                $copiedDetail->description = $detail->description;
                $copiedDetail->tendency = $detail->tendency;
                $copiedDetail->section = $detail->section;
                $copiedDetail->page = $detail->page;
                $copiedDetail->gender = $detail->gender;
                $copiedDetail->web = $detail->web;
                $copiedDetail->source = $detail->source;
                $copiedDetail->alias = $detail->alias;
                $copiedDetail->measure = $detail->measure;
                $copiedDetail->cost = $detail->cost;
                $copiedDetail->subtitle = $detail->subtitle;
                $copiedDetail->communication_risk = $detail->communication_risk;
                $copiedDetail->show = $detail->show;
                $copiedDetail->news_id = $copiedObject->id;
                $copiedDetail->topic_id = $detail->topic_id;
                $copiedDetail->media_id = $detail->media_id;
                $copiedObject->extra_title = $detail->extra_title;
                $copiedObject->observations = $detail->observations;

                $copiedDetail->save();
            }

            foreach ($news->uploads as $upload) {
                $copiedUpload = new NewsUpload();
                $copiedUpload->type = $upload->type;
                $copiedUpload->file_name = $upload->file_name;
                $copiedUpload->news_id = $copiedObject->id;
                $copiedUpload->save();
            }

            foreach ($news->urls as $url) {
                $copiedUrl = new NewsUrl();
                $copiedUrl->url = $url->url;
                $copiedUrl->news_id = $copiedObject->id;
                $copiedUrl->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getMessage()
            ], 500);
        }

        return response()->json($copiedObject, 200);

    }

    public function destroyDetail($id, $detailId)
    {
        $news = News::findOrFail($id);
        $newsDetail = NewsDetail::findOrFail($detailId);
        $newsDetail->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function getUploads($id)
    {
        $news = News::findOrFail($id);
        $uploads = $news->uploads()->get();
        return response()->json($uploads);
    }

    public function upload(Request $r, $id)
    {
        $news = News::findOrFail($id);

        $isNewsFooter = $r->get('newsFooter', false) ? true : false;

        $file = $r->file('file');
        // TODO: new
        $extension = File::extension($file->getClientOriginalName());
        $directory = public_path() . '/uploads';
        $filename =  parent::generateGUID() . '.' . $extension;

        $upload_success = $r->file('file')->move($directory, $filename);

        $upload = new NewsUpload();
        $upload->type = $extension;
        $upload->news_id = $id;
        $upload->file_name = $filename;
        $upload->news_footer = $isNewsFooter;
        $upload->save();

        return response()->json($upload, 200);
    }

    public function getURLS($id)
    {
        $news = News::findOrFail($id);
        $urls = $news->urls()->get();
        return response()->json($urls);
    }

    public function addURL(Request $r, $id)
    {
        $news = News::findOrFail($id);
        $url = new NewsUrl();
        $url->url = $r->get('url');
        $url->news_id = $id;
        $url->save();

        return response()->json($url, 200);
    }

    public function destroyUpload($id, $uploadId)
    {
        $news = News::findOrFail($id);
        $upload = NewsUpload::where('news_id', '=', $id)
            ->where('id', '=', $uploadId)
            ->get()->first();

        if ($upload) {
            $upload->delete();
            return response()->json([
                'status' => 'ok'
            ], 200);
        }

        return response()->json([
            'status' => 'Upload not found'
        ], 404);
    }

    public function destroyUrl($id, $urlId)
    {
        $news = News::findOrFail($id);
        $url = NewsUrl::where('news_id', '=', $id)
            ->where('id', '=', $urlId)
            ->get()->first();

        if ($url) {
            $url->delete();
            return response()->json([
                'status' => 'ok'
            ], 200);
        }
        return response()->json([
            'status' => 'URL not found'
        ], 404);
    }

    private function getDetailsIds($data)
    {
        $ids = [];
        if ($detailData = $data['media']['printed']) {
            if(isset($detailData['id'])) {
                $ids[] = $detailData['id'];
            }
        }
        if ($detailData = $data['media']['digital']) {
            if(isset($detailData['id'])) {
                $ids[] = $detailData['id'];
            }
        }
        if ($detailData = $data['media']['radio']) {
            if(isset($detailData['id'])) {
                $ids[] = $detailData['id'];
            }
        }
        if ($detailData = $data['media']['tv']) {
            if(isset($detailData['id'])) {
                $ids[] = $detailData['id'];
            }
        }
        if ($detailData = $data['media']['source']) {
            if(isset($detailData['id'])) {
                $ids[] = $detailData['id'];
            }
        }
        return $ids;
    }

    private function getNewsDetailInstances($data, $newsId)
    {
        $result = [];
        if ($detailData = $data['media']['printed']) {
            $result[] = $detailData;
        }
        if ($detailData = $data['media']['digital']) {
            $result[] = $detailData;
        }
        if ($detailData = $data['media']['radio']) {
            $result[] = $detailData;
        }
        if ($detailData = $data['media']['tv']) {
            $result[] = $detailData;
        }
        if ($detailData = $data['media']['source']) {
            $result[] = $detailData;
        }

        return $result;
    }
}
