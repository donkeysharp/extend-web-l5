<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function getReport(Request $r)
    {
        $client = Client::findOrFail($r->get('client_id'));
        $clientId = $client->id;
        $date = ReportGenerator::getDates($r->get('month', 1), $r->get('year', 2015));
        $clasification = $r->get('clasification', 'B');
        $from = $date[0];
        $to = $date[1];
        $pastDates = ReportGenerator::getPastDates($r->get('month', 1), $r->get('year', 2015));
        $reportGenerator = new ReportGenerator();

        $result = [];
        $result['press'] = [];
        $result['radio'] = [];
        $result['tv'] = [];

        $result['press']['Report1'] = $reportGenerator->report1($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report2'] = $reportGenerator->report2($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report3'] = $reportGenerator->report3($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report4'] = $reportGenerator->report4($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report5'] = $reportGenerator->report5($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        // $result['press']['Report8'] = $result['press']['Report3'];
        $result['press']['Report8'] = $reportGenerator->report8($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report6'] = $reportGenerator->report6($from, $to, $clientId, $clasification, ReportGenerator::PRESS);
        $result['press']['Report7'] = $reportGenerator->report7($from, $to, $clientId, $clasification, ReportGenerator::PRESS);

        $result['radio']['Report1'] = $reportGenerator->report1($from, $to, $clientId, $clasification, ReportGenerator::RADIO);
        $result['radio']['Report2'] = $reportGenerator->report2($from, $to, $clientId, $clasification, ReportGenerator::RADIO);
        $result['radio']['Report3'] = $reportGenerator->report3($from, $to, $clientId, $clasification, ReportGenerator::RADIO);
        $result['radio']['Report7'] = $reportGenerator->report7($from, $to, $clientId, $clasification, ReportGenerator::RADIO);
        $result['radio']['Report6'] = $reportGenerator->report6($from, $to, $clientId, $clasification, ReportGenerator::RADIO);

        $result['tv']['Report1'] = $reportGenerator->report1($from, $to, $clientId, $clasification, ReportGenerator::TV);
        $result['tv']['Report2'] = $reportGenerator->report2($from, $to, $clientId, $clasification, ReportGenerator::TV);
        $result['tv']['Report3'] = $reportGenerator->report3($from, $to, $clientId, $clasification, ReportGenerator::TV);
        $result['tv']['Report7'] = $reportGenerator->report7($from, $to, $clientId, $clasification, ReportGenerator::TV);
        $result['tv']['Report6'] = $reportGenerator->report6($from, $to, $clientId, $clasification, ReportGenerator::TV);

        $result['general']['GeneralReportA'] = $reportGenerator->generalReportA(
                                                            $result['press']['Report1'],
                                                            $result['radio']['Report1'],
                                                            $result['tv']['Report1']);
        $result['general']['GeneralReportB'] = $reportGenerator->generalReportB(
                                                            $result['press']['Report3'],
                                                            $result['radio']['Report3'],
                                                            $result['tv']['Report3']);
        $result['general']['GeneralReportC'] = $reportGenerator->generalReportC($pastDates, $client->id, $clasification);

        return $result;
    }

    // public function checkReport()
    // {
    //     $md5 = Input::get('md5', '');
    //     $cacheRecord = DB::table('report_cache')
    //         ->where('md5', '=', $md5)
    //         ->get(['md5']);

    //     if ($cacheRecord) {
    //         return Response::json([
    //             'reportExist' => true
    //         ]);
    //     }
    //     return Response::json([
    //         'reportExist' => false
    //     ]);
    // }

    public function exportReport(Request $r)
    {
        $data = $r->all();
        // if (isset($data['md5'])) {
        //     $data = DB::table('report_cache')
        //         ->where('md5', '=', $data['md5'])
        //         ->get()[0];
        //     $data = json_decode($data->data, true);
        //     unset($data['checksum']);
        // } else {
        //     DB::table('report_cache')->insert([
        //         'md5' => $data['checksum'],
        //         'data' => json_encode($data)
        //     ]);
        //     unset($data['checksum']);
        // }
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $imageCounter = 0;
        $filenames = [];
        foreach ($data as $key => $value) {
            $table = $value['table'];
            $image = $value['image'];
            $image2 = isset($value['image2']) ? $value['image2'] : null;
            $subtitle = isset($value['subtitle']) ? $value['subtitle'] : null;
            if ($subtitle) {
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, "<h1>$subtitle</h1>");
            }

            $tableStyle = new \PhpOffice\PhpWord\Style\Table();
            $tableStyle->setBorderSize(1);

            $wordTable = $section->addTable($tableStyle);
            for ($i = 0; $i < count($table); ++$i) {
                $wordTable->addRow();
                for ($j = 0; $j < count($table[$i]); ++$j) {
                    $wordTable->addCell(1750)->addText($table[$i][$j]);
                }
            }

            $filename = public_path() . '/image' . $imageCounter . '.png';
            $this->saveBase64Image($image, $filename);
            $section->addImage($filename);
            $filenames[] = $filename;
            if ($image2) {
                $filename = public_path() . '/image' . $imageCounter . '.1.png';
                $this->saveBase64Image($image2, $filename);
                $section->addImage($filename);
                $filenames[] = $filename;
            }
            $imageCounter++;
        }
        $phpWord->save(public_path() . '/reporte.docx');

        // Delete generated charts
        for ($i = 0; $i < count($filenames); $i++) {
            unlink($filenames[$i]);
        }
        return response()->json([
            'filename' => 'reporte.docx'
        ]);
    }

    private function saveBase64Image($image, $filename)
    {
        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        file_put_contents($filename, $image);
    }
}
