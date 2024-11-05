<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nnjeim\World\World;

class Service
{
    public function getAge($date)
    {
        $dob = new DateTime($date);
        $today   = new DateTime('today');
        $age = $dob->diff($today)->y;
        return $age;
    }

    public function getNowDate(){
        return Carbon::now();
    }

    public function getCountryData()
    {
        $action =  World::countries();
        if ($action->success) {
            $countries = $action->data;
        }
        return $countries;
    }

    public function deleteImage($image): void
    {
        if (Storage::disk('public')->exists($image) && $image != 'images/undefined-user.png' && $image != 'images/video-preview.png'){
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

    public function updateImage(array $data, string $arrayKey, string $storePath, $previousImage){
        if (array_key_exists($arrayKey, $data)){
            if ($previousImage != null){
                if ($previousImage != 'images/undefined-user.png' || $previousImage != 'images/video-preview.png'){
                    $this->deleteImage($previousImage);
                }
            }
            $data[$arrayKey] = $data[$arrayKey]->store($storePath, 'public');
        }else{
            $data[$arrayKey] = $previousImage;
        }
        return  $data[$arrayKey];
    }

    public function secondToMinute($seconds){
        $minutes = floor($seconds / 60);  // Get the number of whole minutes
        $remaining_seconds = $seconds % 60;  // Get the remaining seconds

        return $minutes . "m " . $remaining_seconds . "s";
    }

    public function convertToDatetime($timestamp){
        return date('M d, Y ~ h:i A', strtotime($timestamp));
    }

    public function convertToDate($timestamp){
        return date('M d, Y', strtotime($timestamp));
    }

    public function convertToTimestamp($date, $time)
    {
        return Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
    }

    public function convertToTime($timestamp){
        return date('h:i A', strtotime($timestamp));
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
        $numbers = mt_rand(00000, 999999);
        return 'INV-'.$numbers.'-'.$numbers;
    }

    public function coachManagedTeams($coach){
        return Team::with('coaches', 'players')
            ->whereHas('coaches', function($q) use ($coach) {
                $q->where('coachId', $coach->id);
            })->get();
    }

    public function getNextDayTimestamp()
    {
        return Carbon::now()->copy()->addDay();
    }
}
