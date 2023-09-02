<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('will not use final classes')
    ->expect('src\\')
    ->classes->not->toBeFinal();
