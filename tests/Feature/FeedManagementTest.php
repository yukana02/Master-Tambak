<?php

namespace Tests\Feature;

use App\Models\Feed;
use App\Models\FeedCategory;
use App\Models\Pond;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FeedManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_feed(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));

        $this->actingAs($user)
            ->post(route('feeds.store'), [
                'name' => 'Pelet Pembesaran',
                'composition' => 'Protein 30%, lemak 5%',
                'sack_weight_kg' => 30,
                'fcr' => 1.5,
                'is_active' => 1,
            ])
            ->assertRedirect(route('feeds.index'));

        $this->assertDatabaseHas('feeds', [
            'name' => 'Pelet Pembesaran',
            'sack_weight_kg' => 30,
            'fcr' => 1.5,
            'is_active' => true,
        ]);
    }

    public function test_kasir_cannot_manage_feeds(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Kasir']));

        $this->actingAs($user)
            ->get(route('feeds.index'))
            ->assertForbidden();
    }

    public function test_feeding_log_drives_pond_harvest_progress(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));
        $feed = Feed::create([
            'name' => 'Pelet 781',
            'composition' => 'Protein 30%',
            'sack_weight_kg' => 30,
            'fcr' => 1.5,
            'is_active' => true,
        ]);

        $pond = Pond::create([
            'name' => 'Kolam Test',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'feed_id' => $feed->id,
            'target_harvest_weight_kg' => 100,
        ]);

        $this->actingAs($user)
            ->post(route('ponds.feedings.store', $pond), [
                'feed_id' => $feed->id,
                'fed_at' => now()->toDateString(),
                'quantity' => 5,
                'unit' => 'sak',
                'notes' => 'Pemberian pakan pagi',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pond_feedings', [
            'pond_id' => $pond->id,
            'feed_id' => $feed->id,
            'quantity' => 5,
            'unit' => 'sak',
            'feed_weight_kg' => 150,
            'estimated_meat_kg' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('ponds.index'))
            ->assertOk()
            ->assertSee('Pelet 781')
            ->assertSee('100,0 kg')
            ->assertSee('Target tercapai');
    }

    public function test_feeding_log_accepts_kg_input(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));
        $feed = Feed::create([
            'name' => 'Pelet Halus',
            'sack_weight_kg' => 25,
            'fcr' => 2,
            'is_active' => true,
        ]);
        $pond = Pond::create([
            'name' => 'Kolam Kg',
            'fish_type' => 'Nila',
            'fish_count' => 500,
            'target_harvest_weight_kg' => 30,
        ]);

        $this->actingAs($user)
            ->post(route('ponds.feedings.store', $pond), [
                'feed_id' => $feed->id,
                'fed_at' => now()->toDateString(),
                'quantity' => 20,
                'unit' => 'kg',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pond_feedings', [
            'pond_id' => $pond->id,
            'feed_weight_kg' => 20,
            'estimated_meat_kg' => 10,
        ]);
    }

    public function test_pond_show_summarizes_feed_totals_by_category_and_feed(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));
        $category = FeedCategory::create(['name' => 'Pembesaran']);
        $feed = Feed::create([
            'feed_category_id' => $category->id,
            'name' => 'Pelet Summary',
            'sack_weight_kg' => 30,
            'fcr' => 1.5,
            'is_active' => true,
        ]);
        $pond = Pond::create([
            'name' => 'Kolam Summary',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'seed_source' => 'Pak Rasyid',
            'dead_fish_count' => 12,
            'pond_size_notes' => '10 x 20 m',
        ]);

        $this->actingAs($user)->post(route('ponds.feedings.store', $pond), [
            'feed_id' => $feed->id,
            'fed_at' => now()->toDateString(),
            'quantity' => 2,
            'unit' => 'pembesaran',
        ]);

        $this->actingAs($user)
            ->get(route('ponds.show', $pond))
            ->assertOk()
            ->assertSee('Sumber Bibit')
            ->assertSee('Pak Rasyid')
            ->assertSee('Ikan Mati')
            ->assertSee('12 ekor')
            ->assertSee('10 x 20 m')
            ->assertSee('Total Pakan Aktif')
            ->assertSee('Pembesaran')
            ->assertSee('Pelet Summary')
            ->assertSee('2,00 pembesaran')
            ->assertSee('60,00 kg');
    }

    public function test_super_admin_can_create_user_from_role_menu(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Role::create(['name' => 'Super Admin']));
        Role::create(['name' => 'Admin']);

        $this->actingAs($superAdmin)
            ->post(route('roles.users.store'), [
                'name' => 'Operator Kolam',
                'email' => 'operator@example.test',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'Admin',
            ])
            ->assertRedirect();

        $user = User::where('email', 'operator@example.test')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_pond_list_shows_predicted_harvest_date_from_daily_average(): void
    {
        Carbon::setTestNow('2026-05-16 08:00:00');

        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));
        $feed = Feed::create([
            'name' => 'Pelet Prediksi',
            'sack_weight_kg' => 30,
            'fcr' => 1.5,
            'is_active' => true,
        ]);
        $pond = Pond::create([
            'name' => 'Kolam Prediksi',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'target_harvest_weight_kg' => 100,
        ]);

        $this->actingAs($user)->post(route('ponds.feedings.store', $pond), [
            'feed_id' => $feed->id,
            'fed_at' => '2026-05-01',
            'quantity' => 1,
            'unit' => 'sak',
        ]);
        $this->actingAs($user)->post(route('ponds.feedings.store', $pond), [
            'feed_id' => $feed->id,
            'fed_at' => '2026-05-10',
            'quantity' => 1,
            'unit' => 'sak',
        ]);

        $this->actingAs($user)
            ->get(route('ponds.index'))
            ->assertOk()
            ->assertSee('Prediksi panen')
            ->assertSee('25 May 2026');

        Carbon::setTestNow();
    }

    public function test_harvest_confirmation_archives_summary_and_resets_active_feedings(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin']));
        $feed = Feed::create([
            'name' => 'Pelet Panen',
            'sack_weight_kg' => 30,
            'fcr' => 1.5,
            'is_active' => true,
        ]);
        $pond = Pond::create([
            'name' => 'Kolam Panen',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'feed_id' => $feed->id,
            'target_harvest_weight_kg' => 100,
            'stocking_date' => '2026-05-01',
        ]);

        $this->actingAs($user)->post(route('ponds.feedings.store', $pond), [
            'feed_id' => $feed->id,
            'fed_at' => '2026-05-10',
            'quantity' => 5,
            'unit' => 'sak',
        ]);

        $this->actingAs($user)
            ->post(route('ponds.harvests.store', $pond), [
                'harvested_at' => '2026-05-16',
                'notes' => 'Panen pertama',
            ])
            ->assertRedirect(route('ponds.show', $pond));

        $this->assertDatabaseHas('pond_harvests', [
            'pond_id' => $pond->id,
            'harvested_at' => '2026-05-16 00:00:00',
            'fish_type' => 'Lele',
            'fish_count' => 1000,
            'target_harvest_weight_kg' => 100,
            'total_feed_weight_kg' => 150,
            'total_estimated_meat_kg' => 100,
            'feeding_count' => 1,
            'notes' => 'Panen pertama',
        ]);
        $this->assertDatabaseMissing('pond_feedings', [
            'pond_id' => $pond->id,
        ]);

        $pond->refresh();
        $this->assertNull($pond->feed_id);
        $this->assertNull($pond->harvest_date);
        $this->assertSame('2026-05-16', $pond->stocking_date->toDateString());
        $this->assertNull($pond->harvest_progress_percent);
    }
}
