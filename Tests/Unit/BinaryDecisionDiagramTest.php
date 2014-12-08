<?php
namespace AndreasWolf\BinaryDecisionDiagrams\Tests\Unit;

use AndreasWolf\BinaryDecisionDiagrams\BinaryDecisionDiagram;


/**
 *
 *
 * @author Andreas Wolf <aw@foundata.net>
 */
class BinaryDecisionDiagramTests extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function diagramCanDecideOnSimpleTrueFalseCase() {
		$nodes = array(
			array(), array(), array('A', 0, 1),
		);
		$subject = new BinaryDecisionDiagram(array('A'), $nodes);

		$this->assertFalse($subject->getResultForInput(array('A' => FALSE)));
		$this->assertTrue($subject->getResultForInput(array('A' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramDecidesCorrectlyOnSimpleInverter() {
		$nodes = array(
			array(), array(), array('A', 1, 0),
		);
		$subject = new BinaryDecisionDiagram(array('A'), $nodes);

		$this->assertFalse($subject->getResultForInput(array('A' => TRUE)));
		$this->assertTrue($subject->getResultForInput(array('A' => FALSE)));
	}

	/**
	 * @test
	 */
	public function diagramDecidesCorrectlyIfBothCasesHaveSameOutput() {
		$nodes = array(
			array(), array(), array('A', 0, 0),
		);
		$subject = new BinaryDecisionDiagram(array('A'), $nodes);

		$this->assertFalse($subject->getResultForInput(array('A' => FALSE)));
		$this->assertFalse($subject->getResultForInput(array('A' => FALSE)));
	}

	/**
	 * @test
	 */
	public function diagramCorrectlyEvaluatesMultipleInputs() {
		// this function represents A && B
		$nodes = array(
			array(), array(), array('B', 0, 1), array('A', 0, 2)
		);
		$subject = new BinaryDecisionDiagram(array('A', 'B'), $nodes);

		$this->assertFalse($subject->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertFalse($subject->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
		$this->assertFalse($subject->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($subject->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
	}

}
