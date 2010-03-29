<table>
    <tbody>
        <tr>
            <td id="logotipo">
                <a href="<?php echo PROY_URL ?>">
                    <img src="IMG/logo.gif" alt="Logotipo BCA"/>
                </a>
            </td>
	    <td id="centro">
		<!--<a href=""><img src="IMG/portada/superior_central.jpg" alt="<?php echo PROY_NOMBRE; ?>" title="<?php echo PROY_NOMBRE; ?>"/></a>!-->
	    </td>
            <td id="telefonos">
                    <p class="medio-oculto">
                        <span>ESA: (503) 2243-6017</span><br />
                        <?php if (!S_iniciado()) { ?>
                        <a href="<?php echo PROY_URL ?>inicio" title="Iniciar sesión">Iniciar sesión</a>
                        <?php } else { ?>
                        <a href="<?php echo PROY_URL ?>fin" title="Cerrar sesión">Cerrar sesión</a>
                        <?php } ?>
                    </p>
            </td>
    </tbody>
</table>
