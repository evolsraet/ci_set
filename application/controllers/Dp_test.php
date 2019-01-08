<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    디자인 패턴 테스트
    - classes 폴더
    - kmh_helper classed 오토로드
        - 샘플 : Builder 디자인 패턴
 */

use Builder\Director;
use Builder\CarBuilder;
use Builder\BikeBuilder;

class Dp_test extends CI_Controller {

	protected $director;

	public function index()
	{
        $this->setUp();
        // kmh_print( $this->director );

        $my_car = $this->director->build( new CarBuilder() );
        $my_bike = $this->director->build( new BikeBuilder() );

        // kmh_print( $my_car );
        // kmh_print( $my_bike ) );

        kmh_print( 'my_car Parts : ' );
        kmh_print( $my_car->getParts() );
        kmh_print( 'my_bike Parts : ' );
        kmh_print( $my_bike->getParts() );

        $builders = $this->getBuilder();
        kmh_print( $builders );
	}

    protected function setUp()
    {
        $this->director = new Director();
    }

    public function getBuilder()
    {
        return array(
            array(new CarBuilder()),
            array(new BikeBuilder())
        );
    }
}
