<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'company_id', 'enabled', 'editable'];

    public static function generteDefaultForCompany($companyId) {
        $resources = [
            "Apropiación o abuso de los recursos de la compañía",
            "Conflicto de intereses",
            "Sobornos",
            "Alteración de registros, reportes o documentos de la compañía",
            "Mal uso de datos personales",
            "Mal uso de información confidencial",
            "Discriminación - Acoso Sexual u Hostigamiento laboral",
            "Incumplimiento legal y regulatorio",
            "Imcumplimiento de políticas y procedimeintos",
            "Seguridad y salud en el trabajo",
            "Lavado de activos y financiamiento de terrorismo",
            "Otras actividades no éticas"
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
