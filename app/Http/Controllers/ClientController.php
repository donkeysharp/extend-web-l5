<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\Client;
use App\Models\Contact;

class ClientController extends Controller
{
    public function index(Request $r)
    {
        $limit = 10; $page = $r->get('page', 1);

        $clients = Client::orderBy('name')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($clients->all(), Client::count(), $limit);
        return view('clients.index')
            ->with('clients', $paginator);
    }

    public function indexJson()
    {
        $clients = Client::orderBy('name')->get()->all();

        return response()->json($clients, 200);
    }

    public function create()
    {
        $model = new Client();
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
        return view('clients.edit')
            ->with('model', $model)
            ->with('cities', $cities);
    }

    public function edit($id)
    {
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
        $model = Client::with('contacts')->findOrFail($id);
        return view('clients.edit')
            ->with('model', $model)
            ->with('cities', $cities);
    }

    public function store(Request $r)
    {
        $data = $r->all();
        $client = new Client();
        $client->name = $data['name'];
        $client->phone = $data['phone'];
        $client->description = $data['description'];
        $client->address = $data['address'];
        $client->city = $data['city'];
        $client->website = $data['website'];
        $client->save();

        if ($r->expectsJson()) {
            return response()->json($client, 200);
        }
        return redirect('/dashboard/clients/' . $client->id . '/edit')
            ->with('message', 'Cliente creado exitosamente.');
    }

    public function update(Request $r, $id)
    {
        $data = $r->all();
        $client = Client::findOrFail($id);
        $client->name = $data['name'];
        $client->phone = $data['phone'];
        $client->description = $data['description'];
        $client->address = $data['address'];
        $client->city = $data['city'];
        $client->website = $data['website'];
        $client->save();

        return redirect('/dashboard/clients/' . $client->id . '/edit')
            ->with('message', 'Cliente actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function storeContact(Request $r, $id)
    {
        $data = $r->all();
        $client = Client::findOrFail($id);
        $contact = new Contact();
        $contact->name = $data['name'];
        $contact->email = $data['email'];
        $contact->position = $data['position'];
        $contact->phone = $data['phone'];
        $contact->client_id = $id;
        $contact->save();

        return redirect('/dashboard/clients/' . $client->id . '/edit')
            ->with('message', 'Contacto adicionado exitosamente.');
    }

}
