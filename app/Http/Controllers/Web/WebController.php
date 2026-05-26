<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\Gener02;
use App\Models\Notificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class WebController extends Controller
{
    public function about()
    {
        return Inertia::render('Web/About');
    }

    public function contact()
    {
        return Inertia::render('Web/Contact');
    }

    public function products()
    {
        return Inertia::render('Web/Products');
    }

    public function documentation()
    {
        $videos = [
            [
                'id' => 'registro-empresas',
                'title' => 'Registro de empresas',
                'description' => 'Guía paso a paso para registrar su empresa en el portal COMFACA En Línea.',
                'video' => '/videos/registro-empresas.mp4',
                'duration' => null,
            ],
            [
                'id' => 'afiliacion-trabajadores',
                'title' => 'Afiliación de trabajadores',
                'description' => 'Aprenda cómo afiliar a sus empleados de forma rápida y sencilla.',
                'video' => '/videos/afiliacion-trabajadores.mp4',
                'duration' => null,
            ],
            [
                'id' => 'afiliacion-beneficiario',
                'title' => 'Afiliación de beneficiarios',
                'description' => 'Tutorial para afiliar beneficiarios de empleados registrados.',
                'video' => '/videos/afiliacion-beneficiario.mp4',
                'duration' => null,
            ],
            [
                'id' => 'afiliacion-conyuge',
                'title' => 'Afiliación de compañero(a) permanente',
                'description' => 'Cómo afiliar al compañero(a) permanente de un trabajador.',
                'video' => '/videos/afiliacion-conyuge.mp4',
                'duration' => null,
            ],
        ];

        $manuales = [
            [
                'id' => 'manual-empresa',
                'title' => 'Manual para Empleadores',
                'description' => 'Guía completa para la gestión de empresa, afiliación de trabajadores y novedades.',
                'file' => '/manuales/manual-empresa.pdf',
                'size' => '2.4 MB',
                'disabled' => true,
            ],
            [
                'id' => 'manual-trabajador',
                'title' => 'Manual para Trabajadores',
                'description' => 'Información sobre afiliados, consulta de estado y servicios disponibles.',
                'file' => '/manuales/manual-trabajador.pdf',
                'size' => '1.8 MB',
                'disabled' => true,
            ],
            [
                'id' => 'manual-pensionado',
                'title' => 'Manual para Pensionados',
                'description' => 'Guía para pensionados afiliados a COMFACA y acceso a servicios.',
                'file' => '/manuales/manual-pensionado.pdf',
                'size' => '1.5 MB',
                'disabled' => true,
            ],
            [
                'id' => 'guia-rapida',
                'title' => 'Guía Rápida de Uso',
                'description' => 'Resumen de las funciones principales del portal COMFACA En Línea.',
                'file' => '/manuales/guía-afiliaciones.pdf',
                'size' => null,
                'disabled' => false,
            ],
        ];

        return Inertia::render('Web/Documentation', [
            'videos' => $videos,
            'manuales' => $manuales,
        ]);
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Enviar correo a afiliacionyregistro@comfaca.com
        Mail::to('afiliacionyregistro@comfaca.com')
            ->send(new ContactFormMail(
                $validated['name'],
                $validated['email'],
                $validated['subject'],
                $validated['message']
            ));

        // Notificar a 1 admin aleatorio (SAFI o UIS)
        $adminId = $this->getAdminUserId();
        if ($adminId) {
            Notificaciones::create([
                'titulo'  => 'Nuevo mensaje de contacto web',
                'descri'  => "Nombre: {$validated['name']}\nCorreo: {$validated['email']}\nAsunto: {$validated['subject']}",
                'user'    => $adminId,
                'estado'  => 'P',
                'result'  => null,
                'dia'     => now()->toDateString(),
                'hora'    => now()->toTimeString(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Mensaje enviado correctamente.']);
    }

    private function getAdminUserId(): ?int
    {
        $admin = Gener02::whereIn('tipfun', ['SAFI', 'UIS'])
            ->inRandomOrder()
            ->first();

        return $admin?->id;
    }
}
