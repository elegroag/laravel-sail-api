<?php
echo View::getContent();
Tag::addJavascript('core/global');
echo TagUser::help($title, $help);
echo Tag::addJavascript('Cajas/consulta');
echo $html;
