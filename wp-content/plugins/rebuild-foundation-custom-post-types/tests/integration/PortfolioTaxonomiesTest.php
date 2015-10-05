<?php

class Rebuild_SitesTaxonomiesTest extends WP_UnitTestCase {

	protected $reg;

	public function setUp() {
		$this->reg = new Rebuild_Sites_Post_Type_Registrations;
	}

	public function testTaxonomyIsRegistered() {
		$this->assertArrayNotHasKey( 'rebuild_sites_category', get_taxonomies(), 'rebuild_sites_category is registered.' );
		$this->assertArrayNotHasKey( 'rebuild_sites_tag', get_taxonomies(), 'rebuild_sites_tag is registered.' );
		$this->reg->register();
		$this->assertArrayHasKey( $this->reg->taxonomies[0], get_taxonomies(), 'rebuild_sites_category not registered.' );
		$this->assertArrayHasKey( $this->reg->taxonomies[1], get_taxonomies(), 'rebuild_sites_tag not registered.' );
	}

	// public function testGetRebuild_SitesItem() {
	// 	$this->reg->register();

	// 	$item_args = array(
	// 		'post_type' => $this->reg->post_type,
	// 		'post_title' => 'Test',
	// 	);

	// 	$item = $this->factory->post->create_and_get( $item_args );

	// 	$this->assertEquals( $this->reg->post_type, $item->post_type );
	// 	$this->assertEquals( 'Test', $item->post_title );
	// }
	
	public function tearDown() {
		$this->reg->unregister();
	}

}
