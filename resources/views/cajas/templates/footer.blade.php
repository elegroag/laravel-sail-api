<footer class="footer">
    <div class="row">
        <div class="col pl-5">
            <div class="copyright pt-0">
                &copy; 2019 <a href="#" class="ml-1" target="_blank">Sistemas y Soluciones Integradas</a>
                <?
                $db = DbBase::rawConnect();
                $xquery =  $db->fetchOne("SELECT DATABASE() AS database_name");

                $db = DbBase::rawConnect();
                $xhost =  $db->fetchOne("SELECT @@hostname AS hostname");
                echo "<span style='margin:2px; margin-left: 10px; color:#444'>DB: {$xquery['database_name']} - {$xhost['hostname']}</span>";
                ?>
            </div>
        </div>
    </div>
</footer>

<script type="text/javascript">
    function activeItemMenu(element, parent) {
        $("[data-id='" + element + "']").addClass('active');
        $('.show_' + parent).trigger('click').addClass('active');
    }
</script>

<div class="modal fade" id="capture-modal-info" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    <div class="card-header bg-secondary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h4 class="mb-0">Información</h4>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="result_info">
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>