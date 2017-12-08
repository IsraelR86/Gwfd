<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
?>
<DIV STYLE="background-color: #FFFFFF; height: 20px; padding: 0px;">
</DIV>
<DIV STYLE="background-color: #000000; height: 26px; text-align: right; color: #FFFFFF; font-family: 'Century Gothic'; vertical-align:middle;" >
	<!-- <FONT STYLE="font-size:9pt;">Síguenos en redes: </font>	
    <A HREF="https://www.facebook.com/gofwd"><IMG SRC="<?= Url::to('@web/img/Logo.png', true) ?>" ALT="FB" STYLE="vertical-align:middle; width:20px; height:20px"></A>
    <A HREF="twitter.com/gofwd"><IMG STYLE="vertical-align:middle; width:20px; height:20px; margin-right:15px;" SRC="<?= Url::to('@web/img/Logo.png', true) ?>" ALT="Twitter"></A> -->
</DIV>
<DIV STYLE="background-color: #EE5656; font-family: 'Century Gothic';">
	<BR>
	<DIV STYLE="background-color: #FFFFFF; border-radius:10px; margin: 20px; padding-right: 30px; padding-left: 30px; min-width: 510px">
		<IMG SRC="<?= Url::to('@web/img/Logo.png', true) ?>" STYLE="height:100px; width:125px; position:relative; top:30px; float: right;">
		<BR>
		<P STYLE="color: #48C0C3; font-size: 40pt;">¡OLVIDASTE TU CLAVE!</P>
		<P STYLE="text-align: center; font-size: 21pt;">Pero ya te generamos una nueva para que regreses a goFWD.</P>
		<P STYLE="font-size: 14pt;">A continuación tus nuevas credenciales:
		<BR>
		<BR>Nueva Clave: <?= $clave ?>
		<BR>
		<BR>Te sugerimos que la actualices en cuanto ingreses nuevamente desde "Mi Info". ¡Nos vemos en línea!
		<BR>
		<BR>Un abrazo,
		<BR>
		<BR><B>Familia goFWD</B>
		<BR><BR></P>
	</DIV>
	<DIV STYLE="text-align: justify; font-size:9pt; color: #FFFFFF; margin: 20px"><B>MUESTRA <a href="<?= Url::toRoute(['site/politicas'], true) ?>" target="_blank">POLITICAS PRIVACIDAD</a>:</B>
        “Este mensaje se dirige exclusivamente a su destinatario y puede contener información privilegiada o confidencial.
        Si no es Ud. el destinatario indicado, queda notificado  de que la utilización, divulgación y/o copia sin autorización
        está prohibida en virtud de la legislación vigente. Si ha recibido este mensaje por error, le rogamos que nos lo
        comunique inmediatamente por esta misma vía y proceda a su destrucción.”
	</DIV>
	<BR>
</DIV>