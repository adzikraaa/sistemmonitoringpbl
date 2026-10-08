<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProposalStatusTest extends TestCase
{
    public function test_mahasiswa_proposal_page_shows_status_and_feedback(): void
    {
        $response = $this->get('/mahasiswa/proposal');

        $response->assertStatus(200)
            ->assertSee('Status Proposal')
            ->assertSee('Feedback Pembimbing')
            ->assertSee('Disetujui');
    }
}
