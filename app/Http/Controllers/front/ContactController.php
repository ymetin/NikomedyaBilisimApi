<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        Mail::to('info@nikomedyabilisim.com')->send(new ContactEmail($mailData));

        return response()->json([
            'status' => true,
            'message' => 'Bizimle iletişime geçtiniğiniz için teşekkür ederiz.'
        ]);
    }
}
