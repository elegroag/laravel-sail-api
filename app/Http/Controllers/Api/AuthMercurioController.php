<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DebugException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Adapter\DbBase;
use App\Services\Autentications\AutenticaService;
use Illuminate\Validation\ValidationException;
use App\Services\Request as RequestParam;
use App\Services\Signup\SignupDomestico;
use App\Services\Signup\SignupEmpresas;
use App\Services\Signup\SignupFacultativos;
use App\Services\Signup\SignupIndependientes;
use App\Services\Signup\SignupPensionados;
use App\Services\Signup\SignupService;

class AuthMercurioController extends Controller
{
    private $db;
    public function __construct()
    {
        $this->db = DbBase::rawConnect();
    }

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
                    'coddoc' => $request->documentType,
                    'documento' => $request->identification,
                    'password' => $request->password,
                    'tipo' => $request->selectedUserType
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

    public function registerAction(Request $request)
    {
        try {
            $this->db->begin();
            $signupService = new SignupService();
            $data = $request->all();
            $data['calemp'] = calemp_use_tipo_value($request->input('selected_user_type'));

            switch ($request->input('tipo')) {
                case 'E':
                    $signupEntity = new SignupEmpresas();
                    break;
                case 'I':
                    $signupEntity = new SignupIndependientes();
                    break;
                case 'F':
                    $signupEntity = new SignupFacultativos();
                    break;
                case 'O':
                    $signupEntity = new SignupPensionados();
                    break;
                case 'S':
                    $signupEntity = new SignupDomestico();
                    break;
                case 'P':
                case 'T':
                    $signupEntity = null;
                    break;
                default:
                    throw new DebugException("Error el tipo de afiliación es requerido", 1);
                    break;
            }

            $response = $signupService->execute(
                $signupEntity,
                new RequestParam($data)
            );

            $this->db->commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'data' => $response
            ], 201);
        } catch (DebugException $e) {
            $this->db->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage()
            ], 500);
        }
    }
}
