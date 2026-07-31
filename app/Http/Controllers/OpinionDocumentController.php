<?php

namespace App\Http\Controllers;

use App\Models\OpinionDocument;
use Illuminate\Support\Facades\Storage;

class OpinionDocumentController extends Controller
{

    public function download(OpinionDocument $document)
    {

        if(!Storage::disk('public')->exists($document->path)){

            abort(404);

        }


        return Storage::disk('public')
            ->download(
                $document->path,
                $document->original_name
            );

    }

}