<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\Source;

class SourceController extends Controller
{
    public function index(Request $r)
    {
        if ($r->expectsJson()) {
            $sources = Source::all();
            return response()->json($sources);
        }

        $limit = 10; $page = $r->get('page', 1);

        $sources = Source::orderBy('source')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($sources->all(), Source::count(), $limit);
        return view('sources.index')
            ->with('sources', $paginator);
    }

    public function create()
    {
        $source = new Source();
        return view('sources.edit')
            ->with('model', $source);
    }

    public function store(Request $r)
    {
        if (!$r->get('source', false)) {
            if ($r->expectsJson()) {
                return response()->json([
                    'status' => 'Invalid source value'
                ], 400);
            }
            return redirect('dashboard/create')
                ->with('error', 'Datos inválidos');
        }
        $source = new Source();
        $source->source = $r->get('source');
        $source->description = $r->get('description', null);
        $source->save();

        if ($r->expectsJson()) {
            return response()->json($source, 200);
        }
        return redirect('dashboard/sources')
            ->with('message', 'Fuente creada exitosamente');
    }

    public function edit($id)
    {
        $source = Source::findOrFail($id);

        return view('sources.edit')
            ->with('model', $source);
    }

    public function update(Request $r, $id)
    {
        if (!$r->get('source', false)) {
            return redirect('dashboard/create')
                ->with('error', 'Datos inválidos');
        }

        $source = Source::findOrFail($id);
        $source->source = $r->get('source');
        $source->description = $r->get('description', null);
        $source->save();

        return redirect('dashboard/sources')
            ->with('message', 'Fuente actualizada exitosamente');
    }

    public function destroy($id)
    {
        $source = Source::findOrFail($id);
        $source->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

}
