<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JQAdm\Product\Category;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperJqadm::view();
		$this->context = \TestHelperJqadm::context();

		$this->object = new \Aimeos\Admin\JQAdm\Product\Category\Standard( $this->context );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );

		$this->view->item = $manager->create();
		$result = $this->object->create();

		$this->assertStringContainsString( 'item-category', $result );
		$this->assertEmpty( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );

		$this->view->item = $manager->find( 'CNC', ['catalog'] );
		$result = $this->object->copy();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Internet&quot;/', $result );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Kaffee&quot;/', $result );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Neu&quot;/', $result );
	}


	public function testDelete()
	{
		$result = $this->object->delete();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );

		$this->view->item = $manager->find( 'CNC', ['catalog'] );
		$result = $this->object->get();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Internet&quot;/', $result );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Kaffee&quot;/', $result );
		$this->assertRegexp( '/&quot;catalog.label&quot;:&quot;Neu&quot;/', $result );
	}


	public function testSave()
	{
		$param = array(
			'category' => [[
				'product.lists.id' => '',
				'product.lists.type' => 'default',
				'catalog.id' => '-1',
			]]
		);

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$this->view->item = $manager->create()->setCode( 'jqadm:product/category' )->setId( -1 );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->save();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
		$this->assertEquals( 1, count( $this->view->item->getListItems( 'catalog' ) ) );
	}


	public function testSavePromotion()
	{
		$param = array(
			'category' => [[
				'product.lists.id' => '',
				'product.lists.type' => 'promotion',
				'catalog.id' => '-1',
			]]
		);

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$this->view->item = $manager->create()->setCode( 'jqadm:product/category' )->setId( -1 );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->save();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
		$this->assertEquals( 1, count( $this->view->item->getListItems( 'catalog' ) ) );
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Product\Category\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->view = \TestHelperJqadm::view();
		$this->view->item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$object->setView( $this->view );

		$this->expectException( \RuntimeException::class );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Product\Category\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$this->view = \TestHelperJqadm::view();
		$this->view->item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$object->setView( $this->view );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$object->save();
	}


	public function testSearch()
	{
		$this->assertEmpty( $this->object->search() );
	}


	public function testGetSubClient()
	{
		$this->expectException( \Aimeos\Admin\JQAdm\Exception::class );
		$this->object->getSubClient( 'unknown' );
	}
}
