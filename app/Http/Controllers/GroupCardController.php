<?php

namespace App\Http\Controllers;

use App\Models\GroupCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GroupCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupCards = GroupCard::with('user')->where('user_id',Auth::user()->id)->orderBy('id','desc')->paginate(9);
        return Inertia::render('GroupCards/Index',['groupCards'=>$groupCards]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $groupCard = new GroupCard();
        $groupCard->name = $request->name;
        $groupCard->color = $request->color;
        $groupCard->user_id = Auth::user()->id;

        $groupCard->save();
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $groupCard = GroupCard::findOrFail($id);
        $groupCard->name = $request->name;
        $groupCard->color = $request->color;
        $groupCard->user_id = Auth::user()->id;
        $groupCard->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $groupCard = GroupCard::findOrFail($id);
        $groupCard->delete();
        return redirect()->back();
    }
}
