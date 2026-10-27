<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\University;
use App\Models\Course;
use App\Models\CourseIntake;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Operations Admin',
            'email' => 'ops@inoodex.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_manage_countries()
    {
        $country = Country::create([
            'name' => 'Canada',
            'code' => 'CA',
            'currency' => 'CAD',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.countries.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Countries/Index')
            ->has('countries.data', 1)
        );

        $postResponse = $this->actingAs($this->user)->post(route('admin.countries.store'), [
            'name' => 'Australia',
            'code' => 'AU',
            'currency' => 'AUD',
            'status' => 1,
        ]);
        $postResponse->assertRedirect(route('admin.countries.index'));
        $this->assertDatabaseHas('countries', ['code' => 'AU']);

        $updateResponse = $this->actingAs($this->user)->put(route('admin.countries.update', $country), [
            'name' => 'Canada Updated',
            'code' => 'CA',
            'currency' => 'CAD',
            'status' => 1,
        ]);
        $updateResponse->assertRedirect(route('admin.countries.index'));
        $this->assertDatabaseHas('countries', ['name' => 'Canada Updated']);
    }

    public function test_can_manage_universities_and_courses()
    {
        $country = Country::create(['name' => 'United Kingdom', 'code' => 'GB', 'status' => 1]);
        $university = University::create([
            'country_id' => $country->id,
            'name' => 'University of Oxford',
            'short_name' => 'Oxon',
            'status' => 1,
        ]);

        $uniIndex = $this->actingAs($this->user)->get(route('admin.universities.index'));
        $uniIndex->assertStatus(200);
        $uniIndex->assertInertia(fn (Assert $page) => $page
            ->component('Universities/Index')
            ->has('universities.data', 1)
        );

        $course = Course::create([
            'university_id' => $university->id,
            'name' => 'MSc Computer Science',
            'degree_level' => 'Master',
            'duration' => '1 Year',
            'tuition_fee' => 35000,
            'status' => 1,
        ]);

        $courseIndex = $this->actingAs($this->user)->get(route('admin.courses.index'));
        $courseIndex->assertStatus(200);
        $courseIndex->assertInertia(fn (Assert $page) => $page
            ->component('Courses/Index')
            ->has('courses.data', 1)
        );

        $courseUpdate = $this->actingAs($this->user)->put(route('admin.courses.update', $course), [
            'university_id' => $university->id,
            'name' => 'MSc Computer Science Updated',
            'degree_level' => 'Master',
            'duration' => '1 Year',
            'tuition_fee' => 36000,
            'status' => 1,
        ]);
        $courseUpdate->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['name' => 'MSc Computer Science Updated']);

        $intake = CourseIntake::create([
            'course_id' => $course->id,
            'intake_name' => 'Autumn 2026',
            'status' => 1,
        ]);

        $intakeIndex = $this->actingAs($this->user)->get(route('admin.course-intakes.index'));
        $intakeIndex->assertStatus(200);
        $intakeIndex->assertInertia(fn (Assert $page) => $page
            ->component('CourseIntakes/Index')
            ->has('intakes.data', 1)
        );
    }

    public function test_can_manage_currencies()
    {
        $currency = Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 135.50,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.currencies.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Currencies/Index')
            ->has('currencies.data', 1)
        );

        $createResponse = $this->actingAs($this->user)->get(route('admin.currencies.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertInertia(fn (Assert $page) => $page->component('Currencies/Create'));

        $editResponse = $this->actingAs($this->user)->get(route('admin.currencies.edit', $currency));
        $editResponse->assertStatus(200);
        $editResponse->assertInertia(fn (Assert $page) => $page->component('Currencies/Edit'));
    }

    public function test_can_view_settings()
    {
        Setting::create(['key' => 'app_name', 'value' => 'Inoodex Global CRM']);

        $response = $this->actingAs($this->user)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Settings/Index')
            ->has('settings')
        );
    }
}
