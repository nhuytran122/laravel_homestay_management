<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;

trait HasFileUpload
{
    public function uploadFileToCollection(HasMedia $model, ?UploadedFile $file, string $collection): void{
        if($file && $file->isValid()){
            $model->addMedia($file)->toMediaCollection($collection);
        }
    }

    public function replaceMedia(HasMedia $model, UploadedFile $file, string $collection): void
    {
        $model->clearMediaCollection($collection);
        $model->addMedia($file)->toMediaCollection($collection);
    }

    public function deleteMedia(HasMedia $model, string $collection)
    {
        $model->clearMediaCollection($collection);
    }

    public function deleteMediaById(HasMedia $model, int $mediaId)
    {
        $media = $model->media()->find($mediaId);
        if ($media) {
            $media->delete();
        }
    }
}


    