<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function secondToMinute($seconds){
        $minutes = floor($seconds / 60);  // Get the number of whole minutes
        $remaining_seconds = $seconds % 60;  // Get the remaining seconds

        return $minutes . "m " . $remaining_seconds . "s";
    }

    public function convertTimestamp($timestamp){
        return date('M d, Y ~ h:i A', strtotime($timestamp));
    }

    public function priceFormat($price){
        return 'Rp. ' . number_format($price);
    }

    public function description($description){
        if ($description == null){
            $description = 'No description yet';
        }else{
            $description = Str::limit($description, 150);
        }
        return $description;
    }

    public function generateInvoiceNumber(){
        $numbers = range(1, 10000);
        return 'INV-'.shuffle($numbers);
    }
}
