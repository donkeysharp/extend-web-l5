<?php namespace App\Http\Controllers;

use DB;
use App\Models\Client;


class ReportGenerator
{
    const PRESS = 1;
    const PRINTED = 1;
    const DIGITAL = 2;
    const RADIO  = 3;
    const TV = 4;
    const SOURCE = 5;

    public function report1($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $query = DB::table('media as m')
            ->join('news_details as nd', 'm.id', '=', 'nd.media_id')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $query = $this->getExtraQuery($query, $filterByMedia, $clasification);
        $result = $query->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('m.name')
            ->orderBy('m.name')
            ->select(DB::raw('m.name, count(nd.id) as news'))
            ->get();

        return $result;
    }

    public function report2($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $query = DB::table('topics as t')
            ->join('news_details as nd', 't.id', '=', 'nd.topic_id')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $query = $this->getExtraQuery($query, $filterByMedia, $clasification);
        $result = $query->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('t.name')
            ->orderBy('t.name')
            ->select(DB::raw('t.name, count(nd.id) as news'))
            ->get();

        return $result;
    }

    public function report3($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $q1 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q1 = $this->getExtraQuery($q1, $filterByMedia, $clasification);
        $positiveNews = $q1->where('nd.tendency', '=', 1)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as positive'))
            ->first();

        $q2 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q2 = $this->getExtraQuery($q2, $filterByMedia, $clasification);
        $negativeNews = $q2->where('nd.tendency', '=', 2)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as negative'))
            ->first();

        $q3 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q3 = $this->getExtraQuery($q3, $filterByMedia, $clasification);
        $neutralNews = $q3->where('nd.tendency', '=', 3)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as neutral'))
            ->first();

        $client = Client::findOrFail($clientId);

        return [
            'client' => $client->name,
            'positive' => $positiveNews->positive,
            'negative' => $negativeNews->negative,
            'neutral' => $neutralNews->neutral,
        ];
    }

    public function report4($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $query = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $query = $this->getExtraQuery($query, $filterByMedia, $clasification);
        $result = $query->whereRaw('ifnull(length(nd.gender), 0) > 0')
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('nd.gender')
            ->select(DB::raw('nd.gender, count(nd.gender) as news'))
            ->get();

        return$result;
    }

    public function report5($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $query = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', $clientId);
        $query = $this->getExtraQuery($query, $filterByMedia, $clasification);
        $result = $query->whereRaw('ifnull(length(nd.source), 0) > 0')
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('nd.source')
            ->select(DB::raw('nd.source, count(nd.source) as news'))
            ->get();

        return$result;
    }

    public function report6($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $q1 = DB::table('media as m')
            ->join('news_details as nd', 'm.id','=','nd.media_id')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q1 = $this->getExtraQuery($q1, $filterByMedia, $clasification);
        $positiveNews = $q1->where('tendency', '=', 1)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('m.name')
            ->orderBy('m.name')
            ->select(DB::raw('m.name, count(nd.id) as positive'))
            ->get();

        $q2 = DB::table('media as m')
            ->join('news_details as nd', 'm.id','=','nd.media_id')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q2 = $this->getExtraQuery($q2, $filterByMedia, $clasification);
        $negativeNews = $q2->where('tendency', '=', 2)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('m.name')
            ->orderBy('m.name')
            ->select(DB::raw('m.name, count(nd.id) as negative'))
            ->get();

        $q3 = DB::table('media as m')
            ->join('news_details as nd', 'm.id','=','nd.media_id')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q3 = $this->getExtraQuery($q3, $filterByMedia, $clasification);
        $neutralNews = $q3->where('tendency', '=', 3)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('m.name')
            ->orderBy('m.name')
            ->select(DB::raw('m.name, count(nd.id) as neutral'))
            ->get();

        $result = [];
        foreach($positiveNews as $news) {
            if (!isset($result[$news->name])) {
                $result[$news->name] = [];
                $result[$news->name]['positive'] = $news->positive;
                $result[$news->name]['negative'] = '0';
                $result[$news->name]['neutral'] = '0';
            } else {
                $result[$news->name]['positive'] = $news->positive;
            }
        }
        foreach($negativeNews as $news) {
            if (!isset($result[$news->name])) {
                $result[$news->name] = [];
                $result[$news->name]['positive'] = '0';
                $result[$news->name]['negative'] = $news->negative;
                $result[$news->name]['neutral'] = '0';
            } else {
                $result[$news->name]['negative'] = $news->negative;
            }
        }
        foreach($neutralNews as $news) {
            if (!isset($result[$news->name])) {
                $result[$news->name] = [];
                $result[$news->name]['positive'] = '0';
                $result[$news->name]['negative'] = '0';
                $result[$news->name]['neutral'] = $news->neutral;
            } else {
                $result[$news->name]['neutral'] = $news->neutral;
            }
        }

        return $result;
    }

