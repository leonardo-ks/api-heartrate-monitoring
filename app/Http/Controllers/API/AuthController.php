<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\UserResource;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=7F9CF5&color=EBF4FF'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['success' => true, 'data' => $user, 'access_token' => $token,]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['success' => true, 'message' => 'Login Success', 'access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();

        return ['success' => true, 'message' => 'Berhasil logout'];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('id', auth()->user()->id);
        $user->update(['name' => $request->name, 'email' => $request->email, 'gender' => $request->gender, 'dob' => $request->dob, 'profile' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=7F9CF5&color=EBF4FF']);

        return response()->json(['success' => true, 'message' => 'Change created', 'data' => new UserResource($user->first())]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if(!Hash::check($request->old_password, auth()->user()->password)){
            return response()->json(['success' => false, "message" => "Password lama tidak sesuai"]);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['success' => true, "message" => "Password berhasil diubah"]);
    }

    public function addContact(Request $request) {
        $validator = Validator::make($request->all(), [
            'contact' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $contacts = auth()->user()->contacts;

        $contact_arr = preg_split("/\,/", $contacts);
        foreach($contact_arr as $contact) {
            if ($contact == $request->contact) {
                return response()->json(['success' => false, "message" => "Kontak sudah didaftarkan"]);
            }
        }
        if ($contacts != null) {
            $contacts = $contacts . "," . $request->contact;
        } else {
            $contacts = $request->contact;
        }

        User::whereId(auth()->user()->id)->update([
            'contacts' => $contacts
        ]);

        return response()->json(['success' => true, "message" => "Kontak berhasil ditambahkan"]);
    }

    public function getContactList() {
        $contacts = auth()->user()->contacts;
        $contact_arr = preg_split("/\,/", $contacts);
        $data = array();
        foreach($contact_arr as $contact) {
            array_push($data, User::whereId($contact)->first());
        }
        return response()->json(['success' => true, "message" => "Success", "data" => $data]);
    }

    public function search($name) {
        $user = User::where('name', 'like', '%'.$name.'%')->get();
        if(count($user) > 0) {
            return response()->json(['success' => true, "message" => "Success", "data" => $user]);
        }
    }
}
