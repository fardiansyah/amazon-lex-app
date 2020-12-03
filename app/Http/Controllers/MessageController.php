<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSentEvent;
use App\Events\LexResponseEvent;
use \App;
use Aws\Exception\AwsException;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        return Message::with('user')
            ->where('owner_id', $user->id)
            ->get();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $message = $user->messages()->create([
            'message' => $request->input('message'),
            'owner_id' => $user->id
        ]);
        // broadcast user message
        broadcast(new MessageSentEvent($message, $user, $user->id));

        $client = App::make('aws')->createClient('LexRuntimeService');
        try {
            $result = $client->postText([
                'botAlias' => 'Prod', // REQUIRED
                'botName' => 'OrderFlowers', // REQUIRED
                'inputText' => $message->message, // REQUIRED
                'requestAttributes' => array(),
                'sessionAttributes' => array(),      
                'userId' => $user->name, // REQUIRED
            ]);
            $lex_user = User::where('name','lex')->first();
            $lex_response_message = $lex_user->messages()->create([
                'message' => $result['message'],
                'owner_id' => $user->id
            ]);

            //broadcast lex response
            broadcast(new MessageSentEvent($lex_response_message, $lex_user, $user->id));
        } catch (AwsException $e) {
            // output error message if fails
            error_log($e->getMessage());
        }

        
        return response()->json(['status' => 'Message Sent']);
    }
}
