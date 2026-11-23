<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\News;
use App\Support\Html;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * CRUD generik. Perilaku tiap menu ditentukan oleh config/admin_resources.php.
 */
class ResourceController extends Controller
{
    protected function def(string $resource): array
    {
        $def = config("admin_resources.$resource");
        abort_unless($def, 404);
        abort_unless(auth()->user()?->canManage($resource), 403, 'Anda tidak memiliki akses ke menu ini.');
        $def['key'] = $resource;
        $def['fields'] = $def['fields'] ?? [];

        return $def;
    }

    public function index(Request $request, string $resource)
    {
        $def = $this->def($resource);
        $query = $def['model']::query();

        if (! empty($def['with'])) {
            $query->with($def['with']);
        }

        // Pencarian (kolom biasa atau relasi "relasi.kolom")
        if (($q = trim((string) $request->query('q'))) !== '' && ! empty($def['search'])) {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($w) use ($def, $like) {
                foreach ($def['search'] as $col) {
                    if (str_contains($col, '.')) {
                        [$relation, $column] = explode('.', $col, 2);
                        $w->orWhereHas($relation, fn ($r) => $r->where($column, 'like', $like));
                    } else {
                        $w->orWhere($col, 'like', $like);
                    }
                }
            });
        }

        // Filter (hanya kolom yang didaftarkan di definisi)
        foreach (($def['filters'] ?? []) as $col => $filter) {
            $value = $request->query("f_$col");
            if ($value !== null && $value !== '') {
                $query->where($col, is_numeric($value) ? (int) $value : $value);
            }
        }

