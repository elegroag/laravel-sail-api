@php
use Illuminate\Support\Facades\DB;

$database_name = DB::getDatabaseName();

$xhost = DB::table('information_schema.SCHEMATA')
    ->selectRaw('@@hostname as hostname')
    ->where('SCHEMA_NAME', DB::getDatabaseName())
    ->first();
@endphp

<footer class="footer {{ $footerClass ?? '' }}">
    <div class="row">
        <div class="col pl-5">
            <div class="copyright pt-0">
                &copy; 2019 <a href="#" class="ml-1" target="_blank">Caja de Compensación Familiar del Caquetá</a>
                <span style='margin:2px; margin-left: 10px; color:#444'>
                    {{ $database_name }} 
                    Mode API: {{ (config('app.api_mode') === 'development') ? 'Desarrollo' : 'Producción' }}
                </span>
            </div>
        </div>
    </div>
</footer>
