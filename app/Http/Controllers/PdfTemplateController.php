<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Apenas admin (role 1)
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado. Apenas administradores podem gerenciar templates.');
        }

        $search = $request->input('search');

        $templates = PdfTemplate::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pdf-templates.index', compact('templates', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        return view('pdf-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['header_image', 'footer_image']);

        // Upload de imagens
        if ($request->hasFile('header_image')) {
            $data['header_image'] = $request->file('header_image')->store('pdf-templates/headers', 'public');
        }

        if ($request->hasFile('footer_image')) {
            $data['footer_image'] = $request->file('footer_image')->store('pdf-templates/footers', 'public');
        }

        PdfTemplate::create($data);

        return redirect()->route('pdf-templates.index')
            ->with('success', 'Template criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PdfTemplate $pdfTemplate)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        return view('pdf-templates.show', compact('pdfTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PdfTemplate $pdfTemplate)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        return view('pdf-templates.edit', compact('pdfTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PdfTemplate $pdfTemplate)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['header_image', 'footer_image']);

        // Upload de imagens
        if ($request->hasFile('header_image')) {
            if ($pdfTemplate->header_image) {
                Storage::disk('public')->delete($pdfTemplate->header_image);
            }
            $data['header_image'] = $request->file('header_image')->store('pdf-templates/headers', 'public');
        }

        if ($request->hasFile('footer_image')) {
            if ($pdfTemplate->footer_image) {
                Storage::disk('public')->delete($pdfTemplate->footer_image);
            }
            $data['footer_image'] = $request->file('footer_image')->store('pdf-templates/footers', 'public');
        }

        $pdfTemplate->update($data);

        return redirect()->route('pdf-templates.index')
            ->with('success', 'Template atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PdfTemplate $pdfTemplate)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        // Deletar imagens
        if ($pdfTemplate->header_image) {
            Storage::disk('public')->delete($pdfTemplate->header_image);
        }
        if ($pdfTemplate->footer_image) {
            Storage::disk('public')->delete($pdfTemplate->footer_image);
        }

        $pdfTemplate->delete();

        return redirect()->route('pdf-templates.index')
            ->with('success', 'Template excluído com sucesso.');
    }

    public function preview(Request $request)
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Acesso negado.');
        }

        // Retorna preview em tempo real via AJAX
        $data = $request->all();

        return response()->json([
            'success' => true,
            'preview' => view('pdf-templates.preview-content', compact('data'))->render()
        ]);
    }
}
