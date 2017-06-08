<?php namespace App\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;

class Html
{
    public static function mediaType($type)
    {
        if ($type == 1) {
            return 'Impreso';
        } else if($type == 2) {
            return 'Digital';
        } else if($type == 3) {
            return 'Radio';
        } else if($type == '4') {
            return 'TV';
        } else if($type == 5) {
            return 'Fuente';
        }
        return $type;
    }

    public static function tendency($type)
    {
        if ($type == 1) {
            return 'Positiva';
        } else if($type == 2) {
            return 'Negativa';
        } else if($type == 3) {
            return 'Neutra';
        }
        return $type;
    }

    public static function paginator(Paginator $paginator, $url, $query=false)
    {
        $page = $paginator->currentPage();
        $total = $paginator->total();
        $limit = $paginator->perPage();
        $items = $paginator->items();
        $lastPage = $paginator->lastPage();

        $template = '';
        $template .= '<ul class="pagination">';
        $template .= '<li ';
        if ($page === 1) {
            $template .= 'class="disabled"';
        }
        if (!$query) {
            $template .= "><a href=\"$url?page=1\">&laquo;</a></li>";
        } else {
            $template .= "><a href=\"$url&page=1\">&laquo;</a></li>";
        }

        for($i = 1; $i <= $lastPage; $i++) {
            $template .= "<li ";
            if ($i === $page) {
                $template .= 'class="active"';
            }
            if (!$query) {
                $template .= "><a href=\"$url?page=$i\">$i</a></li>";
            } else {
                $template .= "><a href=\"$url&page=$i\">$i</a></li>";
            }
        }
        $template .= "<li ";
        if ($page == $lastPage) {
            $template .= 'class="disabled"';
        }
        if (!$query){
            $template .= "><a href=\"$url?page=$lastPage\">»</a></li>";
        } else {
            $template .= "><a href=\"$url&page=$lastPage\">»</a></li>";
        }
        $template .= "</ul>";

        return $template;
    }

    public static function literalDate($date)
    {
        $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        if (!$date) {
            $date = Carbon\Carbon::now();
        }

        $res = '';
        $res .= $date->day . ' de ' . $months[$date->month - 1] . ' de ' . $date->year;
        return $res;
    }
}
