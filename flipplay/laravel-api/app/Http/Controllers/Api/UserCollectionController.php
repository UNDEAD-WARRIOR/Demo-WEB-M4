<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\User;
use App\Models\Media;

class UserCollectionController extends Controller
{
    // Menampilkan koleksi media milik pengguna
    public function index(User $user)
    {
        $media = $user->mediaCollection()->latest('media_user.created_at')->get();

        return MediaResource::collection($media);
    }

    // Menambahkan media ke koleksi pengguna
    public function store(User $user, Media $media)
    {
        $alreadyExists = $user->mediaCollection()
            ->where('media.id', $media->id)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Media sudah ada dalam koleksi pengguna'
            ], 409);
        }

        $user->mediaCollection()->attach($media->id);

        return response()->json([
            'success' => true,
            'message' => 'Media berhasil ditambahkan ke koleksi'
        ], 201);
    }

    // Menghapus media dari koleksi pengguna
    public function destroy(User $user, Media $media)
    {
        $alreadyExists = $user->mediaCollection()
            ->where('media.id', $media->id)
            ->exists();

        if (!$alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Media tidak ditemukan dalam koleksi pengguna'
            ], 404);
        }

        $user->mediaCollection()->detach($media->id);

        return response()->json([
            'success' => true,
            'message' => 'Media berhasil dihapus dari koleksi'
        ]);
    }
}
