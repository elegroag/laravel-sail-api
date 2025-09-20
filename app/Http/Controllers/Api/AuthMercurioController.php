<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Autentications\AutenticaService;
use Illuminate\Validation\ValidationException;
use App\Services\Request as RequestParam;

class AuthMercurioController extends Controller
{

    public function authenticateAction(Request $request)
    {
        try {
            $request->validate([
                'documentType' => 'required|string|min:1',
                'identification' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'selectedUserType' => 'required|string|min:2'
            ]);

            $autenticaService = new AutenticaService();
            $response = $autenticaService->execute(
                new RequestParam([
                    'tipo' => $request->documentType,
                    'documento' => $request->identification,
                    'password' => $request->password,
                    'selectedUserType' => $request->selectedUserType
                ])
            );

            return response()->json([
                'success' => true,
                'message' => 'Validación exitosa',
                'data' => $response
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage()
            ], 500);
        }
    }
}
