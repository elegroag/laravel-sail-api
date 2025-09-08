const buscarAplicados = function(){
    $.get(Utils.getKumbiaURL($Kumbia.controller+"/integracion_servicio")).done(function(response)
    {
        if(response.success) {
            if(response.data.fuera_servicio == false)
            {
                swal.fire({
                    title: "Notificación",
                    html: "<p class='text-left' style='font-size:1rem'>"+response.data.msj+'</p>',
                    showCloseButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Continuar'
                }).then(function(e) {
                    if (e.value === true) 
                    {
                        setTimeout(function(){
                            window.location.href = Utils.getKumbiaURL("principal/index");
                        }, 100);
                    }
                });
            }else{
                return false;
            }
        }
    }).fail(function(err){
        console.log(err.responseText);
        return false;
    });
}

$(document).ready(function()
{
    buscarAplicados();
    setInterval(function(){
        buscarAplicados();
    }, 10000);
});