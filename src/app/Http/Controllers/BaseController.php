<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class BaseController extends Controller
{
    public const ITEM_IMAGE_DIRECTORY = 'item-images';
    public const ITEM_IMAGE_PREFIX = 'item';
    public const USER_IMAGE_DIRECTORY = 'user-images';
    public const USER_IMAGE_PREFIX = 'user';
    public const DEFAULT_PROFILE_IMAGE_DIRECTORY = 'default-profiles';
    public const DEFAULT_PROFILE_IMAGE_NAME = 'default-profile.jpeg';
    public const COACHTECH_IMAGE_DIRECTORY = 'default-items';
    public const TRASH_IMAGE_DIRECTORY = 'trash-images';
    public const TRASH_IMAGE_NAME = 'trash.jpeg';
    public const CHECK_LOGIN_TIME = 1;

    public function getPublicImageNumber($imageDirectoryName = null, $keyword = null)
    {
        $files = Storage::disk('public')->files($imageDirectoryName ?? '');

        if($keyword){
            if ($imageDirectoryName) {
                $regex = '/' . preg_quote($imageDirectoryName, '/') . '\/' . preg_quote($keyword, '/') . '/i';
            } else {
                $regex = '/' . preg_quote($keyword, '/') . '/i';
            }

            $imageFiles = array_filter($files, function ($file) use ($regex) {
                return preg_match($regex, $file);
            });
        }else{//$keyword
            $imageFiles = $files;
        }//$keyword

        return count($imageFiles);
    }
    
}