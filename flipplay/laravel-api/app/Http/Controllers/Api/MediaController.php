<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    // Menampilkan semua media
    public function index()
    {
        $media = Media::with('users')->latest()->get();

        return MediaResource::collection($media);
    }

    // Menambahkan media baru
    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    // Menampilkan detail media
    public function show(Media $media)
    {
        return new MediaResource($media->load('users'));
    }

    // Memperbarui media
    public function update(UpdateMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media->refresh()->load('users'));
    }

    // Mencari media berdasarkan judul
    public function search(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $media = Media::with('users')
            ->where('title', 'like', '%' . $request->title . '%')
            ->get();

        return MediaResource::collection($media);
    }

    // Menghapus media
    public function destroy(Media $media)
    {
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media berhasil dihapus'
        ]);
    }
}
