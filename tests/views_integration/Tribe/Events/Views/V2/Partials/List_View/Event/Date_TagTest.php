<?php

namespace Tribe\Events\Views\V2\Partials\List_View\Event;

use Tribe\Test\PHPUnit\Traits\With_Post_Remapping;
use Tribe\Test\Products\WPBrowser\Views\V2\HtmlPartialTestCase;

class Date_TagTest extends HtmlPartialTestCase
{
	use With_Post_Remapping;

	protected $partial_path = 'list/event/date-tag';

	/**
	 * Test render with event
	 */
	public function test_render_with_event() {
		$event = $this->get_mock_event( 'events/single/1.json' );
		$this->assertMatchesSnapshot( $this->get_partial_html( [ 'event' => $event ] ) );
	}
}
