<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsPostRequest;
use App\Http\Requests\UpdateNewsPostRequest;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsPostController extends Controller
{
    public function index(): View
    {
        return view('admin.news.index', [
            'posts' => NewsPost::query()
                ->orderByDesc('published_at')
                ->orderBy('display_order')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(StoreNewsPostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('parish/news', 'public');
        }

        unset($data['cover_image']);

        NewsPost::query()->create($data);

        return redirect()->route('admin.news.index')->with('status', 'Noticia creada correctamente.');
    }

    public function edit(int $id): View
    {
        return view('admin.news.edit', [
            'post' => NewsPost::query()->findOrFail($id),
        ]);
    }

    public function update(UpdateNewsPostRequest $request, int $id): RedirectResponse
    {
        $post = NewsPost::query()->findOrFail($id);
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = $post->published_at ?? now();
        }

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image_path) {
                Storage::disk('public')->delete($post->cover_image_path);
            }

            $data['cover_image_path'] = $request->file('cover_image')->store('parish/news', 'public');
        }

        unset($data['cover_image']);

        $post->update($data);

        return redirect()->route('admin.news.index')->with('status', 'Noticia actualizada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $post = NewsPost::query()->findOrFail($id);

        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }

        $post->delete();

        return redirect()->route('admin.news.index')->with('status', 'Noticia eliminada correctamente.');
    }
}
