<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\GroupCard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
{
    public function index($id)
    {
        $cards = Card::with('tasks')->where('activity_id', $id)->with('activity')->paginate(6);
        $groupCardName = GroupCard::where('id',$id)->pluck('name');
        return Inertia::render('Cards/Index', ['cards' => $cards,'id'=>$id,'groupCardName'=>$groupCardName]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:cards',
            'color' => 'required',
            'activity_id' => 'required|exists:activities,id',
        ]);

        Card::create($request->all());

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $card = Card::findOrFail($id);
    
        $request->validate([
            'name' => 'required|unique:cards,name,' . $id,
            'color' => 'required',
            'activity_id' => 'required|exists:activities,id',
        ]);
    
        $card->update($request->all());
    
        return redirect()->back();
    }
    
    public function destroy($id)
    {
        $card = Card::findOrFail($id);
    
        $card->delete();
    
        return redirect()->back();
    }
}
