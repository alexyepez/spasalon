<?php

// Mostrar precio con descuento si el cliente tiene membresía activa
function mostrarPrecioCliente($precio, $cliente = null) {
    if (!$cliente || !method_exists($cliente, 'getMembresiaActiva') || !$cliente->getMembresiaActiva()) {
        return '$' . number_format($precio, 2);
    }

    $membresiaActiva = $cliente->getMembresiaActiva();
    if (!$membresiaActiva) {
        return '$' . number_format($precio, 2);
    }

    $membresia = Membresia::find($membresiaActiva->membresia_id);
    if (!$membresia || !$membresia->descuento) {
        return '$' . number_format($precio, 2);
    }

    $precioConDescuento = $precio - ($precio * $membresia->descuento / 100);

    return '<span class="precio-original">$' . number_format($precio, 2) . '</span> ' .
        '<span class="precio-descuento">$' . number_format($precioConDescuento, 2) . '</span> ' .
        '<span class="etiqueta-descuento">(' . $membresia->descuento . '% descuento)</span>';
}