        // Urutan: hanya kolom tabel yang tampil di daftar (daftar putih -> aman dari injeksi)
        $sortable = collect(array_keys($def['columns']))->reject(fn ($c) => str_contains($c, '.'))->all();
        $sort = $request->query('sort');
        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $request->query('dir') === 'desc' ? 'desc' : 'asc');
        }
        [$col, $dir] = $def['order'] ?? ['id', 'desc'];
        $query->orderBy($col, $dir);
        if (! empty($def['order2'])) {
            $query->orderBy($def['order2'][0], $def['order2'][1]);
        }
        $query->orderBy('id');

        $perPage = (int) $request->query('per_page');
        if (! in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        return view('admin.resource.index', [
            'def' => $def,
            'items' => $query->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
        ]);
    }

    public function create(string $resource)
    {
        $def = $this->def($resource);
        abort_if(($def['create'] ?? true) === false, 404);

        return view('admin.resource.form', [
            'def' => $def,
            'item' => null,
            'newsOptions' => $def['key'] === 'announcements'
                ? News::published()->orderByDesc('published_at')->get(['title', 'slug'])
                : collect(),
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $def = $this->def($resource);
        abort_if(($def['create'] ?? true) === false, 404);

        $data = $this->collect($request, $def, null);

        try {
            $created = $def['model']::create($data);
        } catch (QueryException $e) {
            $this->deleteStoredImages($def, $data);

            return $this->dbError($e);
        }

        ActivityLog::record('create', "Menambah {$def['label']}", $created);

        return redirect()->route('admin.resource.index', $resource)->with('ok', 'Data berhasil ditambahkan.');
    }

    public function edit(string $resource, int $id)
    {
        $def = $this->def($resource);
        abort_if(($def['edit'] ?? true) === false, 404);

        return view('admin.resource.form', [
            'def' => $def,
            'item' => $def['model']::findOrFail($id),
            'newsOptions' => $def['key'] === 'announcements'
                ? News::published()->orderByDesc('published_at')->get(['title', 'slug'])
                : collect(),
        ]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $def = $this->def($resource);
        abort_if(($def['edit'] ?? true) === false, 404);

        $item = $def['model']::findOrFail($id);
        $existingMedia = [];
        foreach ($def['fields'] as $field) {
            if ($field['type'] === 'images') {
                $existingMedia[$field['name']] = array_values(array_filter((array) $item->{$field['name']}));
            }
        }
        $data = $this->collect($request, $def, $item);
        $newMedia = [];
        foreach ($existingMedia as $name => $paths) {
            $newMedia[$name] = array_values(array_diff((array) ($data[$name] ?? []), $paths));
        }

        try {
            $item->update($data);
        } catch (QueryException $e) {
            foreach ($newMedia as $paths) {
                foreach ($paths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return $this->dbError($e);
        }

        foreach ($existingMedia as $name => $paths) {
            foreach (array_diff($paths, (array) ($data[$name] ?? [])) as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        ActivityLog::record('update', "Mengubah {$def['label']}", $item, $this->diff($def, $item));

        return redirect()->route('admin.resource.index', $resource)->with('ok', 'Data berhasil diperbarui.');
    }

    public function destroy(string $resource, int $id)
    {
        $def = $this->def($resource);
        $item = $def['model']::findOrFail($id);
        $mediaToDelete = [];

        foreach ($def['fields'] as $f) {
            if ($f['type'] === 'images') {
                foreach ((array) $item->{$f['name']} as $path) {
                    $mediaToDelete[] = $path;
                }
            } elseif (in_array($f['type'], ['image', 'file'], true) && $item->{$f['name']}) {
                Storage::disk('public')->delete($item->{$f['name']});
            }
        }
        ActivityLog::record('delete', "Menghapus {$def['label']}", $item);
        $item->delete();
        foreach ($mediaToDelete as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.resource.index', $resource)->with('ok', 'Data dihapus.');
    }

    /** Validasi + olah unggahan berkas; mengembalikan array siap simpan. */
    protected function collect(Request $request, array $def, ?Model $item): array
    {
        $rules = [];
        foreach ($def['fields'] as $f) {
            $rules[$f['name']] = match ($f['type']) {
                'image' => $f['rules'] ?? 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                'images' => 'nullable|array|max:'.$this->remainingImageCapacity($request, $f['name'], $item),
                'file' => $f['rules'] ?? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:20480',
                'checkbox' => 'nullable|boolean',
                default => $f['rules'] ?? 'nullable',
            };

            if ($f['type'] === 'images') {
                $rules[$f['name'].'.*'] = 'image|mimes:jpg,jpeg,png,webp|max:4096';
                $rules['remove_'.$f['name']] = 'nullable|array';
                $rules['remove_'.$f['name'].'.*'] = 'string';
            }
        }

        $data = $request->validate($rules);

        foreach ($def['fields'] as $f) {
            $name = $f['name'];

            if (in_array($f['type'], ['image', 'file'], true)) {
                unset($data[$name]);
                if ($def['key'] === 'pages' && $name === 'image' && $item && $item->$name) {
                    $content = (string) ($data['content'] ?? $item->content ?? '');
                    if (str_contains($content, 'storage/'.$item->$name) && ($request->hasFile($name) || $request->boolean("remove_$name"))) {
                        throw ValidationException::withMessages([
                            $name => 'Gambar ini masih digunakan di isi halaman. Hapus gambar dari isi halaman terlebih dahulu.',
                        ]);
                    }
                }

                if ($request->hasFile($name)) {
                    $data[$name] = $request->file($name)->store("uploads/{$def['key']}", 'public');
                    if ($item && $item->$name) {
                        Storage::disk('public')->delete($item->$name);
                    }
                } elseif ($item && $request->boolean("remove_$name")) {
                    if ($item->$name) {
                        Storage::disk('public')->delete($item->$name);
                    }
                    $data[$name] = null;
                }
            } elseif ($f['type'] === 'images') {
                unset($data[$name]);
                unset($data["remove_$name"]);
                $existing = array_values(array_filter((array) ($item ? $item->{$name} : [])));
                $removed = array_intersect($existing, (array) $request->input("remove_$name", []));
                $content = (string) ($data['content'] ?? ($item ? $item->content : ''));
                if (collect($removed)->contains(fn ($path) => str_contains($content, 'storage/'.$path))) {
                    throw ValidationException::withMessages([
                        "remove_$name" => 'Gambar ini masih digunakan di isi halaman. Hapus gambar dari isi halaman terlebih dahulu.',
                    ]);
                }

                $paths = array_values(array_diff($existing, $removed));
                foreach ((array) $request->file($name, []) as $file) {
                    $paths[] = $file->store("uploads/{$def['key']}", 'public');
                }
                $data[$name] = $paths;
            } elseif ($f['type'] === 'editor') {
                $data[$name] = Html::clean($data[$name] ?? null);
            } elseif ($f['type'] === 'checkbox') {
                $data[$name] = $request->boolean($name);
            } elseif ($f['type'] === 'number' && ($data[$name] ?? null) === null && array_key_exists('default', $f)) {
                $data[$name] = $f['default'];
            }
        }

        return $data;
    }

    protected function deleteStoredImages(array $def, array $data): void
    {
        foreach ($def['fields'] as $field) {
            if ($field['type'] === 'images') {
                foreach ((array) ($data[$field['name']] ?? []) as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }

    protected function remainingImageCapacity(Request $request, string $name, ?Model $item): int
    {
        $existing = array_values(array_filter((array) ($item ? $item->{$name} : [])));
        $removed = array_intersect($existing, (array) $request->input("remove_$name", []));

        return max(0, 10 - count(array_diff($existing, $removed)));
    }

    /** Ringkasan perubahan untuk log (isi editor tidak disimpan utuh; nilai dipotong). */
    protected function diff(array $def, Model $item): array
    {
        $editors = collect($def['fields'])->where('type', 'editor')->pluck('name')->all();
        $imageCollections = collect($def['fields'])->where('type', 'images')->pluck('name')->all();
        $diff = [];

        foreach ($item->getChanges() as $field => $new) {
            if ($field === 'updated_at') {
                continue;
            }
            if (in_array($field, $editors, true)) {
                $diff[$field] = '(isi diubah)';
            } elseif (in_array($field, $imageCollections, true)) {
                $diff[$field] = [
                    'dari' => count((array) $item->getOriginal($field)).' gambar',
                    'ke' => count((array) $new).' gambar',
                ];
            } else {
                $diff[$field] = [
                    'dari' => Str::limit((string) $item->getOriginal($field), 80),
                    'ke' => Str::limit((string) $new, 80),
                ];
            }
        }

        return $diff;
    }

    protected function dbError(QueryException $e)
    {
        report($e);

        return back()->withInput()->withErrors([
            'db' => 'Data tidak dapat disimpan. Kemungkinan duplikat (mis. slug sudah dipakai pada seksi yang sama) atau ada isian wajib yang kosong.',
        ]);
    }
}
