<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Denouncement;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->rol == User::ROL_OWNER) {
            $totalCompanies = Company::count();
            $activeCompanies = Company::where('enabled', true)->count();
            $totalDenouncements = Denouncement::count();
            $totalDenouncementsClosed = Denouncement::where('state', State::CLOSED)->count();
            $totalDenouncementsCreated = Denouncement::where('state', State::CREATED)->count();
            $topCompanies = Company::take(5)->get()->pluck('name');

            $topCompaniesDenouncements = Denouncement::groupBy('company_id')
                ->join('companies', 'companies.id', '=', 'denouncements.company_id')
                ->select('companies.name', DB::raw('count(*) as total'))
                ->take(5)
                ->get();

            return compact(
                'totalCompanies', 
                'activeCompanies', 
                'topCompanies',
                'totalDenouncements',
                'totalDenouncementsClosed',
                'totalDenouncementsCreated',
                'topCompaniesDenouncements'
            );
        }
        
        if (in_array($user->rol, User::getCompanyAdminRoles())) {
            $totalDenouncements = Denouncement::where('company_id', $user->company_id)->count();
            $totalDenouncementsClosed = Denouncement::where('company_id', $user->company_id)->where('state', State::CLOSED)->count();
            
            $totalDenouncementsCreated = Denouncement::where('company_id', $user->company_id)->whereIn('state', [
                State::CREATED, 
                State::ASSGINED,
                State::WAIT_ANSWER,
                State::REPLIED,
            ])->count();
            
            $activeInvestigators = User::where([
                'company_id' => $user->company_id,
                'enabled' => true,
                'rol' => User::ROL_INVESTIGATOR
            ])->count();

            $topCategoryDenouncements = Denouncement::groupBy('category_id')
                ->where('denouncements.company_id', $user->company_id)
                ->join('categories', 'categories.id', '=', 'denouncements.category_id')
                ->select('categories.name', DB::raw('count(*) as total'))
                ->take(5)
                ->get();

            $lastDenouncements = Denouncement::where('denouncements.company_id', $user->company_id)
                ->join('informers', 'informers.id', '=', 'denouncements.informer_id')
                ->orderBy('denouncements.id', 'DESC')
                ->select('denouncements.*', 'informers.first_name as first_name')
                ->take(5)
                ->get();

            return compact(
                'totalDenouncements',
                'totalDenouncementsClosed',
                'totalDenouncementsCreated',
                'activeInvestigators',
                'topCategoryDenouncements',
                'lastDenouncements'
            );
        }

        if ($user->rol == User::ROL_INVESTIGATOR) {
            $lastDenouncements = Denouncement::where('denouncements.company_id', $user->company_id)
                ->where('investigator_id', $user->id)
                ->where('state', '<>', State::CLOSED)
                ->where('state', '<>', State::DESESTIMATE)
                ->join('informers', 'informers.id', '=', 'denouncements.informer_id')
                ->orderBy('denouncements.id', 'DESC')
                ->select('denouncements.*', 'informers.first_name as first_name')
                ->take(5)
                ->get();

                $meAssigned = Denouncement::where([
                        'company_id' => $user->company_id,
                        'investigator_id' => $user->id,
                    ])->where('state', '<>', State::CLOSED)
                    ->where('state', '<>', State::DESESTIMATE)
                    ->count();

                $meClosed = Denouncement::where([
                        'company_id' => $user->company_id,
                        'investigator_id' => $user->id,
                    ])->where('state', State::CLOSED)
                    ->count();

                return compact(
                    'lastDenouncements',
                    'meAssigned',
                    'meClosed'
                );
        }
        
        return [];
    }
}
