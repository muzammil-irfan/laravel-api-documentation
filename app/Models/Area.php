<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'company_id', 'enabled', 'editable'];

    public static function generteDefaultForCompany($companyId) {
        $resources = [
            'Comercio Exterior',
            'Administración y Finanzas',
            'Compras',
            'Contraloría',
            'Recursos Humanos',
            'Contabilidad',
            'Producción',
            'Planeamiento',
            'Seguridad',
            'Comercial y Marketing',
            'Auditoría',
            'Despacho',
            'Gerencia General',
            'Calidad',
            'Sistemas'
        ];

        $resourcesForCompany = [];
        $now = date("Y-m-d H:i:s");

        foreach ($resources as $name) {
            $resourcesForCompany[] = [
                'name' => $name,
                'company_id' => $companyId,
                'enabled' => 1,
                'editable' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return self::insert($resourcesForCompany);
    }
}
