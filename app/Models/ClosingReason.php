<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosingReason extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'company_id', 'enabled'];

    public static function generteDefaultForCompany($companyId) {
        $resources = [
            'Caso Solventado',
        ];

        $resourcesForCompany = [];
        $now = date("Y-m-d H:i:s");

        foreach ($resources as $name) {
            $resourcesForCompany[] = [
                'name' => $name,
                'company_id' => $companyId,
                'enabled' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return self::insert($resourcesForCompany);
    }
}
