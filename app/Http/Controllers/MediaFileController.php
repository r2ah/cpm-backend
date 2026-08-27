<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaFilesRequest;
use App\Models\MediaFiles;
use App\Traits\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    use Upload;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaFilesRequest $request): JsonResponse
{
    if (!$request->hasFile('file')) {
        return response()->json([
            'success' => false,
            'message' => 'No se recibió ningún archivo.'
        ], 422);
    }

    $file = $request->file('file');
 
    $path = $this->UploadFile($file, 'Documents');

    $media = MediaFiles::create([
    'name' => $file->getClientOriginalName(),
    'path' => $path,
]);

    return response()->json([
    'success' => true,
    'data' => [
        'id' => $media->id,
        'name' => $media->name,
        'path' => $media->path,
    ],
    'message' => 'Archivo subido correctamente.'
]);
}



public function download(MediaFiles $file)
{
    if(!$file->path){
        return response()->json([
            'message'=>'Archivo no encontrado'
        ],404);
    }


    $path = storage_path(
        'app/public/'.$file->path
    );


    if(!file_exists($path)){

        return response()->json([
            'message'=>'Archivo físico no existe',
            'path'=>$path
        ],404);

    }


    return response()->download($path);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) : JsonResponse
    {
//get the file id and retrieve the file record from the database
        $file_id = $request->input('file_id');
        $file = MediaFiles::where('id', $file_id)->first();
        //check if the request has a file
        if ($request->hasFile('file')) {
            //check if the existing file is present and delete it from the storage
            if (!is_null($file->path)) {
                $this->deleteFile($file->path);
            }
            //upload the new file
            $path = $this->UploadFile($request->file('file'), 'Documents');

            //upadate the file path in the database
            $file->update(['name' => $request->file('file')->getClientOriginalName(),'path' => $path
]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : JsonResponse
    {
        //
    }
}
