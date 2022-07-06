<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function send(Request $request)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key=AAAA7NpuTF0:APA91bGoZNzN0veBBz6e9dX8BSGlOrbzlsmyoNLVQ4SCm4m_bv7RYswZ38kzSWWi9VCtthYWIxWLaVHHRZmA41ypwt6YOX4AXx2OrKWzR5YZ3ELsy-RBOl4xRax0-80GqP0Yr66J8dPy'
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => '/topics/hrm' . strtolower(auth()->user()->name),
            'data' => [
                'title' => 'Perhatian',
                'message' => 'Kontak anda ' . auth()->user()->name . ' terdeteksi mendapatkan detak jantung melampaui batas yang ditetapkan!'
            ]
        ]);

        return response()->json(['success' => true, 'message' => $response]);
    }
}
