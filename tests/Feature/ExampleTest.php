<?php

test('it can open the welcome page', function () {
    $this->get('/')
        ->assertStatus(200);
});
