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
    <A HREF="https://www.facebook.com/glamnowapp"><IMG SRC="<?= Url::to('@web/img/Logo.png', true) ?>" ALT="FB" STYLE="vertical-align:middle; width:20px; height:20px"></A>
    <A HREF="twitter.com/glamnowapp"><IMG STYLE="vertical-align:middle; width:20px; height:20px; margin-right:15px;" SRC="<?= Url::to('@web/img/Logo.png', true) ?>" ALT="Twitter"></A> -->
</DIV>
<DIV STYLE="background-color: #EE5656; font-family: 'Century Gothic';">
	<BR>
		<DIV STYLE="background-color: #FFFFFF; border-radius:10px; margin: 20px; padding-right: 30px; padding-left: 30px; min-width: 510px">
		<table style="margin: 0 auto;">
			<tr>
				<td>
					<IMG SRC="<?= Url::to('@web/img/Logo.png', true) ?>" STYLE="height:100px; width:125px; position:relative; top:30px; float: left;">
				</td>
				<td>
					<IMG SRC="<?= Url::to('@web/img/Logo-Certamen.png', true) ?>" STYLE="height:105px; width:330px; position:relative; top:30px; float: center;">
				</td>
				<td>
					<IMG SRC="<?= Url::to('@web/img/FESE-logo.png', true) ?>" STYLE="height:105px; width:330px; position:relative; top:30px; float: right;">
				</td>
			</tr>
		</table>
		<BR>
		<P STYLE="color: #48C0C3; font-size: 40pt;">¡Hay un nuevo concurso!</P>
		<P STYLE="text-align: center; font-size: 21pt;">Nos encanta que te hayas unido a la primer plataforma de talento emprendedor.  Estaremos ofertando constantemente Concursos para Emprendedores como tú, para que tus grandes ideas se pongan en práctica.  Recuerda que mientras más completo tengas el portafolio de tus proyectos, mejores portunidades tendrás de ser seleccionado en los muchos concursos que se gestionarán en esta plataforma.  Agrega el video de tu marca, tu logotipo, tu imágen, tus redes sociales... todo vale.  En tus manos tienes la herramienta más cómoda para obtener recursos y dar difusión a tu nueva empresa.</P>
		<P STYLE="font-size: 14pt;">Si tienes dudas de las políticas de privacidad, te compartimos las nuestras.  Y puedes comunicarte con nosotros enviándonos un correo a hola@gofwd.mx.</P>
		<P STYLE="font-size: 14pt;">
		<BR>
		<BR>
		<BR>Un abrazo, y vamos adelante.
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
