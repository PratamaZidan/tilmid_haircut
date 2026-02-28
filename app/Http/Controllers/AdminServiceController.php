<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminServiceController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->query('q',''));
        $category = $r->query('category','all'); // all|haircut|treatment

        $servicesQuery = Service::query()->orderBy('category')->orderBy('sort_order');

        if ($category !== 'all') {
            $servicesQuery->where('category', $category);
        }

        if ($q !== '') {
            $servicesQuery->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhere('code','like',"%{$q}%");
            });
        }

        $services = $servicesQuery->get();

        return view('admin.price', compact('services','q','category'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'code' => ['required','string','max:50','alpha_dash', 'unique:services,code'],
            'name' => ['required','string','max:120'],
            'price' => ['required','integer','min:0'],
            'category' => ['required', Rule::in(['haircut','treatment'])],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['required', Rule::in(['0','1'])],
            'is_public' => ['required', Rule::in(['0','1'])],
        ]);

        Service::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'category' => $data['category'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)$data['is_active'],
            'is_public' => (bool)$data['is_public'],
        ]);

        return back()->with('ok', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $r, Service $service)
    {
        $data = $r->validate([
            'code' => ['required','string','max:50','alpha_dash', Rule::unique('services','code')->ignore($service->id)],
            'name' => ['required','string','max:120'],
            'price' => ['required','integer','min:0'],
            'category' => ['required', Rule::in(['haircut','treatment'])],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active' => ['required', Rule::in(['0','1'])],
            'is_public' => ['required', Rule::in(['0','1'])],
        ]);

        $service->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'category' => $data['category'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)$data['is_active'],
            'is_public' => (bool)$data['is_public'],
        ]);

        return back()->with('ok', 'Layanan berhasil diupdate.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('ok', 'Layanan berhasil dihapus.');
    }

    public function toggleActive(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);
        return back()->with('ok', 'Status layanan diperbarui.');
    }
}