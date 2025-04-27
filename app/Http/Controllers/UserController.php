<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Imports\NimEmailImport;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'nim' => 'required',
        ]);

        if($validatedData['email'] != 'sa@ciputra.ac.id'){
            if (!str_ends_with($validatedData['email'], '@student.ciputra.ac.id')) {
                return redirect()->back()->withErrors(['email' => 'Invalid email format. Please use an email ending with @student.ciputra.ac.id']);
            }
            $existingEmail = User::where('email', $validatedData['email'])->first();
            $existingNIM = User::where('nim', $validatedData['nim'])->first();

            if(!$existingNIM && !$existingEmail){
                return redirect()->back()->withErrors(['email' => 'Please confirm attendance at the front desk first.']);
            // } else if (!$existingEmail && $existingNIM){
            //      return redirect()->back()->withErrors(['email' => 'Incorrect email.']);
            // } else if (!$existingNIM && $existingEmail){
            //      return redirect()->back()->withErrors(['nim' => 'Incorrect nim.']);
            } else {
                $user = User::where('nim', $validatedData['nim'])->first();
                if($user){
                    $import = new NimEmailImport();
                    $filePath = public_path('images/Student List 2025_04_24 1810.xlsx');
                    $data = Excel::toArray($import, $filePath)[0];

                    foreach ($data as $row) {
                        if ($row['nis'] == $user->nim && $row['official_email'] == $validatedData['email']) {
                            if($user->candidate_id != null){
                                return redirect()->back()->withErrors(['email' => 'Email has already been used to vote.']);
                            } else {
                                $user->email = $validatedData['email'];
                                $user->save();
                                Auth::login($user);
                                return redirect()->route('main');
                            }
                        }
                    }
                    return redirect()->back()->withErrors(['email' => 'Incorrect NIM or email.', 'nim']);                   
                } else {
                    return redirect()->back()->withErrors(['email' => 'Please confirm attendance at the front desk first.']);
                }
            }
        } else {
            if ($validatedData['nim'] == '001') {
                $user = User::where('email', 'sa@ciputra.ac.id')->first();
                Auth::login($user);
                return redirect()->route('stats');
            }
            return redirect()->back()->withErrors(['nim' => 'Incorrect NIM.']);
        }
    }

    public static function attendanceList(){
        $voters = User::where('role', 2)->paginate(10);
    
        return view('attendanceList', [
            'voters' => $voters,
        ]);
    }

    public static function voterSearch(Request $request){
        if($request->has('search')){
            $voters = User::where('role', 2)
                            ->where(function ($query) use ($request) {
                                $query->where('nim', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%');
                            })
                            ->paginate(10)->withQueryString();
        } else {
            $voters = User::where('role', 2)->paginate(10);
        }
    
        return view('attendanceList', [
            'voters' => $voters
        ]);
    }

    public function check(Request $request){
        $qrcode = $request->input('qr_code');


        $split = explode('+', $qrcode);
        $nim = $split[0];
        $name = $split[1];
        $name = preg_replace('/Shift/', '', $split[1]); // remove "Shift"
        $name = preg_replace('/\s+/', ' ', $name); // replace multiple spaces with single space
        $name = trim($name); // clean leading/trailing spaces


        $nim = preg_replace('/\D/', '', $nim);

        $existingNIM = User::where('nim', $nim)->first();

        if(!$existingNIM){
            $import = new NimEmailImport();
            $filePath = public_path('images/Student List 2025_04_24 1810.xlsx');
            $data = Excel::toArray($import, $filePath)[0];
            foreach ($data as $row) {
                if ($row['nis'] == $nim) {
                    User::create([
                        'name' => $name,
                        'nim' => $nim,
                        'role' => 2,
                        'presence' => 1,
                    ]);
                    return redirect()->route('attendanceList');
                }
            }
        } else {
            return redirect()->back()->withErrors(['nim' => 'NIM has already been used to vote.']);
        }

        return redirect()->route('attendanceList');
    }

    public function attendance($id){
        $user = User::where('id', $id)->first();

        if ($user){
            $user->presence = 1;
            $user->save();
        }

        return redirect()->route('attendanceList');
    }

    public function uncheck($id){
        $user = User::where('id', $id)->first();

        if ($user){
            $user->presence = 0;
            $user->save();
        }

        return redirect()->route('attendanceList');
    }

    /**
     * Function: googleLogin
     * This function will redirect to Google
     */

    public function googleLogin() {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Function: googleAuthentication
     * This function will authenticate the user through the Google Account
     */

    public function googleAuthentication() {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if($googleUser->email == 'sa@ciputra.ac.id'){
                $user = User::where('email', 'sa@ciputra.ac.id')->first();
                Auth::login($user);
                return redirect()->route('stats');
            }else{
                if (!str_ends_with($googleUser->email, '@student.ciputra.ac.id')) {
                    return redirect()->route('signup')->withErrors(['email' => 'Please use an email ending with @student.ciputra.ac.id']);
                } else {
                    if ($user) {
                        return redirect()->route('signup')->withErrors(['email' => 'Email has already been used to vote.']);
                    } else {
                        $userData = User::create([
                            'name' => $googleUser->name,
                            'email' => $googleUser->email,
                            'password' => Hash::make('Password@1234'),
                            'google_id' => $googleUser->id,
                            'role' => 2,
                        ]);
                    
                        if ($userData) {
                            Auth::login($userData);
                            return redirect()->route('main');
                        }
                    }
                }
            }
        } catch (Exception $e) {
            dd($e);
        }
    }
}
