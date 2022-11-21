<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Denouncement;
use App\Models\Evidence;
use App\Models\Informer;
use App\Models\Notification;
use App\Models\Source;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DenouncementController extends Controller
{
    // protected $user;
    public function __construct()
    {
        $this->middleware('auditor', ['except' => ['index', 'show']]);
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        return Denouncement::join('informers', 'informers.id', '=', 'denouncements.informer_id')
            ->join('categories', 'categories.id', '=', 'denouncements.category_id')
            ->leftJoin('users', 'users.id', '=', 'denouncements.investigator_id')
            ->where('denouncements.company_id', $user->company_id)
            ->where(function ($query) use ($user) {
                if ($user->rol == User::ROL_INVESTIGATOR) {
                    $query->where('investigator_id', $user->id);
                }
            })
            ->select(
                'informers.*',
                'denouncements.*',
                'categories.name as category',
                'users.first_name as investigator_first_name',
                'users.last_name as investigator_last_name'
            )
            ->get();
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
        $webByDefault = !is_null($request->get('company_id'));
        $companyId = !is_null($request->get('company_id')) ? $request->get('company_id') : Auth::user()->company_id;

        if (is_null($companyId)) {
            return [];
        }

        $informer = Informer::create($request->get('denouncement'));

        $complement = [
            'state' => State::CREATED,
            'informer_id' => $informer->id,
            'company_id' => $companyId,
        ];

        $requestDenouncement = $request->get('denouncement');

        if ($webByDefault) {
            $source = Source::where([
                'name' => 'web',
                'company_id' => $companyId
            ])->first();

            if (!is_null($source)) {
                $requestDenouncement['source_id'] = $source->id;
            } else {
                $requestDenouncement['source_id'] = Source::where('company_id', $companyId)->first()->id;
            }
        }

        $denouncement = Denouncement::create(array_merge($requestDenouncement, $complement));
        $denouncement->id_in_hash = md5($companyId . rand(0, 9999) . $denouncement->id);
        $denouncement->save();

        $evidences = $request->get('evidences');

        if (count($evidences)) {
            foreach ($evidences as $evidence) {
                Evidence::create([
                    'resource_url' => $evidence,
                    'is_primary' => true,
                    'denounces_id' => $denouncement->id
                ]);
            }
        }

        $this->notifyDenouncementCreated($denouncement->id);

        return json_encode(['id' => $denouncement->id_in_hash]);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();

        $denouncementQuery = Denouncement::where('denouncements.company_id', $user->company_id)
            ->where(function ($query) use ($user) {
                if ($user->rol == User::ROL_INVESTIGATOR) {
                    $query->where('denouncements.investigator_id', $user->id);
                }
            });

        return $this->getDenouncementAndCompleteQuery($id, $denouncementQuery);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function externalShow($slug, $id)
    {
        $company = Company::where([
            'slug' => $slug,
            'enabled' => true
        ])->first();

        $companyId = !is_null($company) ? $company->id : 0;

        $denouncement = Denouncement::where('id_in_hash', $id)->first();

        $denouncementQuery = Denouncement::where('denouncements.company_id', $companyId);

        return $this->getDenouncementAndCompleteQuery($denouncement ? $denouncement->id : 0, $denouncementQuery);
    }

    private function getDenouncementAndCompleteQuery($id, $query)
    {
        $denouncement = $query->join('categories', 'categories.id', '=', 'denouncements.category_id')
            ->join('sources', 'sources.id', '=', 'denouncements.source_id')
            ->join('offices', 'offices.id', '=', 'denouncements.office_id')
            ->join('informers', 'informers.id', '=', 'denouncements.informer_id')
            ->join('areas', 'areas.id', '=', 'denouncements.area_id')
            ->leftJoin('users', 'users.id', '=', 'denouncements.investigator_id')
            ->select(
                'denouncements.*',
                'categories.name as category',
                'sources.name as source',
                'offices.name as office',
                'areas.name as area',
                'informers.first_name as informer_first_name',
                'informers.email as informer_email',
                'informers.last_name as informer_last_name',
                'informers.job as informer_job',
                'informers.phone as informer_phone',
                'informers.phone as informer_phone',
                'informers.relationship as informer_relationship',
                'users.first_name as investigator_first_name',
                'users.last_name as investigator_last_name'
            )
            ->find($id);

        $evidences = !is_null($denouncement) ? Evidence::where('denounces_id', $id)->get() : null;

        return compact('denouncement', 'evidences');
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
        Denouncement::where('id', $id)->update($request->except(['denounces_id']));


        $state = $request->get('state');

        if ($state == State::ASSGINED) {
            $this->notifyInvestigatorAssigned($id);
            $this->notifyDenouncementAssigned($id);
        }

        if ($state == State::DESESTIMATE) {
            $this->notifyDenouncementDesistimate($id);
        }

        if ($state == State::CLOSED) {
            $this->notifyDenouncementClosed($id);
        }

        return [];
    }

    private function notifyInvestigatorAssigned($denouncementId)
    {
        $denouncement = Denouncement::find($denouncementId);

        Notification::create([
            'user_id' => $denouncement->investigator_id,
            'title' => 'Asignada la denuncia #' . $denouncement->id,
            'type' => '',
            'description' => 'Le han asignado una denuncia.',
            'denounces_id' => $denouncementId,
        ]);

        MailController::baseSendInvestigatorMail($denouncementId, [
            'subject' => 'Ethos Perú - Asignada la denuncia #%id%',
            'message' => 'le han asignado la denuncia #<code>%id%</code>.'
        ]);
    }

    private function notifyDenouncementAssigned($denouncementId)
    {
        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Investigador asignado #%id%',
            'message' => 'ha sido asignado un investigador en la denuncia #<code>%id%</code>. Puedes consultar esta denuncia y ver su progreso en <a href="%url%">%url%</a>.'
        ]);
    }

    private function notifyDenouncementDesistimate($denouncementId)
    {
        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Se ha desestimado #%id%',
            'message' => 'Se ha desestimado la denuncia #<code>%id%</code>. Puedes consultar esta denuncia y ver su progreso en <a href="%url%">%url%</a>.'
        ]);
    }

    private function notifyDenouncementClosed($denouncementId)
    {
        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Se ha cerrado el caso #%id%',
            'message' => 'se ha cerrado la denuncia #<code>%id%</code>. Puedes consultar esta denuncia y ver su progreso en <a href="%url%">%url%</a>.'
        ]);
    }

    private function notifyDenouncementCreated($denouncementId)
    {
        $denouncement = Denouncement::find($denouncementId);
        $company = Company::find($denouncement->company_id);

        $user = User::where([
            'company_id' => $denouncement->company_id,
            'rol' => User::ROL_ADMIN,
            'enabled' => true
        ])
            ->first();

        if (!is_null($user)) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Creada la denuncia #' . $denouncementId,
                'type' => '',
                'description' => 'Se ha creado una denuncia.',
                'denounces_id' => $denouncementId,
            ]);
        }

        $message = 'desde Ethos Perú hemos creado una denuncia en la empresa '
            . $company->name
            . ' con el siguiente código <code>%id%</code>.';

        MailController::baseSendAdminMail($denouncementId, [
            'subject' => 'Ethos Perú - Se ha creado la denuncia #%id%',
            'message' => $message
        ]);

        $message = 'desde Ethos Perú hemos creado una denuncia en la empresa '
            . $company->name
            . ' con el siguiente código <code>%id%</code>.'
            . ' Puedes consultarla y ver su progreso en <a href="%url%">%url%</a>.';

        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Se ha creado la denuncia #%id%',
            'message' => $message
        ]);
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
