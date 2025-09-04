<footer class="footer">
    <div class="row">
        <div class="col pl-5">
            <div class="copyright pt-0">
                &copy; 2019 <a href="#" class="ml-1" target="_blank">Sistemas y Soluciones Integradas</a>
                @php
                use App\Models\Adapter\DbBase;
                $db = DbBase::rawConnect();
                $xquery =  $db->fetchOne("SELECT DATABASE() AS database_name");

                $db = DbBase::rawConnect();
                $xhost =  $db->fetchOne("SELECT @@hostname AS hostname");
                echo "<span style='margin:2px; margin-left: 10px; color:#444'>DB: {$xquery->database_name} - {$xhost->hostname}</span>";
                @endphp
                MODO: {{ (env('APP_ENV') == 'development') ? 'Desarrollo' : 'Producción' }}
            </div>
        </div>
    </div>
</footer>