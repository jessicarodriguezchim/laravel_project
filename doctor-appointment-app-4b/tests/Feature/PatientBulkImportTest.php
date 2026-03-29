<?php

use App\Jobs\ProcessPatientsImport;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

test('guests cannot access patient bulk import form', function () {
    $this->get(route('admin.patients.import'))
        ->assertRedirect();
});

test('authenticated user can open bulk import form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.patients.import'))
        ->assertOk();
});

test('bulk import upload dispatches background job and stores file', function () {
    Storage::fake('local');
    Bus::fake();

    $user = User::factory()->create();
    $csv = "name,email,id_number,phone,address\nJane,jane@example.com,ABC123,5551234567,Addr\n";
    $file = UploadedFile::fake()->createWithContent('patients.csv', $csv);

    $this->actingAs($user)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertRedirect(route('admin.patients.index'));

    Bus::assertDispatched(ProcessPatientsImport::class, function (ProcessPatientsImport $job) {
        return str_starts_with($job->path, 'imports/')
            && str_starts_with($job->importId, 'import_');
    });

    $stored = collect(Storage::disk('local')->allFiles('imports'));
    expect($stored)->not->toBeEmpty();
});

test('bulk import with Spanish slug headers creates patient when queue is sync', function () {
    Storage::fake('local');
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $csv = "nombre,email,numero_de_id,telefono\nJuan Perez,juan.slugheaders@example.com,ID-SLUG-1,5512345678\n";
    $file = UploadedFile::fake()->createWithContent('patients-es.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertRedirect(route('admin.patients.index'));

    expect(User::where('email', 'juan.slugheaders@example.com')->exists())->toBeTrue();
    expect(Patient::whereHas('user', fn ($q) => $q->where('email', 'juan.slugheaders@example.com'))->exists())->toBeTrue();
});

test('bulk import matches documento nacional style headers via column name patterns', function () {
    Storage::fake('local');
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $csv = "nombre,correo,documento_nacional,telefono\nMaria L,maria.docnat@example.com,DOC-998877,5588776655\n";
    $file = UploadedFile::fake()->createWithContent('patients-docnat.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertRedirect(route('admin.patients.index'));

    expect(User::where('email', 'maria.docnat@example.com')->exists())->toBeTrue();
    expect(Patient::whereHas('user', fn ($q) => $q->where('email', 'maria.docnat@example.com'))->exists())->toBeTrue();
});

test('bulk import rejects duplicate emails within the file', function () {
    Storage::fake('local');
    Bus::fake();

    $admin = User::factory()->create();
    $csv = "name,email,id_number,phone\nA,a@dup.test,D1,5551111111\nB,a@dup.test,D2,5552222222\n";
    $file = UploadedFile::fake()->createWithContent('dup-email.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertSessionHasErrors('file');

    Bus::assertNothingDispatched();
});

test('bulk import rejects duplicate id numbers within the file', function () {
    Storage::fake('local');
    Bus::fake();

    $admin = User::factory()->create();
    $csv = "name,email,id_number,phone\nA,a1@dup.test,SAME-ID,5551111111\nB,a2@dup.test,SAME-ID,5552222222\n";
    $file = UploadedFile::fake()->createWithContent('dup-id.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertSessionHasErrors('file');

    Bus::assertNothingDispatched();
});

test('bulk import rejects when email already exists in database', function () {
    Storage::fake('local');
    Bus::fake();

    $admin = User::factory()->create();
    User::factory()->create([
        'email' => 'taken@example.com',
        'id_number' => 'ID-TAKEN-1',
        'phone' => '5550000001',
    ]);

    $csv = "name,email,id_number,phone\nX,taken@example.com,ID-NEW-99,5559999999\n";
    $file = UploadedFile::fake()->createWithContent('existing-email.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertSessionHasErrors('file');

    Bus::assertNothingDispatched();
});

test('bulk import rejects when document already exists in database', function () {
    Storage::fake('local');
    Bus::fake();

    $admin = User::factory()->create();
    User::factory()->create([
        'email' => 'doc@example.com',
        'id_number' => 'DOC-7788',
        'phone' => '5550000002',
    ]);

    $csv = "name,email,id_number,phone\nY,newmail@example.com,DOC-7788,5559999998\n";
    $file = UploadedFile::fake()->createWithContent('existing-doc.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.patients.import.store'), ['file' => $file])
        ->assertSessionHasErrors('file');

    Bus::assertNothingDispatched();
});