    public function report7($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $q1 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q1 = $this->getExtraQuery($q1, $filterByMedia, $clasification);
        $positiveNews = $q1->where('nd.tendency', '=', 1)
            ->whereRaw('not isnull(nd.source) and length(nd.source) > 0')
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('nd.source', 'nd.alias')
            ->select(DB::raw('nd.source, nd.alias, count(nd.tendency) as positive'))
            ->get();

        $q2 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q2 = $this->getExtraQuery($q2, $filterByMedia, $clasification);
        $negativeNews = $q2->where('nd.tendency', '=', 2)
            ->whereRaw('not isnull(nd.source) and length(nd.source) > 0')
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('nd.source')
            ->select(DB::raw('nd.source, nd.alias, count(nd.tendency) as negative'))
            ->get();

        $q3 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q3 = $this->getExtraQuery($q3, $filterByMedia, $clasification);
        $neutralNews = $q3->where('nd.tendency', '=', 3)
            ->whereRaw('not isnull(nd.source) and length(nd.source) > 0')
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->groupBy('nd.source')
            ->select(DB::raw('nd.source, nd.alias, count(nd.tendency) as neutral'))
            ->get();

        $result = [];
        foreach($positiveNews as $news) {
            if (!isset($result[$news->source])) {
                $result[$news->source] = [];
                $result[$news->source]['alias'] = $news->alias?: '';
                $result[$news->source]['positive'] = $news->positive;
                $result[$news->source]['negative'] = '0';
                $result[$news->source]['neutral'] = '0';
            } else {
                $result[$news->source]['positive'] = $news->positive;
            }
        }
        foreach($negativeNews as $news) {
            if (!isset($result[$news->source])) {
                $result[$news->source] = [];
                $result[$news->source]['alias'] = $news->alias?: '';
                $result[$news->source]['positive'] = '0';
                $result[$news->source]['negative'] = $news->negative;
                $result[$news->source]['neutral'] = '0';
            } else {
                $result[$news->source]['negative'] = $news->negative;
            }
        }
        foreach($neutralNews as $news) {
            if (!isset($result[$news->source])) {
                $result[$news->source] = [];
                $result[$news->source]['alias'] = $news->alias?: '';
                $result[$news->source]['positive'] = '0';
                $result[$news->source]['negative'] = '0';
                $result[$news->source]['neutral'] = $news->neutral;
            } else {
                $result[$news->source]['neutral'] = $news->neutral;
            }
        }

        return $result;
    }

    public function report8($from, $to, $clientId, $clasification, $filterByMedia)
    {
        $q1 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q1 = $this->getExtraQuery($q1, $filterByMedia, $clasification);
        $positiveNews = $q1->where('nd.sourceTendency', '=', 1)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as positive'))
            ->first();

        $q2 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q2 = $this->getExtraQuery($q2, $filterByMedia, $clasification);
        $negativeNews = $q2->where('nd.sourceTendency', '=', 2)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as negative'))
            ->first();

        $q3 = DB::table('news_details as nd')
            ->join('news as n', 'n.id', '=', 'nd.news_id')
            ->where('n.client_id', '=', $clientId);
        $q3 = $this->getExtraQuery($q3, $filterByMedia, $clasification);
        $neutralNews = $q3->where('nd.sourceTendency', '=', 3)
            ->where('n.date', '>=', $from)
            ->where('n.date', '<=', $to)
            ->select(DB::raw('count(nd.id) as neutral'))
            ->first();

        $client = Client::findOrFail($clientId);

        return [
            'client' => $client->name,
            'positive' => $positiveNews->positive,
            'negative' => $negativeNews->negative,
            'neutral' => $neutralNews->neutral,
        ];
    }

