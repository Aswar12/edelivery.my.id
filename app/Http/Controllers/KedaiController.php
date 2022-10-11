<?php

namespace App\Http\Controllers;

use App\Models\Kedai;
use App\Models\User;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class KedaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Kedai::with('user');

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <a class="inline-block border border-gray-700 bg-gray-700 text-white rounded-md px-2 py-1 m-1 transition duration-500 ease select-none hover:bg-gray-800 focus:outline-none focus:shadow-outline" 
                            href="' . route('dashboard.kedai.edit', $item->id) . '">
                            Edit
                        </a>
                        <form class="inline-block" action="' . route('dashboard.kedai.destroy', $item->id) . '" method="POST">
                        <button class="border border-red-500 bg-red-500 text-white rounded-md px-2 py-1 m-2 transition duration-500 ease select-none hover:bg-red-600 focus:outline-none focus:shadow-outline" >
                            Hapus
                        </button>
                            ' . method_field('delete') . csrf_field() . '
                        </form>';
                })
                ->rawColumns(['action'])
                ->make();
        }
        return view('pages.dashboard.kedai.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $products = Product::all();
        return view('pages.dashboard.kedai.create', compact(['products','users']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        Kedai::create($data);

        return redirect()->route('dashboard.kedai.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kedai  $kedai
     * @return \Illuminate\Http\Response
     */
    public function show(Kedai $kedai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kedai  $kedai
     * @return \Illuminate\Http\Response
     */
    public function edit(Kedai $kedai)
    {
        $users = User::all();
        return view('pages.dashboard.kedai.edit',[
            'item' => $kedai,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kedai  $kedai
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kedai $kedai)
    {
        $data = $request->all();

        $kedai->update($data);

        return redirect()->route('dashboard.kedai.index');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kedai  $kedai
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kedai $kedai)
    {
        $kedai->delete();

        return redirect()->route('dashboard.kedai.index');
    }
}
