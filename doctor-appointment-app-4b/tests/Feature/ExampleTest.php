<?php

it('redirects the home page to admin', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});
