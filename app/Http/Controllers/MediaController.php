<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\Media;

class MediaController extends Controller
{
    public function index(Request $r)
    {
        $limit = 10; $page = $r->get('page', 1);

        $media = Media::orderBy('name')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($media->all(), Media::count(), $limit);
        return view('media.index')
            ->with('media', $paginator);
    }

    public function create()
    {
        $model = new Media();
        $types = [
            '' => '--- Seleccione un tipo ---',
            '1' => 'Impreso',
            '2' => 'Digital',
            '3' => 'Radio',
            '4' => 'TV',
            '5' => 'Fuente'
        ];
        $cities = [
            '' => '-- Seleccione una ciudad --',
            'Beni' => 'Beni',
            'Cochabamba' => 'Cochabamba',
            'La Paz' => 'La Paz',
            'Oruro' => 'Oruro',
            'Pando' => 'Pando',
            'Potosi' => 'Potosí',
            'Santa Cruz' => 'Santa Cruz',
            'Sucre' => 'Sucre',
            'Tarija' => 'Tarija'
        ];
        return view('media.edit')
            ->with('model', $model)
            ->with('types', $types)
            ->with('cities', $cities);
    }

    public function edit($id)
    {
        $model = Media::findOrFail($id);
        $types = [
            '' => '--- Seleccione un tipo ---',
            '1' => 'Impreso',
            '2' => 'Digital',
            '3' => 'Radio',
            '4' => 'TV',
            '5' => 'Fuente'
        ];
        $cities = [
            '' => '-- Seleccione una ciudad --',
            'Beni' => 'Beni',
            'Cochabamba' => 'Cochabamba',
            'La Paz' => 'La Paz',
            'Oruro' => 'Oruro',
            'Pando' => 'Pando',
            'Potosi' => 'Potosí',
            'Santa Cruz' => 'Santa Cruz',
            'Sucre' => 'Sucre',
            'Tarija' => 'Tarija'
        ];
        return view('media.edit')
            ->with('model', $model)
            ->with('types', $types)
            ->with('cities', $cities);
    }

    public function store(Request $r)
    {
        $data = $r->all();
        $media = new Media();
        $media->name = $data['name'];
        $media->type = $data['type'];
        $media->description = $data['description'];
        $media->city = $data['city'];
        $media->website = $data['website'];
        $media->save();

        if ($r->expectsJson()) {
            return response()->json($media, 200);
        }

        return redirect('dashboard/media/' . $media->id . '/edit')
            ->with('message', 'Medio creado exitosamente');
    }

    public function update(Request $r, $id)
    {
        $data = $r->all();
        $media = Media::findOrFail($id);
        $media->name = $data['name'];
        $media->type = $data['type'];
        $media->description = $data['description'];
        $media->city = $data['city'];
        $media->website = $data['website'];
        $media->save();

        return redirect('dashboard/media/' . $media->id . '/edit')
            ->with('message', 'Medio actualizado exitosamente');
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        $media->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }
}
