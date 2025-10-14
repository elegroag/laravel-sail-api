@foreach ($mercurio37 as $item)
<div class='col-md-4 mb-2 show-adjuntos'>
    <button class='btn-icon btn-block btn-outline-default' 
        type='button' 
        data-toggle='adjunto' 
        data-cid='{{$id}}' 
        data-file='{{$item->archivo}}'  
        data-coddoc='{{$item->coddoc}}' 
        >
            <span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>
            <span class='btn-inner--text'>{{$item->detalle}}</span>
    </button>
</div>
@endforeach