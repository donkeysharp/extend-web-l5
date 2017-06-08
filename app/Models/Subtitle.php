<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Subtitle extends Model
{
    protected $table = 'subtitles';
    protected $hidden = ['updated_at', 'created_at'];
}
