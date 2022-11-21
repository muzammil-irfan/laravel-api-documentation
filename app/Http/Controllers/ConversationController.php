<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Denouncement;
use App\Models\Evidence;
use App\Models\Notification;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ConversationController extends Controller
{
    public function store(Request $request)
    {
        $informerId = null;
        $userId = Auth::user() ? Auth::user()->id : null;

        if (! is_null($request->get('external'))) {
            $informerId = Denouncement::where('id', $request->get('denounces_id'))->first();
            $informerId = $informerId ? $informerId->informer_id : null;
        }

        if (! ($informerId || $userId)) {
            return null;
        }

        $informerCanSee = (!is_null($request->get('visible')) && $request->get('visible')) || (! is_null($request->get('wait')) && $request->get('wait')) || !is_null($informerId);

        $conversationId = Conversation::create([
            'message' => $request->get('message'),
            'user_id' => $userId,
            'informer_can_see' => $informerCanSee,
            'informer_id' => $informerId,
            'denounces_id' => $request->get('denounces_id')
        ])->id;

        if (count($request->get('resource'))) {
            foreach ($request->get('resource') as $resource) {
                Evidence::create([
                    'resource_url' => $resource,
                    'is_primary' => false,
                    'denounces_id' => $request->get('denounces_id'),
                    'conversation_id' => $conversationId
                ]);
            }
            
        }

        if (! is_null($request->get('wait')) && $request->get('wait')) {
            Denouncement::where('id', $request->get('denounces_id'))->update([
                'state' => State::WAIT_ANSWER
            ]);

            $this->notifyInvestigatorWaitForAnswer($request->get('denounces_id'));
        }

        if ($informerId) {
            Denouncement::where('id', $request->get('denounces_id'))->update([
                'state' => State::REPLIED
            ]);
            
            $this->notifyInformerReplies($request->get('denounces_id'));
        }

        if ($informerCanSee && is_null($informerId)) {
            $this->notifyInvestigatorWriteMessage($request->get('denounces_id'));
        }


        $convesation = Conversation::
              leftJoin('users', 'users.id', '=', 'conversations.user_id')
            ->leftJoin('informers', 'informers.id', '=', 'conversations.informer_id')
            ->select(
                'conversations.message',
                'conversations.created_at',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.rol as user_rol',
                'informers.first_name as informer_first_name',
                'informers.last_name as informer_last_name'
            )
            ->find($conversationId);

        $evidences = Evidence::where('conversation_id', $conversationId)->get();

        $convesation->evidences = $evidences;

        return $convesation;
    }

    private function notifyInformerReplies($denouncementId) {
        $denouncement = Denouncement::find($denouncementId);

        Notification::create([
            'user_id' => $denouncement->investigator_id,
            'title' => 'Han respondido en #' . $denouncement->id,
            'type' => '',
            'description' => 'El informante ha respondido en la denuncia.',
            'denounces_id' => $denouncementId,
        ]);

        MailController::baseSendInvestigatorMail($denouncementId, [
            'subject' => 'Ethos Perú - Han respondido en #%id%',
            'message' => 'el informante ha respondido en la denuncia #<code>%id%</code>.'
        ]);
    }

    private function notifyInvestigatorWaitForAnswer($denouncementId) {
        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Esperamos por tu respuesta #%id%',
            'message' => 'el investigador a solicitado una respuesta de su parte en la denuncia #<code>%id%</code>. Puedes consultar esta denuncia y ver su progreso en <a href="%url%">%url%</a>.' 
        ]);
    }

    private function notifyInvestigatorWriteMessage($denouncementId) {
        MailController::baseSendInformerMail($denouncementId, [
            'subject' => 'Ethos Perú - Nuevo mensaje #%id%',
            'message' => 'el investigador a escrito un nuevo mensaje en la denuncia #<code>%id%</code>. Puedes consultar esta denuncia y ver su progreso en <a href="%url%">%url%</a>.' 
        ]);
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
        
        $conversations = Conversation::where(function ($query) use ($user) {
                if (is_null($user)) {
                    $query->where('informer_can_see', true);
                }
            })
            ->leftJoin('users', 'users.id', '=', 'conversations.user_id')
            ->leftJoin('informers', 'informers.id', '=', 'conversations.informer_id')
            ->select(
                'conversations.message',
                'conversations.created_at',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.rol as user_rol',
                'informers.first_name as informer_first_name',
                'informers.last_name as informer_last_name',
                'conversations.id as id'
            )
            ->where('conversations.denounces_id', $id)
            ->orderBy('conversations.id', 'DESC')
            ->get();

        foreach ($conversations as $conversation) {
            $evidences = Evidence::where('conversation_id', $conversation->id)->get();

            $conversation->evidences = $evidences;
        }

        return $conversations;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function externalShow($id)
    {
        $user = Auth::user();
        
        $denouncement = Denouncement::where('id_in_hash', $id)->first();

        $denouncementId = $denouncement ? $denouncement->id : 0;

         $conversations = Conversation::where(function ($query) use ($user) {
                if (is_null($user)) {
                    $query->where('informer_can_see', true);
                }
            })
            ->leftJoin('users', 'users.id', '=', 'conversations.user_id')
            ->leftJoin('informers', 'informers.id', '=', 'conversations.informer_id')
            ->select(
                'conversations.message',
                'conversations.created_at',
                'users.rol as user_rol',
                'conversations.id as id'
            )
            ->where('conversations.denounces_id', $denouncementId)
            ->orderBy('conversations.id', 'DESC')
            ->get();

            foreach ($conversations as $conversation) {
            $evidences = Evidence::where('conversation_id', $conversation->id)->get();

            $conversation->evidences = $evidences;
        }

        return $conversations;
    }

}
