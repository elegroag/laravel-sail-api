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
        } catch (DebugException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registerAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'tipdoc' => 'required|string|min:1',
                'razsoc' => 'required|string|min:5',
                'nit' => 'required|integer|digits_between:6,18',
                'tipsoc' => 'required|string|min:1',
                'tipper' => 'required|string|min:1',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'first_name' => 'required|string|min:3',
                'last_name' => 'required|string|min:3',
                'is_delegado' => 'required|boolean',
                'tipo' => 'required|string|min:1',
            ]);


            $data = $request->all();
            $data['calemp'] = calemp_use_tipo_value($request->input('selected_user_type'));

            $esDelegado = $request->boolean('is_delegado');

            if ($esDelegado) {
                $data['cedrep'] = $request->input('rep_documento');
                $data['repleg'] = $request->input('rep_nombre');
                $data['coddocrepleg'] = $request->input('rep_coddoc');
            } else {
                $data['cedrep'] = $request->input('documento');
                $data['repleg'] = $request->input('nombre');
                $data['coddocrepleg'] = $request->input('coddoc');
            }

            switch ($request->input('tipo')) {
                case 'E':
                    if ($request->input('is_delegado')) {
                        $request->validate([
                            'rep_nombre' => 'required|string|min:5',
                            'rep_documento' => 'required|integer|digits_between:6,18',
                            'rep_email' => 'required|email',
                            'rep_telefono' => 'required|integer|digits_between:6,10',
                            'rep_coddoc' => 'required|string|min:1',
                            'cargo' => 'required|string|min:5'
                        ]);
                    }
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

            $response = (new SignupService())->execute(
                $signupEntity,
                new RequestParam($data)
            );

            $this->db->commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'data' => $response
            ], 201);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (DebugException $e) {
            $this->db->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage()
            ], 500);
        }
    }
}
