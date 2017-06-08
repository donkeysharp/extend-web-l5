<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Client;

class Bulletin extends Model
{
    protected $table = 'bulletins';

    public function details()
    {
        return $this->belongsToMany('App\Models\NewsDetail', 'bulletin_news_detail');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