    public function generalReportA($pressReport1, $radioReport1, $tvReport1)
    {
        $result = [];
        $pressTotal = $radioTotal = $tvTotal = 0;
        foreach ($pressReport1 as $item) {
            $pressTotal += $item->news;
        }
        foreach ($radioReport1 as $item) {
            $radioTotal += $item->news;
        }
        foreach ($tvReport1 as $item) {
            $tvTotal += $item->news;
        }

        $result = [
            'press' => $pressTotal,
            'radio' => $radioTotal,
            'tv' => $tvTotal
        ];
        return $result;
    }

    public function generalReportB($pressReport3, $radioReport3, $tvReport3)
    {
        $result = [];
        $result['press'] = [
            'positive' => $pressReport3['positive'],
            'negative' => $pressReport3['negative'],
            'neutral' => $pressReport3['neutral']
        ];
        $result['radio'] = [
            'positive' => $radioReport3['positive'] ?: '0',
            'negative' => $radioReport3['negative'] ?: '0',
            'neutral' => $radioReport3['neutral'] ?: '0'
        ];
        $result['tv'] = [
            'positive' => $tvReport3['positive'] ?: '0',
            'negative' => $tvReport3['negative'] ?: '0',
            'neutral' => $tvReport3['neutral'] ?: '0'
        ];
        return $result;
    }

    public function getMonthReport($from, $to, $clientId, $clasification)
    {
        $result = $this->report3($from, $to, $clientId, $clasification, false);
        return $result;
    }

    public function generalReportC($dates, $clientId, $clasification)
    {
        $report1 = $this->getMonthReport($dates[0][0], $dates[0][1], $clientId, $clasification);
        $report2 = $this->getMonthReport($dates[1][0], $dates[1][1], $clientId, $clasification);
        $report3 = $this->getMonthReport($dates[2][0], $dates[2][1], $clientId, $clasification);

        $result = [
            $dates[0][0] => $report1,
            $dates[1][0] => $report2,
            $dates[2][0] => $report3,
        ];

        return $result;
    }

    private function getExtraQuery($query, $mediaType, $clasification)
    {
        if ($mediaType == self::PRINTED || $mediaType == self::DIGITAL) {
            $query->where(function($q)
            {
                $q->where('nd.type', '=', 1)
                    ->orWhere('nd.type', '=', 2);
            });
        } else if ($mediaType == self::RADIO) {
            $query->where('nd.type', '=', 3);
        } else if ($mediaType == self::TV) {
            $query->where('nd.type', '=', 4);
        } else if ($mediaType == self::SOURCE) {
            $query->where('nd.type', '=', 5);
        } else if ($mediaType == false) {
            $query->where('nd.type', '<>', 5);
        }

        if ($clasification == 'A') {
            $query->where('n.clasification', '=', 'A');
        } else if ($clasification == 'B') {
            $query->where(function($q) {
                $q->where('n.clasification', '=', 'B');
                // $q->orWhere('n.clasification', '=', 'C');
            });
        }

        return $query;
    }

    public static function getDates($month, $year)
    {
        if ($month <= 0 || $month > 12) {
            return [$year . '-01-01', $year . '01-31'];
        }
        if ($month === 2) {
            if ($year % 4 == 0) {
                return [$year . '-02-01', $year .'-02-29'];
            }
            return [$year . '-02-01', $year . '-02-28'];
        }

        $lastDay = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        return [$year . '-' . $month . '-01', $year . '-' . $month . '-' . $lastDay[$month]];
    }

    public static function getPastDates($month, $year)
    {
        $date3 = static::getDates($month, $year);
        $month--;
        if ($month == 0) {
            $month = 12;
            $year--;
        }
        $date2 = static::getDates($month, $year);
        $month--;
        if ($month == 0) {
            $month = 12;
            $year--;
        }
        $date1 = static::getDates($month, $year);
        return [$date1, $date2, $date3];
    }
}
