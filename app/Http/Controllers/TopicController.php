<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index(Request $r)
    {
        $limit = 10; $page = $r->get('page', 1);

        $topics = Topic::orderBy('name')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($topics->all(), Topic::count(), $limit);
        return view('topics.index')
            ->with('topics', $paginator);
    }

    public function create()
    {
        $model = new Topic();
        return view('topics.edit')
            ->with('model', $model);
    }

    public function edit($id)
    {
        $topic = Topic::findOrFail($id);
        return view('topics.edit')
            ->with('model', $topic);
    }

    public function store(Request $r)
    {
        $data = $r->all();
        $topic = new Topic();
        $topic->name = $data['name'];
        $topic->description = $data['description'];
        $topic->save();

        if ($r->expectsJson()) {
            return response()->json($topic, 200);
        }

        return redirect('dashboard/topics/'. $topic->id .'/edit')
            ->with('message', 'Tema creado exitosamente');
    }

    public function update(Request $r, $id)
    {
        $data = $r->all();
        $topic = Topic::findOrFail($id);
        $topic->name = $data['name'];
        $topic->description = $data['description'];
        $topic->save();

        return redirect('dashboard/topics/' . $topic->id . '/edit')
            ->with('message', 'Tema actualizado exitosamente');
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }
}
