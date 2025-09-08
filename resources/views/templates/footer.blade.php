@php
use Illuminate\Support\Facades\DB;

$database_name = DB::getDatabaseName();

$xhost = DB::table('information_schema.SCHEMATA')
    ->selectRaw('@@hostname as hostname')
    ->where('SCHEMA_NAME', DB::getDatabaseName())
    ->first();
@endphp

<footer class="footer">
    <div class="row">
        <div class="col pl-5">
            <div class="copyright pt-0">
                &copy; 2019 <a href="#" class="ml-1" target="_blank">Sistemas y Soluciones Integradas</a>
                <span style='margin:2px; margin-left: 10px; color:#444'>
                    DB: {{ $database_name }} - {{ $xhost->hostname }}
                    Mode API: {{ (env('API_MODE') == 'development') ? 'Desarrollo' : 'Producción' }}
                </span>
            </div>
        </div>
    </div>
</footer>
