<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function __construct() {
        $this->middleware('auditor');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $destination = 'evidences/'; // your upload folder
        $image = $request->file('file');
        
        

        $name = explode('.', $image->getClientOriginalName());
        
        $filename = md5($name[0] . time() . rand(0, time())) . '.' . $name[count($name) - 1]; // get the filename

        $image->move($destination, $filename); // move file to destination

        return $this->getWithServerAddress($filename);
    }

    private function getWithServerAddress($fileName) {
    
        $host = $_SERVER['HTTP_HOST'];
        
        return (count(explode('//', $host))>1 ? '' : '//') . $host . '/evidences/' . $fileName;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
