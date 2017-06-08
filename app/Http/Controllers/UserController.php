<?php namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $r)
    {
        $limit = 10; $page = $r->get('page', 1);

        $users = User::orderBy('name')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get();

        // TODO: new
        $paginator = new Paginator($users->all(), User::count(), $limit);
        return view('users.index')
            ->with('users', $paginator);
    }

    public function create(Request $r)
    {
        $user = new User($r->old());
        return view('users.edit')
            ->with('model', $user);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit')
            ->with('model', $user);
    }

    public function store(Request $r)
    {
        $data = $r->all();
        if($data['password'] != $data['confirm']) {
            return redirect('dashboard/users/create')
                ->with('error', 'Error al crear usuario')
                ->withInput($r->except('_token', 'password', 'confirm'));
        }

        $user = new User();
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect('dashboard/users/' . $user->id . '/edit')
            ->with('message', 'Usuario creado exitosamente');
    }

    public function update(Request $r, $id)
    {
        $data = $r->all();
        if($data['password'] != $data['confirm']) {
            return redirect('dashboard/users/create')
                ->with('error', 'Error al actualizar usuario')
                ->withInput($r->except('_token', 'password', 'confirm'));
        }

        $user = User::findOrFail($id);
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect('dashboard/users/' . $user->id . '/edit')
            ->with('message', 'Usuario actualizado exitosamente');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'ok'
        ], 200);
    }
}
