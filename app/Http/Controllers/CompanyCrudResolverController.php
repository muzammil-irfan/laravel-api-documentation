<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyCrudResolverController extends Controller
{
    public static function store($entity, $request) {
        $company = Company::find(Auth::user()->company_id);

        $resource = $request->all();
        $resource['company_id'] = $company->id;
        $resource['country_id'] = $company->country_id;
        $resource['editable'] = true;
        $resource['enabled'] = true;


        $exist = $entity::where([
            'name' => $resource['name'],
            'company_id' => $resource['company_id']
        ])->first();

        if (! is_null($exist)) {
            return response()->json(['error' => 'Este nombre ya existe'], 400); 
        }

        return $entity::create($resource);
    }
    
    public static function update($entity, $id, $request, $hasConditions = true) {
        $companyId = Auth::user()->company_id;

        $conditions = [
            'id' => $id,
            'company_id' => $companyId
        ];

        if (is_null($request->get('name')) && (! is_null($request->get('enabled')))) {
            return $entity::where($conditions)->update([
                'enabled' => $request->get('enabled')
            ]);
        }

        $resource = array_filter($request->only(['name', 'enabled']), function($value) {
            return ! is_null($value);
        });

        
        if (isset($resource['name']) && $hasConditions) {
            $conditions['editable'] = true;
        }

        if ( (isset($conditions['editable']) && $hasConditions) || !$hasConditions ) {
            $exist = $entity::where([
                'name' => $resource['name'],
                'company_id' => $companyId
            ])->first();

            $same = $entity::where([
                'name' => $resource['name'],
                'id' => $id
            ])->first();
    
            if (! is_null($exist) && is_null($same)) {
                return response()->json(['error' => 'Este nombre ya existe'], 400); 
            }
        }

        return $entity::where($conditions)->update($resource);
    }
}
