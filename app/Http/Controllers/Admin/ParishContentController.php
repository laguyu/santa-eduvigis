<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParishContentRequest;
use App\Http\Requests\UpdateParishContentRequest;
use App\Models\ParishContent;
use App\Services\ParishContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParishContentController extends Controller
{
    public function __construct(
        private readonly ParishContentService $parishContentService,
    ) {
    }

    public function index(): View
    {
        return view('admin.contents.index', [
            'contents' => $this->parishContentService->getPaginatedAdminContent(),
        ]);
    }

    public function create(): View
    {
        return view('admin.contents.create');
    }

    public function store(StoreParishContentRequest $request): RedirectResponse
    {
        $this->parishContentService->create($request->validated());

        return redirect()
            ->route('admin.contents.index')
            ->with('status', 'Seccion creada correctamente.');
    }

    public function edit(int $id): View
    {
        return view('admin.contents.edit', [
            'content' => $this->parishContentService->getById($id),
        ]);
    }

    public function update(UpdateParishContentRequest $request, int $id): RedirectResponse
    {
        $content = $this->parishContentService->getById($id);
        $this->parishContentService->update($content, $request->validated());

        return redirect()
            ->route('admin.contents.index')
            ->with('status', 'Seccion actualizada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $content = $this->parishContentService->getById($id);

        if ($content->isProtected()) {
            return redirect()
                ->route('admin.contents.index')
                ->with('status', 'Las secciones base del home no se pueden eliminar. Si no quieres mostrarla, desactiva la seccion.');
        }

        $this->parishContentService->delete($content);

        return redirect()
            ->route('admin.contents.index')
            ->with('status', 'Seccion eliminada correctamente.');
    }
}
