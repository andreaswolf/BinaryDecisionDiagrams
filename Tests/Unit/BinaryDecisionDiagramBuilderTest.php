<?php
namespace AndreasWolf\BinaryDecisionDiagrams\Tests\Unit;


use AndreasWolf\BinaryDecisionDiagrams\BinaryDecisionDiagramBuilder;


class BinaryDecisionDiagramBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function diagramForOneVariablePassthroughFunctionIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A'));

		$subject->addAllowedInput(array('A' => FALSE), FALSE);
		$subject->addAllowedInput(array('A' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramForOneVariableInverterIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A'));

		$subject->addAllowedInput(array('A' => TRUE), FALSE);
		$subject->addAllowedInput(array('A' => FALSE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertTrue($diagram->getResultForInput(array('A' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramForBooleanAndOfTwoVariablesIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B'));

		$subject->addAllowedInput(array('A' => TRUE, 'B' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
	}

	/**
	 * @test
	 */
	public function partiallyDefinedInputForFunctionIsAccepted() {
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B'));

		$subject->addAllowedInput(array('A' => TRUE), TRUE);
		$subject->addAllowedInput(array('A' => FALSE, 'B' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
	}

}
