<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for debug information generated by the parser
 */
class debug_info_test extends TestCase {
	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom();
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	/** @dataProvider dataProvider_for_print_r */
	public function test_print_r($expected, $html)
	{
		$this->html->load($html);
		$this->assertEquals($expected, print_r($this->html, true));
	}

	public function dataProvider_for_print_r()
	{
		return array(
			'should return __debugInfo' => array(
				'expected' => file_get_contents(__DIR__ . '/data/debug_info/print_r_expected.txt'),
				'html' => file_get_contents(__DIR__ . '/data/debug_info/print_r_testdata.html')
		));
	}
}