<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parish_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        DB::table('parish_contents')->insert([
            [
                'key' => 'hero',
                'title' => 'Parroquia Santa Eduviges',
                'subtitle' => 'Una comunidad que ora, sirve y acompana.',
                'body' => 'Bienvenidos a nuestro templo. Encuentra aqui horarios, sacramentos, noticias parroquiales y espacios para crecer en la fe.',
                'cta_text' => 'Solicitar intencion de misa',
                'cta_url' => '#contacto',
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mass_schedule',
                'title' => 'Horarios de Misas',
                'subtitle' => 'Eucaristia diaria y dominical.',
                'body' => "Lunes a Viernes: 7:00 a.m. y 6:30 p.m.\nSabado: 7:00 a.m. y 6:00 p.m.\nDomingo: 7:00 a.m., 9:00 a.m., 11:00 a.m. y 6:00 p.m.",
                'cta_text' => 'Ver confesion',
                'cta_url' => '#sacramentos',
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sacraments',
                'title' => 'Sacramentos',
                'subtitle' => 'Acompanamiento pastoral en cada etapa de vida.',
                'body' => "Bautismos: Sabados 10:00 a.m.\nMatrimonios: Formacion previa obligatoria.\nConfesiones: Martes y Jueves 5:30 p.m.",
                'cta_text' => 'Iniciar proceso',
                'cta_url' => '#contacto',
                'display_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'news',
                'title' => 'Noticias Parroquiales',
                'subtitle' => 'Actualidad de nuestra comunidad.',
                'body' => 'Rosario misionero cada primer viernes de mes. Campana de solidaridad para familias vulnerables durante todo el mes.',
                'cta_text' => 'Ver actividades',
                'cta_url' => '#comunidad',
                'display_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'community',
                'title' => 'Vida Comunitaria',
                'subtitle' => 'Pastoral juvenil, catequesis y grupos de oracion.',
                'body' => 'Sumanos a nuestros ministerios: liturgia, lectores, coro, caridad y formacion biblica.',
                'cta_text' => 'Quiero servir',
                'cta_url' => '#contacto',
                'display_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact',
                'title' => 'Contacto Parroquial',
                'subtitle' => 'Estamos para atenderte.',
                'body' => "Direccion: Calle Principal, Barrio Central\nTelefono: +57 300 000 0000\nEmail: contacto@santaeduviges.org",
                'cta_text' => 'Escribir por correo',
                'cta_url' => 'mailto:contacto@santaeduviges.org',
                'display_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parish_contents');
    }
};
