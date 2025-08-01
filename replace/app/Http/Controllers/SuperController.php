<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeAccountMail;

class SuperController extends Controller
{
    public function dashboard()
    {
        return view('super.dashboard');
    }
    public function sendWelcomeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'to'        =>  'required|string',
        ]);

        $to = $request->to;
        $email = $request->email;
        $password = $request->password;

        Mail::to($to)->send(new WelcomeAccountMail($email, $password));

        return back()->with('success', 'Welcome email sent successfully!');
    }
}
