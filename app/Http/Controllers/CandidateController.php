<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main', [
           'candidate_1' => Candidate::find(1),
           'candidate_2' => Candidate::find(2)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $candidate = Candidate::find($id);   

        return view('details', [
            'candidate' => $candidate
         ]);
    }

    public function stats(){
        $candidate_1 = Candidate::find(1);
        $candidate_2 = Candidate::find(2);

        if ($candidate_1->number_of_votes == 0){
            $percentage_1 = 0;
        } else {
            $percentage_1 = round(($candidate_1->number_of_votes / ($candidate_1->number_of_votes + $candidate_2->number_of_votes)) * 100, 2);
        }

        if ($candidate_2-> number_of_votes == 0){
            $percentage_2 = 0;
        } else {
            $percentage_2 = round(($candidate_2->number_of_votes / ($candidate_1->number_of_votes + $candidate_2->number_of_votes)) * 100, 2);
        }

        return view('stats', [
            'candidate_1' => $candidate_1,
            'candidate_2' => $candidate_2,
            'percentage_1' => $percentage_1,
            'percentage_2' => $percentage_2,
         ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $candidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $candidate = Candidate::find($id);

        $candidate->number_of_votes += 1;
        Auth::user()->candidate_id = $candidate->id;

        Auth::user()->save();
        $candidate->save();

        return view('/finish');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        //
    }
}
