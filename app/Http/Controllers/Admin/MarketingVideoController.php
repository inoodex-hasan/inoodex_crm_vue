<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingVideo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MarketingVideoController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingVideo::latest();

        if ($search = $request->get('search')) {
            $query->where('video_name', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $videos = $query->paginate(15)->withQueryString();

        return Inertia::render('Marketing/Videos/Index', [
            'videos' => $videos,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'video_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'not_edited', 'editing', 'ready', 'uploaded'])],
        ]);

        MarketingVideo::create($validated);

        return redirect()
            ->route('admin.marketing.videos.index')
            ->with('success', 'Video added successfully.');
    }

    public function edit(MarketingVideo $video)
    {
        return Inertia::render('Marketing/Videos/Edit', [
            'video' => $video,
        ]);
    }

    public function update(Request $request, MarketingVideo $video)
    {
        $validated = $request->validate([
            'video_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'not_edited', 'editing', 'ready', 'uploaded'])],
        ]);

        $video->update($validated);

        return redirect()
            ->route('admin.marketing.videos.index')
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(MarketingVideo $video)
    {
        $video->delete();
        return redirect()
            ->route('admin.marketing.videos.index')
            ->with('success', 'Video deleted successfully.');
    }
}
