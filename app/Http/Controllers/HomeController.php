<?php

namespace App\Http\Controllers;
use App\Models\Flavour;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {     
        $url = action([ApiController::class, 'index']);
        
        return view('home',['url' => $url]);
    }

    public function forbidden(Request $request){
        
        return view('forbidden');
    }
}
