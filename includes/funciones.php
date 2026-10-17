<?php
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function limpiar($v) { return trim(strip_tags((string)$v)); }

// Genera un código único de 6 caracteres hexadecimales (ej: A7F294)
function generarCodigo(mysqli $conn): string {
    do {
        $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $st = $conn->prepare("SELECT id FROM empleados WHERE codigo_acceso = ?");
        $st->bind_param('s', $codigo);
        $st->execute();
        $existe = $st->get_result()->num_rows > 0;
        $st->close();
    } while ($existe);
    return $codigo;
}

// Calcula horas trabajadas en formato H:MM
function horasTrabajadas($entrada, $salida): string {
    if (!$entrada || !$salida) return '-';
    $seg = strtotime($salida) - strtotime($entrada);
    if ($seg < 0) return '-';
    return floor($seg / 3600) . ':' . str_pad(floor(($seg % 3600) / 60), 2, '0', STR_PAD_LEFT);
}

function fechaCR($f) { return $f ? date('d/m/Y', strtotime($f)) : '-'; }
function hora24($h) { return $h ? substr($h, 0, 5) : '-'; }