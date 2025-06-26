<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Handle the contact form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        // Invia email di conferma all'utente
        try {
            Mail::send('emails.contact-confirmation', [
                'user' => $user,
                'subject' => $validated['subject'],
                'messageContent' => $validated['message']
            ], function ($message) use ($user, $validated) {
                $message->to($user->email, $user->name)
                        ->subject('Conferma ricezione messaggio: ' . $validated['subject']);
            });

            return redirect()->route('contact.index')->with('success', 'Messaggio inviato con successo! Ti è stata inviata una conferma via email.');
        } catch (\Exception $e) {
            return redirect()->route('contact.index')->with('error', 'Errore nell\'invio del messaggio. Riprova più tardi.');
        }
    }
}
