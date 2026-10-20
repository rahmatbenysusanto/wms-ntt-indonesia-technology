<?php

namespace Tests\Feature;

use App\Models\PackingList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackingListRepackTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create([
            'username' => 'user' . User::count(),
            'role' => 'admin',
        ]);
    }

    private function packingList(string $status): PackingList
    {
        return PackingList::create([
            'status' => $status,
            'qty_item' => 1,
            'qty' => 1,
            'created_by' => $this->user()->id,
        ]);
    }

    public function test_repack_saved_on_released_packing_list(): void
    {
        $pl = $this->packingList('released');

        $this->actingAs($this->user())
            ->post(route('outbound.packaging-list.repack'), [
                'id' => $pl->id,
                'koli_qty' => 1,
                'length' => 120,
                'width' => 80,
                'height' => 100,
                'weight' => 45.5,
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $pl->refresh();
        $this->assertSame(1, $pl->koli_qty);
        $this->assertEquals(120, $pl->repack_length);
        $this->assertEquals(45.5, $pl->repack_weight);
        $this->assertNotNull($pl->repacked_at);
        $this->assertNotNull($pl->repacked_by);
    }

    public function test_repack_rejected_on_draft_packing_list(): void
    {
        $pl = $this->packingList('draft');

        $this->actingAs($this->user())
            ->post(route('outbound.packaging-list.repack'), [
                'id' => $pl->id,
                'koli_qty' => 1,
                'length' => 120,
                'width' => 80,
                'height' => 100,
                'weight' => 45,
            ])
            ->assertOk()
            ->assertJson(['status' => false]);

        $this->assertNull($pl->refresh()->repacked_at);
    }

    public function test_repack_requires_dimensions(): void
    {
        $pl = $this->packingList('released');

        $this->actingAs($this->user())
            ->postJson(route('outbound.packaging-list.repack'), [
                'id' => $pl->id,
                'koli_qty' => 1,
            ])
            ->assertStatus(422);
    }

    public function test_final_pdf_renders_repack_summary(): void
    {
        $pl = $this->packingList('released');
        $pl->update([
            'koli_qty' => 2,
            'repack_length' => 120,
            'repack_width' => 80,
            'repack_height' => 100,
            'repack_weight' => 45.5,
            'repacked_at' => now(),
        ]);

        $html = view('pdf.packing-list', ['packingList' => $pl->load('details', 'customer'), 'mode' => 'final'])->render();

        $this->assertStringContainsString('FINAL (REPACKED)', $html);
        $this->assertStringContainsString('120 x 80 x 100', $html);
        $this->assertStringContainsString('Total Koli : 2 Koli', $html);
    }
}
