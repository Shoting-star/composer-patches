<?php

/**
 * @file
 * Test the RootComposer resolver.
 */

namespace cweagans\Composer\Tests;

use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Composer\Composer;
use Composer\Installer\PackageEvent;
use Composer\IO\NullIO;
use Composer\Package\RootPackage;
use cweagans\Composer\Resolvers\RootComposer;
use cweagans\Composer\Tests\Dummy\PatchCollectionDummy;

class RootComposerResolverTest extends Unit
{
    public function testResolve()
    {
        $patch_collection = new PatchCollectionDummy();
        $root_package = new RootPackage('test/package', '1.0.0.0', '1.0.0');
        $root_package->setExtra(['patches' => []]);
        $composer = new Composer();
        $composer->setPackage($root_package);
        $io = new NullIO();
        $event = Stub::make(PackageEvent::class, []);

        // Empty patch list.
        $resolver = new RootComposer($composer, $io);
        $resolver->resolve($patch_collection, $event);
        $this->assertEquals(0, $patch_collection->getPatchCount());

        // One patch.
        $patch = new \stdClass();
        $patch->url = 'http://drupal.org';
        $patch->description = 'Test patch';
        $root_package->setExtra([
            'patches' => [
                'test/package' => [
                    0 => $patch,
                ]
            ]
        ]);
        $composer->setPackage($root_package);

        $resolver = new RootComposer($composer, $io);
        $resolver->resolve($patch_collection, $event);
        $this->assertEquals(1, $patch_collection->getPatchCount());
    }

}
