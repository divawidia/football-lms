<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
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

    public function getAcademyTeams(){
        return Team::where('teamSide', 'Academy Team')->get();
    }

    public function getUserFullName(User $user): string
    {
        return $user->firstName . ' ' . $user->lastName;
    }

    public function storeImage(array $data, string $arrayKey, string $storePath, string $defaultImage){
        if (array_key_exists($arrayKey, $data)){
            $data[$arrayKey] = $data[$arrayKey]->store($storePath, 'public');
        }else{
            $data[$arrayKey] = $defaultImage;
        }
        return  $data[$arrayKey];
    }
}
