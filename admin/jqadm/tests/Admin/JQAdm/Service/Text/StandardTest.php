<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Admin\JQAdm\Service\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->view = \TestHelperJqadm::getView();
		$this->context = \TestHelperJqadm::getContext();

		$langManager = \Aimeos\MShop\Factory::createManager( $this->context, 'locale/language' );

		$this->view->pageLanguages = $langManager->searchItems( $langManager->createSearch() );
		$this->view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'service' )->createItem();

		$this->object = new \Aimeos\Admin\JQAdm\Service\Text\Standard( $this->context );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );

		$this->view->item = $manager->createItem();
		$result = $this->object->create();

		$this->assertContains( 'item-text', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );

		$this->view->item = $manager->findItem( 'unitcode', ['text'] );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Unittest service name', $result );
	}


	public function testDelete()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );

		$this->view->item = $manager->createItem();
		$result = $this->object->delete();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );

		$this->view->item = $manager->findItem( 'unitcode', ['text'] );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Unittest service name', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );
		$listTypeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'service/lists/type' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'text/type' );

		$listTypeId = $listTypeManager->findItem( 'default', [], 'text' )->getId();
		$typeId = $typeManager->findItem( 'name', [], 'service' )->getId();

		$item = $manager->createItem();

		$param = array(
			'site' => 'unittest',
			'text' => array(
				array(
					'text.id' => '',
					'text.content' => 'test name',
					'text.languageid' => 'de',
					'text.typeid' => $typeId,
					'service.lists.type' => 'default',
					'service.lists.typeid' => $listTypeId
				),
				array(
					'text.id' => '',
					'text.content' => 'short desc',
					'text.languageid' => 'de',
					'text.typeid' => $typeId,
					'service.lists.type' => 'default',
					'service.lists.typeid' => $listTypeId
				),
				array(
					'text.id' => '',
					'text.content' => 'long desc',
					'text.languageid' => 'de',
					'text.typeid' => $typeId,
					'service.lists.type' => 'default',
					'service.lists.typeid' => $listTypeId
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 3, count( $item->getListItems() ) );

		foreach( $item->getListItems( 'text' ) as $listItem )
		{
			$this->assertEquals( 'text', $listItem->getDomain() );

			$refItem = $listItem->getRefItem();
			$this->assertEquals( 'de', $refItem->getLanguageId() );
		}
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Service\Text\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->view = \TestHelperJqadm::getView();
		$this->view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'service' )->createItem();

		$object->setView( $this->view );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Service\Text\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$this->view = \TestHelperJqadm::getView();
		$this->view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'service' )->createItem();

		$object->setView( $this->view );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$this->object->getSubClient( 'unknown' );
	}
}
