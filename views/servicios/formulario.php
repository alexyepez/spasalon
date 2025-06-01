<div class="campo">
    <label for="nombre">Nombre</label>
    <input
        type="text"
        id="nombre"
        placeholder="Nombre Servicio"
        name="nombre"
        value="<?php echo $servicio->nombre; ?>"
    />
</div>

<div class="campo">
    <label for="precio">Precio</label>
    <input
        type="number"
        id="precio"
        placeholder="Precio Servicio"
        name="precio"
        value="<?php
        // Verificamos múltiples condiciones para decidir si mostrar el valor o una cadena vacía
        echo (isset($servicio->precio) &&
            $servicio->precio !== '' &&
            $servicio->precio !== '0' &&
            $servicio->precio !== 0)
            ? $servicio->precio : '';
        ?>"
    />
</div>

<div class="campo">
    <label for="descripción">Descripción</label>
    <input
        type="text"
        id="descripción"
        placeholder="Descripción Servicio"
        name="descripcion"
        value="<?php echo $servicio->descripcion; ?>"
    />
</div>