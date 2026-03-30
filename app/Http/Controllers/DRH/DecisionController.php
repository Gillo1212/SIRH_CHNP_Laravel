<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Mouvement;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function index()
    {
        return redirect()->route('drh.validations.decisions');
    }

    public function signer(Request $request, int $id)
    {
        return redirect()->route('drh.validations.signer', $id)->with('success', 'Redirection vers la signature.');
    }
}
