<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\ParishContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class ParishContentDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): self
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        return $this;
    }

    public function test_protected_home_section_cannot_be_deleted(): void
    {
        $content = ParishContent::query()->where('key', 'hero')->firstOrFail();

        $response = $this
            ->withoutMiddleware(PreventRequestForgery::class)
            ->actingAsAdmin()
            ->delete(route('admin.contents.destroy', $content->id));

        $response->assertRedirect(route('admin.contents.index'));
        $response->assertSessionHas('status', 'Las secciones base del home no se pueden eliminar. Si no quieres mostrarla, desactiva la seccion.');
        $this->assertDatabaseHas('parish_contents', ['id' => $content->id]);
    }

    public function test_custom_section_can_be_deleted(): void
    {
        $content = ParishContent::query()->create([
            'key' => 'retiros',
            'title' => 'Retiros',
            'display_order' => 10,
            'is_active' => true,
        ]);

        $response = $this
            ->withoutMiddleware(PreventRequestForgery::class)
            ->actingAsAdmin()
            ->delete(route('admin.contents.destroy', $content->id));

        $response->assertRedirect(route('admin.contents.index'));
        $response->assertSessionHas('status', 'Seccion eliminada correctamente.');
        $this->assertDatabaseMissing('parish_contents', ['id' => $content->id]);
    }

    public function test_protected_home_section_slug_cannot_be_changed(): void
    {
        $content = ParishContent::query()->where('key', 'hero')->firstOrFail();

        $response = $this
            ->withoutMiddleware(PreventRequestForgery::class)
            ->actingAsAdmin()
            ->from(route('admin.contents.edit', $content->id))
            ->put(route('admin.contents.update', $content->id), [
                'key' => 'hero-editado',
                'title' => 'Hero',
                'subtitle' => 'Subtitulo',
                'body' => 'Contenido',
                'display_order' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.contents.edit', $content->id));
        $response->assertSessionHasErrors('key');
        $this->assertDatabaseHas('parish_contents', [
            'id' => $content->id,
            'key' => 'hero',
        ]);
    }
}