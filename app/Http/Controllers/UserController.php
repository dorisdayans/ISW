<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->whereNull('deleted_at')->latest()->paginate(10);

        return response()->json([
            'message' => 'Listado de usuarios',
            'data' => $users,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'Usuario Creado Correctamente',
            'data' => $user->load('role')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load('role'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        /*
            UpdateUserRequest: valida antes de entrar al método.
            User $user: Route Model Binding (si no existe, 404 automático).
            $user->update(...): actualiza solo campos permitidos en $fillable.
            fresh() recarga el usuario desde BD (por si cambió algo).
            load('role') devuelve el rol completo.
        */

        $user->update($request->validated());

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'data' => $user->fresh()->load('role'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete(); // Soft delete: llena deleted_at

        return response()->json([
            'message' => 'Usuario eliminado lógicamente (soft delete).',
        ], 200);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado o no está eliminado.',
            ], 404);
        }

        $user->restore();

        return response()->json([
            'message' => 'Usuario reactivado correctamente.',
            'data' => $user->fresh()->load('role'),
        ], 200);
    }
}
