@php
$rutaImg = base_path("public/img/Mercurio/logob.png");
$rutaImg = "http://186.119.116.228:8091/Mercurio/public/img/Mercurio/logob.png";
@endphp

<div style='padding:0px;margin:0px'>
    <table width='100%' bgcolor='#EEEEEE' cellpadding='0' cellspacing='0' border='0'>
        <tbody>
            <tr>
                <td align='center' style='font-family:Helvetica,Arial;padding:0px'>
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' style='width:100%;max-width:690px'>
                        <tbody>
                            <tr>
                                <td style='padding:0px'>
                                    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                        <tbody>
                                            <tr>
                                                <td style='background: white;'>

                                                    <img style='display:block;border:none' src='{{ $rutaImg }}'
                                                        width='30%' height='' title='Sistemas Y Solucuiones Integradas'
                                                        alt='Sistemas y Soluciones Integradas' class='CToWUd'>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td bgcolor='#FFFFFF'
                                                    style='padding:20px 20px 0;border: none;border-top:none;border-bottom:none'>
                                                    <div
                                                        style='font-family:Helvetica,Arial;font-size:22px;line-height:32px;color:#00638a'>
                                                        &nbsp;</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td bgcolor='#FFFFFF'
                                                    style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>
                                                    <div
                                                        style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>
                                                        <table align='center' width='100%' border='0'>
                                                            <tr>
                                                                <td bgcolor='#FFFFFF'
                                                                    style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>
                                                                    <div
                                                                        style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>
                                                                        " . $msj . "</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style='padding:0px;background:#fff;border:1px solid #e1e1e1;border-top:none;border-bottom:none'>
                                </td>
                            </tr>
                            <tr>
                                <td valign='middle'
                                    style='padding:21px;background:#f5f5f5;border:1px solid #e1e1e1;border-top:1px solid #eee;border-bottom:1px solid #eeeeee;font-family:Helvetica,Arial;font-size:14px;font-style:italic;line-height:20px;color:#787878'>
                                    {$mercurio02->getRazsoc()} <br />Direccion: {$mercurio02->getDireccion()}
                                    <br />Email: {$mercurio02->getEmail()} <br />Telefono:
                                    {$mercurio02->getTelefono()}<br /><br /> Website: <a
                                        style='font-family:Helvetica,Arial;font-size:14px;line-height:20px;color:#478eae;text-decoration:none'
                                        href='http://{$mercurio02->getPagweb()}'
                                        target='_blank'>{$mercurio02->getPagweb()}</a>.
                                </td>
                            </tr>
                            <tr>
                                <td valign='middle'>
                                    <div style='background:#373737;border:1px solid #e1e1e1;border-top:none'>
                                        <table width='100%' cellpadding='0' cellspacing='0' border='0'
                                            style='padding:0 20px'>
                                            <tbody>
                                                <tr>
                                                    <td height='50' valign='middle' align='left'
                                                        style='font-family:Helvetica,Arial;font-size:11px;color:#8e8e8e'>
                                                        Mercurio - Sistemas y Soluciones Integradas S.A.S - 2019</td>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
