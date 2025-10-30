<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Empleado
 *
 * @property int $id_empleado
 * @property string $nombre
 * @property string|null $departamento
 * @property string|null $puesto
 * @property string $salario_base
 * @property string|null $bonificacion
 * @property string|null $descuento
 * @property \Illuminate\Support\Carbon|null $fecha_contratacion
 * @property \Illuminate\Support\Carbon|null $fecha_nacimiento
 * @property string|null $sexo
 * @property string $evaluacion_desempeno
 * @property int $estado
 * @property-read string $salario_neto
 */
class Empleado extends Model
{
    /** Tabla asociada */
    protected $table = 'empleados';

    /** Clave primaria personalizada */
    protected $primaryKey = 'id_empleado';

    /** No usamos created_at/updated_at en esta tabla */
    public $timestamps = false;

    /** Atributos asignables */
    protected $fillable = [
        'nombre',
        'departamento',
        'puesto',
        'salario_base',
        'bonificacion',
        'descuento',
        'fecha_contratacion',
        'fecha_nacimiento',
        'sexo',
        'evaluacion_desempeno',
        'estado',
    ];

    /** Casts para tipos nativos */
    protected $casts = [
        'id_empleado' => 'integer',
        'salario_base' => 'decimal:2',
        'bonificacion' => 'decimal:2',
        'descuento' => 'decimal:2',
        'evaluacion_desempeno' => 'decimal:2',
        'estado' => 'integer',
        'fecha_contratacion' => 'date',
        'fecha_nacimiento' => 'date',
    ];

    /** Atributos agregados al array/json del modelo */
    protected $appends = [
        'salario_neto',
    ];

    /**
     * Accesor calculado: salario neto = salario_base + bonificacion - descuento
     * Devuelve string con 2 decimales para mantener consistencia con cast decimal.
     */
    public function getSalarioNetoAttribute()
    {
        $salarioBase = $this->attributes['salario_base'] ?? 0;
        $bon = $this->attributes['bonificacion'] ?? 0;
        $desc = $this->attributes['descuento'] ?? 0;

        $neto = (float) $salarioBase + (float) $bon - (float) $desc;

        return number_format($neto, 2, '.', '');
    }

    /** Scope para empleados activos (estado = 1) */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}
