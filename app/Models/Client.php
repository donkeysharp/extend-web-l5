<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function contacts()
    {
        return $this->hasMany('App\Models\Contact');
    }

    public function customSubtitles()
    {
        return $this->belongsToMany('App\Models\Subtitle', 'custom_subtitles');
    }
}
