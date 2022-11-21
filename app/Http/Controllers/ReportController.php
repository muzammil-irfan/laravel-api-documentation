<?php

namespace App\Http\Controllers;

use App\Models\Denouncement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        return Denouncement::where(function($query) use ($request, $user){
                if ($user->rol == User::ROL_OWNER) {
                    if (! is_null($request->get('company_id')) && count($request->get('company_id'))) {
                        $query->where('denouncements.company_id', $request->get('company_id'));
                    }
                } else {
                    $query->where('denouncements.company_id', $user->company_id);
                }

                if (! is_null($request->get('priority')) && count($request->get('priority'))) {
                    $query->whereIn('priority', $request->get('priority'));
                }

                if (! is_null($request->get('state')) && count($request->get('state'))) {
                    $query->whereIn('state', $request->get('state'));
                }

                if (! is_null($request->get('category_id')) && count($request->get('category_id'))) {
                    $query->whereIn('category_id', $request->get('category_id'));
                }

                if (! is_null($request->get('source_id')) && count($request->get('source_id'))) {
                    $query->whereIn('source_id', $request->get('source_id'));
                }

                if (! is_null($request->get('start')) && count($request->get('start'))) {
                    $query->where('denouncements.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->get('start'))));
                }

                if (! is_null($request->get('end'))) {
                    $query->where('denouncements.created_at', '<=', date('Y-m-d H:i:s', strtotime($request->get('end') . ' 23:59:59')));
                }

                
            })
            ->join('categories', 'categories.id', '=', 'denouncements.category_id')
            ->join('sources', 'sources.id', '=', 'denouncements.source_id')
            ->join('companies', 'companies.id', '=', 'denouncements.company_id')
            ->join('offices', 'offices.id', '=', 'denouncements.office_id')
            ->join('informers', 'informers.id', '=', 'denouncements.informer_id')
            ->join('areas', 'areas.id', '=', 'denouncements.area_id')
            ->leftJoin('users', 'users.id', '=', 'denouncements.investigator_id')
            ->select(
                'denouncements.*', 
                'categories.name as category',
                'companies.name as company',
                'sources.name as source',
                'offices.name as office',
                'areas.name as area',
                'informers.first_name as informer_first_name',
                'informers.email as informer_email',
                'informers.last_name as informer_last_name',
                'users.first_name as investigator_first_name',
                'users.last_name as investigator_last_name'
            )
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
