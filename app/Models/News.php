<?php namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;


class News extends Model
{
    protected $table = 'news';
    protected $hidden = ['created_at', 'updated_at', 'subtitle'];

    public function details()
    {
        return $this->hasMany('App\Models\NewsDetail');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function urls()
    {
        return $this->hasMany('App\Models\NewsUrl');
    }

    public function uploads()
    {
        return $this->hasMany('App\Models\NewsUpload');
    }

    public function getDateAttribute($value)
    {
        if(gettype($value) === 'string') {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        }
        return $value;
    }
}
