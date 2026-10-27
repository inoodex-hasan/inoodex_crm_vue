<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingPoster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MarketingPosterController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingPoster::latest();

        if ($search = $request->get('search')) {
            $query->where('poster_name', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $posters = $query->paginate(15)->withQueryString();

        return Inertia::render('Marketing/Posters/Index', [
            'posters' => $posters,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'poster_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'not_ready', 'designing', 'ready', 'uploaded'])],
        ]);

        MarketingPoster::create($validated);

        return redirect()
            ->route('admin.marketing.posters.index')
            ->with('success', 'Poster added successfully.');
    }

    public function edit(MarketingPoster $poster)
    {
        return Inertia::render('Marketing/Posters/Edit', [
            'poster' => $poster,
        ]);
    }

    public function update(Request $request, MarketingPoster $poster)
    {
        $validated = $request->validate([
            'poster_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'not_ready', 'designing', 'ready', 'uploaded'])],
        ]);

        $poster->update($validated);

        return redirect()
            ->route('admin.marketing.posters.index')
            ->with('success', 'Poster updated successfully.');
    }

    public function destroy(MarketingPoster $poster)
    {
        $poster->delete();
        return redirect()
            ->route('admin.marketing.posters.index')
            ->with('success', 'Poster deleted successfully.');
    }
}
