<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        Log::info('Incoming request:', $request->all());

        // TODO: Сделать правильную валидацию

        try {
            $validated = $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|unique:users,email',
                    'password' => 'required|string|min:8'
                ],
                [
                    'name.required' => 'Заполните имя',
                    'email.required' => 'Не заполнено поле email',
                    'email.unique' => 'Пользователь с данным email уже существует',
                    'password.required' => 'Не заполнено поле пароль',
                    'password.min' => 'Пароль должен содержать минимум 8 символов',
                ]
            );

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);
            return response()->json($user, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation errors:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id); // Удалить пользователя
        return response()->json(null, 204); // Ответ без данных
    }
}
