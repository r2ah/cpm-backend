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
    public function store(StoreMediaFilesRequest $request) : JsonResponse
    {
        $file_details = [];

        //check if request has files
        if ($request->hasFile('files')) {
          // loop through each file and upload it

            foreach ($request->file('files') as $key => $file) {
                try {
                    // Validate file
                    /*
                    $request->validate([
                        'file' => 'required|mimes:jpg,png,pdf|max:2048',
                    ]);                    
                    */
                    //Upload to Storage
                    $path = $this->UploadFile($request->file('file'), 'Documents');

                    //reformat the file details
                    array_push($file_details, [
                        'path' => $path,
                    ]);                
                } catch (FileException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File operation failed: ' . $e->getMessage()
                    ], 404);
                }
            }

            //add each file details to database
            foreach ($file_details as $key => $value) {
                MediaFiles::create(['path' => $value]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $file_details,
            'message' => 'File(s) Uploaded Successfully'
        ], 200);
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
            $file->update(['path' => $path]);
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
