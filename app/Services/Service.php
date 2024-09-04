<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function getAge($date)
    {
        $dob = new DateTime($date);
        $today   = new DateTime('today');
        $age = $dob->diff($today)->y;
        return $age;
    }

    public function deleteImage($image): void
    {
        if (Storage::disk('public')->exists($image) && $image != 'images/undefined-user.png'){
            Storage::disk('public')->delete($image);
        }
    }
}
