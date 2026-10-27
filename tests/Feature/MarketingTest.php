<?php

namespace Tests\Feature;

use App\Models\MarketingCampaign;
use App\Models\MarketingPoster;
use App\Models\MarketingVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Marketing Manager',
            'email' => 'marketing@example.com',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_can_view_marketing_campaigns_list(): void
    {
        MarketingCampaign::create([
            'name' => 'Spring 2026 Promo',
            'boosting_status' => 'on',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/marketing/campaigns');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Marketing/Campaigns/Index')
            ->has('campaigns.data', 1)
        );
    }

    public function test_can_create_marketing_campaign(): void
    {
        $response = $this->actingAs($this->user)->post('/dashboard/marketing/campaigns', [
            'name' => 'Fall 2026 Intake Campaign',
            'boosting_status' => 'off',
            'notes' => 'Facebook and Google Display campaign',
        ]);

        $response->assertRedirect('/dashboard/marketing/campaigns');
        $this->assertDatabaseHas('marketing_campaigns', [
            'name' => 'Fall 2026 Intake Campaign',
            'boosting_status' => 'off',
        ]);
    }

    public function test_can_view_marketing_videos(): void
    {
        MarketingVideo::create([
            'video_name' => 'Study Abroad in UK Reel',
            'status' => 'editing',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/marketing/videos');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Marketing/Videos/Index')
            ->has('videos.data', 1)
        );
    }

    public function test_can_view_marketing_posters(): void
    {
        MarketingPoster::create([
            'poster_name' => 'Canada Visa Approved Banner',
            'status' => 'ready',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/marketing/posters');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Marketing/Posters/Index')
            ->has('posters.data', 1)
        );
    }

    public function test_can_view_marketing_documents(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard/marketing/documents');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Marketing/Documents/Index')
        );
    }

    public function test_can_view_marketing_video_and_poster_edit(): void
    {
        $video = MarketingVideo::create([
            'video_name' => 'Promo Reel 2',
            'status' => 'editing',
        ]);
        $poster = MarketingPoster::create([
            'poster_name' => 'USA Intake Flyer',
            'status' => 'designing',
        ]);

        $videoRes = $this->actingAs($this->user)->get("/dashboard/marketing/videos/{$video->id}/edit");
        $videoRes->assertStatus(200);
        $videoRes->assertInertia(fn (Assert $page) => $page->component('Marketing/Videos/Edit'));

        $posterRes = $this->actingAs($this->user)->get("/dashboard/marketing/posters/{$poster->id}/edit");
        $posterRes->assertStatus(200);
        $posterRes->assertInertia(fn (Assert $page) => $page->component('Marketing/Posters/Edit'));
    }
}
